<?php

namespace App\Livewire;

use App\Models\BuyFxOrder;
use App\Models\UserTransaction;
use App\Notifications\RejectBuyFxOrder;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class BuyFxOrdersTab extends Component
{
    use LivewireAlert;

    public $orders = [];
    public $rejectMessage;

    public function mount()
    {
        $this->orders = BuyFxOrder::latest()->get();
    }
    public function render()
    {
        return view('livewire.buy-fx-orders-tab');
    }
}
