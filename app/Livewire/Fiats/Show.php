<?php

namespace App\Livewire\Fiats;

use App\Models\ExchangeRate;
use App\Models\Holdings;
use App\Services\HashIdService;
use Livewire\Component;

class Show extends Component
{
    public $currency;

    public $prices = [];

    public $dates = [];

    public $holding;

    public $possible_earnings = 0;

    public $current_rate = 0;

    public $earnings = 0;

    public function mount($fiat)
    {
        $this->currency = ExchangeRate::firstWhere('code', $fiat);

        $basePrice = floor($this->currency->rate);

        $range = 0;

        switch (true) {
            case $basePrice <= 10:
                $range = 5;
                break;
            case $basePrice <= 100:
                $range = 20;
                break;
            case $basePrice <= 500:
                $range = 50;
                break;
            case $basePrice <= 1000:
                $range = 100;
                break;
            case $basePrice <= 10000:
                $range = 150;
                break;
        }


        $this->prices  = generatePriceRange(round($basePrice), 7, $range);

        $this->dates = getLastNDays(7);

        if (request()->has('holding')) {
            $this->holding = (new HashIdService)->getRecord(request('holding'), Holdings::class);

            $this->current_rate = ExchangeRate::where('code', $this->holding->asset_symbol)->first()->rate;

            $this->possible_earnings = $this->holding->asset_amount * $this->current_rate;

            $this->earnings = $this->holding->status == Holdings::STATUS_ACTIVE ? $this->possible_earnings : $this->holding->amount_earned;
        }
    }

    public function render()
    {
        return view('livewire.fiats.show')->title('Fiat Details');
    }
}
