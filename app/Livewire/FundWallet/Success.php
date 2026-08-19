<?php

namespace App\Livewire\FundWallet;

use Livewire\Component;

class Success extends Component
{
    public function render()
    {
        return view('livewire.fund-wallet.success')->title('Fund Wallet Success');
    }
}
