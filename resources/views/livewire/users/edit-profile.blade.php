<div class=" bg-white">
    <div class=" px-4 py-6 bg-white min-h-screen max-w-3xl lg:p-6">
        <section>
            <header>
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 lg:text-xl lg:font-semibold">
                    {{ __('Profile Information') }}
                </h2>

                <p class="mt-1 text-sm md:text-base text-gray-600  md:mt-2 lg:text-base">
                    {{ __("Update your account's profile information and email address.") }}
                </p>
            </header>



            <form method="post" wire:submit.prevent='save' class="mt-6 lg:mt-8 space-y-4 lg:space-y-6">
                @csrf

                <div>
                    <x-input-label for="name" :value="__('First Name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full disabled:bg-gray-100" wire:model.defer='first_name' required disabled />
                    <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                </div>
                <div>
                    <x-input-label for="name" :value="__('Last Name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full disabled:bg-gray-100" wire:model.defer='last_name' required disabled />
                    <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                </div>


                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" wire:model.defer='email' required autocomplete="email" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />


                </div>


                <div>
                    <x-input-label for="phone" :value="__('Phone Number')" />
                    <x-text-input id="phone" name="phone" type="number" class="mt-1 block w-full" wire:model.defer='phone' required />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>


                <div class="">
                    <x-primary-button class=" w-full rounded-xl lg:w-80">{{ __('Save Changes') }}</x-primary-button>


                </div>
            </form>
        </section>
    </div>
</div>