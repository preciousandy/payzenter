<div class=" px-4 py-6 bg-white min-h-screen">
    <div class=" max-w-5xl">

        <h2 class=" text-lg md:text-xl lg:text-2xl font-semibold text-center">
            Create Ajo Contribution
        </h2>

        <x-loading-spinner wire:target='create, cover_image' />

        <form class=" mt-6 space-y-4 lg:space-y-6" wire:submit.prevent='create'>
            <div>
                <x-input-label for="name" :value="__('Contribution Name')" />
                <x-text-input id="name" type="text" class="mt-2 block w-full" wire:model='name' required autofocus />
                <x-input-error class="mt-2 " :messages="$errors->get('name')" />
            </div>

            <div class="">
                <x-input-label for="description" :value="__('Reason for Contribution')" />
                <textarea name="" id="description" rows="2" class=" w-full rounded-md bg-white border border-gray-300 mt-2" wire:model='description' required></textarea>
                <x-input-error class="mt-2 " :messages="$errors->get('description')" />
            </div>



            <div>
                <x-input-label for="phone" :value="__('Type of Contribution')" />

                <select wire:model.live="type" id="" class=" p-3 w-full rounded-lg border-gray-300 mt-2" required>
                    <option value="">Select type</option>
                    <option value="personal">Personal</option>
                    <option value="business">Business</option>

                </select>
                <x-input-error class="mt-2" :messages="$errors->get('type')" />
            </div>

            <div>
                <x-input-label for="phone" :value="__('Select frequency')" />

                <select wire:model="frequency" id="" class=" p-3 w-full rounded-lg border-gray-300 mt-2" required>
                    <option value="">Select Frequency</option>
                    <option value="weekly">Weekly (Every 7 days)</option>
                    <option value="monthly">Monthly (Every 30 days)</option>
                    <option value="yearly">Yearly (Every 365 days)</option>

                </select>
                <x-input-error class="mt-2" :messages="$errors->get('frequency')" />
            </div>


            <div>
                <x-input-label for="amount" :value="__('Contribution Amount')" />

                <x-text-input id="amount" type="number" class="mt-2 block w-full" wire:model='amount' required />
                <x-input-error class="mt-2 " :messages="$errors->get('amount')" />
            </div>

            @if ($type)
            <div>
                @if ($type === 'personal')
                <x-input-label for="amount" :value="__('Number of Members(Including you)')" />
                @else
                <x-input-label for="amount" :value="__('Number of Members(Excluding you)')" />
                @endif

                <x-text-input id="amount" type="number" class="mt-2 block w-full" wire:model='no_of_members' required />
                <x-input-error class="mt-2 " :messages="$errors->get('no_of_members')" />
            </div>
            @endif

            <div>
                <x-input-label for="end_data" :value="__('Contribution  start date')" />
                <x-text-input id="end_data" type="date" class="mt-1 block w-full" wire:model='start_date' required />
                <x-input-error class="mt-2 " :messages="$errors->get('start_date')" />
            </div>
            <div>
                <x-input-label for="cover_image" :value="__('Contribution logo')" />

                <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 mt-1" aria-describedby="user_avatar_help" id="cover_image" type="file" wire:model='cover_image'>
                <x-input-error :messages="$errors->get('cover_image')" class="mt-2" />

            </div>

            <div>
                <label for="terms" class="inline-flex items-center">
                    <input id="terms" type="checkbox" class="rounded  border-gray-300  text-primary shadow-sm focus:ring-primary " required>
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Accept Terms & Conditions') }}</span>
                </label>
            </div>

            <x-primary-button class=" w-full lg:w-80 mx-auto ">
                Create Ajo Contribution
            </x-primary-button>


        </form>
    </div>

</div>