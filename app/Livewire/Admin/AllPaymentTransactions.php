<?php

namespace App\Livewire\Admin;

use App\Models\PaymentTransaction;
use Livewire\Component;
use Livewire\WithPagination;

class AllPaymentTransactions extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.admin.all-payment-transactions', [
            'transactions' => PaymentTransaction::with('user')->latest()->paginate(20)
        ])->extends('layouts.admin');
    }
}
