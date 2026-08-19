<?php

namespace App\Livewire\Holdings;

use Livewire\Component;

class Welcome extends Component
{
    public function render()
    {
        return view('livewire.holdings.welcome')->title('Welcome to SwiftPay Holdings');
    }
}
