<?php

namespace App\Livewire;

use App\Models\ExchangeRate;
use App\Models\StockPrice;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class StocksList extends Component
{
    public $stocks = [];
    public $stocksCopy = [];
    public $isLoading = true;
    public $naira_to_dollar = 0;

    public $query = '';

    public function mount()
    {

        $this->naira_to_dollar = ExchangeRate::firstWhere('code', 'USD')->rate;
        $this->stocks = StockPrice::all();
        $this->stocksCopy = $this->stocks;
    }

    public function updatedQuery()
    {
        $this->stocks = $this->stocksCopy->filter(function ($stock) {
            return str_contains(strtolower($stock->company), strtolower($this->query));
        });
    }


    public function render()
    {
        return view('livewire.stocks-list');
    }
}
