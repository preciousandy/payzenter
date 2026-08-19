<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class ErrorLogs extends Component
{
  public function render()
  {
    return view('livewire.admin.error-logs')->extends('layouts.admin');
  }
}
