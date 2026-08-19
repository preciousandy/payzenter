<?php

namespace App\Livewire;

use App\Models\CryptoCurrency;
use App\Models\ExchangeRate;
use App\Models\UserNotificationToken;
use Livewire\Component;

class CurrencyRates extends Component
{
    public $rates = [];

    public $cryptos = [];

    public function mount()
    {
        $exchangeRates = ExchangeRate::all();

        $exchangeRates->each(function ($item) {
            $this->rates[$item->code] = $item->rate;
        });

        $cryptocurrencies = CryptoCurrency::limit(5)->get();

        $cryptocurrencies->each(function ($crypto) {

            $this->cryptos[$crypto->symbol] = [
                'price_in_usd' => $crypto->price_in_usd,
                'icon' => $crypto->icon,
                'name' => $crypto->name,
            ];
        });

        session()->put('has_viewed_currency_rates', true);
    }
    public function render()
    {
        return view('livewire.currency-rates')->title('Currency Rates');
    }
}
