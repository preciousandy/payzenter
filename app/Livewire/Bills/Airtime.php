<?php

namespace App\Livewire\Bills;

use App\Helpers\SettingHelper;
use App\Models\AirtimeDataHistory;
use App\Models\Network;
use App\Models\UserTransaction;
use App\Services\BillsService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Airtime extends Component
{
    use LivewireAlert;

    public  $network;
    public $amount;
    public $phone;
    public $pin;
    public $networks;
    public $networkCode;
    public $airtime_bundles = [];
    public $showPreviewForm = false;
    public $cost;
    public $airtime_cashback_fixed;
    public $airtime_cashback_percentage;
    public $networkId;
    public $total_cashback = 0;

    public $recent_history = [];

    public function mount()
    {
        $this->networks = Network::all();

        $this->network = $this->networks->first();

        $this->airtime_bundles = airtimeBundles();

        $this->airtime_cashback_fixed = SettingHelper::get(SettingHelper::AIRTIME_CASHBACK_FIXED);
        $this->airtime_cashback_percentage = SettingHelper::get(SettingHelper::AIRTIME_CASHBACK_PERCENTAGE);


        $this->recent_history = AirtimeDataHistory::where([
            'user_id' => auth()->id(),
            'type' => AirtimeDataHistory::TYPE_AIRTIME
        ])->orderBy('updated_at', 'desc')->take(5)->get();

        // dummy data
        // $this->phone = "09089897878";
        // $this->amount = 750;
    }

    public function render()
    {
        return view('livewire.bills.airtime');
    }

    public function test()
    {
        $billsService = new \App\Services\BillsService();

        $response = $billsService->fetchEbillsDataPlans();
        dd($response);
    }

    // public function updatedNetworkCode($value)
    // {
    //     $this->network = collect($this->networks)->firstWhere('code', $value);
    // }

    public function updatedNetworkId()
    {
        $this->network = Network::findOrFail($this->networkId);
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
            'amount' => 'required|integer|min:50',
        ]);

        $percentageCashback = getPercentageAmount($this->amount, $this->airtime_cashback_percentage);

        $this->cost = $this->amount - $percentageCashback - $this->airtime_cashback_fixed;

        $this->total_cashback = $percentageCashback + $this->airtime_cashback_fixed;

        if ($this->cost > auth()->user()->wallet_balance) {
            $this->addError('amount', 'Insufficient funds');
            return;
        }

        $this->showPreviewForm = true;
    }

    public function buyAirtime()
    {
        $this->validate([
            'phone' => 'required|numeric|digits:11',
            'amount' => 'required|integer|min:50',
            'pin' => 'required|numeric|digits:4'
        ]);

        if ($this->cost > auth()->user()->wallet_balance) {
            $this->addError('amount', 'Insufficient funds');
            return;
        }

        if (!Hash::check($this->pin, auth()->user()->pin)) {
            $this->alert('error', 'Invalid pin');
            return;
        }

        try {
            $billsService = new BillsService();

            $response = $billsService->purchaseAirtime($this->network->vtu_code, $this->amount, $this->phone);

            if ($response['status'] == 'success') {

                DB::beginTransaction();


                auth()->user()->decrement('wallet_balance', $this->cost);

                // record user transaction
                auth()->user()->transactions()->create([
                    'amount' => $this->amount,
                    'type' => UserTransaction::TYPE_DEBIT,
                    'message' => 'You bought ₦' . $this->amount . ' airtime for ' . $this->phone,
                    'status' => UserTransaction::STATUS_SUCCESSFUL,
                    'source' => UserTransaction::SOURCE_AIRTIME,
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
                        'type' => AirtimeDataHistory::TYPE_AIRTIME
                    ]);
                }

                DB::commit();

                return $this->flash('success', 'Airtime purchased successfully', [], route('bills.index'));
            } else {

                throw new \Exception($response['message']);
            }
        } catch (\Throwable $th) {
            return $this->alert('error', $th->getMessage());
        }
    }
}
