<?php

namespace App\Livewire\SendToAfrica;

use App\Models\ExchangeRate;
use App\Models\MultipleTransfer;
use App\Models\SwiftpayTransfer;
use App\Models\Transfer;
use Livewire\Component;

class Index extends Component
{
    public $transfers = [];

    public $totalAmount = 0;

    public $dollarRate = 0;

    public function mount()
    {
        $transfers = collect([])
            ->merge(
                SwiftpayTransfer::where('source_link', 'send_to_africa')
                    ->where('user_id', auth()->id())
                    ->get()
                    ->map(fn($transfer) => [
                        'reference' => $transfer->reference,
                        'amount' => $transfer->amount,
                        'account_name' => $transfer->receiver->name,
                        'type' => "SwiftPay Transfer",
                        'date' => $transfer->created_at->format('d M, Y'),
                        'status' => $transfer->status == 'success' ? 'successful' : $transfer->status,
                        'profile_pic' => $transfer->receiver->profile_photo,
                        'created_at' => $transfer->created_at, // Add created_at for sorting
                    ])
            )
            ->merge(
                Transfer::where('source_link', 'send_to_africa')
                    ->where('user_id', auth()->id())
                    ->get()
                    ->map(fn($transfer) => [
                        'reference' => $transfer->reference,
                        'amount' => $transfer->amount,
                        'account_name' => $transfer->account_name,
                        'type' => "Bank Transfer",
                        'date' => $transfer->created_at->format('d M, Y'),
                        'status' => $transfer->status,
                        'created_at' => $transfer->created_at, // Add created_at for sorting
                    ])
            )
            ->merge(
                MultipleTransfer::where('source_link', 'send_to_africa')
                    ->where('user_id', auth()->id())
                    ->get()
                    ->map(fn($transfer) => [
                        'reference' => $transfer->reference,
                        'amount' => $transfer->total_amount,
                        'account_name' => $transfer->accounts,
                        'type' => $transfer->type == 'bank' ? "Multiple Bank Transfer" : "Multiple SwiftPay Transfer",
                        'date' => $transfer->created_at->format('d M, Y'),
                        'status' => $transfer->status,
                        'created_at' => $transfer->created_at, // Add created_at for sorting
                    ])
            )
            ->sortByDesc('created_at') // Sort by created_at in descending order
            ->toArray();

        $this->transfers = $transfers;


        $this->totalAmount = collect($this->transfers)->sum('amount');

        $this->dollarRate = ExchangeRate::where('code', 'USD')->first()->price;
    }

    public function render()
    {
        return view('livewire.send-to-africa.index')->title("Send to Africa");
    }
}
