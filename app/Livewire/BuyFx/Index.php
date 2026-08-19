<?php

namespace App\Livewire\BuyFx;

use App\Models\BuyFxOrder;
use App\Models\ExchangeRate;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Index extends Component
{
    use LivewireAlert;

    public function render()
    {
        return view('livewire.buy-fx.index', [
            'currencies' => ExchangeRate::orderByStatusOpenFirst()->get(),
            'history' => BuyFxOrder::where('user_id', auth()->id())->latest()->get()
        ])->title("Bureau De Change Buy Forex");
    }

    public function buyCurrency($currency)
    {
        if ($currency['status'] ===  ExchangeRate::STATUS_CLOSED) {
            $this->alert('error', 'This currency is currently unavailable for purchase');
            return;
        }
        return redirect()->route('bureau-da-change.buy-fx.show', $currency['code']);
    }
}
