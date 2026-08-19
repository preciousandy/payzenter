<div class=" min-h-screen px-4 py-6 relative ">

    <x-blank-page-header />

    <img src="{{asset('logo 3.png')}}" alt="" class=" h-16 lg:h-20 hidden lg:block mt-2">

    <div class=" lg:absolute lg:top-1/2  lg:left-1/2 lg:-translate-x-1/2  lg:-translate-y-1/2">
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form wire:submit.prevent='sendLink' class=" rounded-xl w-full  mt-4 max-w-xl ">
            <img src="{{asset('logo 3.png')}}" alt="" class=" h-16 lg:hidden">
            <h3 class=" text-xl font-semibold   pl-4 lg:p-0 mt-2 lg:text-2xl ">
                {{ __('Forgot password') }}
            </h3>

            <div class="mb-4 text-sm lg:text-base xl:text-lg text-gray-600 mt-6">
                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </div>

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full placeholder:text-gray-400" type="email" wire:model="email" required autofocus autocomplete="email" placeholder="example@gmail.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>



            <div class=" mt-6">
                <x-primary-button class=" ">
                    {{ __('Reset Password') }}
                </x-primary-button>
            </div>



        </form>
    </div>
</div>