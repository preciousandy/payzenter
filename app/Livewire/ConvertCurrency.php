<?php

namespace App\Livewire;

use App\Models\ExchangeRate;
use Livewire\Component;

class ConvertCurrency extends Component
{
    public $rates = [];

    public $amount = 0;
    public $convertedAmount = 0;

    public $selectedCurrency = 'NGN';

    public $selectedConvertedCurrency = 'USD';

    public function mount()
    {

        $exchangeRates = ExchangeRate::all();

        $this->rates['NGN'] = [
            'rate' => 1,
            'logo' => asset('nigeria-flag.png')
        ];

        $exchangeRates->each(function ($item) {
            $this->rates[$item->code] = [
                'rate' => $item->rate,
                'logo' => $item->logo_url
            ];
        });
    }

    public function updatedAmount()
    {
        if ($this->amount) {
            $total = $this->amount * ($this->rates[$this->selectedCurrency]['rate'] / $this->rates[$this->selectedConvertedCurrency]['rate']);

            $this->convertedAmount = number_format($total, 2);
        }
    }

    public function updatedSelectedConvertedCurrency()
    {
        $this->updatedAmount();
    }


    public function render()
    {
        return view('livewire.convert-currency')->extends('layouts.auth');
    }
}
