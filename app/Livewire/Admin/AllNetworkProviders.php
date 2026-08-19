<?php

namespace App\Livewire\Admin;

use App\Models\Network;
use Livewire\Component;

class AllNetworkProviders extends Component
{
    public function render()
    {
        return view('livewire.admin.all-network-providers', [
            'networks' => Network::orderBy('name')->withCount('dataPlans')->get(),
        ])->extends('layouts.admin');
    }
}
