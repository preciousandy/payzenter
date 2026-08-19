<?php

namespace App\Livewire\Investments;

use App\Models\CryptoCurrency;
use App\Models\StockPrice;
use Livewire\Component;

use function App\Console\Commands\applyPercentage;

class Portfolio extends Component
{
    public $investments;

    public $totalAsset = 0;

    public function mount()
    {
        $this->investments = auth()->user()->investments()->active()->latest()->get()->each(function ($investment) {
            if ($investment->asset_type == 'stock') {
                $investment->current_rate = StockPrice::where('symbol', $investment->asset_symbol)->first()->change_percent;
            } elseif ($investment->asset_type == 'crypto') {
                $investment->current_rate = CryptoCurrency::where('symbol', $investment->asset_symbol)->first()->change_percentage;
            }

            $investment->current_earnings = calculatePercentageChange($investment->current_rate, $investment->amount_invested);
        });

        $this->totalAsset = $this->investments->sum('amount_invested');
    }


    public function render()
    {
        return view('livewire.investments.portfolio')->title('My Investments Portfolio');
    }
}
