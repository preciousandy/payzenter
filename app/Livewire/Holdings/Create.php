<?php

namespace App\Livewire\Holdings;

use App\Helpers\SettingHelper;
use App\Models\ExchangeRate;
use App\Models\Holdings;
use App\Models\UserTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Create extends Component
{
    use LivewireAlert;

    public $name, $type, $symbol, $price, $icon_url;
    public $formattedPrice;
    public $amount, $end_date;
    public $asset_amount = 0;
    public $fixed_fee = 0;
    public $percentage_fee = 0;
    public $totalFee = 0;

    public $total = 0;

    public $showPreviewForm = false;
    public $pin;


    public function mount()
    {
        $this->name = request('name');
        $this->type = request('type');
        $this->symbol = request('symbol');
        $this->formattedPrice =  formatNumber(request('price'));

        $this->icon_url = request('icon_url');


        $this->price = request('price');

        $this->fixed_fee = SettingHelper::get(SettingHelper::SAVE_HOLDINGS_FEE);
        $this->percentage_fee = SettingHelper::get(SettingHelper::HOLDINGS_PERCENTAGE_FEE);
    }
    public function render()
    {
        return view('livewire.holdings.create')->title('Create Holding');
    }

    public function updatedAmount($value)
    {
        if ($value) {
            $this->asset_amount = round($value / $this->price, 4);
            $this->totalFee = getTotalFee($value, $this->fixed_fee, $this->percentage_fee);
        } else {
            $this->totalFee = 0;
            $this->asset_amount = 0;
        }
    }

    public function save()
    {
        $this->validate([
            'amount' => 'required|numeric',
            'end_date' => 'required|date',
            'name' => 'required',
            'type' => 'required',
            'symbol' => 'required',
            'price' => 'required',
        ]);

        $total = $this->amount + $this->totalFee;

        $this->total = $total;

        if ($total  > auth()->user()->wallet_balance) {
            return $this->alert('error', 'Insufficient funds');
        }

        $this->showPreviewForm = true;
    }

    public function submit()
    {

        $this->validate([
            'pin' => 'required|numeric|min:4'
        ]);

        if (!Hash::check($this->pin, auth()->user()->pin)) {
            $this->alert('error', 'Incorrect pin');
            return;
        }



        DB::beginTransaction();

        auth()->user()->holdings()->create([
            'asset_name' => $this->name,
            'asset_symbol' => $this->symbol,
            'asset_type' => $this->type,
            'asset_amount' =>  $this->asset_amount,
            'amount' =>   round($this->amount, 2),
            'end_date' => $this->end_date,
            'naira_start_rate' => round($this->price, 2),
            'status' => Holdings::STATUS_ACTIVE,
            'icon_url' => $this->icon_url ?? null,
        ]);


        auth()->user()->decrement('wallet_balance', $this->total);

        UserTransaction::create([
            'user_id' => auth()->id(),
            'amount' => $this->total,
            'type' => UserTransaction::TYPE_DEBIT,
            'message' => 'Bought ₦' . $this->amount . ' worth of ' . $this->name . ' at $' . $this->price . ' per unit',
            'status' => UserTransaction::STATUS_SUCCESSFUL,
            'source' => UserTransaction::SOURCE_HOLDINGS,
            'fee' => $this->totalFee
        ]);

        DB::commit();


        return $this->flash('success', 'Holding created successfully', [], route('holdings.index'));
    }
}
