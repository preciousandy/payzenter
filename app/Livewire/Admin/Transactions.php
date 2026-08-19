<?php

namespace App\Livewire\Admin;

use App\Models\UserTransaction;
use Livewire\Component;
use Livewire\WithPagination;

class Transactions extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.admin.transactions', [
            'transactions' => UserTransaction::with('user')->latest()->paginate(20)
        ])->extends('layouts.admin');
    }
}
