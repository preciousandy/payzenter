<?php

namespace App\Livewire\Admin;

use App\Models\CablePackage;
use Livewire\Component;

class AllCablePackages extends Component
{
    public function render()
    {
        return view('livewire.admin.all-cable-packages', [
            'cablePackages' => CablePackage::orderBy('name')->with('cableProvider')->get()
        ])->extends('layouts.admin');
    }
}
