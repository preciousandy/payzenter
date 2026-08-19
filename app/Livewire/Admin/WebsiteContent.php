<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class WebsiteContent extends Component
{
    public function render()
    {
        return view('livewire.admin.website-content')->extends('layouts.admin');
    }
}
