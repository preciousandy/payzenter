<div x-data="{showModal: false}" @close-modal.window="showModal = false">
  <x-secondary-button @click="showModal = !showModal" class=" bg-white py-3">
    <span class=" fa fa-edit mr-1"></span> Edit User Info
  </x-secondary-button>

  <div x-show="showModal" x-transition x-cloak class=" fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4">
    <div class=" bg-gray-50 p-4 lg:p-8 rounded-lg w-full md:w-full max-w-2xl space-y-5">
      <div class=" flex justify-between items-center mb-6">
        <h2 class=" text-2xl font-semibold">Edit {{$user->name}} Information</h2>
        <button @click="showModal = false">
          <span class="fa fa-close text-2xl"></span>
        </button>
      </div>

      <form wire:submit.prevent="updateUserInfo" class="space-y-6">
        <x-loading-spinner wire:target='updateUserInfo' />

        <div class="grid grid-cols-2 gap-4">
          <div>
            <x-input-label for="first_name" :value="__('First Name')" />
            <x-text-input id="first_name" class="block mt-1 w-full" type="text" wire:model="first_name" required />
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
          </div>
          <div>
            <x-input-label for="last_name" :value="__('Last Name')" />
            <x-text-input id="last_name" class="block mt-1 w-full" type="text" wire:model="last_name" required />
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
          </div>
        </div>

        <div>
          <x-input-label for="email" :value="__('Email')" />
          <x-text-input id="email" class="block mt-1 w-full" type="email" wire:model="email" required />
          <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
          <x-input-label for="phone" :value="__('Phone Number')" />
          <x-text-input id="phone" class="block mt-1 w-full" type="tel" wire:model="phone" required />
          <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div class="flex justify-end space-x-3 pt-4">
          <x-secondary-button type="button" @click="showModal = false">
            Cancel
          </x-secondary-button>
          <x-primary-button type="submit">
            <span class="fa fa-save mr-1"></span>
            Update Information
          </x-primary-button>
        </div>
      </form>
    </div>
  </div>
</div>