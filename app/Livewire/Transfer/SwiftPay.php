<?php

namespace App\Livewire\Transfer;

use App\Models\Beneficiary;
use App\Models\SwiftpayTransfer;
use App\Models\User;
use App\Models\UserTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class SwiftPay extends Component
{
    use LivewireAlert;

    public User  $user;
    public  $name;
    public $username, $amount;
    public $pin;
    public $description;
    public $showPreviewForm = false;
    public $showSuccessfullModal = false;

    public $ifAddtoBeneficiary = false;

    public $recentTransfers = [];

    public $transaction;

    public $source_link;

    public function mount()
    {
        $this->recentTransfers = auth()->user()->swiftpayTransfers()->latest()->limit(5)->get()->unique('beneficiary_id');

        $this->source_link = request()->query('source_link');

        // add test data
        // $this->username = 'christalk';
        // $this->amount = 1000;
        // $this->description = 'Test transfer';
        // $this->user = User::where('username', $this->username)->first();
    }

    public function render()
    {
        return view('livewire.transfer.swift-pay')->title('SwiftPay Transfer');
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

    public function confirmTransfer()
    {
        $this->validate([
            'amount' => ['required', 'numeric', 'min:100'],
            'description' => 'nullable|string|max:255',
        ]);


        if ($this->amount > auth()->user()->wallet_balance) {
            $this->alert('error', 'Insufficient funds');
            return;
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

        try {

            DB::beginTransaction();
            $this->user->wallet_balance += $this->amount;
            $this->user->save();

            auth()->user()->update([
                'wallet_balance' => auth()->user()->wallet_balance - $this->amount
            ]);

            auth()->user()->transactions()->create([
                'amount' => $this->amount,
                'type' => UserTransaction::TYPE_DEBIT,
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'message' => 'Transfered ' . $this->amount . ' to ' . $this->user->name,
                'source' => UserTransaction::SOURCE_SWIFTPAY_TRANSFER,
                'fee' => 0 // No fee for SwiftPay transfers
            ]);

            $this->user->transactions()->create([
                'amount' => $this->amount,
                'type' => UserTransaction::TYPE_CREDIT,
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'message' => 'Received ' . $this->amount . ' from ' . auth()->user()->name,
                'source' => UserTransaction::SOURCE_SWIFTPAY_TRANSFER,
                'fee' => 0 // No fee for SwiftPay transfers
            ]);

            // if ($this->ifAddtoBeneficiary) {
            //     Beneficiary::create([
            //         'user_id' => auth()->id(),
            //         'type' => Beneficiary::TYPE_SWIFTPAY,
            //         'swiftpay_username' => $this->user->username,
            //         'acct_name' => $this->user->name,
            //     ]);
            // }

            $this->transaction =   SwiftpayTransfer::create([
                'user_id' => auth()->id(),
                'beneficiary_id' => $this->user->id,
                'reference' => 'SWPYTRNFR' . now()->timestamp,
                'description' => 'SWPY/' . $this->description,
                'amount' => $this->amount,
                'status' => SwiftpayTransfer::STATUS_SUCCESSFUL,
                'source_link' => $this->source_link
            ]);

            DB::commit();

            $this->showPreviewForm = false;

            $this->showSuccessfullModal = true;

            // return    $this->flash('success', 'Transfer successful', [], route('transfer.index'));
        } catch (\Throwable $th) {

            // auth()->user()->transactions()->create([
            //     'amount' => $this->amount,
            //     'type' => UserTransaction::TYPE_DEBIT,
            //     'status' => UserTransaction::STATUS_FAILED,
            //     'message' => 'Transfered ' . $this->amount . ' to ' . $this->user->name,
            //     'source' => UserTransaction::SOURCE_SWIFTPAY_TRANSFER
            // ]);


            return $this->alert('error', $th->getMessage());
        }
    }
}
