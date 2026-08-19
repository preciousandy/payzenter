<?php

namespace App\Livewire;

use App\Models\ExchangeRate;
use Livewire\Component;

class FiatsList extends Component
{
    public $fiats = [];
    public $fiatsCopy = [];

    public $query = '';

    public function mount()
    {
        $this->fiats = ExchangeRate::orderBy('currency')->get();

        $this->fiatsCopy = $this->fiats;
    }

    public function updatedQuery()
    {
        $this->fiats = $this->fiatsCopy->filter(function ($fiat) {
            return str_contains(strtolower($fiat->currency), strtolower($this->query));
        });
    }

    public function render()
    {
        return view('livewire.fiats-list');
    }
}
