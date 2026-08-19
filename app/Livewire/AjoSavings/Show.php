<?php

namespace App\Livewire\AjoSavings;

use App\Helpers\SettingHelper;
use App\Http\Livewire\WithRateLimiting;
use App\Models\AjoSavings;
use App\Models\UserTransaction;
use App\Services\HashIdService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Show extends Component
{
    use LivewireAlert;
    use WithRateLimiting;

    public $savings;

    public $data = [];
    public $labels = [];
    public $early_close_fee;


    public $pin;

    public function mount($hash_id)
    {
        $this->savings = (new HashIdService)->getRecord($hash_id, AjoSavings::class);

        // dd($this->savings->payback_amount, $this->savings->pay_back_amount,);

        $this->data =  $this->savings->transactions->map(function ($transaction) {
            if ($transaction->status == UserTransaction::STATUS_SUCCESSFUL) {
                return $transaction->amount;
            } else {
                return 0;
            }
        });


        $dates =  $this->savings->transactions->pluck('created_at');

        $this->labels =  $dates->map(function ($date) {
            return $date->format('Y-m-d');
        });

        // dd($this->data, $this->labels);

        $this->early_close_fee = SettingHelper::get(SettingHelper::AJO_SAVINGS_EARLY_CLOSE_FEE);
    }

    public function render()
    {
        return view('livewire.ajo-savings.show')->title('Ajo Savings Details');
    }

    public function endSavings()
    {

        try {
            $this->rateLimit('endSavings', 1, 60);

            DB::beginTransaction();
            $totalFee = getTotalFee($this->savings->balance, 0,  SettingHelper::get(
                SettingHelper::AJO_SAVINGS_EARLY_CLOSE_FEE
            ));

            $referrer_bonus = 0;

            if ($this->savings->referred_by) {
                $referrer_bonus =
                    getTotalFee($this->savings->balance, 0,  SettingHelper::get(
                        SettingHelper::AJO_SAVINGS_REFERRAL_BONUS
                    ));

                $this->savings->referrer->increment('wallet_balance', $referrer_bonus);

                $this->savings->referrer->transactions()->create([
                    'amount' => $referrer_bonus,
                    'type' => UserTransaction::TYPE_CREDIT,
                    'message' => 'You earned referral bonus for ' . $this->savings->user->name . ' Ajo savings account',
                    'status' => UserTransaction::STATUS_SUCCESSFUL,
                    'source' => UserTransaction::SOURCE_AJO_SAVINGS,
                    'fee' => 0 // No fee for referral bonuses
                ]);
            }

            $amount = $this->savings->balance - $totalFee;

            $this->savings->user->increment('wallet_balance', $amount);

            $this->savings->user->transactions()->create([
                'amount' => $amount,
                'type' => UserTransaction::TYPE_CREDIT,
                'message' => 'You closed ' . $this->savings->type . ' Ajo savings account   before the end date',
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'source' => UserTransaction::SOURCE_AJO_SAVINGS,
                'fee' => $totalFee // Fee charged for early closure
            ]);

            $this->savings->update([
                'status' => AjoSavings::STATUS_CLOSED,
                'amount_earned' => $amount,
                'end_date' => now(),
            ]);

            DB::commit();

            $this->flash('success', 'Ajo savings account closed successfully', [], route('ajo-savings.index'));
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
            return;
        }
    }

    public function payback()
    {
        $this->validate([
            'pin' => 'required|digits:4',
        ]);

        if (!Hash::check($this->pin, auth()->user()->pin)) {
            $this->alert('error', 'Incorrect pin');
            return;
        }

        // check if user has enough balance
        if (auth()->user()->wallet_balance < $this->savings->payback_amount) {
            $this->alert('error', 'Insufficient balance');
            return;
        }

        DB::beginTransaction();

        $amount = $this->savings->payback_amount;

        auth()->user()->decrement('wallet_balance', $amount);

        UserTransaction::create([
            'user_id' => auth()->id(),
            'amount' => $amount,
            'type' => UserTransaction::TYPE_DEBIT,
            'message' => 'You paid back the outstanding balance of your ' . $this->savings->type . ' Ajo savings',
            'status' => UserTransaction::STATUS_SUCCESSFUL,
            'source' => UserTransaction::SOURCE_AJO_SAVINGS,
            'fee' => 0 // No fee for payback
        ]);

        $this->savings->increment('balance', $amount);

        $this->savings->transactions()->create([
            'amount' => $amount,
            'message' => 'Payback amount deposited',
            'status' => UserTransaction::STATUS_SUCCESSFUL,
            'source' => 'wallet'
        ]);

        $this->savings->transactions()->where('status', UserTransaction::STATUS_FAILED)->update([
            'status' => UserTransaction::STATUS_SUCCESSFUL
        ]);

        DB::commit();

        $this->flash('success', 'Payback amount deposited successfully', [], route('ajo-savings.show', $this->savings->hash_id));
    }
}
