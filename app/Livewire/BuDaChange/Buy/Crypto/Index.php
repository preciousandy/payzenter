<?php

namespace App\Livewire\BuDaChange\Buy\Crypto;

use App\Models\BuyCryptoOrder;
use App\Models\CryptoCurrency;
use App\Models\ExchangeRate;
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

        // dd($cryptoCurrencies);

        return view('livewire.bu-da-change.buy.crypto.index', [
            'currencies' => $cryptoCurrencies,
            'history' => BuyCryptoOrder::whereUserId(auth()->id())->latest()->get(),
            // 'dollarRate' => 1600
        ])->title("Bureau De Change Buy Crypto");
    }
}
