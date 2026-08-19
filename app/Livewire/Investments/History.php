<?php

namespace App\Livewire\Investments;

use Livewire\Component;

class History extends Component
{
    public $investments;

    public function mount()
    {
        $this->investments = auth()->user()->investments()->completed()->latest()->get();
    }

    public function render()
    {
        return view('livewire.investments.history')->title('Investments History');
    }
}
