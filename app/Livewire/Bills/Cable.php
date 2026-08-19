<?php

namespace App\Livewire\Bills;

use App\Models\Cable as ModelsCable;
use App\Models\CableHistory;
use App\Models\CablePackage;
use App\Models\UserTransaction;
use App\Services\BillsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Cable extends Component
{
    use LivewireAlert;


    public ModelsCable $cable;

    public $packages = [];

    public $card_number;
    public $package_code;
    public $package;
    public $phone;
    public $price;
    public $pin;
    public $customer;
    public $cables = [];
    public $cableId;
    public $showPreviewForm = false;

    public $recent_history = [];

    // card no 7038947492
    // 08122240618

    public function mount()
    {
        $this->cables = ModelsCable::all();

        $this->cable = $this->cables->first();

        $this->cableId = $this->cable->id;

        $this->packages = $this->cable->packages;

        $this->recent_history = CableHistory::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // dd($this->recent_history);

        // dummy data

        // $this->card_number = '7038947492';
        // $this->phone = '08122240618';
        // $this->package_code = $this->packages[0]->package_code;

        // $this->customer = [
        //     'customer_name' => 'John Doe',
        // ];

        // $this->price = $this->packages[0]->price;
    }

    public function setHistoryDetails($history_id)
    {
        $history = CableHistory::find($history_id);

        if ($history) {
            $this->card_number = $history->card_number;
            $this->phone = $history->phone;
            $this->customer = $history->customer_name ?  [
                'customer_name' => $history->customer_name,
            ] : null;


            $this->cable = collect($this->cables)->firstWhere('code', $history->provider_code);

            $this->cableId = $this->cable->id;


            $this->packages = $this->cable->packages;

            $this->package_code = $history->package_code;

            // dd($history, $this->packages->toArray());

            $this->price = collect($this->packages)->firstWhere('package_code', $history->package_code)->price;
        }
    }

    // public function test()
    // {
    //     $billsService = new BillsService();

    //     $response = $billsService->fetchEbillsTvPlans();

    //     dd($response);
    // }

    public function render()
    {
        return view('livewire.bills.cable')->title('Buy Cable Subscription');
    }

    public function updatedCableId($value)
    {
        $this->cable = ModelsCable::find($value);
        $this->packages = $this->cable->packages;

        // dd($this->cable);
    }

    public function updatedPackageCode($value)
    {
        $this->package = CablePackage::firstWhere('package_code', $value);
        $this->price = $this->package->price;
    }

    public function verifyCustomer()
    {
        $this->validate([
            // 'phone' => 'required|numeric|digits:11',
            "card_number" => "required|numeric"
        ]);

        $billsService = new BillsService();

        $response = $billsService->fetchEbillsCustomer($this->card_number, $this->cable->code);

        if ($response && $response['status'] == 'success') {
            // dd($response['data']);
            $this->customer = $response['data'];
        } else {
            $errorMessage = $response['message'] ?? 'Unable to verify account details';
            $this->alert('error', $errorMessage);
        }
    }

    public function confirmPurchase()
    {
        $this->validate([
            "card_number" => "nullable|numeric",
            'package_code' => 'required|string',
            'price' => 'required|numeric',
            "phone" => "nullable|numeric|digits_between:11,15",
        ]);

        if ($this->price > auth()->user()->wallet_balance) {
            $this->alert('error', 'Insufficient funds');
            return;
        }

        if (!$this->card_number && !$this->phone) {
            $this->alert('error', 'Please provide either a Smart Card number or a Phone number');
            return;
        }

        $this->showPreviewForm = true;
    }

    public function buyCable()
    {
        $this->validate([
            "card_number" => "nullable|numeric",
            'pin' => 'required|numeric|digits:4',
            "price" => "required|numeric",
            "phone" => "nullable|numeric|digits_between:11,15",
        ]);

        if (!Hash::check($this->pin, auth()->user()->pin)) {
            $this->alert('error', 'Incorrect pin');
            return;
        }

        if (!$this->card_number && !$this->phone) {
            $this->alert('error', 'Please provide either a Smart Card number or a Phone number');
            return;
        }

        try {
            $billsService = new BillsService();

            $customer_id = $this->card_number ?? $this->phone;


            $response = $billsService->purchaseTvCable(
                $customer_id,
                $this->cable->code,
                $this->package_code,

            );

            // $response = [
            //     'status' => 'success',
            //     'message' => 'Cable TV purchase was successful',
            //     'data' => [
            //         'status' => 'completed-api', 
            //         'voucher' => '1234567890',
            //         'reference' => 'abc123', 
            //     ],
            //     'reference' => '4334jejrje'
            // ];

            if ($response['status'] !== 'success') {
                throw new \Exception($response['message']);
            }

            $orderStatus = $response['data']['status']; // completed-api, processing-api, refunded
            $token = null;

            if ($this->cable->code == 'showmax' && $orderStatus == 'completed-api') {
                $token = $response['data']['voucher'] ?? null;
            }


            // Only deduct wallet if order completed
            if ($orderStatus == 'refunded') {
                throw new \Exception("Cable purchase failed. Transaction was refunded.");
            }

            $statusMessage = match ($orderStatus) {
                'completed-api' => "Cable TV purchase was  successfully",
                'processing-api' => "Cable TV purchase is being processed and may take a while to reflect",
                default => "Cable TV purchase failed. Please try again later.",
            };


            $transaction_message = "You purchased a " . ucfirst($this->cable->name) . " subscription of " . $this->package->name . " for " . ($this->card_number ?? $this->phone);

            if ($this->cable->code == 'showmax' && $token) {
                $transaction_message .= ". Your voucher is: " . $token;
            }



            DB::beginTransaction();

            auth()->user()->wallet_balance -= $this->price;
            auth()->user()->save();

            // record user transaction
            auth()->user()->transactions()->create([
                'amount' => $this->price,
                'type' => UserTransaction::TYPE_DEBIT,
                'message' => $transaction_message,
                'status' => $orderStatus == 'completed-api' ? UserTransaction::STATUS_SUCCESSFUL : UserTransaction::STATUS_PENDING,
                'source' => UserTransaction::SOURCE_CABLE,
                'reference' => $response['reference'],
                'fee' => 0 // No fee for cable subscriptions
            ]);

            $history = CableHistory::where([
                'user_id' => auth()->id(),
                'card_number' => $this->card_number,
                'provider_code' => $this->cable->code,
            ])->first();

            if ($history) {
                $history->update([
                    'updated_at' => now(),
                ]);
            } else {
                CableHistory::create([
                    'user_id' => auth()->id(),
                    'card_number' => $this->card_number ?? null,
                    'phone' => $this->phone ?? null,
                    'provider_code' => $this->cable->code,
                    'package_code' => $this->package->package_code,
                    'customer_name' => $this->customer['customer_name'] ?? null,
                ]);
            }

            DB::commit();

            return $this->flash('success', $statusMessage, [], route('bills.index'));
        } catch (\Throwable $th) {
            return $this->alert('error', $th->getMessage());
        }
    }
}
