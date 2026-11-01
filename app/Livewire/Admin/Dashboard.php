<?php

namespace App\Livewire\Admin;

use App\Models\GatewayTransaction;
use App\Models\Kyc;
use App\Models\MerchantApiKey;
use App\Models\MerchantKyb;
use App\Models\Transfer;
use App\Models\User;
use App\Models\UserTransaction;
use App\Models\UserVirtualAccount;
use App\Models\VirtualAccount;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    // Users & Platform
    public $totalUsers;
    public $usersThisMonth;
    public $usersThisWeek;
    public $totalWalletBalance;
    public $OtpVerifiedUsers;
    public $kycVerifiedUsers;
    public $usersOnWeb;
    public $usersOnMobile;

    // Fee Revenues
    public $todays_fees;
    public $this_weeks_fees;
    public $this_months_fees;
    public $all_time_fees;

    // Payment Gateway Processing
    public $totalGatewayVolume = 0;
    public $totalGatewayTransactions = 0;
    public $successfulGatewayTransactions = 0;
    public $pendingGatewayTransactions = 0;
    public $failedGatewayTransactions = 0;
    public $gatewaySuccessRate = 0;
    public $totalGatewayFees = 0;

    // Virtual Accounts (PalmPay & Udara360)
    public $totalVirtualAccounts = 0;
    public $palmpayVirtualAccounts = 0;
    public $udara360VirtualAccounts = 0;
    public $flutterwaveVirtualAccounts = 0;
    public $dedicatedMerchantAccounts = 0;

    // Payouts & Settlements
    public $totalPayoutVolume = 0;
    public $totalPayoutTransactions = 0;
    public $successfulPayouts = 0;
    public $pendingPayouts = 0;

    // Merchants
    public $totalMerchants = 0;
    public $verifiedMerchants = 0;

    // Charts
    public $transactionDates = [];
    public $dailyTransactions = [];
    public $dailyTransactionsVolume = [];

    // Recent Transactions
    public $recentGatewayTransactions = [];

    public function mount()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // 1. Users & Security Analytics
        $this->totalUsers = User::count();
        $this->usersThisWeek = User::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
        $this->usersThisMonth = User::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        $this->totalWalletBalance = User::sum('wallet_balance');
        $this->OtpVerifiedUsers = User::where('is_otp_verified', true)->count();
        $this->kycVerifiedUsers = User::where('kyc_status', Kyc::STATUS_VERIFIED)->count();
        $this->usersOnWeb = User::where('platform', User::PLATFORM_WEB)->count();
        $this->usersOnMobile = User::where('platform', User::PLATFORM_MOBILE)->count();

        // 2. Gateway Fee Revenues
        $this->todays_fees = UserTransaction::whereDate('created_at', Carbon::today())->sum('fee');
        $this->this_weeks_fees = UserTransaction::whereBetween('created_at', [$startOfWeek, $endOfWeek])->sum('fee');
        $this->this_months_fees = UserTransaction::whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('fee');
        $this->all_time_fees = UserTransaction::sum('fee');

        // 3. Payment Gateway Transactions
        $this->totalGatewayTransactions = GatewayTransaction::count();
        $this->successfulGatewayTransactions = GatewayTransaction::where('status', 'success')->count();
        $this->pendingGatewayTransactions = GatewayTransaction::whereIn('status', ['pending', 'paying', 'unpaid'])->count();
        $this->failedGatewayTransactions = GatewayTransaction::whereIn('status', ['failed', 'cancelled', 'closed'])->count();

        $rawSuccessVolume = GatewayTransaction::where('status', 'success')->sum('amount');
        $this->totalGatewayVolume = $rawSuccessVolume > 0 ? ((float) $rawSuccessVolume / 100) : 0;

        $rawFeeVolume = GatewayTransaction::where('status', 'success')->sum('fee');
        $this->totalGatewayFees = $rawFeeVolume > 0 ? ((float) $rawFeeVolume / 100) : 0;

        $this->gatewaySuccessRate = $this->totalGatewayTransactions > 0
            ? round(($this->successfulGatewayTransactions / $this->totalGatewayTransactions) * 100, 1)
            : 0;

        // 4. Virtual Bank Accounts
        $this->totalVirtualAccounts = VirtualAccount::count();
        $this->palmpayVirtualAccounts = VirtualAccount::where('provider', 'palmpay')->count();
        $this->udara360VirtualAccounts = VirtualAccount::where('provider', 'udara360')->count();
        $this->flutterwaveVirtualAccounts = VirtualAccount::where('provider', 'flutterwave')->count();
        $this->dedicatedMerchantAccounts = UserVirtualAccount::count();

        // 5. Settlements & Payout Transfers
        $this->totalPayoutVolume = Transfer::where('status', Transfer::STATUS_SUCCESS)->sum('amount');
        $this->totalPayoutTransactions = Transfer::count();
        $this->successfulPayouts = Transfer::where('status', Transfer::STATUS_SUCCESS)->count();
        $this->pendingPayouts = Transfer::where('status', Transfer::STATUS_PENDING)->count();

        // 6. Merchants
        $this->totalMerchants = User::whereHas('merchantApiKeys')->orWhereNotNull('merchant_webhook_url')->count();
        if ($this->totalMerchants === 0) {
            $this->totalMerchants = $this->totalUsers;
        }
        $this->verifiedMerchants = MerchantKyb::whereNotNull('settlement_verified_at')->count();

        // 7. Daily Processing Activity (Last 14 days)
        $dates = collect();
        for ($i = 13; $i >= 0; $i--) {
            $dates->push(Carbon::today()->subDays($i)->format('Y-m-d'));
        }
        $this->transactionDates = $dates->toArray();

        $dailyCountData = GatewayTransaction::selectRaw('DATE(created_at) as date, count(*) as count')
            ->whereDate('created_at', '>=', Carbon::today()->subDays(13))
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $dailyVolData = GatewayTransaction::selectRaw('DATE(created_at) as date, SUM(amount) / 100 as total')
            ->where('status', 'success')
            ->whereDate('created_at', '>=', Carbon::today()->subDays(13))
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $this->dailyTransactions = [];
        $this->dailyTransactionsVolume = [];
        foreach ($this->transactionDates as $d) {
            $this->dailyTransactions[] = $dailyCountData[$d] ?? 0;
            $this->dailyTransactionsVolume[] = round((float) ($dailyVolData[$d] ?? 0), 2);
        }

        // 8. Recent Gateway Transactions (10 most recent)
        $this->recentGatewayTransactions = GatewayTransaction::with('merchant')
            ->latest('id')
            ->take(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.dashboard')->extends('layouts.admin');
    }
}
