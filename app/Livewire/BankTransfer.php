<?php

namespace App\Livewire;

use Livewire\Component;

class BankTransfer extends Component
{
    public function render()
    {
        return view('livewire.bank-transfer')->title('Fund Account via Bank Transfer');
    }
}
