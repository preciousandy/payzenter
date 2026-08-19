<?php

namespace App\Livewire\Gateway;

use Livewire\Component;

class Documentation extends Component
{
    public function render()
    {
        return view('livewire.gateway.documentation')->title('Developer API');
    }
}

