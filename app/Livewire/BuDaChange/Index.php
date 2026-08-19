<?php

namespace App\Livewire\BuDaChange;

use App\Models\CryptoCurrency;
use App\Models\ExchangeRate;
use App\Models\SellCryptoOrder;
use App\Services\CryptoService;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Index extends Component
{
    use LivewireAlert;

    public $dollarRate = 1000;

    public function mount()
    {
        $res = (new CryptoService)->getCurrencySwapRate(strtolower('usdt'));

        if ($res['status'] == 'error') {
            return  $this->flash('error', $res['message'], [], route('bureau-da-change.sell-welcome'));
        }

        // dd($res);

        $dollarRate = round($res['data']['quoted_price'], 2);

        $this->dollarRate = $dollarRate;
    }

    public function render()
    {
        $allCryptoCurrencies = CryptoCurrency::all();

        $quidaxSupportedCurrencies = getQuidaxSupportedCryptos();

        $cryptoCurrencies = $allCryptoCurrencies->filter(function ($currency) use ($quidaxSupportedCurrencies) {
            return in_array($currency->symbol, $quidaxSupportedCurrencies);
        });




        return view('livewire.bu-da-change.index', [
            'currencies' => $cryptoCurrencies,
            'history' => SellCryptoOrder::whereUserId(auth()->id())->latest()->get(),
        ])->title("Sell Your Cryptocurrency");
    }
}
