<?php

namespace App\Livewire\GroupSavings;

use Livewire\Component;

class Welcome extends Component
{
    public function render()
    {
        return view('livewire.group-savings.welcome')->title('Welcome to SwiftPay Group Savings');
    }
}
