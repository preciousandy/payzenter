<?php

namespace App\Livewire\SellFx;

use App\Models\BuyFxOrder;
use App\Models\ExchangeRate;
use App\Models\SellFxOrder;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Index extends Component
{
    use LivewireAlert;

    public function render()
    {
        return view('livewire.sell-fx.index', [
            'currencies' =>  ExchangeRate::orderBySellStatusOpenFirst()->get(),
            'history' => SellFxOrder::where('user_id', auth()->id())->latest()->get()
        ])->title("Bureau De Change Sell Forex");
    }

    public function buyCurrency($currency)
    {
        if ($currency['sell_status'] ===  ExchangeRate::STATUS_CLOSED) {
            $this->alert('error', 'This currency is currently unavailable for selling');
            return;
        }
        return redirect()->route('bureau-da-change.sell-fx.show', $currency['code']);
    }
}
