<?php

namespace App\Livewire;

use App\Models\GatewayTransaction;
use App\Models\Transfer;
use App\Models\UserVirtualAccount;
use App\Models\VirtualAccount;
use Carbon\Carbon;
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
        $today = Carbon::today();

        // 1. Transaction count received today so far
        $todayTransactionsCount = GatewayTransaction::query()
            ->where('merchant_id', $user->id)
            ->whereDate('created_at', $today)
            ->count();

        $todaySuccessfulCount = GatewayTransaction::query()
            ->where('merchant_id', $user->id)
            ->where('status', 'success')
            ->whereDate('created_at', $today)
            ->count();

        // 2. Today's payments: exact amount received by PayZenter on merchant's website today only
        $todayPaymentsKobo = GatewayTransaction::query()
            ->where('merchant_id', $user->id)
            ->where('status', 'success')
            ->whereDate('created_at', $today)
            ->sum('amount');
        $todayPaymentsAmount = $todayPaymentsKobo > 0 ? ((float) $todayPaymentsKobo / 100) : 0;

        // 3. Virtual Accounts: count of accounts generated with this PayZenter account
        $generatedVaCount = VirtualAccount::query()
            ->where('user_id', $user->id)
            ->count();
        $dedicatedVaCount = UserVirtualAccount::query()
            ->where('user_id', $user->id)
            ->count();
        $totalVirtualAccounts = max($generatedVaCount, $dedicatedVaCount);

        // Virtual account total collections volume
        $vaVolumeKobo = GatewayTransaction::query()
            ->where('merchant_id', $user->id)
            ->where('status', 'success')
            ->where(function ($q) {
                $q->where('method', 'virtual_account')
                  ->orWhere('method', 'transfer');
            })
            ->sum('amount');
        $virtualAccountsVolume = $vaVolumeKobo > 0 ? ((float) $vaVolumeKobo / 100) : 0;

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

        // Calculate Onboarding / Activation progress
        $hasPersonalInfo = (bool) ($kyb && $kyb->surname && $kyb->first_name && $kyb->last_name && $kyb->address && ($kyb->identity_document_path || $kyb->proof_of_address_path));
        $hasBusinessInfo = (bool) ($kyb && $kyb->business_name && $kyb->business_email && $kyb->business_type && $kyb->bvn);
        $hasSettlement = (bool) ($kyb && $kyb->settlement_account_number && $kyb->settlement_bank_id && $kyb->settlement_account_name);
        $hasDocs = (bool) ($kyb && $kyb->cac_registration_number && ($kyb->business_resolution_path || $kyb->company_profile_path || $kyb->status_report_path));
        $kybSubmitted = (bool) optional($kyb)->submitted_at;
        $hasPin = !is_null($user->pin);

        $completedStepsCount = 1; // Step 0: Account created & verified
        if ($hasPersonalInfo) $completedStepsCount++;
        if ($hasBusinessInfo) $completedStepsCount++;
        if ($hasSettlement) $completedStepsCount++;
        if ($kybSubmitted || ($hasDocs && $hasPin)) $completedStepsCount = 5;

        $activationProgress = min(100, (int) round(($completedStepsCount / 5) * 100));
        $isActivated = $kybSubmitted && $hasSettlement && $hasPin;

        return view('livewire.home', [
            'kyb' => $kyb,
            'virtualAccount' => $user->virtualAccount,
            'apiKeysCount' => $user->merchantApiKeys()->whereNull('revoked_at')->count(),
            'todayTransactionsCount' => $todayTransactionsCount,
            'todaySuccessfulCount' => $todaySuccessfulCount,
            'todayPaymentsAmount' => $todayPaymentsAmount,
            'totalVirtualAccounts' => $totalVirtualAccounts,
            'virtualAccountsVolume' => $virtualAccountsVolume,
            'recentGatewayTransactions' => $recentGatewayTransactions,
            'recentWithdrawals' => $recentWithdrawals,
            'hasPersonalInfo' => $hasPersonalInfo,
            'hasBusinessInfo' => $hasBusinessInfo,
            'hasSettlement' => $hasSettlement,
            'hasDocs' => $hasDocs,
            'kybSubmitted' => $kybSubmitted,
            'hasPin' => $hasPin,
            'completedStepsCount' => $completedStepsCount,
            'activationProgress' => $activationProgress,
            'isActivated' => $isActivated,
        ]);
    }
}
