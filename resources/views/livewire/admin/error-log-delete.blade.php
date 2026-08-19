<div x-data="{showDeleteModal: false}">
  <x-loading-spinner wire:target='deleteErrorLog' />

  <x-secondary-button @click="showDeleteModal = true" class="text-sm rounded-xl border-red-600 text-red-700 hover:bg-red-50">
    <span class="fa fa-trash mr-2"></span> Delete
  </x-secondary-button>

  <div x-show="showDeleteModal" x-transition x-cloak class="fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4">
    <div class="bg-gray-50 p-4 lg:p-8 rounded-lg w-full max-w-lg">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-red-600">Delete Error Log</h2>
        <button @click="showDeleteModal = false">
          <span class="fa fa-close fa-lg"></span>
        </button>
      </div>

      <div class="mb-6">
        <div class="flex justify-center mb-4">
          <div class="bg-red-100 rounded-full p-3">
            <span class="fa fa-exclamation-triangle text-red-600 text-2xl"></span>
          </div>
        </div>

        <h3 class="text-lg font-medium text-center mb-2">
          Are you sure you want to delete this error log?
        </h3>

        <div class="bg-gray-100 p-3 rounded-lg mb-4  whitespace-normal">
          <p class="text-sm text-gray-600"><strong>Error ID:</strong> #{{ $errorLog->id }}</p>
          <p class="text-sm text-gray-600"><strong>Source:</strong> {{ $errorLog->formatted_error_source }}</p>
          <p class="text-sm text-gray-600"><strong>Message:</strong> {{ strlen($errorLog->error_message) > 50 ? substr($errorLog->error_message, 0, 50) . '...' : $errorLog->error_message }}</p>
          <p class="text-sm text-gray-600"><strong>Date:</strong> {{ $errorLog->created_at->format('d M Y, H:i') }}</p>
        </div>

        <p class="text-sm text-gray-600 text-center whitespace-normal">
          This action cannot be undone. The error log and all associated data will be permanently removed.
        </p>
      </div>

      <div class="flex gap-3">
        <x-secondary-button @click="showDeleteModal = false" class="flex-1 justify-center">
          Cancel
        </x-secondary-button>
        <x-primary-button wire:click="deleteErrorLog" class="flex-1 justify-center bg-red-600 hover:bg-red-700">
          <span class="fa fa-trash mr-2"></span>
          Delete Error Log
        </x-primary-button>
      </div>
    </div>
  </div>
</div>