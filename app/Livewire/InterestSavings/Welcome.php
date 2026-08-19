<?php

namespace App\Livewire\InterestSavings;

use Livewire\Component;

class Welcome extends Component
{
    public function render()
    {
        return view('livewire.interest-savings.welcome')->title('Welcome to SwiftPay Interest Savings');
    }
}
