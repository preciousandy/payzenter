<?php

namespace App\Livewire\Admin;

use App\Models\Kyc;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class PendingKyc extends Component
{
    use WithPagination;

    public function render()
    {
        $baseQuery = User::where(function ($q) {
            $q->whereHas('kyc')->orWhereHas('merchantKyb');
        });

        $totalSubmitted = (clone $baseQuery)->count();
        $pendingCount = (clone $baseQuery)->where('kyc_status', Kyc::STATUS_PENDING)->count();
        $verifiedCount = (clone $baseQuery)->where('kyc_status', Kyc::STATUS_VERIFIED)->count();
        $rejectedCount = (clone $baseQuery)->where('kyc_status', Kyc::STATUS_REJECTED)->count();

        return view('livewire.admin.pending-kyc', [
            'totalSubmitted' => $totalSubmitted,
            'pendingCount' => $pendingCount,
            'verifiedCount' => $verifiedCount,
            'rejectedCount' => $rejectedCount,
        ])->extends('layouts.admin');
    }
}
