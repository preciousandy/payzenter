{{-- currencies table --}}
<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-black  bg-gray-50 dark:bg-gray-700 dark:text-gray-400 uppercase">
            <tr>
                <th scope="col" class="px-6 py-3">
                    Name
                </th>

                <th scope="col" class="px-6 py-3">
                    symbol
                </th>
                <th scope="col" class="px-6 py-3">
                    Price in USD
                </th>
                <th scope="col" class="px-6 py-3">
                    Price in NGN
                </th>

                <th scope="col" class="px-6 py-3">
                    Status
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($currencies as $currency)

            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-sky-50 dark:hover:bg-gray-600 0">
                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$currency->name}}
                </td>
                <td class="px-6 py-4 ">
                    {{$currency->symbol}}
                </td>
                <td class="px-6 py-4">
                    {{formatNumber($currency->price_in_usd)}}
                </td>
                <td class="px-6 py-4">
                    {{formatNumber($currency->price_in_usd * $dollarRate)}}
                </td>

                <td class="px-6 py-4 ">
                    {{$currency->status}}
                </td>

                <td class="px-6 py-4 space-x-3 flex items-center">
                    @if ($currency->status == 'pending')
                    <div x-data="{showFailedModal: false}">
                        <x-secondary-button @click="showFailedModal = true" class=" bg-red-100 border-red-500 text-red-500">
                            Failed
                        </x-secondary-button>

                        <div x-show="showFailedModal" x-transition x-cloak class=" fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4">
                            <div class=" bg-gray-50 p-4 lg:p-8 rounded-lg w-full md:w-full lg:w-1/3 xl:w-1/4 space-y-5">
                                <div class=" flex justify-between items-center mb-6">
                                    <h2 class=" text-2xl font-semibold">Write reason for failed transfer</h2>
                                    <button @click="showFailedModal = false">
                                        <span class="fa fa-close"></span>
                                    </button>
                                </div>

                                <form action="" class=" space-y-4" wire:submit.prevent="failedTransfer({{$transfer->id}})">
                                    <div>
                                        <x-input-label for="name" :value="__('Reason:')" />
                                        <textarea name="" id="" rows="4" class=" w-full rounded-md bg-white border mt-2" wire:model='message' required></textarea>
                                        <x-input-error class="mt-2 " :messages="$errors->get('message')" />
                                    </div>

                                    <x-primary-button class=" w-full">
                                        {{ __('Submit') }}
                                    </x-primary-button>
                                </form>
                            </div>
                        </div>


                    </div>
                    <x-secondary-button wire:click="approveTransfer({{$transfer->id}})" class=" bg-blue-100">
                        Completed
                    </x-secondary-button>
                    @endif
                </td>



            </tr>
            @endforeach


        </tbody>
    </table>


</div>