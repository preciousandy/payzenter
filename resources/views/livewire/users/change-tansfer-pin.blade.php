<div class=" bg-white">
    <section class=" px-4 p-6 max-w-3xl min-h-screen">
        <header>
            <h2 class="text-lg lg:text-xl lg:font-semibold font-medium text-gray-900 dark:text-gray-100">
                {{ __('Update Transaction PIN') }}
            </h2>


        </header>

        <form method="post" wire:submit.prevent='update' class="mt-6 lg:mt-8 space-y-4 lg:space-y-6">
            @csrf
            <div>
                <x-input-label for="update_password_current_password" :value="__('Current PIN')" />
                <x-text-input id="update_password_current_password" wire:model.defer="current_pin" type="number" class="mt-1 block w-full" autocomplete="current-password" required />
                <x-input-error class="mt-2" :messages="$errors->get('current_pin')" />
            </div>

            <div>
                <x-input-label for="update_password_password" :value="__('New PIN')" />
                <x-text-input id="update_password_password" wire:model.defer="pin" type="number" class="mt-1 block w-full" autocomplete="new-password" required />
                <x-input-error class="mt-2" :messages="$errors->get('pin')" />
            </div>

            <div>
                <x-input-label for="update_password_password_confirmation" :value="__('Confirm PIN')" />
                <x-text-input id="update_password_password_confirmation" wire:model.defer="confirm_pin" type="number" class="mt-1 block w-full" autocomplete="new-password" required />
                <x-input-error class="mt-2" :messages="$errors->get('confirm_pin')" />
            </div>

            <div class="">
                <x-primary-button class=" w-full rounded-xl lg:w-80">{{ __('Update Transaction PIN') }}</x-primary-button>


            </div>
        </form>
    </section>
</div>