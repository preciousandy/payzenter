<div class=" px-4 py-6 min-h-screen lg:h-screen relative">

    <a href="#" class=" block w-fit ">
        <img src="{{asset('logo 3.png')}}" alt="" class=" h-20 hidden lg:block ">
    </a>

    <x-loading-spinner wire:target='submit' />

    <div class=" lg:absolute lg:top-1/2  lg:left-1/2 lg:-translate-x-1/2  lg:-translate-y-1/2 max-w-xl mx-auto w-full">
        <h2 class=" mb-6 text-xl lg:text-2xl font-semibold">
            {{ __('Create Your Account Number') }}
        </h2>

        <form method="POST" class="  rounded-xl w-full " wire:submit.prevent='submit'>


            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-2 w-full placeholder:text-gray-400 disabled:bg-gray-100" type="text" wire:model='email' required disabled />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>



            <div class=" mt-4">
                <x-input-label for="email" :value="__(' Your BVN')" />
                <x-text-input id="email" class="block mt-2 w-full placeholder:text-gray-400" type="number" wire:model='bvn' required />
                <x-input-error :messages="$errors->get('bvn')" class="mt-2" />
            </div>


            <div class="  mt-6">
                <x-primary-button class="   rounded-xl">
                    {{ __('Generate Account Number') }}
                </x-primary-button>
            </div>


        </form>
    </div>

</div>