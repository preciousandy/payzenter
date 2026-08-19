<?php

namespace App\Livewire\TransferSwiftPay;

use App\Models\SwiftpayTransfer;
use App\Services\HashIdService;
use Livewire\Component;

class Receipt extends Component
{
    public $transfer;

    public function mount($hashId)
    {
        $this->transfer = (new HashIdService)->getRecord($hashId, SwiftpayTransfer::class);
    }


    public function render()
    {
        return view('livewire.transfer-swift-pay.receipt')->title('SwiftPay Transfer Receipt');
    }
}
