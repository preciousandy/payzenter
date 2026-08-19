<?php

namespace App\Livewire\Investments;

use App\Helpers\SettingHelper;
use App\Models\Investment;
use App\Models\UserTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Create extends Component
{
    use LivewireAlert;


    public $name, $type, $symbol, $icon_url, $change_percentage;
    public $amount, $end_date;
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


        $this->icon_url = request('icon_url');


        $this->change_percentage = request('change_percentage');

        $this->fixed_fee = SettingHelper::get(SettingHelper::INVESTMENT_FEE);
        $this->percentage_fee = SettingHelper::get(SettingHelper::INVESTMENT_PERCENTAGE_FEE);
    }

    public function updatedAmount($value)
    {
        if ($value) {
            $this->totalFee = getTotalFee($value, $this->fixed_fee, $this->percentage_fee);
        } else {
            $this->totalFee = 0;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'type' => 'required',
            'symbol' => 'required',
            'icon_url' => 'required',
            'change_percentage' => 'required',
            'amount' => 'required',
            'end_date' => 'required',
        ]);

        $total = $this->amount + $this->totalFee;

        $this->total = $total;

        if (auth()->user()->wallet_balance < $total) {
            return $this->alert('error', 'Insufficient balance');
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


        $investment = auth()->user()->investments()->create([
            'asset_name' => $this->name,
            'asset_type' => $this->type,
            'asset_symbol' => $this->symbol,
            'asset_icon_url' => $this->icon_url,
            'change_percentage' => $this->change_percentage,
            'amount_invested' => $this->amount,
            'end_date' => $this->end_date,
            'status' => Investment::STATUS_ACTIVE
        ]);

        auth()->user()->decrement('wallet_balance', $this->total);

        auth()->user()->transactions()->create([
            'type' => UserTransaction::TYPE_DEBIT,
            'amount' => $this->total,
            'status' => UserTransaction::STATUS_SUCCESSFUL,
            'message' => 'Investmented in ' . $this->name .  ' with ₦' . $this->amount . ' with fee of ₦' . $this->totalFee,
            'source' => UserTransaction::SOURCE_INVESTMENTS,
            'fee' => $this->totalFee
        ]);


        DB::commit();


        return $this->flash('success', 'Investment created successfully', [], route('investments.portfolio'));
    }


    public function render()
    {
        return view('livewire.investments.create')->title('Create Investment');
    }
}
