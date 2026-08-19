<div x-data="{showModal: false}">
  <x-loading-spinner wire:target='updateStatus' />

  <x-secondary-button @click="showModal = true" class="text-sm rounded-xl">
    <span class="fa fa-eye mr-2"></span> View
  </x-secondary-button>

  <div x-show="showModal" x-transition x-cloak class="fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4 overflow-y-auto">
    <div class="bg-gray-50 p-4 lg:p-8 rounded-lg w-full max-w-4xl overflow-y-auto max-h-[90vh]">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Error Log Details</h2>
        <button @click="showModal = false">
          <span class="fa fa-close fa-2x"></span>
        </button>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div>
          <h4 class="text-gray-500 font-medium">Error ID</h4>
          <p class="text-lg font-semibold mt-1">#{{ $errorLog->id }}</p>
        </div>

        <div>
          <h4 class="text-gray-500 font-medium">User</h4>
          <p class="text-lg font-semibold mt-1">
            {{ $errorLog->user ? $errorLog->user->name : 'System Error' }}
          </p>
        </div>

        <div>
          <h4 class="text-gray-500 font-medium">Error Source</h4>
          <p class="text-lg font-semibold mt-1">{{ $errorLog->formatted_error_source }}</p>
        </div>

        <div>
          <h4 class="text-gray-500 font-medium">Severity</h4>
          <span class="inline-block px-3 py-1 rounded-full text-sm font-medium mt-1
                        @if($errorLog->severity === 'low') bg-green-100 text-green-600
                        @elseif($errorLog->severity === 'medium') bg-yellow-100 text-yellow-600
                        @elseif($errorLog->severity === 'high') bg-orange-100 text-orange-600
                        @else bg-red-100 text-red-600
                        @endif">
            {{ ucfirst($errorLog->severity) }}
          </span>
        </div>

        @if($errorLog->error_code)
        <div>
          <h4 class="text-gray-500 font-medium">Error Code</h4>
          <p class="text-lg font-semibold mt-1">{{ $errorLog->error_code }}</p>
        </div>
        @endif

        <div>
          <h4 class="text-gray-500 font-medium">Date & Time</h4>
          <p class="text-lg font-semibold mt-1">{{ $errorLog->created_at->format('d M Y, H:i:s') }}</p>
        </div>

        @if($errorLog->file_path)
        <div>
          <h4 class="text-gray-500 font-medium">File Path</h4>
          <p class="text-sm mt-1 font-mono bg-gray-100 p-2 rounded">{{ $errorLog->file_path }}{{ $errorLog->line_number ? ':' . $errorLog->line_number : '' }}</p>
        </div>
        @endif

        @if($errorLog->request_url)
        <div>
          <h4 class="text-gray-500 font-medium">Request URL</h4>
          <p class="text-sm mt-1 font-mono bg-gray-100 p-2 rounded">{{ $errorLog->request_url }}</p>
        </div>
        @endif

        @if($errorLog->ip_address)
        <div>
          <h4 class="text-gray-500 font-medium">IP Address</h4>
          <p class="text-lg font-semibold mt-1">{{ $errorLog->ip_address }}</p>
        </div>
        @endif
      </div>

      <div class="mb-6">
        <h4 class="text-gray-500 font-medium mb-2">Error Message</h4>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
          <p class="text-red-800">{{ $errorLog->error_message }}</p>
        </div>
      </div>

      @if($errorLog->error_details)
      <div class="mb-6">
        <h4 class="text-gray-500 font-medium mb-2">Error Details</h4>
        <div class="bg-gray-100 rounded-lg p-4 max-h-48 overflow-y-auto">
          <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ $errorLog->error_details }}</pre>
        </div>
      </div>
      @endif

      @if($errorLog->user_agent)
      <div class="mb-6">
        <h4 class="text-gray-500 font-medium mb-2">User Agent</h4>
        <p class="text-sm bg-gray-100 p-2 rounded">{{ $errorLog->user_agent }}</p>
      </div>
      @endif

      @if($errorLog->resolved_by)
      <div class="mb-6">
        <h4 class="text-gray-500 font-medium mb-2">Resolution Details</h4>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
          <p class="text-green-800"><strong>Resolved by:</strong> {{ $errorLog->resolved_by }}</p>
          <p class="text-green-800 mt-1"><strong>Resolved at:</strong> {{ $errorLog->resolved_at?->format('d M Y, H:i:s') }}</p>
        </div>
      </div>
      @endif

      <!-- Status Update Form -->
      <div class="border-t pt-6">
        <h4 class="text-lg font-semibold mb-4">Update Status</h4>

        <form wire:submit.prevent="updateStatus" class="space-y-4">
          <div>
            <x-input-label for="status" :value="__('Status')" />
            <select wire:model="status" id="status" class="mt-1 block w-full rounded-md border-gray-300">
              @foreach(App\Models\ErrorLog::getStatuses() as $key => $label)
              <option value="{{ $key }}">{{ $label }}</option>
              @endforeach
            </select>
            <x-input-error :messages="$errors->get('status')" class="mt-2" />
          </div>

          <div>
            <x-input-label for="adminNotes" :value="__('Admin Notes')" />
            <textarea wire:model="adminNotes" id="adminNotes" rows="4" class="mt-1 block w-full rounded-md border-gray-300" placeholder="Add notes about the resolution or any actions taken..."></textarea>
            <x-input-error :messages="$errors->get('adminNotes')" class="mt-2" />
          </div>

          <div class="flex justify-end">
            <x-primary-button type="submit" class="rounded-xl">
              <span class="fa fa-save mr-2"></span>
              Update Status
            </x-primary-button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>