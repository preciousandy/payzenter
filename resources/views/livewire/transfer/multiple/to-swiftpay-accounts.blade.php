<div>

    <x-loading-spinner wire:target='transfer, confirm' />

    <div x-data="{showAddUserModal: false}" x-on:close-modal.window="showAddUserModal = false">
        <button class="bg-indigo-50 text-center text-sm md:text-base lg:text-lg mx-auto w-full p-3 font-medium text-primary rounded-lg md:p-4 lg:p-6" @click="showAddUserModal = true">
            <span class=" fa fa-plus-circle text-3xl lg:text-5xl text-primary block mb-1 ">
            </span>
            <span>
                Add user
            </span>
        </button>

        <div x-show="showAddUserModal" x-transition x-cloak class=" fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4">
            <div class=" bg-gray-50 p-4 lg:p-6 rounded-lg w-full md:w-full  space-y-5 max-w-xl">
                <div class=" flex justify-between items-center mb-6">
                    <h2 class=" text-lg font-semibold lg:text-xl">Add SwiftPay Account</h2>
                    <button @click="showAddUserModal = false">
                        <span class="fa fa-close text-2xl lg:text-3xl text-gray-500"></span>
                    </button>
                </div>

                <form>
                    <div>
                        <x-input-label for="username" :value="__('SwiftPay Tag')" />
                        <div class="flex mt-2">
                            <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-50 border border-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                                <x-lucide-at-sign class=" h-5 text-gray-700" />
                            </span>
                            <input type="text" id="website-admin" class="rounded-none rounded-e-lg bg-white border border-gray-300 text-gray-900 focus:ring-1 focus:ring-primary outline-none focus:border-primary block flex-1 min-w-0 w-full text-sm p-2.5 dark:focus:ring-primary focus:border-l-0 lg:py-4" wire:model='username' placeholder="johndoe" />
                        </div>
                        <x-input-error :messages="$errors->get('username')" class="mt-2" />

                        @if ($name)
                        <p class=" font-medium text-primary bg-blue-100 text-sm p-2 rounded-md mt-1">
                            <span class="fa fa-check-circle mr-1"></span> {{$name}}
                        </p>
                        @endif
                    </div>

                    @if ($name)
                    <div class=" mt-4">
                        <x-input-label for="username" :value="__('Amount')" />
                        <x-text-input id="username" type="number" class="mt-1 block w-full" wire:model='amount' required />
                        <x-input-error class="mt-2 " :messages="$errors->get('amount')" />
                    </div>

                    <div class=" mt-4">
                        <x-input-label for="pin" :value="__('Description')" />
                        <x-text-input id="pin" type="text" class="mt-1 block w-full" wire:model='description' required />
                        <x-input-error class="mt-2 " :messages="$errors->get('description')" />
                    </div>
                    @else
                    <div class=" mt-4">
                        <x-input-label for="username" :value="__('Amount')" />
                        <x-text-input id="username" type="number" class="mt-1 block w-full disabled:bg-gray-100" wire:model='amount' disabled />
                        <x-input-error class="mt-2 " :messages="$errors->get('amount')" />
                    </div>

                    <div class=" mt-4">
                        <x-input-label for="pin" :value="__('Description')" />
                        <x-text-input id="pin" type="text" class="mt-1 block w-full disabled:bg-gray-100" wire:model='description' disabled />
                        <x-input-error class="mt-2 " :messages="$errors->get('description')" />
                    </div>
                    @endif


                    @if ($name)
                    <x-primary-button class=" w-full rounded-xl mt-6 " type="button" wire:click='addAccount'>
                        {{ __('Add user') }}
                    </x-primary-button>

                    <button type="button" class=" block w-fit mx-auto p-2 text-sm font-medium mt-3 text-center" wire:click='clearDetails'>
                        <span class=" fa fa-close mr-1"></span> Clear Details
                    </button>
                    @else
                    <x-primary-button class=" w-full rounded-xl mt-6 " type="button" wire:click='setUser'>
                        Confirm User
                    </x-primary-button>
                    @endif

                </form>
            </div>
        </div>


    </div>

    <div class=" mt-6 lg:mt-8" x-data="{showPreviewForm: @entangle('showPreviewForm'), showSuccessfullModal : @entangle('showSuccessfullModal')}">
        <h3 class=" text-lg font-medium lg:text-2xl">
            SwiftPay Accounts
        </h3>


        <div class=" mt-4 space-y-2">
            @forelse ($swiftpayAccounts as $account)

            <div class=" bg-slate-100 py-3 px-2 lg:p-4 rounded-lg flex items-center gap-x-2 lg:gap-x-4">
                <img src="{{$account['user']['profile_photo']}}" alt="" class=" h-10 w-10 lg:h-14 lg:w-14 rounded-full object-cover">
                <div class=" w-full flex items-center justify-between gap-4">
                    <div>
                        <h3 class=" font-medium text-sm md:text-base lg:text-lg">
                            {{$account['user']['name']}}

                        </h3>

                        <p class=" text-xs md:text-sm lg:text-base text-gray-600 lg:mt-1">
                            {{'@'}}{{$account['user']['username']}}
                        </p>
                    </div>

                    <div class="flex items-center gap-1 whitespace-nowrap">
                        <p class=" text-lg font-semibold md:text-xl lg:text-2xl">
                            <x-naira-symbol /> {{formatNumber($account['amount'])}}
                        </p>
                        <button class=" p-3" wire:click="removeAccount({{$account['user']['id']}})">
                            <span class=" fa fa-times h-6 w-6 lg:h-8 lg:w-8 flex justify-center items-center text-white bg-red-700 rounded-full"></span>
                        </button>
                    </div>
                </div>
            </div>

            @empty

            <div class=" flex justify-center items-center h-40 text-sm text-gray-600 md:text-base lg:text-lg">
                <p>
                    No Swiftpay accounts added yet
                </p>
            </div>

            @endforelse

            <x-input-error class="mt-2 " :messages="$errors->get('swiftpayAccounts')" />
        </div>


        <div class=" mt-6">

            <p class=" lg:text-lg">
                Total Amount: <span class=" font-semibold text-lg md:text-xl lg:text-2xl">
                    <x-naira-symbol /> {{formatNumber($totalAmount)}}
                </span>
            </p>

            <p class=" text-sm md:text-base lg:text-lg text-gray-700 lg:mt-2">
                Transfer Fee: <span class=" font-medium text-base md:text-lg lg:text-xl">
                    <x-naira-symbol /> {{formatNumber(0)}}
            </p>

            <x-primary-button class=" w-48 lg:w-80 mx-auto block rounded-lg mt-6 lg:mt-8" type="submit" wire:click='confirm'>
                {{ __('Confirm') }}
            </x-primary-button>
        </div>

        {{-- order Preview --}}
        <div x-show="showPreviewForm" x-cloak x-transition class=" fixed top-0 left-0 z-30 bg-black/50 w-full h-full  flex items-end lg:justify-center lg:items-center overflow-hidden">

            <div class=" bg-white w-full  rounded-t-2xl p-4 lg:max-w-2xl lg:rounded-2xl lg:p-6 max-h-[90%] overflow-y-auto ">

                <div class=" flex justify-between items-center py-3 border-b-2">
                    <h3 class="  text-lg font-medium md:text-xl lg:font-semibold">
                        Confirm Transfer
                    </h3>

                    <button class=" text-2xl lg:text-3xl" @click="showPreviewForm = false">
                        <span class="fa fa-close text-red-700"></span>
                    </button>
                </div>


                <div class=" text-center space-y-1 lg:space-y-3 mt-2">
                    <h4 class=" lg:text-lg font-medium text-gray-700">
                        Total
                    </h4>

                    <h2 class=" text-3xl lg:text-4xl font-bold">
                        <x-naira-symbol /> {{formatNumber($totalAmount)}}
                    </h2>
                </div>

                <div class=" mt-4 space-y-2 bg-blue-50/50 p-2 rounded-xl border border-blue-300  md:text-lg lg:space-y-3">
                    <div class=" flex justify-between items-center gap-2">
                        <label for="">Recipients</label>
                        <p class=" font-medium ">
                            {{count($swiftpayAccounts) > 0 ? $swiftpayAccounts[0]['name'] . " and " . count($swiftpayAccounts) - 1 . " others" : '' }}
                        </p>
                    </div>
                    <div class=" flex justify-between items-center gap-2">
                        <label for="">Type</label>
                        <p class=" ">
                            SwiftPay Transfer
                        </p>
                    </div>
                    <div class=" flex justify-between items-center gap-2">
                        <label for="">SwiftPay fees</label>
                        <p class=" ">
                            {{formatNumber(0)}} NGN
                        </p>
                    </div>

                </div>

                <h4 class=" font-medium mt-4 mb-2 md:text-lg">Payment Method</h4>
                <div class="  bg-gray-50 p-2 rounded-xl  lg:p-3">
                    <x-input-label for="pin" :value="__('SwiftPay Balance')" />
                    <p class=" text-2xl font-semibold text-primary mt-1 lg:text-3xl">
                        <x-naira-symbol /> {{formatNumber(auth()->user()->wallet_balance)}}
                    </p>
                </div>

                <x-enter-pin id="swiftpay-accounts" />

                <x-primary-button type="button" class="   mt-4 mb-10 lg:mt-6 lg:mb-0 rounded-lg" wire:click='transfer'>
                    {{ __('Pay') }}
                </x-primary-button>

            </div>

        </div>

        {{-- transfer successful form --}}
        <div x-show="showSuccessfullModal" x-cloak x-transition class="fixed top-0 left-0 z-30 bg-black/50 w-full h-full flex items-end overflow-hidden lg:items-center lg:justify-center">

            <div class=" bg-white w-full  rounded-t-2xl p-4 lg:max-w-lg lg:p-8 lg:rounded-2xl ">

                <img src="{{asset('transfer-success.png')}}" alt="" class=" mx-auto h-40 lg:h-48">

                <h3 class=" text-lg font-semibold text-center md:text-xl lg:text-2xl">
                    Transfer Successful
                </h3>

                <p class=" text-sm text-gray-700 text-center mt-2 lg:mt-3 md:text-base lg:text-lg ">
                    Your multiple transfer of
                    <x-naira-symbol />{{formatNumber($totalAmount)}} NGN was successful
                </p>

                @if ($transaction)
                <a href="{{route('transfer.multiple.receipt', $transaction?->hash_id)}}">
                    <x-primary-button type="button" class=" mt-6 mb-8 rounded-lg lg:mb-4">
                        {{ __('View Receipt') }}
                    </x-primary-button>
                </a>
                @endif

            </div>

        </div>
    </div>
</div>