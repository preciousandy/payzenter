<?php

namespace App\Livewire\Cards;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.cards.index')->layout('layouts.auth')->title('Virtual Cards');
    }
}
