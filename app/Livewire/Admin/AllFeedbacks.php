<?php

namespace App\Livewire\Admin;

use App\Models\Feedback;
use Livewire\Component;

class AllFeedbacks extends Component
{
    public function render()
    {
        return view('livewire.admin.all-feedbacks', [
            'feedbacks' => Feedback::latest()->get()
        ])->extends('layouts.admin');
    }
}
