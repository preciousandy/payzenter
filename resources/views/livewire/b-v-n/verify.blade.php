<div class=" px-4 py-6">

    <x-loading-spinner wire:target='submit' />

    <form method="POST" class="  rounded-xl w-full lg:w-1/4 space-y-4" wire:submit.prevent='submit'>

        <div>
            <x-input-label for="email" :value="__('First Name')" />
            <x-text-input id="email" class="block mt-2 w-full placeholder:text-gray-400" type="text" wire:model='first_name' required />
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="email" :value="__('Last Name')" />
            <x-text-input id="email" class="block mt-2 w-full placeholder:text-gray-400" type="text" wire:model='last_name' required />
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>


        <div>
            <x-input-label for="email" :value="__('Enter your BVN')" />
            <x-text-input id="email" class="block mt-2 w-full placeholder:text-gray-400" type="number" wire:model='bvn' required />
            <x-input-error :messages="$errors->get('bvn')" class="mt-2" />
        </div>


        <div class=" mt-6">
            <x-primary-button class="   rounded-xl">
                {{ __('Verify') }}
            </x-primary-button>
        </div>


    </form>

</div>