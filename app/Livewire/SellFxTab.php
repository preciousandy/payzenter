<?php

namespace App\Livewire;

use App\Models\ExchangeRate;
use Livewire\Component;

class SellFxTab extends Component
{
    public function render()
    {
        return view('livewire.sell-fx-tab', [
            'rates' => ExchangeRate::orderBy('currency')->get()
        ]);
    }
}
