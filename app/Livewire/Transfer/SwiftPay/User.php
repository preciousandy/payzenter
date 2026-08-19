<?php

namespace App\Livewire\Transfer\SwiftPay;

use App\Models\Beneficiary;
use App\Models\SwiftpayTransfer;
use App\Models\User as ModelsUser;
use App\Models\UserTransaction;
use App\Services\HashIdService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class User extends Component
{
    use LivewireAlert;

    public $user;
    public $amount;
    public $pin;
    public $description = "";
    public $showPreviewForm = false;

    public $ifAddtoBeneficiary = false;

    public $isABeneficiary = false;

    public $showSuccessfullModal = false;

    public $transaction;

    public $source_link;


    public function mount($hashId)
    {
        $this->user = (new HashIdService)->getRecord($hashId, ModelsUser::class);

        $this->isABeneficiary = auth()->user()->beneficiaries()->where('type', Beneficiary::TYPE_SWIFTPAY)->where('swiftpay_username', $this->user->username)->exists();

        $this->source_link = request()->query('source_link');
    }

    public function render()
    {
        return view('livewire.transfer.swift-pay.user')->title('Transfer to SwiftPay User');
    }

    // public function test()
    // {
    //     dd($this->pin);
    // }

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

        if ($this->user->username == auth()->user()->username) {
            $this->alert('error', 'You cannot transfer to yourself');
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
                'source' => UserTransaction::SOURCE_SWIFTPAY_TRANSFER
            ]);

            $this->user->transactions()->create([
                'amount' => $this->amount,
                'type' => UserTransaction::TYPE_CREDIT,
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'message' => 'Received ' . $this->amount . ' from ' . auth()->user()->name,
                'source' => UserTransaction::SOURCE_SWIFTPAY_TRANSFER
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
        } catch (\Throwable $th) {

            // auth()->user()->transactions()->create([
            //     'amount' => $this->amount,
            //     'type' => UserTransaction::TYPE_DEBIT,
            //     'status' => UserTransaction::STATUS_FAILED,
            //     'message' => 'Transfered ' . $this->amount . ' to ' . $this->user->name,
            //     'source' => UserTransaction::SOURCE_SWIFTPAY_TRANSFER
            // ]);

            return redirect()->back()->with('error', 'An error occurred');
        }
    }
}
