<?php

namespace App\Livewire\Admin;

use App\Models\SellFxOrder;
use App\Models\UserTransaction;
use App\Notifications\RejectSellFxOrder;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class SellFxOrderModal extends Component
{
    use LivewireAlert;

    public $order;
    public $rejectMessage;

    public function mount($orderId)
    {
        $this->order = SellFxOrder::find($orderId);
    }

    public function render()
    {
        return view('livewire.admin.sell-fx-order-modal');
    }

    public function approveBuyOrder($id)
    {
        $order = SellFxOrder::findOrFail($id);

        DB::beginTransaction();

        $order->status = SellFxOrder::STATUS_COMPLETED;
        $order->save();

        $order->user->increment('wallet_balance', $order->naira_amount);

        $order->user->transactions()->create([
            'amount' => $order->naira_amount,
            'type' => UserTransaction::TYPE_CREDIT,
            'message' => "Selling of {$order->currency_name} was approved",
            'status' => UserTransaction::STATUS_SUCCESSFUL,
            'source' => UserTransaction::SOURCE_BUREAU_DA_CHANGE,
            'fee' => 0 // Fee already factored into the order amount
        ]);

        DB::commit();

        $this->flash('success', 'Order approved successfully', [], route('admin.fx-orders'));
    }

    public function rejectSellOrder($id)
    {
        $this->validate([
            'rejectMessage' => 'required',
        ]);

        $order = SellFxOrder::findOrFail($id);

        DB::beginTransaction();

        $order->update([
            'status' => SellFxOrder::STATUS_FAILED,
            'status_message' => $this->rejectMessage,
        ]);

        $order->user->notify(new RejectSellFxOrder($this->rejectMessage, $order));

        DB::commit();

        $this->flash('success', 'Order rejected successfully', [], route('admin.fx-orders'));
    }
}
