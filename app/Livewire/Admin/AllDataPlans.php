<?php

namespace App\Livewire\Admin;

use App\Models\DataPlan;
use Livewire\Component;

class AllDataPlans extends Component
{
    public function render()
    {
        return view('livewire.admin.all-data-plans', [])->extends('layouts.admin');
    }
}
