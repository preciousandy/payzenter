<form class=" mt-6 space-y-3" wire:submit.prevent='submitStep1'>
    <div>
        <x-input-label for="email" :value="__('First Name')" />
        <x-text-input id="email" class="block mt-2 w-full placeholder:text-gray-400 disabled:bg-slate-100 disabled:cursor-not-allowed" type="text" wire:model='first_name' disabled />
        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="email" :value="__('Last Name')" />
        <x-text-input id="email" class="block mt-2 w-full placeholder:text-gray-400 disabled:bg-slate-100 disabled:cursor-not-allowed" type="text" wire:model='last_name' disabled />
        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="other_names" :value="__('Other Names')" />
        <x-text-input id="other_names" class="block mt-2 w-full placeholder:text-gray-400" type="text" wire:model='other_names' />
        <x-input-error :messages="$errors->get('other_names')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="date_of_birth" :value="__('Date Of Birth')" />
        <x-text-input id="date_of_birth" class="block mt-2 w-full placeholder:text-gray-400" type="date" wire:model='date_of_birth' />
        <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="email" :value="__('Gender')" />
        <div class=" flex items-center gap-x-4 mt-2">
            <div class="flex items-center ">
                <input id="default-radio-1" type="radio" value="male" name="default-radio" wire:model='gender' class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                <label for="default-radio-1" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Male</label>
            </div>
            <div class="flex items-center">
                <input checked id="default-radio-2" type="radio" value="female" name="default-radio" wire:model='gender' class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                <label for="default-radio-2" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Female</label>
            </div>
        </div>
        <x-input-error :messages="$errors->get('gender')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="address" :value="__('Address')" />
        <x-text-input id="address" class="block mt-2 w-full placeholder:text-gray-400" type="text" wire:model='address' required />
        <x-input-error :messages="$errors->get('address')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="occupation" :value="__('Occupation')" />
        <x-text-input id="occupation" class="block mt-2 w-full placeholder:text-gray-400" type="text" wire:model='occupation' required />
        <x-input-error :messages="$errors->get('occupation')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="bvn" :value="__('BVN')" />
        <x-text-input id="bvn" class="block mt-2 w-full placeholder:text-gray-400" type="text" wire:model='bvn' required />
        <x-input-error :messages="$errors->get('bvn')" class="mt-2" />
    </div>

    <div class=" flex justify-end">
        <x-primary-button class="  rounded-xl w-36" type="submit">
            {{ __('Next') }} <span class=" fa fa-chevron-right ml-1"> </span>
        </x-primary-button>
    </div>





</form>