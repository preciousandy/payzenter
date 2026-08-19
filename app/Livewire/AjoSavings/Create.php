<?php

namespace App\Livewire\AjoSavings;

use App\Models\AjoSavings;
use App\Models\User;
use App\Models\UserTransaction;
use App\Services\HashIdService;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Create extends Component
{
    use LivewireAlert;
    public $type;

    public  $amount, $end_date, $referral_code;
    public $payment_method = 'wallet';

    public $card_token;

    public function mount()
    {
        $this->card_token = auth()->user()->card_token;
    }

    public function render()
    {
        return view('livewire.ajo-savings.create')->title('Create Ajo Savings');
    }

    public function create()
    {
        $data =  $this->validate([
            'type' => 'required',
            'amount' => 'required|numeric',
            'end_date' => 'required|date',
            'payment_method' => 'required',
        ]);

        if ($this->referral_code) {
            $referred_by = (new HashIdService)->getRecord($this->referral_code, User::class);

            if ($referred_by->id == auth()->id()) {
                return $this->alert('error', 'You cannot refer yourself');
            }

            $data['referred_by'] = $referred_by->id;
        }

        if ($this->payment_method == 'card' && !$this->card_token) {
            $this->alert('error', 'Please add a card to your account to continue');
            return;
        }


        DB::beginTransaction();

        // check if user has enough balance
        if ($data['payment_method'] == 'wallet') {
            if (auth()->user()->wallet_balance < $data['amount']) {
                return    $this->addError('amount', 'Insufficient wallet balance');
            }

            $data['balance'] =  $data['amount'];

            auth()->user()->decrement('wallet_balance', $data['amount']);

            auth()->user()->transactions()->create([
                'amount' => $data['amount'],
                'source' => UserTransaction::SOURCE_AJO_SAVINGS,
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'message' =>  "Created and a {$data['type']} Ajo Savings and deposited N{$data['amount']}",
                'type' => UserTransaction::TYPE_DEBIT,
                'fee' => 0 // No fee for AJO savings
            ]);
        } else {
            $data['balance'] = 0;
        }


        $data['status'] = AjoSavings::STATUS_ACTIVE;

        $savings =  auth()->user()->ajoSavings()->create($data);

        $savings->transactions()->create([
            'amount' => $data['amount'],
            'source' => 'wallet',
            'status' => 'success'
        ]);

        DB::commit();

        $this->flash('success', 'Ajo savings created successfully', [], route('ajo-savings.index'));
    }
}
