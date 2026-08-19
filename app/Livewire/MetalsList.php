<?php

namespace App\Livewire;

use App\Models\MetalRate;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class MetalsList extends Component
{
    public $metals = [];
    public $metalsCopy = [];

    public $query = '';


    public function mount()
    {
        $this->metals = MetalRate::orderBy('metal')->get();

        $this->metalsCopy = $this->metals;
    }

    public function updatedQuery()
    {
        $this->metals = $this->metalsCopy->filter(function ($metal) {
            return str_contains(strtolower($metal->metal), strtolower($this->query));
        });
    }

    public function render()
    {
        return view('livewire.metals-list');
    }
}
