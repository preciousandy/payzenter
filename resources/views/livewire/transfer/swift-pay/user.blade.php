<div class=" bg-white">
    <div class=" px-4 py-6 lg:p-6 bg-white min-h-screen max-w-5xl">
        <h3 class=" font-medium text-center md:text-lg lg:text-xl lg:font-semibold">Transfer to SwiftPay Account</h3>

        <x-loading-spinner wire:target="confirmTransfer, transfer" />

        <div class=" mt-6">
            <img src="{{$user->profile_photo}}" alt="" class=" w-24 h-24 rounded-full mx-auto object-cover lg:h-28 lg:w-28">

            <div class=" w-fit mx-auto text-center mt-4 lg:mt-6">
                <h1 class="text-xl lg:text-3xl font-extrabold">{{$user->name}}</h1>
                <p class="text-gray-600 mt-1 md:text-lg md:font-medium md:mt-3">{{'@'}}{{$user->username}}</p>
            </div>

            <div x-data="{showPreviewForm: @entangle('showPreviewForm'), showSuccessfullModal: @entangle('showSuccessfullModal')}" class=" mt-6">

                <form wire:submit.prevent='confirmTransfer' class="space-y-4 lg:space-y-6">

                    <div>
                        <x-input-label for="username" :value="__('Amount')" />
                        <x-text-input id="username" type="number" class="mt-1 block w-full" wire:model='amount' required />
                        <x-input-error class="mt-2 " :messages="$errors->get('amount')" />
                    </div>

                    <div class=" mt-6">
                        <x-input-label for="pin" :value="__('Description')" />
                        <x-text-input id="pin" type="text" class="mt-1 block w-full" wire:model='description' />
                        <x-input-error class="mt-2 " :messages="$errors->get('description')" />
                    </div>



                    <x-primary-button class=" w-48 mx-auto block rounded-lg" type="submit">
                        {{ __('Confirm') }}
                    </x-primary-button>


                </form>

                {{-- order Preview --}}
                <div x-show="showPreviewForm" x-cloak x-transition class=" fixed top-0 left-0 z-30 bg-black/50 w-full h-full flex items-end lg:justify-center lg:items-center overflow-hidden">

                    <div class=" bg-white w-full  rounded-t-2xl p-4 lg:max-w-xl lg:rounded-2xl lg:p-6 max-h-[90%] overflow-y-auto ">

                        <div class=" flex justify-between items-center pb-3 border-b-2">
                            <h3 class=" text-lg font-medium md:text-xl lg:font-semibold">
                                Confirm Transfer
                            </h3>

                            <button class=" text-2xl lg:text-3xl" @click="showPreviewForm = false">
                                <span class="fa fa-close text-red-700"></span>
                            </button>
                        </div>

                        <div class=" text-center space-y-1 mt-2 lg:mt-4">

                            <h2 class=" text-3xl lg:text-4xl font-bold">
                                <x-naira-symbol /> {{formatNumber($amount)}}
                            </h2>
                        </div>

                        <div class=" mt-4 space-y-2 md:text-lg lg:space-y-3">
                            <div class=" flex justify-between items-center">
                                <label for="">SwiftPay Tag</label>
                                <p class="  font-semibold text-green-600">
                                    {{'@'}}{{$user?->username}}
                                </p>
                            </div>
                            <div class=" flex justify-between items-center">
                                <label for="">Recipient Name</label>
                                <p class=" ">
                                    {{$user?->name}}
                                </p>
                            </div>
                            <div class=" flex justify-between items-center">
                                <label for="">Amount</label>
                                <p class=" ">
                                    {{formatNumber($amount)}} NGN
                                </p>
                            </div>
                            <div class=" flex justify-between items-center">
                                <label for="">Remarks</label>
                                <p class=" ">
                                    {{$description}}
                                </p>
                            </div>

                        </div>

                        <h4 class=" font-medium mt-4 mb-2 md:text-lg">Payment Method</h4>
                        <div class="  bg-gray-50 p-2 rounded-xl ">
                            <x-input-label for="pin" :value="__('SwiftPay Balance')" />
                            <p class=" text-2xl font-semibold text-primary mt-1 lg:text-3xl">
                                <x-naira-symbol /> {{formatNumber(auth()->user()->wallet_balance)}}
                            </p>
                        </div>

                        <x-enter-pin />

                        <x-primary-button type="button" class=" my-4 rounded-lg lg:mt-6" wire:click='transfer'>
                            {{ __('Pay') }}
                        </x-primary-button>

                    </div>

                </div>

                {{-- transfer successful form --}}
                <div x-show="showSuccessfullModal" x-cloak x-transition class=" fixed top-0 left-0 z-30 bg-black/50 w-full h-full flex items-end overflow-hidden lg:items-center lg:justify-center">

                    <div class=" bg-white w-full  rounded-t-2xl p-4 lg:max-w-lg lg:p-8 lg:rounded-2xl ">

                        <img src="{{asset('transfer-success.png')}}" alt="" class=" mx-auto h-40 lg:h-48">

                        <h3 class=" text-lg font-semibold text-center md:text-xl lg:text-2xl">
                            Transfer Successful
                        </h3>

                        <p class=" text-sm text-gray-700 text-center mt-2 lg:mt-3 md:text-base lg:text-lg ">
                            Your transfer of
                            <x-naira-symbol />{{formatNumber($amount)}} NGN to {{$user?->name}} was successful
                        </p>

                        @if ($transaction)
                        <a href="{{route('transfer.swiftpay.receipt', $transaction?->hash_id)}}">
                            <x-primary-button type="button" class="   mt-6 mb-8 rounded-lg lg:mb-4">
                                {{ __('View Receipt') }}
                            </x-primary-button>
                        </a>
                        @endif

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>