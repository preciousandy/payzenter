<?php

namespace App\Livewire\FundWallet;

use App\Models\CryptoCurrency;
use Livewire\Component;

class Index extends Component
{
    public $internationalCurrencies = [];

    private $selectedCurrencies = ['BTC', 'ETH', 'USDT', 'BNB'];

    public function mount()
    {

        $this->internationalCurrencies = CryptoCurrency::whereIn('symbol', $this->selectedCurrencies)->get();
    }


    public function render()
    {
        return view('livewire.fund-wallet.index')->title('Fund Wallet');
    }
}
