<?php

namespace App\Livewire\Savings\InterestSavings;

use App\Helpers\SettingHelper;
use App\Models\Savings;
use App\Models\UserTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Create extends Component
{
    use LivewireAlert;

    public $name, $auto_save_amount, $end_date;

    public $amount;


    public $type = 'locked';

    public $if_auto_save = false;

    public $frequency = 'daily';

    public $interest;

    public $daily_earnings = 0;
    public $weekly_earnings = 0;
    public $monthly_earnings = 0;

    public $showPreviewForm = false;

    public $pin;

    public $fee;

    public $if_has_locked_savings = false;

    public $total_amount;

    public function mount()
    {
        $this->setInterestRate();

        $this->fee = SettingHelper::get(SettingHelper::CREATE_INTEREST_SAVINGS_FEE);

        $this->if_has_locked_savings = Savings::where('user_id', auth()->id())
            ->where('type', Savings::TYPE_LOCKED)
            ->where('status', Savings::STATUS_ACTIVE)
            ->exists();
    }

    public function render()
    {
        return view('livewire.savings.interest-savings.create')->title("Create Interest Savings");
    }

    public function updatedType()
    {
        $this->setInterestRate();

        if ($this->amount && $this->amount > 0) {
            $this->calculateEarnings($this->amount);
        }
    }

    public function calculateEarnings($amount)
    {
        $this->daily_earnings = getPercentageAmount($amount, $this->interest);
        $this->weekly_earnings = getPercentageAmount($amount, $this->interest * 7);
        $this->monthly_earnings = getPercentageAmount($amount, $this->interest * 30);
    }

    public function updatedAmount($value)
    {
        if ($value && $value > 0) {
            $this->calculateEarnings($value);
        } else {
            $this->daily_earnings = 0;
            $this->weekly_earnings = 0;
            $this->monthly_earnings = 0;
        }
    }

    public function setInterestRate()
    {
        if ($this->type == 'locked') {
            $this->interest = SettingHelper::get(SettingHelper::LOCKED_SAVINGS_INTEREST);
        } else {
            $this->interest = SettingHelper::get(SettingHelper::UNLOCKED_SAVINGS_INTEREST);
        }
    }

    public function confirmSavings()
    {
        $this->validate([
            'name' => 'required|min:5|max:255',
            'amount' => 'required|numeric|min:100',
            'end_date' => 'nullable|date',
            'if_auto_save' => 'required|boolean',
        ]);

        $this->total_amount = $this->amount + $this->fee;



        if ($this->if_auto_save) {
            $this->validate([
                'auto_save_amount' => 'required|numeric|min:100',
                'frequency' => 'required|string|in:weekly,monthly,daily',
            ]);
        }

        // $this->daily_earnings = getPercentageAmount($this->amount, $this->interest);
        // $this->weekly_earnings = getPercentageAmount($this->amount, $this->interest * 7);
        // $this->monthly_earnings = getPercentageAmount($this->amount, $this->interest * 30);

        $this->showPreviewForm = true;
    }

    public function create()
    {
        $data = $this->validate([
            'name' => 'required|min:5|max:255',
            'type' => 'required|string',
            'amount' => 'required|numeric|min:100',
            'end_date' => 'nullable|date',
            'if_auto_save' => 'required|boolean',
            'pin' => 'required|digits:4'
        ]);

        if ($this->total_amount > auth()->user()->wallet_balance) {
            return  $this->alert('error', 'Insufficient wallet balance');
        }

        if (!Hash::check($this->pin, auth()->user()->pin)) {
            $this->alert('error', 'Incorrect pin');
            return;
        }



        if ($this->if_auto_save) {
            $this->validate([
                'auto_save_amount' => 'required|numeric|min:100',
                'frequency' => 'required|string|in:weekly,monthly,daily',
            ]);
        }

        $data['status'] = Savings::STATUS_ACTIVE;
        $data['balance'] = $this->amount;
        $data['interest_accumulated'] = 0;
        unset($data['amount']);
        unset($data['pin']);

        if ($this->if_auto_save) {
            $data['auto_save_amount'] = $this->auto_save_amount;
            $data['frequency'] = $this->frequency;
            $data['auto_save_last_deduction'] = now();
        }


        DB::beginTransaction();

        $savings = auth()->user()->savings()->create($data);



        auth()->user()->decrement('wallet_balance', $this->total_amount);

        // record user transaction
        auth()->user()->transactions()->create([
            'amount' => $this->total_amount,
            'type' => UserTransaction::TYPE_DEBIT,
            'message' => "Created a  {$this->name} savings and deposited " . formatNumber($this->amount),
            'status' => UserTransaction::STATUS_SUCCESSFUL,
            'source' => UserTransaction::SOURCE_SAVE_WITH_INTEREST
        ]);

        DB::commit();

        return $this->flash('success', 'Savings created successfully', [], route('savings.interest-savings'));
    }
}
