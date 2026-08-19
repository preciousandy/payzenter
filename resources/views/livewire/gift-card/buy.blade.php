<div class="px-4 py-6  min-h-screen max-w-4xl lg:p-6">

    <div wire:loading.flex wire:target='submitOrder' class=" fixed z-50 left-0 top-0 w-full h-full bg-white/50  justify-center items-center">
        <div class="lds-dual-ring">
            <div></div>
            <div></div>
        </div>
    </div>

    <h3 class=" text-lg lg:text-xl font-semibold">
        Complete the form below to buy a Gift Card
    </h3>

    <div class=" flex items-center gap-3 mt-4 lg:mt-6 lg:gap-4">
        <img src="{{asset($gift_card['logoUrls'][0])}}" alt="" class=" h-12 w-12 rounded-full lg:h-16 lg:w-16">

        <div>
            <h4 class=" font-medium mb-1 lg:text-lg">
                {{$gift_card['productName']}}
            </h4>

            <p class=" text-sm md:text-base text-gray-600">
                {{$gift_card['recipientCurrencyCode']}}
            </p>
        </div>
    </div>


    <div class=" mt-8" x-data="{showPreviewForm: @entangle('showModal')}">
        <form class=" space-y-4 lg:space-y-6 " wire:submit.prevent='validateOrder'>

            <div>
                @php
                $amount_label = "Amount in " . $gift_card['recipientCurrencyCode'];
                @endphp
                <x-input-label for="recipient_name" :value="__($amount_label)" />

                @if ($gift_card['denominationType'] === 'FIXED')

                <select wire:model="amount" id="" class=" p-3 w-full rounded-lg border-gray-300 mt-1" required>
                    <option value="">Select Amount</option>
                    @foreach ($gift_card['fixedRecipientToSenderDenominationsMap'] as $key => $card_price)
                    <option value="{{$key}}">{{$key}} </option>
                    @endforeach
                </select>

                @else

                <input wire:model="amount" type="number" class=" p-3 w-full rounded-lg border-gray-300 mt-1" required>

                <p class=" text-sm text-gray-700 lg:text-base mt-1 lg:mt-2">
                    Note: The amount must be within the range of {{$gift_card['minRecipientDenomination']}} and
                    {{$gift_card['maxRecipientDenomination']}} {{ $gift_card['recipientCurrencyCode'] }}
                </p>

                @endif

                <p class=" text-sm mt-1 lg:mt-2 lg:text-base text-gray-700 lg:font-medium">
                    SwiftPay charges a {{$fee}}% fee on all transactions
                </p>
            </div>

            <div>
                <x-input-label for="end_data" :value="__('Quantity')" />
                <x-text-input id="end_data" type="number" class="mt-1 block w-full" wire:model='quantity' />
                <x-input-error class="mt-2 " :messages="$errors->get('quantity')" />
            </div>
            <div>
                <x-input-label for="end_data" :value="__('Recipient Name')" />
                <x-text-input id="end_data" type="text" class="mt-1 block w-full" wire:model='name' />
                <x-input-error class="mt-2 " :messages="$errors->get('name')" />
            </div>
            <div>
                <x-input-label for="end_data" :value="__('Recipient Email')" />
                <x-text-input id="end_data" type="text" class="mt-1 block w-full" wire:model='email' />
                <x-input-error class="mt-2 " :messages="$errors->get('email')" />
            </div>

            <div class=" bg-gray-100 p-4 rounded-xl lg:p-6 lg:shadow-sm ">
                <h4>SwiftPay Balance</h4>

                <p class=" text-2xl lg:text-3xl font-bold mt-1">
                    <x-naira-symbol /> {{ formatNumber(auth()->user()->wallet_balance) }}
                </p>
            </div>

            <x-primary-button class=" w-full text-center rounded-lg lg:w-80 mx-auto">
                Proceed
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
                        Total Price
                    </h4>
                    <h2 class=" text-3xl lg:text-4xl font-bold">
                        <x-naira-symbol /> {{formatNumber($totalAmount)}}
                    </h2>
                </div>

                <div class=" mt-4 space-y-2 md:text-lg lg:space-y-3">
                    <div class=" flex justify-between items-center gap-4">
                        <label for="" class=" whitespace-nowrap">Gift Card</label>
                        <p class=" font-semibold text-green-600">
                            {{$gift_card['productName']}}
                        </p>
                    </div>
                    <div class=" flex justify-between items-center">
                        <label for="">Accepting Currency</label>
                        <p class=" ">
                            {{$gift_card['recipientCurrencyCode']}}
                        </p>
                    </div>
                    <div class=" flex justify-between items-center">
                        <label for="">Amount in {{$gift_card['recipientCurrencyCode']}} </label>
                        <p class=" ">
                            {{$amount}}
                        </p>
                    </div>

                    <div class=" flex justify-between items-center">
                        <label for="">Price in NGN</label>
                        <p class=" ">
                            <x-naira-symbol /> {{formatNumber($price)}}
                        </p>
                    </div>

                    <div class=" flex justify-between items-center">
                        <label for="">Fees</label>
                        <p class=" ">
                            <x-naira-symbol /> {{formatNumber($feeAmount)}}
                        </p>
                    </div>
                    <div class=" flex justify-between items-center">
                        <label for="">Quantity </label>
                        <p class=" ">
                            {{$quantity}}
                        </p>
                    </div>
                    <div class=" flex justify-between items-center">
                        <label for="">Recipient Name</label>
                        <p class=" ">
                            {{$name}}
                        </p>
                    </div>
                    <div class=" flex justify-between items-center">
                        <label for="">Recipient Email</label>
                        <p class=" ">
                            {{$email}}
                        </p>
                    </div>

                </div>

                <x-enter-pin />

                <div class=" bg-gray-100 p-3 rounded-xl mt-4 lg:p-4 lg:shadow-sm">
                    <h4 class=" font-semibold mb-2 ">
                        How to redeem?
                    </h4>

                    <div class=" text-sm text-gray-700 leading-5 lg:text-base lg:leading-7">
                        <x-see-more-text text="{{$gift_card['redeemInstruction']['verbose']}}" />
                    </div>

                    {{-- <p class=" text-sm leading-5">
                        {{$gift_card['redeemInstruction']['verbose']}}
                    </p> --}}
                </div>

                <x-primary-button class="   mt-6 rounded-lg mb-4" wire:click='submitOrder'>
                    {{ __('Continue') }}
                </x-primary-button>

            </div>

        </div>
    </div>
</div>