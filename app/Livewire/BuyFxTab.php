<?php

namespace App\Livewire;

use App\Models\ExchangeRate;
use Livewire\Component;

class BuyFxTab extends Component
{
    public function render()
    {
        return view('livewire.buy-fx-tab', [
            'rates' => ExchangeRate::orderBy('currency')->get()
        ]);
    }
}
