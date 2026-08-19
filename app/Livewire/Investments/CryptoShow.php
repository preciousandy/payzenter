<?php

namespace App\Livewire\Investments;

use App\Models\CryptoCurrency;
use App\Models\ExchangeRate;
use App\Models\Investment;
use App\Services\HashIdService;
use Livewire\Component;

class CryptoShow extends Component
{
    public Investment $investment;

    public $crypto;

    public $naira_to_dollar = 0;

    public $historicalDates = [];

    public $possible_earnings = 0;

    public $current_rate = 0;

    public $earnings = 0;


    public function mount($hash_id)
    {
        $this->investment = (new HashIdService)->getRecord($hash_id, Investment::class);

        $this->crypto = CryptoCurrency::where('symbol', $this->investment->asset_symbol)->first();

        $this->naira_to_dollar = ExchangeRate::firstWhere('code', 'USD')->rate;

        $this->current_rate = CryptoCurrency::where('symbol', $this->investment->asset_symbol)->first()->change_percentage;

        $this->possible_earnings = calculatePercentageChange($this->current_rate, $this->investment->amount_invested);

        $this->earnings = $this->investment->status == 'active' ? $this->possible_earnings : $this->investment->amount_earned;

        $this->historicalDates = getLastNDays(count($this->crypto->changes));
    }

    public function render()
    {
        return view('livewire.investments.crypto-show')->title('Crypto Investment Details');
    }
}
