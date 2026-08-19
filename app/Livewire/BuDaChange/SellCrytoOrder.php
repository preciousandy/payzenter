<?php

namespace App\Livewire\BuDaChange;

use App\Models\SellCryptoOrder;
use App\Services\HashIdService;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class SellCrytoOrder extends Component
{
    use WithFileUploads;

    public $order;
    public $wallet_address, $transaction_id;
    public $screenshot;

    public function mount($hashID)
    {
        $this->order = (new HashIdService)->getRecord($hashID, SellCryptoOrder::class);
    }

    public function render()
    {
        return view('livewire.bu-da-change.sell-cryto-order');
    }

    public function completeOrder()
    {
        $this->validate([
            'wallet_address' => 'required',
            'transaction_id' => 'required',
            'screenshot' => 'required|image|max:10024'
        ]);

        $this->order->update([
            'wallet_address' => $this->wallet_address,
            'transaction_id' => $this->transaction_id,
            'screenshot' => $this->screenshot->store('crypto-screenshots', 'public'),
            'status' => SellCryptoOrder::STATUS_PENDING
        ]);

        return redirect()->route('bureau-da-change.sell-index')->with('success', 'Order has been submitted successfully');
    }
}
