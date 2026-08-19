<div class=" bg-white">
    <div class=" px-4 py-6 bg-white min-h-screen max-w-4xl lg:p-6" x-data="{showModal: @entangle('showModal')}">

        <x-loading-spinner wire:target='submitOrder, generatePaymentAddress' />

        <h2 class="text-xl lg:text-2xl font-semibold text-center">
            Sell {{ $currency->name }}
        </h2>

        <div class=" mt-6 space-y-2 lg:mt-8 lg:space-y-3">
            <div class=" flex justify-between items-center">
                <label>Price</label>
                <p class=" text-lg font-semibold text-green-600">
                    <x-naira-symbol /> {{formatNumber($nairaRate)}}
                </p>
            </div>
            <div class=" flex justify-between items-center">
                <label>Payment Method</label>
                <p class=" ">
                    SwiftPay Balance
                </p>
            </div>

        </div>

        <p class=" text-primary mt-5 lg:text-lg">
            <span class=" fa fa-info-circle"></span>
            Note: You need to first fund your SwiftPay {{$currency->name}} wallet before you can sell crypto
        </p>

        <div class=" p-2 bg-gray-100 rounded-xl mt-6 lg:mt-8 lg:p-4 lg:shadow-sm">
            <h3 class=" lg:text-lg">SwiftPay Balance</h3>
            <p class="  font-semibold  mt-2 text-2xl lg:text-3xl">
                <x-naira-symbol /> {{formatNumber(auth()->user()->wallet_balance)}}
            </p>
        </div>

        <div x-cloak x-show="showModal" x-transition class=" bg-black/60 fixed h-full w-full left-0 top-0 z-[999] flex justify-center items-center p-4">

            <div class=" bg-white w-full rounded-2xl p-4 max-w-lg lg:p-6 ">
                <img src="{{asset('checked.png')}}" alt="" class=" h-16 mx-auto">

                <h4 class=" text-xl font-medium text-center mt-3 lg:text-2xl">Success</h4>

                <p class=" text-gray-700 text-center mt-1 text-sm lg:mt-3 lg:text-lg">
                    Your payment wallet address is being generated and you will be notified shortly once it is ready.
                </p>

                <div class=" mt-2" wire:poll.1s='decrement'>

                    @if ($remainingTime)
                    <p id="timer" class=" font-medium">
                        Please wait.. {{ $remainingTime }}s
                    </p>
                    @endif

                    <button @click="location.reload()" class=" pr-btn w-full rounded-full mt-4 bg-red-700 border-red-700 disabled:bg-opacity-20 disabled:cursor-not-allowed " style="border-radius: 9999px" {{ $remainingTime > 0 ? 'disabled' : '' }}>
                        Close
                    </button>

                </div>
            </div>
        </div>

        @if ($quidax_wallet)
        <div class=" p-2 bg-gray-50 rounded-xl mt-6 lg:mt-8 md:p-4 lg:p-6">
            <h3 class=" font-medium lg:text-lg lg:font-semibold">SwiftPay {{$currency->name}} Wallet</h3>

            <div class=" text-sm mt-2 lg:mt-4 lg:space-y-2 lg:text-base ">
                <div class=" flex justify-between items-center">
                    <label for="">Name</label>
                    <p class="  font-medium">
                        {{$quidax_wallet['name']}}
                    </p>
                </div>
                <div class=" flex justify-between items-center">
                    <label for="">Currency code</label>
                    <p class="  font-medium">
                        {{$quidax_wallet['currency']}}
                    </p>
                </div>
                <div class=" flex justify-between items-center">
                    <label for="">Balance</label>
                    <p class=" text-lg font-semibold">
                        {{$quidax_wallet['balance']}}
                    </p>
                </div>
            </div>

            <h3 class=" font-medium mt-4 lg:text-lg">Supported Networks</h3>

            <div class=" mt-2 space-y-2 lg:mt-4 lg:space-y-6">
                @foreach ($networks as $network)
                <div class=" text-sm lg:text-base lg:space-y-3 border border-gray-300 p-3 rounded-xl space-y-2 lg:p-4">
                    <div class=" flex justify-between items-center">
                        <label for="">ID</label>
                        <p class="  font-medium text-primary">
                            {{$network['id']}}
                        </p>
                    </div>
                    <div class=" flex justify-between items-center">
                        <label for="">Name</label>
                        <p class="  font-medium">
                            {{$network['name']}}
                        </p>
                    </div>
                    <div class="  flex justify-between items-center">
                        <label for="">Payment Address</label>

                        @if ($network['payment_address'])
                        <p class="  font-medium break-all max-w-[50%] text-right text-primary">
                            {{$network['payment_address']}}
                        </p>

                        @else
                        <button class=" text-primary font-semibold  " wire:click="generatePaymentAddress('{{$network['id']}}',  '{{$quidax_wallet['currency']}}')">
                            <span class="fa fa-refresh mr-1"></span> Generate Address
                        </button>
                        @endif

                    </div>

                    <div class=" flex justify-between items-center">
                        <label for="">Withdrawals Enabled</label>
                        <p class="  font-medium">
                            {{$network['withdraws_enabled'] ? 'Yes' : "No"}}
                        </p>
                    </div>
                    <div class=" flex justify-between items-center">
                        <label for="">Deposits Enabled</label>
                        <p class="  font-medium">
                            {{$network['deposits_enabled'] ? 'Yes' : "No"}}
                        </p>
                    </div>

                    @if ($network['destination_tag'])
                    <div class=" flex justify-between items-center">
                        <label for="">Destination Tag</label>
                        <p class="  font-medium">
                            {{$network['destination_tag']}}
                        </p>
                    </div>

                    @endif

                    @if ($network['payment_address'])
                    <x-secondary-button class=" w-full " onclick="copyToClipboard('{{$network['payment_address']}}')">
                        <span class="fa fa-copy mr-2"></span> Copy Wallet Address
                    </x-secondary-button>
                    @endif

                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- <p class=" text-primary mt-3 lg:mt-5">
            <span class=" fa fa-info-circle"></span>
            Note: Ensure you input the right info, as we would not be held liable for any loss of asset.
        </p> --}}

        <div x-data="{showPreviewForm: @entangle('showPreviewForm')}">
            <form action="" class="mt-6 space-y-4 md:space-y-6" wire:submit.prevent='createOrder'>
                @php
                $label = "Amount of " . $currency->symbol . " to sell";
                @endphp

                <div>
                    <x-input-label for="amount" :value="__($label)" />
                    <x-text-input id="amount" class="block mt-1 w-full placeholder:text-gray-400" type="text" wire:model.live='amount' required />

                    <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="amount" :value="__('Amount you will receive in NGN')" />
                    <p class=" font-semibold p-4 bg-gray-100 rounded-xl">
                        {{formatNumber($nairaAmount)}} NGN
                    </p>
                </div>

                <x-primary-button class=" mx-auto w-full block ">
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
                        <div class=" flex justify-between items-center gap-x-4">
                            <label class=" whitespace-nowrap">Crypto to Sell</label>
                            <p class=" font-medium break-all max-w-[50%] text-right">
                                {{formatNumber($amount)}} {{$currency->symbol}}
                            </p>
                        </div>
                        <div class=" flex justify-between items-center">
                            <label>Amount to receive </label>
                            <p class=" ">
                                {{formatNumber($nairaAmount)}} NGN
                            </p>
                        </div>



                        <div class=" flex justify-between items-center">
                            <label>Fee</label>
                            <p class=" ">
                                {{formatNumber($feeAmount)}} NGN
                            </p>
                        </div>


                    </div>

                    <x-enter-pin />

                    <x-primary-button class="   mt-6 mb-4" wire:click='submitOrder'>
                        {{ __('Continue') }}
                    </x-primary-button>

                </div>

            </div>
        </div>


    </div>
</div>