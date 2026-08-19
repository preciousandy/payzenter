<div class=" bg-white">
    <div class=" px-4 py-6 bg-white min-h-screen max-w-4xl">

        <x-loading-spinner wire:target='transfer, confirmUser' />


        <div class=" " x-data="{showPreviewForm: @entangle('showPreviewForm')}">
            <form class="space-y-4 lg:space-y-6">

                <h2 class=" text-xl text-center font-semibold lg:text-2xl">Transfer Abroad </h2>


                <div>
                    <h4 class=" font-medium lg:text-lg">Transaction Information</h4>

                    <p class=" text-primary text-sm md:text-base mt-2">
                        Note: Ensure you fill in the right bank details, as we would not be liable for any asset loss due to wrong info.
                    </p>
                </div>

                <div>
                    <x-input-label for="amount" :value="__('Amount')" />
                    <div class=" flex items-center w-full gap-2">

                        <div class=" w-full">
                            <x-text-input id="amount" wire:model="amount" type="number" class="mt-1 block w-full" required />

                        </div>
                        <button id="dropdownUsersButtonData" data-dropdown-toggle="dropdownUsersData" data-dropdown-placement="bottom" class="   font-medium rounded-lg text-sm  ml-2 py-3 lg:py-4 text-center inline-flex items-center lg:w-28 justify-center" type="button">

                            <img src="{{asset($currencyLogos[$selectedCurrency])}}" alt="" class=" h-6 w-6  mr-1 rounded-full object-cover">

                            <span class="  ">{{$selectedCurrency}}</span>

                            <span class=" fa fa-chevron-down ml-2"></span>
                        </button>

                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('amount')" />
                </div>

                <!-- Dropdown menu -->
                <div id="dropdownUsersData" class="z-10 hidden bg-white rounded-lg shadow w-60 dark:bg-gray-700">
                    <ul class="h-auto py-2 overflow-y-auto text-black dark:text-gray-200" aria-labelledby="dropdownUsersButton">
                        @foreach ($currencies as $currency => $countries)
                        <li wire:click="$set('selectedCurrency', '{{$currency}}')">
                            <a href="#" class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                <img class="w-6 h-6 me-2 rounded-full" src="{{asset($currencyLogos[$currency])}}" alt="Jese image">
                                <span>
                                    {{$currency}}
                                </span>
                            </a>
                        </li>
                        @endforeach

                    </ul>

                </div>



                @if ($country && $country === "Nigeria (domiciliary account)")
                <div>
                    <div>
                        <x-input-label for="bank_code" :value="__('Select Domicilliary Bank')" />

                        <select wire:model="bank_code" id="" class=" p-3 lg:py-4 w-full border-gray-300 border rounded-lg   mt-1 text-sm md:text-base " required>
                            <option value="">Select Bank</option>
                            @foreach ($domiciliaryBanks as $code => $bank )
                            <option value="{{ $code }}">{{ $bank }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('bank_code')" />
                    </div>

                    <p class=" text-sm md:text-base text-gray-600 mt-1">
                        SwiftPay charges a {{$percentageFee}}% and
                        <x-naira-symbol /> {{$fixedFee}} fee on all transactions
                    </p>
                </div>
                @endif

                <div>
                    <h4 class=" font-medium lg:text-lg">Receiver Details</h4>

                    <p class=" text-primary text-sm md:text-base mt-2">
                        Note: Make sure you fill in the right receiver details, as we would not be liable for any asset loss due to wrong info.
                    </p>
                </div>

                <div>
                    <x-input-label for="account_name" :value="__('Account Name')" />

                    <x-text-input id="account_name" wire:model="account_name" type="text" class="mt-1 block w-full" required />

                    <x-input-error class="mt-2" :messages="$errors->get('account_name')" />
                </div>

                <div>
                    <x-input-label for="account_number" :value="__('Account Number')" />

                    <x-text-input id="account_number" wire:model="account_number" type="number" class="mt-1 block w-full" required />

                    <x-input-error class="mt-2" :messages="$errors->get('account_number')" />
                </div>


                <div>
                    <x-input-label for="bank_name" :value="__('Bank Name')" />

                    <x-text-input id="bank_name" wire:model="bank_name" type="text" class="mt-1 block w-full" required />

                    <x-input-error class="mt-2" :messages="$errors->get('bank_name')" />
                </div>

                <div>
                    <x-input-label for="swift_code" :value="__('Swift Code')" />

                    <x-text-input id="swift_code" wire:model="swift_code" type="text" class="mt-1 block w-full" required />

                    <x-input-error class="mt-2" :messages="$errors->get('swift_code')" />
                </div>


                <div>
                    <x-input-label for="routing_number" :value="__('Routing Number')" />

                    <x-text-input id="routing_number" wire:model="routing_number" type="text" class="mt-1 block w-full" required />

                    <x-input-error class="mt-2" :messages="$errors->get('routing_number')" />
                </div>

                @if ($selectedCurrency == 'EUR' || $selectedCurrency == 'GBP')
                <div>
                    <x-input-label for="postal_code" :value="__('Postal Code')" />

                    <x-text-input id="postal_code" wire:model="postal_code" type="text" class="mt-1 block w-full" required />

                    <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
                </div>

                <div>
                    <x-input-label for="street_name" :value="__('Street Name')" />

                    <x-text-input id="street_name" wire:model="street_name" type="text" class="mt-1 block w-full" required />

                    <x-input-error class="mt-2" :messages="$errors->get('street_name')" />
                </div>
                <div>
                    <x-input-label for="street_number" :value="__('Street Number')" />

                    <x-text-input id="street_number" wire:model="street_number" type="text" class="mt-1 block w-full" required />

                    <x-input-error class="mt-2" :messages="$errors->get('street_number')" />
                </div>
                <div>
                    <x-input-label for="city" :value="__('City')" />

                    <x-text-input id="city" wire:model="city" type="text" class="mt-1 block w-full" required />

                    <x-input-error class="mt-2" :messages="$errors->get('city')" />
                </div>
                @endif


                <div>
                    <x-input-label for="description" :value="__('Description')" />

                    <x-text-input id="description" wire:model="description" type="text" class="mt-1 block w-full" required />

                    <x-input-error class="mt-2" :messages="$errors->get('description')" />
                </div>


                <x-primary-button type="button" class=" w-full  mt-6 mb-4 lg:w-80 mx-auto" wire:click='confirmTransfer'>
                    {{ __('Continue') }} <span class=" fa fa-chevron-right ml-2"></span>
                </x-primary-button>

            </form>

            {{-- order Preview --}}
            <div x-show="showPreviewForm" x-cloak x-transition class="fixed top-0 left-0 z-30 bg-black/50 w-full h-full flex items-end lg:justify-center lg:items-center overflow-hidden">

                <div class=" bg-white w-full  rounded-t-2xl p-4 lg:max-w-2xl lg:rounded-2xl lg:p-6 max-h-[90%] overflow-y-auto ">

                    <div class=" flex justify-between items-center pb-3 border-b-2">
                        <h3 class=" text-lg font-medium md:text-xl lg:font-semibold">
                            Confirm Transfer
                        </h3>

                        <button class=" text-2xl lg:text-3xl" @click="showPreviewForm = false">
                            <span class="fa fa-close text-red-700"></span>
                        </button>
                    </div>

                    <div class=" text-center space-y-1 mt-2 lg:mt-4">
                        {{-- <h4 class=" lg:text-lg lg:mb-2">
                            Total
                        </h4> --}}
                        <h2 class=" text-3xl lg:text-4xl font-bold">
                            <x-naira-symbol /> {{formatNumber($totalAmount)}}
                        </h2>
                    </div>


                    <div class=" mt-4 space-y-2 md:text-lg lg:space-y-3">
                        <div class=" flex justify-between items-center">
                            <label for="">Account Name</label>
                            <p class=" font-semibold text-green-600">
                                {{$account_name}}
                            </p>
                        </div>
                        <div class=" flex justify-between items-center">
                            <label for="">Bank Name</label>
                            <p class=" ">
                                {{$bank_name}}
                            </p>
                        </div>
                        <div class=" flex justify-between items-center">
                            <label for="">Account Number</label>
                            <p class=" ">
                                {{$account_number}}
                            </p>
                        </div>
                        <div class=" flex justify-between items-center">
                            <label for="">Swift Code</label>
                            <p class=" ">
                                {{$swift_code}}
                            </p>
                        </div>
                        <div class=" flex justify-between items-center">
                            <label for="">Routing Number</label>
                            <p class=" ">
                                {{$routing_number}}
                            </p>
                        </div>
                        <div class=" flex justify-between items-center">
                            <label for="">Amount in NGN</label>
                            <p class=" ">
                                <x-naira-symbol /> {{formatNumber($price)}}
                            </p>
                        </div>
                        <div class=" flex justify-between items-center">
                            <label for="">SwiftPay Fee</label>
                            <p class=" ">
                                <x-naira-symbol /> {{formatNumber($totalFee)}}
                            </p>
                        </div>
                        <div class=" flex justify-between items-center">
                            <label for="">Int'l transfer fee</label>
                            <p class=" ">
                                {{formatNumber($foreignCurrencyFee)}} {{$selectedCurrency}}
                            </p>
                        </div>

                    </div>

                    <div class=" mt-6 bg-blue-50 p-2 lg:p-3 rounded-xl lg:text-lg ">
                        <h4 class=" font-medium mb-2">Payment Method</h4>
                        <label for="">SwiftPay Balance</label> <br>
                        <p class=" text-2xl font-semibold text-primary lg:text-3xl lg:mt-1">
                            <x-naira-symbol /> {{formatNumber(auth()->user()->wallet_balance)}}
                        </p>
                    </div>

                    <x-enter-pin />

                    <x-primary-button type="button" class="   mt-6 mb-4" wire:click='transfer'>
                        {{ __('Pay') }}
                    </x-primary-button>

                </div>

            </div>
        </div>
    </div>
</div>