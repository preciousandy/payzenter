<div x-data="{showEditModal: false}">
    <x-secondary-button @click="showEditModal = true">
        <span class=" fa fa-edit mr-2"></span> Edit
    </x-secondary-button>

    <div x-show="showEditModal" x-transition x-cloak class=" fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4">
        <div class=" bg-gray-50 p-4 lg:p-8 rounded-lg w-full md:w-full max-w-3xl space-y-5 max-h-[90%]  overflow-y-auto">
            <div class=" flex justify-between items-center mb-6">
                <h2 class=" text-2xl font-semibold">Edit {{$exchangeRate->code}} Sell Exchange Rate</h2>
                <button @click="showEditModal = false">
                    <span class="fa fa-close text-2xl"></span>
                </button>
            </div>

            <form action="" wire:submit.prevent='update'>
                <div class=" grid grid-cols-2 gap-4">
                    <img src="{{$exchangeRate->logo_url}}" alt="" class=" h-16 w-16 rounded-full object-cover">
                    <div>
                        <x-input-label for="logo" :value="__('Logo')" />
                        <x-text-input id="logo" class="block mt-1 w-full bg-white rounded-lg" type="file" wire:model="logo" />
                        <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="Currency" :value="__('Currency')" />
                        <x-text-input id="Currency" class="block mt-1 w-full" type="text" wire:model="currency" required autofocus />
                        <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="price" :value="__('Sell Rate')" />
                        <x-text-input id="price" class="block mt-1 w-full" type="text" wire:model="price" required />
                        <x-input-error :messages="$errors->get('price')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="volume" :value="__('Quantity')" />
                        <x-text-input id="volume" class="block mt-1 w-full" type="text" wire:model="volume" required />
                        <x-input-error :messages="$errors->get('volume')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="limit" :value="__('Limit')" />
                        <x-text-input id="limit" class="block mt-1 w-full" type="text" wire:model="limit" required />
                        <x-input-error :messages="$errors->get('limit')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="name" :value="__('Sell Fx Status')" />
                        <div class=" grid grid-cols-2 gap-x-4 mt-2">
                            <button type="button" wire:click="$set('status', 'open')" @class([' p-3 border-green-700 rounded-md border text-primary text-sm font-medium', 'bg-green-600 text-white'=> $status === 'open'])>
                                Open
                            </button>
                            <button type="button" wire:click="$set('status', 'closed')" @class([' p-3 border-red-600 rounded-md border text-red-700 text-sm font-medium', 'bg-red-700 text-white'=> $status === 'closed'])>
                                Closed
                            </button>
                        </div>

                        <x-input-error class="mt-2 " :messages="$errors->get('status')" />
                    </div>
                </div>



                <x-primary-button class=" w-72 mx-auto mt-8">
                    {{ __('Update') }}
                </x-primary-button>
            </form>
        </div>
    </div>


</div>