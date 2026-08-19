<?php

namespace App\Livewire\FundWallet;

use Livewire\Component;

class Failed extends Component
{
    public function render()
    {
        return view('livewire.fund-wallet.failed')->title('Fund Wallet Failed');
    }
}
