<form wire:submit.prevent='login' class="w-full space-y-6">
    <div class="space-y-4">
      
        <h3 class="text-2xl font-bold text-center pl-0 mt-2 lg:text-3xl">
            Hello there! 👋
        </h3>
    </div>

    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input 
            id="email" 
            class="block w-full mt-1 placeholder:text-gray-400" 
            type="email" 
            wire:model="email" 
            required 
            autofocus 
            autocomplete="email" 
            placeholder="johndoe@gmail.com" 
        />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div class="relative">
        <x-input-label for="password" :value="__('Password')" />

        <div class="relative" x-data="{ show: false }">
            <input 
                id="password" 
                class="block w-full mt-1 px-3 py-2 border-gray-300 rounded-md shadow-sm pr-10
                       dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300
                       focus:border-primary focus:ring-primary lg:py-3.5" 
                :type="show ? 'text' : 'password'" 
                wire:model="password" 
                required 
                autocomplete="current-password" 
                placeholder="******" 
            />

            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center h-full text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" x-on:click="show = !show">
                <i class="fa" :class="{'fa-eye-slash': show, 'fa-eye': !show}" aria-hidden="true"></i>
            </button>
        </div>

        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <div class="flex items-center justify-between pt-2">
        <label for="remember_me" class="inline-flex items-center">
            <input 
                id="remember_me" 
                type="checkbox" 
                class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary dark:bg-gray-900 dark:border-gray-700" 
                wire:model="remember"
            >
            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400 lg:text-base">
                {{ __('Keep me Logged In') }}
            </span>
        </label>

        <a class="text-sm underline text-red-700 hover:text-red-900 dark:text-red-500 dark:hover:text-red-300 
                  focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary lg:text-base" 
            href="{{ route('forgot_password') }}">
            {{ __('Forgot password?') }}
        </a>
    </div>

    <div class="pt-2">
        <x-primary-button class="w-full rounded-xl py-3 text-lg font-semibold">
            {{ __('Log in') }}
        </x-primary-button>
    </div>

    <p class="text-center mt-4 text-gray-600 dark:text-gray-400 lg:text-base">
        Don't have an account? 
        <a href="{{ route('register') }}" class="text-primary font-bold hover:underline">
            Sign Up!
        </a>
    </p>
</form>