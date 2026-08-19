<?php

namespace App\Livewire\Transfer;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.transfer.index')->title('Transfers');
    }
}
