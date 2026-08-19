<div class=" px-4 py-6 max-w-5xl ">

    <h2 class=" text-lg lg:text-xl font-semibold">
        Create a new Group Savings
    </h2>

    <x-loading-spinner wire:target='create' />

    <form class=" mt-6 space-y-4 md:space-y-6" wire:submit.prevent='create'>
        <div>
            <x-input-label for="name" :value="__('Name of Savings')" />
            <x-text-input id="name" type="text" class="mt-2 block w-full" wire:model='name' required autofocus />
            <x-input-error class="mt-2 " :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Description')" />
            <textarea name="" id="" rows="3" class=" w-full rounded-md bg-white border mt-2 border-gray-300" wire:model='description' required></textarea>
            <x-input-error class="mt-2 " :messages="$errors->get('description')" />
        </div>




        <div>
            <x-input-label for="name" :value="__('What type of Savings')" />
            <select wire:model.live="type" id="" class=" p-3 w-full rounded-lg border-gray-300 mt-2" required>
                <option value="strict">Strict Savings</option>
                <option value="flexible">Flexible Savings</option>
            </select>

            <div class=" mt-2 text-gray-700 text-sm lg:text-base">
                @if ($type === 'strict')
                <p>
                    <span class=" fa fa-circle-info mr-1"></span> For strict savings, each member is required to save a fixed amount.
                </p>
                @else
                <p>
                    <span class=" fa fa-circle-info mr-1"></span> For flexible savings, each member is required to save any amount of their choice to meet the target amount.
                </p>

                @endif
            </div>

            <x-input-error class="mt-2 " :messages="$errors->get('type')" />
        </div>


        @if ($type === 'strict')
        <div>
            <x-input-label for="member_amount" :value="__('Amount each member is required to save')" />
            <x-text-input id="member_amount" type="number" class="mt-1 block w-full" wire:model='member_target_amount' required />
            <x-input-error class="mt-2 " :messages="$errors->get('member_target_amount')" />
        </div>
        @endif


        @if ($type === 'flexible')
        <div>
            <x-input-label for="amount" :value="__('Target amount')" />

            <x-text-input id="amount" type="number" class="mt-2 block w-full" wire:model='target_amount' required />
            <x-input-error class="mt-2 " :messages="$errors->get('target_amount')" />
        </div>
        @endif



        <div>
            <x-input-label for="end_data" :value="__('End date')" />
            <x-text-input id="end_data" type="date" class="mt-1 block w-full" wire:model='end_date' required />
            <x-input-error class="mt-2 " :messages="$errors->get('end_date')" />
        </div>

        <div>
            <label for="terms" class="inline-flex items-center">
                <input id="terms" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" required>
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Accept Terms & Conditions') }}</span>
            </label>
        </div>

        <x-primary-button class=" w-full">
            Create Group Savings
        </x-primary-button>


    </form>

</div>