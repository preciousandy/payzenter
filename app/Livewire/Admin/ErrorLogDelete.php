<?php

namespace App\Livewire\Admin;

use App\Models\ErrorLog;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ErrorLogDelete extends Component
{
  use LivewireAlert;

  public $errorLog;

  public function render()
  {
    return view('livewire.admin.error-log-delete');
  }

  public function deleteErrorLog()
  {
    $this->errorLog->delete();

    $this->flash('success', 'Error log deleted successfully', [], route('admin.error-logs'));
  }
}
