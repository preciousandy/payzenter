<div class="min-h-screen flex justify-center items-center p-4 bg-gray-100">

    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- Form Container --}}
    <form wire:submit.prevent='login' class="p-6 md:p-10 shadow-2xl w-full max-w-md bg-white rounded-2xl border border-gray-200 transition-all duration-300 hover:shadow-gray-300/50">
        
        {{-- Header --}}
        <div class="text-center mb-10">
            <h3 class="text-3xl font-extrabold text-gray-800 tracking-tight">{{ config('app.name') }}</h3>
            <p class="text-xl font-semibold text-gray-600 mt-1">Admin Portal Login</p>
        </div>
        
        @csrf

        <div class="mt-6">
            <x-input-label for="email" :value="__('Email Address')" class="text-gray-700 font-medium" />
            
            <div class="relative mt-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <i class="fa fa-envelope"></i>
                </span>
                <x-text-input 
                    id="email" 
                    class="block w-full pl-10 pr-3 py-3 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 placeholder:text-gray-400" 
                    type="email" 
                    name="email" 
                    wire:model='email' 
                    required 
                    autofocus 
                    autocomplete="email" 
                    placeholder="admin@paymentgatewayng.com" 
                />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-5">
            <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-medium" />

            <div class="relative mt-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <i class="fa fa-lock"></i>
                </span>
                <x-text-input 
                    id="password" 
                    class="block w-full pl-10 pr-3 py-3 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 placeholder:text-gray-400" 
                    type="password" 
                    wire:model="password" 
                    required 
                    autocomplete="current-password" 
                    placeholder="Enter your password" 
                />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-6 flex justify-between items-center">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" wire:model="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
            {{-- Placeholder for Password Reset Link (common in Admin UIs) --}}
            <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800 hover:underline">
                Forgot password?
            </a>
        </div>

        <div class="mt-8">
            <x-primary-button class="w-full py-3.5 text-lg font-bold rounded-xl bg-indigo-600 hover:bg-indigo-700 transition duration-150 shadow-md shadow-indigo-200/50" wire:loading.attr="disabled">
                {{ __('Secure Login') }}
            </x-primary-button>
        </div>

        <div wire:loading wire:target="login" class="mt-4 text-center text-indigo-600 font-medium">
            Authenticating...
        </div>

    </form>
</div>
