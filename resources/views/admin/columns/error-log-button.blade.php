<div class="flex gap-2">
  @livewire('admin.error-log-modal', ['errorLog' => $errorLog], key('error-log-' . $errorLog->id))
  @livewire('admin.error-log-delete', ['errorLog' => $errorLog], key('error-log-delete-' . $errorLog->id))
</div>