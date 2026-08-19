<?php

namespace App\Livewire\Transfer\SwiftPay;

use App\Models\Beneficiary;
use App\Models\SwiftpayTransfer;
use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Beneficiaries extends Component
{
    use LivewireAlert;

    public $recentTransfers = [];
    public $recentTransfersCopy = [];

    public $favorites = [];

    public $query = '';

    public $favoritesUsernames = [];

    public function mount()
    {
        $this->recentTransfers = SwiftpayTransfer::where('user_id', auth()->id())
            ->latest()
            ->get()
            ->unique('beneficiary_id');
        $this->recentTransfersCopy = $this->recentTransfers;

        $this->favoritesUsernames = Beneficiary::where('user_id', auth()->id())
            ->where('type', Beneficiary::TYPE_SWIFTPAY)
            ->get()->pluck('swiftpay_username')->toArray();

        $this->favorites = User::whereIn('username', $this->favoritesUsernames)->get();
    }

    public function toggleBeneficiary($id)
    {
        $user = User::find($id);

        $beneficiary = Beneficiary::where('user_id', auth()->id())
            ->where('swiftpay_username', $user->username)
            ->first();

        if ($beneficiary) {
            $beneficiary->delete();
            return  $this->flash('success', 'Beneficiary removed successfully', [], route('transfer.swiftpay.beneficiaries'));
        }

        Beneficiary::create([
            'user_id' => auth()->id(),
            'swiftpay_username' => $user->username,
            'type' => Beneficiary::TYPE_SWIFTPAY,
            'acct_name' => $user->name,
        ]);

        return $this->flash('success', 'Beneficiary added successfully', [], route('transfer.swiftpay.beneficiaries'));
    }

    public function transferToUser($username)
    {
        $user = User::whereUsername($username)->first();

        return redirect()->route('transfer.swiftpay.user', $user->hash_id);
    }


    public function updatedQuery()
    {
        $this->recentTransfers = $this->recentTransfersCopy->filter(function ($transfer) {
            return strpos(strtolower($transfer->receiver->name), strtolower($this->query)) !== false;
        });
    }


    public function render()
    {
        return view('livewire.transfer.swift-pay.beneficiaries')->title('SwiftPay Transfer Beneficiaries');
    }
}
