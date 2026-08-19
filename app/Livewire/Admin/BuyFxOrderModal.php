<?php

namespace App\Livewire\Admin;

use App\Models\BuyFxOrder;
use App\Models\UserTransaction;
use App\Notifications\RejectBuyFxOrder;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class BuyFxOrderModal extends Component
{
    use LivewireAlert;

    public BuyFxOrder $order;

    public $rejectMessage;

    public function mount($orderId)
    {
        $this->order = BuyFxOrder::find($orderId);
    }

    public function render()
    {
        return view('livewire.admin.buy-fx-order-modal');
    }

    public function approveBuyOrder($id)
    {
        $order = BuyFxOrder::findOrFail($id);

        DB::beginTransaction();

        $order->status = BuyFxOrder::STATUS_COMPLETED;
        $order->save();

        DB::commit();

        $this->flash('success', 'Order approved successfully', [], route('admin.fx-orders'));
    }

    public function rejectBuyOrder($id)
    {
        $this->validate([
            'rejectMessage' => 'required',
        ]);

        $order = BuyFxOrder::findOrFail($id);

        DB::beginTransaction();

        $order->update([
            'status' => BuyFxOrder::STATUS_FAILED,
            'status_message' => $this->rejectMessage,
        ]);

        $order->user->increment('wallet_balance', $order->naira_amount);

        $order->user->transactions()->create([
            'amount' => $order->naira_amount,
            'type' => UserTransaction::TYPE_CREDIT,
            'message' => "Your order to buy {$order->amount} {$order->currency_name} was rejected and your wallet was credited back with N{$order->naira_amount}",
            'status' => UserTransaction::STATUS_FAILED,
            'source' => UserTransaction::SOURCE_BUREAU_DA_CHANGE
        ]);

        $order->user->notify(new RejectBuyFxOrder($this->rejectMessage, $order));

        DB::commit();

        $this->flash('success', 'Order rejected successfully', [], route('admin.fx-orders'));
    }
}
