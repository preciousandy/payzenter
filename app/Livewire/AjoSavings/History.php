<?php

namespace App\Livewire\AjoSavings;

use Livewire\Component;

class History extends Component
{
    public $savings;

    public function  mount()
    {
        $this->savings = auth()->user()->ajoSavings()->closed()->latest()->get();
    }
    public function render()
    {
        return view('livewire.ajo-savings.history')->title('Ajo Savings History');
    }
}
