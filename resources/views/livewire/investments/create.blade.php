<div class=" px-4 py-6 max-w-5xl">
    <div x-data="{showPreviewForm: @entangle('showPreviewForm')}">
        <form class=" space-y-6" wire:submit='save'>
            <h3 class=" text-xl lg:text-2xl font-semibold text-center">Invest in {{$name}} </h3>
            <div class=" mt-6 lg:mt-8 bg-gray-50 rounded-xl p-3">
                {{-- <h4 class=" font-medium mb-2">Payment Method</h4> --}}
                <label for="" class=" font-medium lg:text-lg">SwiftPay Balance</label> <br>
                <p class=" text-2xl font-semibold text-primary mt-2">
                    <x-naira-symbol /> {{formatNumber(auth()->user()->wallet_balance)}}
                </p>
            </div>

            <div>
                <x-input-label for="end_data" :value="__('Asset Name')" />
                <x-text-input id="end_data" type="text" class="mt-1 block w-full" wire:model='name' disabled />
                <x-input-error class="mt-2 " :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="end_data" :value="__('Asset Percentage Change(%)')" />
                <x-text-input id="end_data" type="number" class="mt-1 block w-full" wire:model='change_percentage' disabled />
                <x-input-error class="mt-2 " :messages="$errors->get('change_percentage')" />
            </div>

            <div>
                <x-input-label for="amount" :value="__('Amount to Invest (In Naira)')" />
                <x-text-input id="amount" type="number" class="mt-1 block w-full" wire:model.live='amount' required />
                <x-input-error class="mt-2 " :messages="$errors->get('amount')" />

                <div class=" flex justify-between items-center mt-1">


                    <div></div>

                    <p class="  ">
                        Fee: <span class=" font-medium ">
                            <x-naira-symbol /> {{formatNumber($totalFee)}}
                        </span>
                    </p>
                </div>


            </div>


            <div>
                <x-input-label for="end_data" :value="__('End date')" />
                <x-text-input id="end_data" type="date" class="mt-1 block w-full" wire:model.defer='end_date' required />
                <x-input-error class="mt-2 " :messages="$errors->get('end_date')" />
            </div>

            <div>
                <label for="terms" class="inline-flex items-center">
                    <input id="terms" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" required>
                    <span class="ms-2 text-sm md:text-base text-gray-600 dark:text-gray-400">{{ __('I have read and agree to the Terms & Conditions and Privacy Policy') }}</span>
                </label>
            </div>

            <x-primary-button class=" px-10 mx-auto  block rounded-xl w-full lg:w-80">
                Invest now
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
                        <x-naira-symbol /> {{formatNumber($total)}}
                    </h2>
                </div>

                <div class="mt-4 space-y-2 md:text-lg lg:space-y-3">
                    <div class=" flex justify-between items-center">
                        <label for="">Asset Name</label>
                        <p class=" ">
                            {{$name}}
                        </p>
                    </div>
                    <div class=" flex justify-between items-center">
                        <label for="">Investment Amount</label>
                        <p class=" ">
                            {{formatNumber($amount)}} NGN
                        </p>
                    </div>
                    <div class=" flex justify-between items-center">
                        <label for="">Asset Percentage Change</label>
                        <p class=" ">
                            {{$change_percentage}} %
                        </p>
                    </div>

                    <div class=" flex justify-between items-center">
                        <label for="">SwiftPay Fees</label>
                        <p class=" ">
                            {{formatNumber($totalFee)}} NGN
                        </p>
                    </div>
                    <div class=" flex justify-between items-center">
                        <label for="">End date</label>
                        <p class=" ">
                            {{date('d M, Y', strtotime($end_date))}}
                        </p>
                    </div>
                </div>

                <x-enter-pin />

                <x-primary-button class="   mt-6 mb-4" wire:click='submit'>
                    {{ __('Continue') }}
                </x-primary-button>

            </div>

        </div>
    </div>
</div>