<?php

namespace App\Livewire;

use App\Models\GatewayTransaction;
use App\Models\Transfer;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Home extends Component
{
    use LivewireAlert;

    public $showBalance = true;

    public function mount()
    {
        $user = auth()->user();

        if (!$user->is_otp_verified) {
            return $this->flash('error', 'Please verify your email address to continue', [], route('verification'));
        }


        if (is_null($user->pin)) {
            return $this->flash('error', 'Please create a transaction pin to continue', [], route('onboarding'));
        }


        $this->showBalance = session('showBalance', true);
    }

    public function toggleShowBalance()
    {
        $this->showBalance = !$this->showBalance;

        session()->put('showBalance', $this->showBalance);
    }

    public function render()
    {
        $user = auth()->user();
        $kyb = $user->merchantKyb;

        $recentGatewayTransactions = GatewayTransaction::query()
            ->where('merchant_id', $user->id)
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $recentWithdrawals = $user->bankTransfers()
            ->where('type', Transfer::TYPE_BANK)
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        return view('livewire.home', [
            'kyb' => $kyb,
            'virtualAccount' => $user->virtualAccount,
            'apiKeysCount' => $user->merchantApiKeys()->whereNull('revoked_at')->count(),
            'recentGatewayTransactions' => $recentGatewayTransactions,
            'recentWithdrawals' => $recentWithdrawals,
        ]);
    }
}
