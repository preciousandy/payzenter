<div x-data="{showEditModal: false}">
    <x-secondary-button @click="showEditModal = true" class=" rounded-xl">
        <span class=" fa fa-pen mr-2"></span> Edit
    </x-secondary-button>

    <div x-show="showEditModal" x-transition x-cloak class=" fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4">
        <div class=" bg-gray-50 p-4 lg:p-8 rounded-lg w-full md:w-full lg:w-1/3 xl:w-1/4 space-y-5">
            <div class=" flex justify-between items-center mb-6">
                <h2 class=" text-2xl font-semibold">Edit Data Plan</h2>
                <button @click="showEditModal = false">
                    <span class="fa fa-close text-2xl"></span>
                </button>
            </div>

            <form action="" class=" space-y-4" wire:submit.prevent='update'>
                <div>
                    <x-input-label for="amt" :value="__('Amount')" />
                    <x-text-input id="amt" class="block mt-1 w-full" type="number" wire:model="amount" required autofocus />
                    <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="price" :value="__('Price')" />
                    <x-text-input id="price" class="block mt-1 w-full" type="number" wire:model="price" required />
                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="code" :value="__('Data plan code')" />
                    <x-text-input id="code" class="block mt-1 w-full" type="text" wire:model="code" required />
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                </div>

                <x-primary-button class=" w-full rounded-xl ">
                    {{ __('Update') }}
                </x-primary-button>
            </form>
        </div>
    </div>


</div>