<div x-data="{showModal: false}">
    <x-secondary-button @click="showModal = true" class=" w-fit whitespace-nowrap">
        <span class=" fa fa-edit mr-2"></span> Edit
    </x-secondary-button>

    <div x-show="showModal" x-transition x-cloak class=" fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4">
        <div class=" bg-gray-50 p-4 lg:p-8 rounded-lg w-full md:w-full max-w-3xl space-y-5 max-h-[90%]  overflow-y-auto">
            <div class=" flex justify-between items-center mb-6">
                <h2 class=" text-2xl font-semibold">
                    Edit Domiciliary Bank
                </h2>
                <button @click="showModal = false">
                    <span class="fa fa-close text-2xl"></span>
                </button>
            </div>

            <form action="" wire:submit.prevent='updateBank'>
                <div class=" ">
                    <div class=" grid grid-cols-2 gap-4 ">
                        <div>
                            <x-input-label for="account_name" :value="__('Account Name')" />
                            <x-text-input id="account_name" type="text" class="mt-1 block w-full" wire:model='account_name' />
                            <x-input-error class="mt-2 " :messages="$errors->get('account_name')" />
                        </div>
                        <div>
                            <x-input-label for="account_number" :value="__('Account Number')" />
                            <x-text-input id="account_number" type="text" class="mt-1 block w-full" wire:model='account_number' />
                            <x-input-error class="mt-2 " :messages="$errors->get('account_number')" />
                        </div>
                        <div>
                            <x-input-label for="bank_name" :value="__('Bank Name')" />
                            <x-text-input id="bank_name" type="text" class="mt-1 block w-full" wire:model='bank_name' />
                            <x-input-error class="mt-2 " :messages="$errors->get('bank_name')" />
                        </div>
                        <div>
                            <x-input-label for="swift_code" :value="__('Swift Code')" />
                            <x-text-input id="swift_code" type="text" class="mt-1 block w-full" wire:model='swift_code' />
                            <x-input-error class="mt-2 " :messages="$errors->get('swift_code')" />
                        </div>
                        <div class=" col-span-2">
                            <x-input-label for="description" :value="__('Description (Side Note):')" />
                            <textarea name="" id="description" rows="4" class=" w-full rounded-md bg-white border border-gray-300 mt-2" wire:model='description' placeholder="The currencies you accept"></textarea>
                            <x-input-error class="mt-2 " :messages="$errors->get('description')" />
                        </div>
                    </div>

                </div>

                <div class=" mt-6">
                    <h3 class=" font-medium ">

                        Select Currencies the bank accepts
                    </h3>
                    <div class=" flex gap-4 flex-wrap mt-3">
                        @foreach ($exchangeRates as $item)
                        <button type="button" wire:click="addCurrency('{{$item->id}}')" @class([' p-3 border-gray-200 rounded-md border text-primary text-sm font-medium flex items-center gap-2', 'bg-green-600 text-white'=> in_array($item->id, $selectedExchangeRates) ])>
                            <img src="{{$item->logo_url}}" alt="" class=" h-6"> {{$item->currency}}
                        </button>
                        @endforeach
                    </div>


                    <x-input-error class="mt-2 " :messages="$errors->get('status')" />
                </div>




                <x-primary-button class=" w-72 mx-auto mt-8">
                    {{ __('Update Bank Details') }}
                </x-primary-button>
            </form>
        </div>
    </div>
</div>