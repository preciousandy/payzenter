<div class=" bg-white">
    <section class=" px-4 py-6 lg:p-6 max-w-3xl min-h-screen">
        <header>
            <h2 class="text-lg font-medium lg:text-xl lg:font-semibold">
                {{ __('Update Password') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 md:text-base">
                {{ __('Ensure your are  using a long, random password to stay secure.') }}
            </p>
        </header>

        <form method="post" wire:submit.prevent='save' class="mt-6 lg:mt-8 space-y-4 lg:space-y-6">
            @csrf
            <!-- Password -->
            <div class="relative">
                <x-input-label for="password" :value="__('Current Password')" />

                <div class="relative" x-data="{ show: false }">
                    <input id="password" class="border-gray-300 dark:border-gray-700  focus:border-indigo-500  focus:ring-indigo-500  rounded-md shadow-sm px-3 py-2 w-full pr-10 mt-1 lg:py-4" :type="show ? 'text' : 'password'" wire:model="current_password" required />

                    <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" x-on:click="show = !show">
                        <span x-show="show" class="fa fa-eye-slash"></span>
                        <span x-show="!show" class="fa fa-eye"></span>

                    </button>
                </div>

                <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="relative">
                <x-input-label for="password" :value="__('New Password')" />

                <div class="relative" x-data="{ show: false }">
                    <input id="password" class="border-gray-300 dark:border-gray-700  focus:border-indigo-500  focus:ring-indigo-500  rounded-md shadow-sm px-3 py-2 w-full pr-10 mt-1 lg:py-4" :type="show ? 'text' : 'password'" wire:model="password" required />

                    <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" x-on:click="show = !show">
                        <span x-show="show" class="fa fa-eye-slash"></span>
                        <span x-show="!show" class="fa fa-eye"></span>

                    </button>
                </div>

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="relative">
                <x-input-label for="password" :value="__('Confirm New Password')" />

                <div class="relative" x-data="{ show: false }">
                    <input id="password" class="border-gray-300 dark:border-gray-700  focus:border-indigo-500  focus:ring-indigo-500  rounded-md shadow-sm px-3 py-2 w-full pr-10 mt-1 lg:py-4" :type="show ? 'text' : 'password'" wire:model="password_confirmation" required autocomplete="new-password" />

                    <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" x-on:click="show = !show">
                        <span x-show="show" class="fa fa-eye-slash"></span>
                        <span x-show="!show" class="fa fa-eye"></span>

                    </button>
                </div>

                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="">
                <x-primary-button class=" w-full rounded-xl lg:w-80 ">{{ __('Update Password') }}</x-primary-button>


            </div>
        </form>
    </section>
</div>