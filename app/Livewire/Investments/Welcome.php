<?php

namespace App\Livewire\Investments;

use Livewire\Component;

class Welcome extends Component
{
    public function render()
    {
        return view('livewire.investments.welcome')->title('Welcome to SwiftPay Investments');
    }
}
