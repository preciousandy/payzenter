<?php

namespace App\Livewire;

use Livewire\Component;

class CryptocurrencyTable extends Component
{
    public $currencies = [];

    public $dollarRate = 0;


    public function render()
    {
        return view('livewire.cryptocurrency-table');
    }
}
