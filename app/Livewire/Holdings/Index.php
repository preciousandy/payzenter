<?php

namespace App\Livewire\Holdings;

use App\Models\ExchangeRate;
use App\Models\Holdings;
use App\Models\MetalRate;
use Livewire\Component;

class Index extends Component
{
    public $holdings;

    public $totalAssets;

    public function mount()
    {
        $this->holdings = Holdings::where('user_id', auth()->id())->active()->latest()->get()->each(function ($holding) {
            if ($holding->asset_type == 'fiat') {
                $holding->current_rate = ExchangeRate::where('code', $holding->asset_symbol)->first()->rate;
            } elseif ($holding->asset_type == 'metal') {
                $holding->current_rate = MetalRate::where('metal', $holding->asset_symbol)->first()->price;
            }
        });

        $this->totalAssets = $this->holdings->sum('amount');
    }


    public function render()
    {
        return view('livewire.holdings.index')->title('Holdings');
    }
}
