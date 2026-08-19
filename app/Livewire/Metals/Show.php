<?php

namespace App\Livewire\Metals;

use App\Models\Holdings;
use App\Models\MetalRate;
use App\Services\HashIdService;
use Livewire\Component;

class Show extends Component
{
    public $metal;
    public $holding;

    public $possible_earnings = 0;

    public $current_rate = 0;

    public $earnings = 0;

    public $prices = [];

    public $dates = [];

    public function mount($symbol)
    {
        $this->metal = MetalRate::where('metal', $symbol)->first();

        $basePrice = floor($this->metal->price);

        $range = 0;

        switch (true) {
            case $basePrice <= 100_000:
                $range = 5_000;
                break;
            case $basePrice <= 1_000_000:
                $range = 50_000;
                break;
            case $basePrice <= 10_000_000:
                $range = 500_000;
                break;
            case $basePrice <= 100_000_000:
                $range = 5_000_000;
                break;
            case $basePrice <= 1_000_000_000:
                $range = 50_000_000;
                break;
        }


        $this->prices  = generatePriceRange(round($basePrice), 7, $range);

        $this->dates = getLastNDays(7);


        if (request()->has('holding')) {
            $this->holding = (new HashIdService)->getRecord(request('holding'), Holdings::class);

            $this->current_rate = MetalRate::where('metal', $this->holding->asset_symbol)->first()->price;

            $this->possible_earnings = $this->holding->asset_amount * $this->current_rate;

            $this->earnings = $this->holding->status == Holdings::STATUS_ACTIVE ? $this->possible_earnings : $this->holding->amount_earned;
        }
    }

    public function render()
    {
        return view('livewire.metals.show')->title('Metal Details');
    }
}
