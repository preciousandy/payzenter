<?php

namespace App\Livewire\Bills;

use App\Helpers\SettingHelper;
use App\Models\BettingProvider;
use App\Models\FundBettingHistory;
use App\Models\UserTransaction;
use App\Services\BillsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Betting extends Component
{
    use LivewireAlert;
    public $providers = [];

    public $customer_id, $provider;
    public $customer;
    public $bundles = [];
    public $showPreviewForm = false;
    public $showSuccessModal = false;

    public $amount, $pin;


    public $recent_history = [];

    public $success_message = "";

    public $fixed_fee;
    public $percentage_fee;

    public $total_fee;

    public function mount()
    {
        $this->providers = BettingProvider::orderBy('name')->get();

        $this->bundles = [1000, 2000, 5000, 10000, 15000, 20000];

        $this->fixed_fee = SettingHelper::get(SettingHelper::FUND_BETTING_FIXED_FEE);
        $this->percentage_fee = SettingHelper::get(SettingHelper::FUND_BETTING_PERCENTAGE_FEE);

        $this->recent_history = FundBettingHistory::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    public function verifyCustomer()
    {
        $this->validate([
            'customer_id' => 'required|numeric',
            'provider' => 'required|string',
        ]);

        $billsService = new BillsService();

        $response = $billsService->fetchEbillsCustomer($this->customer_id, $this->provider);


        // dd($response);
        if ($response && $response['status'] == 'success') {

            $this->customer = $response['data'];
        } else {
            $errorMessage = $response['message'] ?? 'Unable to verify account details';
            $this->alert('error', $errorMessage);
        }
    }

    public function confirmPurchase()
    {
        $this->validate([
            'amount' => 'required|integer|min:100|max:100000',
        ]);

        $this->total_fee = getTotalFee(
            $this->amount,
            $this->fixed_fee,
            $this->percentage_fee
        );

        $total = $this->amount + $this->total_fee;

        if ($total > auth()->user()->wallet_balance) {
            $this->alert('error', 'Insufficient funds');
            return;
        }

        $this->showPreviewForm = true;
    }

    public function submitOrder()
    {
        $this->validate([
            "customer_id" => "required|numeric",
            "provider" => "required|string",
            'pin' => 'required|numeric|digits:4',
            "amount" => "required|numeric"
        ]);

        if (!Hash::check($this->pin, auth()->user()->pin)) {
            $this->alert('error', 'Incorrect pin');
            return;
        }

        try {
            $billsService = new BillsService();

            $response = $billsService->fundBettingAccount(
                $this->customer_id,
                $this->provider,
                $this->amount,
            );

            if ($response['status'] !== 'success') {
                throw new \Exception($response['message']);
            }

            $orderStatus = $response['data']['status'];
            $transactionStatus = UserTransaction::STATUS_PENDING;

            DB::beginTransaction();

            if ($orderStatus == 'refunded') {
                throw new \Exception("Funding of betting account failed. Please try again later.");
            }

            $total = $this->amount + $this->total_fee;

            auth()->user()->wallet_balance -= $total;
            auth()->user()->save();
            $transactionStatus = UserTransaction::STATUS_SUCCESSFUL;

            // record transaction
            auth()->user()->transactions()->create([
                'amount' => $this->amount,
                'type' => UserTransaction::TYPE_DEBIT,
                'message' => match ($orderStatus) {
                    'completed-api' => "Funding of betting account with (N" . number_format($this->amount) . ") for was successful. ",
                    'processing-api' => "Funding of betting account (N" . number_format($this->amount) . ") is processing. You’ll receive a the funds soon.",
                },
                'status' => $transactionStatus,
                'source' => UserTransaction::SOURCE_BETTING,
                'reference' => $response['reference'],
                'fee' => $this->total_fee
            ]);

            // update/create BETTING history
            $history = FundBettingHistory::where([
                'user_id' => auth()->id(),
                'customer_id' => $this->customer_id,
                'provider' => $this->provider,
            ])->first();

            if ($history) {
                $history->update(['updated_at' => now()]);
            } else {
                FundBettingHistory::create([
                    'user_id' => auth()->id(),
                    'customer_id' => $this->customer_id,
                    'provider' => $this->provider,
                    'customer_name' => $this->customer['customer_name'] ?? null,
                    'customer_email' => $this->customer['customer_email'] ?? null,
                    'customer_address' => $this->customer['customer_address'] ?? null,
                ]);
            }

            DB::commit();

            return $this->flash('success', 'Betting account funded successfully. Your transaction is being processed.', [], route('bills.betting'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->alert('error',  $th->getMessage());
        }
    }

    public function setHistoryDetails($history_id)
    {
        $history = FundBettingHistory::find($history_id);

        if ($history) {
            $this->customer_id = $history->customer_id;
            $this->provider  = $history->provider;
            $this->customer = [
                "customer_name" => $history->customer_name,
                "customer_address" => $history->customer_address,
                "customer_email" => $history->customer_email,
            ];
        }
    }

    public function render()
    {
        return view('livewire.bills.betting');
    }
}
