<?php

namespace App\Livewire\Transfer\Multiple;

use App\Models\MultipleTransfer;
use App\Models\User;
use App\Models\UserTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ToSwiftpayAccounts extends Component
{
    use LivewireAlert;

    public $swiftpayAccounts = [];
    public $username;

    public $amount;
    public $description;
    public $showPreviewForm = false;
    public $showSuccessfullModal = false;

    public $totalAmount = 0;

    public $pin;

    public $user;
    public $name;

    public MultipleTransfer $transaction;

    public $source_link;

    public function mount()
    {
        // $users = User::latest()->get()->take(3);

        // foreach ($users as $key => $user) {
        //     $this->swiftpayAccounts[] = [
        //         'user' => $user,
        //         'name' => $user->name,
        //         'amount' => ($key + 1) * 1000,
        //         'description' => 'This is ' . $key + 1 . ' description'
        //     ];
        // }

        // $this->totalAmount = collect($this->swiftpayAccounts)->sum('amount');

        $this->source_link = request()->query('source_link');
    }

    public function setUser()
    {

        if (strtolower($this->username) == strtolower(auth()->user()->username)) {
            $this->addError('username', 'You cannot transfer to yourself');
            return;
        }

        $this->validate(
            [
                'username' => 'required|exists:users,username'
            ],
            [
                'username.exists' => 'The user does not exist'
            ]
        );

        $this->user = User::where('username', $this->username)->first();

        $this->name = $this->user->name;
    }


    public function addAccount()
    {
        $this->validate(
            [
                'username' => 'required|exists:users,username',
                'amount' => 'required|numeric|min:1',
                'description' => 'required',
            ]
        );

        $user = $this->user;

        // check if the user is already added
        if (collect($this->swiftpayAccounts)->contains('user.id', $user->id)) {
            $this->alert('error', 'User already added');
            return;
        }

        $this->swiftpayAccounts[] = [
            'user' => $user,
            'name' => $user->name,
            'amount' => $this->amount,
            'description' => $this->description
        ];

        $this->totalAmount += $this->amount;

        $this->reset(['username', 'amount', 'description', 'user', 'name']);

        $this->dispatch('close-modal');
    }

    public function clearDetails()
    {
        $this->reset(['username', 'amount', 'description', 'user', 'name']);
    }


    public function removeAccount($userId)
    {

        $account = collect($this->swiftpayAccounts)->first(function ($account) use ($userId) {
            return $account['user']['id'] == $userId;
        });

        $this->swiftpayAccounts = collect($this->swiftpayAccounts)->filter(function ($account) use ($userId) {
            return $account['user']['id'] != $userId;
        });

        $this->totalAmount -= $account['amount'];
    }

    public function confirm()
    {
        if (count($this->swiftpayAccounts) < 1) {
            return $this->alert('error', 'Please add at least one account');
        }


        if ($this->totalAmount > auth()->user()->wallet_balance) {
            return $this->addError('amount', 'Insufficient funds');
        }



        $this->showPreviewForm = true;
    }

    public function transfer()
    {
        $this->validate([
            'pin' => 'required|numeric|digits:4',
        ]);

        if (!Hash::check($this->pin, auth()->user()->pin)) {
            $this->alert('error', 'Incorrect pin');
            return;
        }

        DB::beginTransaction();

        foreach ($this->swiftpayAccounts as $account) {
            $user = User::find($account['user']['id']);
            $user->increment('wallet_balance', $account['amount']);
            $user->save();

            $user->transactions()->create([
                'amount' => $account['amount'],
                'message' => "Received " . $account['amount'] . " from " . auth()->user()->name,
                'type' => UserTransaction::TYPE_CREDIT,
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'source' => UserTransaction::SOURCE_SWIFTPAY_TRANSFER
            ]);
        }

        auth()->user()->decrement('wallet_balance', $this->totalAmount);

        auth()->user()->transactions()->create([
            'amount' => $this->totalAmount,
            'message' => "Transferred N{$this->totalAmount} to " . $this->swiftpayAccounts[0]['name'] . " and " . count($this->swiftpayAccounts) - 1 .  " other accounts",
            'type' => UserTransaction::TYPE_DEBIT,
            'status' => UserTransaction::STATUS_SUCCESSFUL,
            'source' => UserTransaction::SOURCE_MULTIPLE_SWIFTPAY_TRANSFER
        ]);

        $transaction = MultipleTransfer::create([
            'accounts' => implode(',', array_column($this->swiftpayAccounts, 'name')),
            'type' => MultipleTransfer::TYPE_SWIFTPAY,
            'total_amount' => $this->totalAmount,
            'user_id' => auth()->id(),
            'status' => MultipleTransfer::STATUS_SUCCESSFUL,
            'reference' => 'SWPYMUL-' . time(),
            'source_link' => $this->source_link
        ]);

        DB::commit();

        $this->transaction = $transaction;

        $this->showSuccessfullModal = true;
    }

    public function render()
    {
        return view('livewire.transfer.multiple.to-swiftpay-accounts');
    }
}
