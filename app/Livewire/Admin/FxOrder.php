<?php

namespace App\Livewire\Admin;

use App\Models\BuyFxOrder;
use App\Models\SellFxOrder;
use App\Models\UserTransaction;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class FxOrder extends Component
{
    use LivewireAlert;


    public $tabMenu = ['Buy Fx Orders', 'Sell Fx Orders', 'Settings'];
    public $currentTab = 'Buy Fx Orders';



    public function render()
    {
        return view('livewire.admin.fx-order', [])->extends('layouts.admin');
    }
}
