<div>
  @livewire('admin.verify-kyc-modal', ['user' => $row ?? $kyc?->user, 'kyc' => $kyc], key('verify-kyc-' . ($row->id ?? $kyc?->id)))
</div>