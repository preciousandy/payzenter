<div class="space-y-4">
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <x-input-label for="first_name" :value="__('First Name')" />
            <x-text-input 
                id="first_name" 
                class="block w-full mt-1" 
                type="text" 
                wire:model="first_name" 
                required 
                autofocus 
                autocomplete="given-name" 
                placeholder="John"
            />
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="last_name" :value="__('Last Name')" />
            <x-text-input 
                id="last_name" 
                class="block w-full mt-1" 
                type="text" 
                wire:model="last_name" 
                required 
                autocomplete="family-name"
                placeholder="Doe"
            />
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>
    </div>

    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input 
            id="email" 
            class="block w-full mt-1" 
            type="email" 
            wire:model="email" 
            required 
            autocomplete="email" 
            placeholder="johndoe@example.com"
        />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="phone" :value="__('Phone Number')" />
        <x-text-input 
            id="phone" 
            class="block w-full mt-1" 
            type="tel" 
            wire:model="phone" 
            required 
            placeholder="(555) 555-5555"
        />
        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
    </div>

    @foreach(['password' => 'Password', 'password_confirmation' => 'Confirm Password'] as $wireModel => $label)
        <div class="relative">
            <x-input-label for="{{ $wireModel }}" :value="__($label)" />

            <div class="relative" x-data="{ show: false }">
                <input 
                    id="{{ $wireModel }}" 
                    class="block w-full mt-1 px-3 py-2 border-gray-300 rounded-md shadow-sm pr-10
                           dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300
                           focus:border-primary focus:ring-primary lg:py-3.5" 
                    :type="show ? 'text' : 'password'" 
                    wire:model="{{ $wireModel }}" 
                    required 
                    autocomplete="{{ $wireModel == 'password' ? 'new-password' : 'off' }}" 
                    placeholder="******"
                />

                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center h-full text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" x-on:click="show = !show">
                    <i class="fa" :class="{'fa-eye-slash': show, 'fa-eye': !show}" aria-hidden="true"></i>
                </button>
            </div>

            <x-input-error :messages="$errors->get($wireModel)" class="mt-2" />
        </div>
    @endforeach
</div>

<p class="text-sm text-center text-gray-600 dark:text-gray-400 lg:text-base pt-2">
    By creating an account, you agree to our 
    <a href="{{ route('terms-and-conditions') }}" class="text-primary font-semibold hover:underline">Terms of Service</a> 
    and 
    <a href="{{ route('privacy-policy') }}" class="text-primary font-semibold hover:underline">Privacy Policy</a>.
</p>

<div class="pt-4">
    <div class="flex items-center">
        <hr class="flex-grow border-t border-gray-300 dark:border-gray-700">
        <span class="mx-4 text-gray-500 dark:text-gray-400 text-sm">Or With</span>
        <hr class="flex-grow border-t border-gray-300 dark:border-gray-700">
    </div>

    <a href="#" class="mt-4 flex items-center bg-white p-2 border border-gray-300 rounded-xl font-medium justify-center 
                        hover:bg-gray-50 transition duration-150 shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:hover:bg-gray-700">
        {{-- Add Google Logo Image Tag Here --}}
        <span class="ms-4 text-sm text-gray-700 dark:text-gray-200 lg:text-base">Continue with Google</span>
    </a>
</div>