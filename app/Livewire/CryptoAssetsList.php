<?php

namespace App\Livewire;

use App\Models\CryptoCurrency;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class CryptoAssetsList extends Component
{
    public $data = [];
    public $dataCopy = [];
    public  $naira_to_dollar = 0;

    public $query = '';

    public function mount()
    {
        $this->naira_to_dollar = ExchangeRate::firstWhere('code', 'USD')->rate;

        $this->data = CryptoCurrency::all();
        $this->dataCopy = $this->data;
    }

    public function updatedQuery()
    {
        $this->data = $this->dataCopy->filter(function ($crypto) {
            return str_contains(strtolower($crypto->name), strtolower($this->query));
        });
    }


    public function render()
    {
        return view('livewire.crypto-assets-list');
    }
}
