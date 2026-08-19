<?php

namespace App\Livewire\Admin;

use App\Models\ErrorLog;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ErrorLogModal extends Component
{
  use LivewireAlert;

  public $errorLog;
  public $status;
  public $adminNotes;

  public function mount()
  {
    $this->status = $this->errorLog->status;
    $this->adminNotes = $this->errorLog->admin_notes;
  }

  public function render()
  {
    return view('livewire.admin.error-log-modal');
  }

  public function updateStatus()
  {
    $this->validate([
      'status' => 'required|string|in:' . implode(',', array_keys(ErrorLog::getStatuses())),
      'adminNotes' => 'nullable|string|max:1000'
    ]);

    $this->errorLog->update([
      'status' => $this->status,
      'admin_notes' => $this->adminNotes,
      'resolved_by' => auth()->user()->name,
      'resolved_at' => in_array($this->status, ['resolved', 'closed']) ? now() : null,
    ]);

    $this->flash('success', 'Error log updated successfully', [], route('admin.error-logs'));
  }
}
