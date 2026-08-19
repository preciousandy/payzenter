<?php

namespace App\Livewire\Holdings;

use App\Models\Holdings;
use Livewire\Component;

class History extends Component
{
    public $holdings;

    public function mount()
    {
        $this->holdings = auth()->user()->holdings()->completed()->latest()->get();
    }

    public function render()
    {
        return view('livewire.holdings.history')->title('Holdings History');
    }
}
