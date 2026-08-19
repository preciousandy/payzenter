<?php

namespace App\Livewire\Bills;

use App\Mail\ElectricityPurchaseNotification;
use App\Models\ElectricityHistory;
use App\Models\ElectricProvider;
use App\Models\UserTransaction;
use App\Services\BillsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Electricity extends Component
{
    use LivewireAlert;

    public $phone, $meter_number, $amount, $pin, $provider;
    public $package = "prepaid";
    public $providers = [];
    public $customer;
    public $bundles = [];
    public $showPreviewForm = false;
    public $showSuccessModal = false;


    public $recent_history = [];

    public $success_message = "";


    public function mount()
    {
        $this->providers = ElectricProvider::all();

        $this->bundles = [1000, 2000, 5000, 10000, 15000, 20000];

        $this->recent_history = ElectricityHistory::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // dummy customer data
        // $this->provider = $this->providers[1]->code;


        // $this->meter_number = 1234567890;

        // $this->amount = 10500;

        // $this->phone = "08012345678";

        // $this->customer = [
        //     "customer_name" => "John Doe",
        //     "customer_address" => "123, Main Street, Lagos",
        //     "customer_phone" => "08012345678",
        //     "customer_email" => "johndoe@test.com"
        // ];
    }

    public function verifyCustomer()
    {
        $billsService = new BillsService();

        $response = $billsService->fetchEbillsCustomer($this->meter_number, $this->provider, $this->package);


        if ($response && $response['status'] == 'success') {
            $this->customer = $response['data'];
        } else {
            $errorMessage = $response['message'] ?? 'Unable to verify account details';
            $this->alert('error', $errorMessage);
        }
    }

    public function setHistoryDetails($history_id)
    {
        $history = ElectricityHistory::find($history_id);


        if ($history) {
            $this->meter_number = $history->meter_number;
            $this->phone = $history->phone;
            $this->package = $history->package_code;
            $this->provider = $history->provider_code;
            $this->customer = [
                "customer_name" => $history->customer_name,
                "customer_address" => $history->customer_address,
            ];
        }
    }

    public function closeSuccessModal()
    {
        $this->showSuccessModal = false;

        return  $this->flash('success', 'Electricity purchased successfully', [], route('bills.index'));
    }

    public function render()
    {
        return view('livewire.bills.electricity')->title('Buy Electricity');
    }

    public function confirmPurchase()
    {
        $this->validate([
            'phone' => 'required|numeric|digits:11',
            'amount' => 'required|integer|min:100',
        ]);

        if ($this->amount > auth()->user()->wallet_balance) {
            $this->alert('error', 'Insufficient funds');
            return;
        }

        $this->showPreviewForm = true;
    }

    public function buyElectricity()
    {
        $this->validate([
            "meter_number" => "required|numeric",
            "provider" => "required|string",
            "package" => "required|string",
            'phone' => 'required|numeric|digits:11',
            "meter_number" => "required|numeric",
            'pin' => 'required|numeric|digits:4',
            "amount" => "required|numeric"
        ]);

        if (!Hash::check($this->pin, auth()->user()->pin)) {
            $this->alert('error', 'Incorrect pin');
            return;
        }

        try {
            $billsService = new BillsService();

            $response = $billsService->purchaseElectricity(
                $this->meter_number,
                $this->provider,
                $this->amount,
                $this->package,
            );

            if ($response['status'] !== 'success') {
                throw new \Exception($response['message']);
            }

            $orderStatus = $response['data']['status'];
            $token = $response['data']['token'] ?? 'No token yet';

            DB::beginTransaction();

            if ($orderStatus == 'refunded') {
                throw new \Exception("Electricity purchase failed. Transaction was refunded.");
            }

            auth()->user()->wallet_balance -= $this->amount;
            auth()->user()->save();
            $transactionStatus = $orderStatus == 'completed-api' ? UserTransaction::STATUS_SUCCESSFUL : UserTransaction::STATUS_PENDING;

            // record transaction
            auth()->user()->transactions()->create([
                'amount' => $this->amount,
                'type' => UserTransaction::TYPE_DEBIT,
                'message' => match ($orderStatus) {
                    'completed-api' => "Purchased electricity worth N" . number_format($this->amount) . " for {$this->meter_number}. Token: {$token}",
                    'processing-api' => "Electricity purchase (N" . number_format($this->amount) . ") for {$this->meter_number} is processing. You'll receive token shortly.",
                    'refunded' => "Electricity purchase (N" . number_format($this->amount) . ") for {$this->meter_number} was refunded.",
                    default => "Electricity purchase for {$this->meter_number} is in status: {$orderStatus}"
                },
                'status' => $transactionStatus,
                'source' => UserTransaction::SOURCE_ELECTRICITY,
                'reference' => $response['reference'],
                'fee' => 0 // No fee for electricity bills
            ]);

            // update/create electricity history
            $history = ElectricityHistory::where([
                'user_id' => auth()->id(),
                'meter_number' => $this->meter_number,
                'provider_code' => $this->provider,
            ])->first();

            if ($history) {
                $history->update(['updated_at' => now()]);
            } else {
                ElectricityHistory::create([
                    'user_id' => auth()->id(),
                    'meter_number' => $this->meter_number,
                    'phone' => $this->phone,
                    'provider_code' => $this->provider,
                    'provider_name' => $this->providers->where('code', $this->provider)->first()->name,
                    'package_code' => $this->package,
                    'customer_name' => $this->customer['customer_name'],
                    'customer_address' => $this->customer['customer_address'],
                ]);
            }

            DB::commit();

            $user = auth()->user();

            if ($orderStatus == 'completed-api') {
                Mail::to($user->email)->queue(new ElectricityPurchaseNotification($user, $response['data']));
            }

            $this->showPreviewForm = false;
            $this->success_message = match ($orderStatus) {
                'completed-api' => "Token: {$token}",
                'processing-api' => "Purchase is processing. You’ll receive your token soon in your email.",
                'refunded' => "Transaction was refunded. No amount was deducted.",
                default => "Transaction status: {$orderStatus}"
            };
            $this->showSuccessModal = true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->alert('error',  $th->getMessage());
        }
    }
}
