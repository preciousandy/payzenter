<?php

namespace App\Livewire\AjoContributions;

use Livewire\Component;

class Welcome extends Component
{
    public function render()
    {
        return view('livewire.ajo-contributions.welcome')->title('Welcome to SwiftPay Ajo Contributions');
    }
}
