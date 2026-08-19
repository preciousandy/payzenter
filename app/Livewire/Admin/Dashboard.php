<?php

namespace App\Livewire\Admin;

use App\Models\AjoContribution;
use App\Models\AjoContributionMember;
use App\Models\AjoContributionRound;
use App\Models\AjoSavings;
use App\Models\BuyFxOrder;
use App\Models\GroupSavings;
use App\Models\GroupSavingsMembers;
use App\Models\Holdings;
use App\Models\InternationalTransfer;
use App\Models\Kyc;
use App\Models\MultipleTransfer;
use App\Models\Savings;
use App\Models\SellFxOrder;
use App\Models\User;
use App\Models\UserTransaction;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public $totalUsers;
    public $usersThisMonth;
    public $usersThisWeek;
    public $totalWalletBalance;
    public $OtpVerifiedUsers;
    public $kycVerifiedUsers;
    public $usersOnWeb;
    public $usersOnMobile;

    public $totalTransactions;
    public $totalTransactionsVolume;
    public $totalUsersThatMadeTransaction;
    public $transactionsThisMonth;
    public $transactionsThisWeek;
    public $transactionDates = [];
    public $dailyTransactions = [];
    public $dailyTransactionsVolume = [];



    public $totalBankTransfers;
    public $totalBankTransfersVolume;
    public $totalBankTransfersUsers;


    public $totalSwiftpayTransfers;
    public $swiftpayTransfersVolume;
    public $swiftpayTransfersUsers;

    public $totalBillsTransactions;
    public $totalBillsTransactionsVolume;
    public $totalBillsTransactionsUsers;
    public $billsCategories = [
        UserTransaction::SOURCE_AIRTIME,
        UserTransaction::SOURCE_DATA,
        UserTransaction::SOURCE_CABLE,
        UserTransaction::SOURCE_ELECTRICITY,
    ];


    public  $totalInternationTransfers;
    public $totalInternationTransfersVolume;
    public $totalInternationTransfersUsers;

    // multiple transfers
    public $totalMultipleTransfers;
    public $totalMultipleTransfersVolume;
    public $totalMultipleTransfersUsers;

    // interest savings
    public $totalSavings;
    public $totalSavingsBalance;
    public $totalSavingsUsers;


    // group savings
    public $totalGroupSavings;
    public $totalGroupSavingsBalance;
    public $totalGroupSavingsMembers;

    // ajo savings
    public $totalAjoSavings;
    public $totalAjoSavingsBalance;
    public $totalAjoSavingsUsers;

    // ajo contributions
    public $totalAjoContributions;
    public $totalAjoContributionsBalance;
    public $totalAjoContributionsMembers;

    // holdings
    public $totalHoldings;
    public $totalHoldingsVolume;
    public $totalHoldingsUsers;

    // bureau de change buy orders
    public $totalBudaChangeBuyOrders;
    public $totalBudaChangeBuyVolume;
    public $totalBudaChangeBuyUsers;

    // bureau de change sell orders
    public $totalBudaChangeSellOrders;
    public $totalBudaChangeSellVolume;
    public $totalBudaChangeSellUsers;

    public $todays_fees;
    public $this_weeks_fees;
    public $this_months_fees;
    public $all_time_fees;

    public function mount()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();


        $this->totalUsers = User::count();
        $this->usersThisWeek = User::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
        $this->usersThisMonth = User::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        $this->totalWalletBalance = User::sum('wallet_balance');
        $this->OtpVerifiedUsers = User::where('is_otp_verified', true)->count();
        $this->kycVerifiedUsers = User::where('kyc_status', Kyc::STATUS_VERIFIED)->count();
        $this->usersOnWeb = User::where('platform', User::PLATFORM_WEB)->count();
        $this->usersOnMobile = User::where('platform', User::PLATFORM_MOBILE)->count();


        $this->totalTransactions = UserTransaction::count();
        $this->totalTransactionsVolume = UserTransaction::sum('amount');
        UserTransaction::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        $this->totalUsersThatMadeTransaction = UserTransaction::distinct('user_id')->count();

        $this->transactionDates = UserTransaction::selectRaw('date(created_at) as date')->distinct()->get()->pluck('date')->toArray();

        $this->dailyTransactions = UserTransaction::selectRaw('date(created_at) as date, count(*) as count')
            ->groupBy('date')
            ->get()
            ->pluck('count')
            ->toArray();

        $this->dailyTransactionsVolume = UserTransaction::selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->pluck('total')
            ->toArray();

        // dd($this->dailyTransactions, $this->dailyTransactionsVolume);



        $this->totalBankTransfers = UserTransaction::where('source', UserTransaction::SOURCE_BANK_TRANSFER)->count();
        $this->totalBankTransfersVolume =
            UserTransaction::where('source', UserTransaction::SOURCE_BANK_TRANSFER)->sum('amount');
        $this->totalBankTransfersUsers =
            UserTransaction::where('source', UserTransaction::SOURCE_BANK_TRANSFER)->distinct('user_id')->count();


        $this->totalSwiftpayTransfers = UserTransaction::where('source', UserTransaction::SOURCE_SWIFTPAY_TRANSFER)->count();
        $this->swiftpayTransfersVolume =
            UserTransaction::where('source', UserTransaction::SOURCE_SWIFTPAY_TRANSFER)->sum('amount');
        $this->swiftpayTransfersUsers = UserTransaction::where('source', UserTransaction::SOURCE_SWIFTPAY_TRANSFER)->distinct('user_id')->count();

        $this->totalBillsTransactions = UserTransaction::whereIn('source', $this->billsCategories)->count();
        $this->totalBillsTransactionsVolume = UserTransaction::whereIn('source', $this->billsCategories)->sum('amount');
        $this->totalBillsTransactionsUsers = UserTransaction::whereIn('source', $this->billsCategories)->distinct('user_id')->count();

        $this->totalInternationTransfers = InternationalTransfer::count();
        $this->totalInternationTransfersVolume = InternationalTransfer::sum('amount_deducted_in_local_currency');
        $this->totalInternationTransfersUsers = InternationalTransfer::distinct('user_id')->count();

        $this->totalMultipleTransfers = MultipleTransfer::count();
        $this->totalMultipleTransfersVolume = MultipleTransfer::sum('total_amount');
        $this->totalMultipleTransfersUsers = MultipleTransfer::distinct('user_id')->count();


        $this->totalSavings = Savings::count();
        $this->totalSavingsBalance = Savings::sum('balance');
        $this->totalSavingsUsers = Savings::distinct('user_id')->count();

        $this->totalGroupSavings = GroupSavings::count();
        $this->totalGroupSavingsBalance = GroupSavings::sum('balance');
        $this->totalGroupSavingsMembers = GroupSavingsMembers::count();

        $this->totalAjoSavings = AjoSavings::count();
        $this->totalAjoSavingsBalance = AjoSavings::sum('balance');
        $this->totalAjoSavingsUsers = AjoSavings::distinct('user_id')->count();

        $this->totalAjoContributions = AjoContribution::count();
        $this->totalAjoContributionsBalance = AjoContributionRound::sum('balance');
        $this->totalAjoContributionsMembers = AjoContributionMember::count();


        $this->totalHoldings = Holdings::count();
        $this->totalHoldingsVolume = Holdings::sum('amount');
        $this->totalHoldingsUsers = Holdings::distinct('user_id')->count();

        $this->totalBudaChangeBuyOrders = BuyFxOrder::count();
        $this->totalBudaChangeBuyVolume = BuyFxOrder::sum('naira_amount');
        $this->totalBudaChangeBuyUsers = BuyFxOrder::distinct('user_id')->count();

        $this->totalBudaChangeSellOrders = SellFxOrder::count();
        $this->totalBudaChangeSellVolume = SellFxOrder::sum('naira_amount');
        $this->totalBudaChangeSellUsers = SellFxOrder::distinct('user_id')->count();


        $this->todays_fees = UserTransaction::whereDate('created_at', Carbon::today())->sum('fee');
        $this->this_weeks_fees = UserTransaction::whereBetween('created_at', [$startOfWeek, $endOfWeek])->sum('fee');
        $this->this_months_fees = UserTransaction::whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('fee');
        $this->all_time_fees = UserTransaction::sum('fee');
    }
    public function render()
    {
        return view('livewire.admin.dashboard')->extends('layouts.admin');
    }
}
