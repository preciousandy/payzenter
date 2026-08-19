<?php

namespace App\Livewire;

use App\Models\MultipleTransfer;
use App\Services\HashIdService;
use Livewire\Component;

class MultipleTransferReceipt extends Component
{

    public MultipleTransfer $transfer;

    public $swiftpayAccounts = [];

    public function mount($id)
    {
        $this->transfer = (new HashIdService)->getRecord($id, MultipleTransfer::class);

        $this->swiftpayAccounts = explode(',', $this->transfer->accounts);
    }

    public function render()
    {
        return view('livewire.multiple-transfer-receipt')->title('Multiple Transfer Receipt');
    }
}
