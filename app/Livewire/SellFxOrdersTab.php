<?php

namespace App\Livewire;

use App\Models\SellFxOrder;
use App\Models\UserTransaction;
use App\Notifications\RejectSellFxOrder;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class SellFxOrdersTab extends Component
{
    use LivewireAlert;

    public $orders = [];

    public $rejectMessage;

    public function mount()
    {
        $this->orders = SellFxOrder::latest()->get();
    }

    public function render()
    {
        return view('livewire.sell-fx-orders-tab');
    }
}
