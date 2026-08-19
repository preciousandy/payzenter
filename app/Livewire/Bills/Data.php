<?php

namespace App\Livewire\Bills;

use App\Helpers\SettingHelper;
use App\Models\AirtimeDataHistory;
use App\Models\DataPlan;
use App\Models\Network;
use App\Models\UserTransaction;
use App\Services\BillsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Data extends Component
{
    use LivewireAlert;

    public  $network;
    public $networkId;
    public $plans = [];
    public $plan;
    public $phone;
    public $is_ported = false;
    public $price;
    public $pin;
    public $networks;
    public $showPreviewForm = false;
    public $planId;
    public $cost;
    public $data_cashback_fixed;
    public $data_cashback_percentage;

    public $total_cashback = 0;

    public $recent_history = [];

    public function mount()
    {
        $this->networks = Network::with('dataPlans')->get();

        $this->network = $this->networks->first();

        $this->plans = $this->network->dataPlans()->orderBy('amount', 'asc')->get();

        $this->data_cashback_fixed = SettingHelper::get(SettingHelper::DATA_CARD_CASHBACK_FIXED);
        $this->data_cashback_percentage = SettingHelper::get(SettingHelper::DATA_CARD_CASHBACK_PERCENTAGE);

        $this->recent_history = AirtimeDataHistory::where([
            'user_id' => auth()->id(),
            'type' => AirtimeDataHistory::TYPE_DATA
        ])->orderBy('updated_at', 'desc')->take(5)->get();
    }

    public function render()
    {
        return view('livewire.bills.data');
    }

    public function updatedPlanId()
    {
        $this->plan = DataPlan::findOrFail($this->planId);
    }

    public function updatedNetworkId()
    {
        $this->network = Network::findOrFail($this->networkId);
        $this->plans = $this->network->dataPlans()->orderBy('amount', 'asc')->get();
    }

    public function setHistoryDetails($historyId)
    {
        $history = AirtimeDataHistory::findOrFail($historyId);

        $this->phone = $history->phone_number;
        $this->network = collect($this->networks)->firstWhere('vtu_code', $history->network_code);
    }

    public function confirmPurchase()
    {
        $this->validate([
            'phone' => 'required|numeric|digits:11',
            'planId' => 'required|integer',
        ], [
            'planId' => 'Please select a plan',
        ]);


        $pecentageCashbackAmount = getPercentageAmount($this->plan->price, $this->data_cashback_percentage);

        $this->cost = $this->plan->price - $pecentageCashbackAmount - $this->data_cashback_fixed;

        $this->total_cashback = $pecentageCashbackAmount + $this->data_cashback_fixed;


        if ($this->cost > auth()->user()->wallet_balance) {
            $this->alert('error', 'Insufficient funds');
            return;
        }

        $this->showPreviewForm = true;
    }

    public function buyData()
    {
        $this->validate([
            'phone' => 'required|numeric|digits:11',
            'pin' => 'required|numeric|digits:4',
        ]);



        if (!Hash::check($this->pin, auth()->user()->pin)) {
            $this->alert('error', 'Incorrect pin');
            return;
        }

        try {
            $billsService = new BillsService();

            $response = $billsService->purchaseDataPlan($this->network->vtu_code, $this->plan->item_code, $this->phone);

            if ($response['status'] == 'success') {

                DB::beginTransaction();

                auth()->user()->decrement('wallet_balance', $this->cost);

                // record user transaction
                auth()->user()->transactions()->create([
                    'amount' => $this->cost,
                    'type' => UserTransaction::TYPE_DEBIT,
                    'message' => "You purchased a data plan of ₦" . $this->plan->price . " for " . $this->phone,
                    "status" => UserTransaction::STATUS_SUCCESSFUL,
                    'source' => UserTransaction::SOURCE_DATA,
                    'reference' => $response['reference'],
                    'fee' => 0
                ]);


                $history = AirtimeDataHistory::where([
                    'user_id' => auth()->id(),
                    'phone_number' => $this->phone,
                    'type' => AirtimeDataHistory::TYPE_AIRTIME
                ])->first();

                if ($history) {
                    $history->update([
                        'updated_at' => now(),
                    ]);
                } else {
                    AirtimeDataHistory::create([
                        'user_id' => auth()->id(),
                        'phone_number' => $this->phone,
                        'network_code' => $this->network->vtu_code,
                        'network_name' => $this->network->name,
                        'type' => AirtimeDataHistory::TYPE_DATA
                    ]);
                }

                DB::commit();

                return $this->flash('success', 'Data plan purchased successfully', [], route('bills.index'));
            } else {
                throw new \Exception($response['message']);
            }
        } catch (\Throwable $th) {
            return $this->alert('error', $th->getMessage());
        }
    }
}
