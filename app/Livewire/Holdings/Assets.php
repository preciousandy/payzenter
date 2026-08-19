<?php

namespace App\Livewire\Holdings;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Assets extends Component
{
    public $isLoading = false;

    public $cryptodata = [];

    public function mount() {}


    public function render()
    {
        return view('livewire.holdings.assets')->title('Holdings Assets');
    }
}
