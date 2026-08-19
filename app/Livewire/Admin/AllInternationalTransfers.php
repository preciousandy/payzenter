<?php

namespace App\Livewire\Admin;

use App\Models\InternationalTransfer;
use Livewire\Component;
use Livewire\WithPagination;

class AllInternationalTransfers extends Component
{
    use WithPagination;

    public $message;

    public function render()
    {
        return view('livewire.admin.all-international-transfers', [
            'transfers' => InternationalTransfer::latest()->paginate(10),
        ])->extends('layouts.admin');
    }

    public function approveTransfer($transferId)
    {
        $transfer = InternationalTransfer::find($transferId);

        $transfer->update([
            'status' => InternationalTransfer::STATUS_COMPLETED,
        ]);

        return redirect()->route('admin.international-transfers')->with('success', 'Transfer approved successfully');
    }

    public function failedTransfer($transferId)
    {
        $this->validate([
            'message' => 'required',
        ]);

        $transfer = InternationalTransfer::find($transferId);

        $transfer->update([
            'status' => InternationalTransfer::STATUS_FAILED,
            'message' => $this->message,
        ]);

        return redirect()->route('admin.international-transfers')->with('success', 'Transfer failed successfully');
    }
}
