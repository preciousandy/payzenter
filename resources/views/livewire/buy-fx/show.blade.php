<div class=" bg-white">
    <div class=" px-4 py-6 lg:p-6  max-w-4xl min-h-screen">

        <h2 class=" text-xl lg:text-2xl font-semibold text-center">
            Buy {{ $currency->code }}
        </h2>

        <div class=" mt-6 lg:mt-8 space-y-2 lg:text-lg lg:space-y-3 ">
            <div class=" flex justify-between items-center">
                <label for="">Currency Name</label>
                <p class=" ">
                    {{$currency->currency}}
                </p>
            </div>
            <div class=" flex justify-between items-center">
                <label for="">Price</label>
                <p class=" text-lg font-semibold text-green-600">
                    <x-naira-symbol /> {{formatNumber($currency->price)}}
                </p>
            </div>
            <div class=" flex justify-between items-center">
                <label for="">Payment Method</label>
                <p class=" ">
                    SwiftPay Balance
                </p>
            </div>
            <div class=" flex justify-between items-center">
                <label for="">Quantity</label>
                <p class=" ">
                    {{$currency->currency_symbol}} {{formatNumber($currency->volume ?? 0)}}
                </p>
            </div>
            <div class=" flex justify-between items-center">
                <label for="">Limit</label>
                <p class=" ">
                    ${{formatNumber($currency->limit ?? 0)}}
                </p>
            </div>

        </div>

        <div class=" p-4 bg-gray-100 rounded-xl mt-6 lg:mt-8 lg:p-6">
            <h3 class=" lg:text-lg">SwiftPay Balance</h3>
            <p class="  font-semibold  mt-2 text-2xl lg:text-3xl">
                <x-naira-symbol /> {{formatNumber(auth()->user()->wallet_balance)}}
            </p>
        </div>

        <p class=" text-primary mt-3 lg:mt-5">
            <span class=" fa fa-info-circle"></span>
            Note: Ensure you input the right info, as we would not be held liable for any loss of asset.
        </p>

        <div x-data="{showPreviewForm: @entangle('showPreviewForm')}">
            <form action="" class=" mt-6 space-y-4 lg:space-y-6" wire:submit.prevent='validateDetails'>


                @php
                $label = "Amount in " . $currency->code;
                @endphp

                <div>
                    <x-input-label for="amount" :value="__($label)" />
                    <x-text-input id="amount" class="block mt-1 w-full placeholder:text-gray-400" type="number" wire:model.live='amount' required />

                    <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="amount" :value="__('Amount in NGN to be paid')" />
                    <p class=" font-semibold p-4 bg-gray-100 rounded-xl mt-1 lg:mt-2">
                        {{number_format($nairaAmount, 2)}} NGN
                    </p>
                    <x-input-error :messages="$errors->get('nairaAmount')" class="mt-2" />
                    <p class=" text-sm md:text-base mt-1">
                        SwiftPay charges a {{$fee}}% fee on all transactions
                    </p>
                </div>

                <div>
                    <h2 class=" text-lg font-semibold">
                        Receiver Account Details
                    </h2>

                    <p class=" text-primary mt-1 text-sm md:text-base">
                        Note: Ensure your Bank supports {{$currency->code}} transfer Recommend
                        Bank - Domiciliary Account ({{$currency->currency}}).
                    </p>
                </div>

                @foreach ($formFields as $field)
                <div class="mb-3">
                    <x-input-label for="{{ $field['label'] }}" :value="__($field['label'])" />
                    <input type="{{ $field['type'] }}" id="{{ $field['label'] }}" wire:model="dynamicFields.{{ $field['label'] }}" {{ $field['required'] ? 'required' : '' }} class=" block w-full border rounded-lg border-gray-300 outline-none p-3 lg:py-4">

                    @error('dynamicFields.' . $field['label']) <span class="error">{{ $message }}</span> @enderror
                </div>
                @endforeach

                <x-primary-button class="   rounded-xl w-full lg:w-80 lg:mx-auto">
                    {{ __('Proceed') }}
                </x-primary-button>


            </form>

            {{-- order Preview --}}
            <div x-show="showPreviewForm" x-cloak x-transition class="fixed top-0 left-0 z-30 bg-black/50 w-full h-full flex items-end lg:justify-center lg:items-center overflow-hidden">

                <div class=" bg-white w-full  rounded-t-2xl p-4 lg:max-w-2xl lg:rounded-2xl lg:p-6 max-h-[90%] overflow-y-auto ">

                    <div class=" flex justify-between items-center pb-3 border-b-2">
                        <h3 class=" text-lg font-medium md:text-xl lg:font-semibold">
                            Order Preview
                        </h3>

                        <button class=" text-2xl lg:text-3xl" @click="showPreviewForm = false">
                            <span class="fa fa-close text-red-700"></span>
                        </button>
                    </div>

                    <div class=" text-center space-y-1 mt-2 lg:mt-4">
                        <h4 class=" lg:text-lg lg:mb-2 text-gray-700">
                            Total
                        </h4>
                        <h2 class=" text-3xl lg:text-4xl font-bold">
                            <x-naira-symbol /> {{formatNumber($totalAmount)}}
                        </h2>
                    </div>

                    <div class="mt-4 space-y-2 md:text-lg lg:space-y-3">
                        <div class=" flex justify-between items-center">
                            <label for="">Amount</label>
                            <p class=" text-lg font-semibold text-green-600">
                                {{$amount}} {{$currency->code}}
                            </p>
                        </div>
                        <div class=" flex justify-between items-center">
                            <label for="">Price in NGN</label>
                            <p class=" ">
                                {{formatNumber($nairaAmount)}} NGN
                            </p>
                        </div>
                        <div class=" flex justify-between items-center">
                            <label for="">Fee</label>
                            <p class=" ">
                                {{formatNumber($feeAmount)}} NGN
                            </p>
                        </div>


                        @foreach ($formFields as $fieldItem)

                        @continue($fieldItem['type'] == 'file')

                        <div class=" flex justify-between items-center">
                            <label for=""> {{$fieldItem['label']}} </label>
                            <p class=" ">
                                {{$dynamicFields[$fieldItem['label']]}}
                            </p>
                        </div>
                        @endforeach
                    </div>

                    <x-enter-pin />

                    <x-primary-button class="   mt-6 mb-4 rounded-xl w-full" wire:click='submitOrder'>
                        {{ __('Continue') }}
                    </x-primary-button>

                </div>

            </div>
        </div>


    </div>
</div>