<?php

namespace App\Livewire\Admin;

use App\Models\Kyc;
use Livewire\Component;
use Livewire\WithPagination;

class PendingKyc extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.admin.pending-kyc', [])->extends('layouts.admin');
    }
}
