<?php

namespace App\Livewire\SendToAfrica;

use Livewire\Component;

class Welcome extends Component
{
    public function render()
    {
        return view('livewire.send-to-africa.welcome')->title('Welcome to SwiftPay Send to Africa');
    }
}
