<div class=" ">
    <x-loading-spinner wire:target='buyAirtime, confirmPurchase, setHistoryDetails, test' />

    <div x-data="{showPreviewForm: @entangle('showPreviewForm')}">
        <form action="" class=" space-y-6 mt-6  lg:mt-10 " wire:submit.prevent='confirmPurchase'>
            <div class=" flex items-center border-b-2 ">
                <button id="dropdownUsersButton" data-dropdown-toggle="dropdownUsers" data-dropdown-placement="bottom" class="   font-medium rounded-lg text-sm  px-3 py-2.5 text-center inline-flex items-center " type="button">

                    <img src="{{asset($network->img_url)}}" alt="" class=" h-10 rounded-lg md:h-14 ">

                    <span class=" fa fa-caret-down ml-2 lg:ml-4 "></span>
                </button>

                <x-text-input id="phone" class="px-2 w-full text-sm md:text-base lg:text-lg bg-inherit border-none focus:outline-0 focus:ring focus:ring-transparent shadow-none" type="number" placeholder="0901 *** ****" wire:model='phone' required />
            </div>

            <!-- Dropdown menu -->
            <div id="dropdownUsers" class="z-10 hidden bg-white rounded-lg shadow w-60 dark:bg-gray-700">
                <ul class="h-48 lg:h-56 py-2 overflow-y-auto text-black dark:text-gray-200" aria-labelledby="dropdownUsersButton">
                    @foreach ($networks as $theNetwork)
                    <li wire:click="$set('networkId', '{{$theNetwork->id}}')">
                        <a href="#" class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                            <img class="w-6 h-6 md:h-8 md:w-8 me-2 rounded-full" src="{{asset($theNetwork->img_url)}}" alt="Jese image">
                            {{$theNetwork->name}}
                        </a>
                    </li>
                    @endforeach

                </ul>

            </div>

            <h3 class=" text-lg font-semibold lg:text-xl" wire:click='test'>Top up </h3>

            <p class=" text-lg">
                Get up {{$airtime_cashback_percentage}}% and
                <x-naira-symbol /> {{$airtime_cashback_fixed}} cashback on every airtime purchase
            </p>

            <div class=" grid grid-cols-3 gap-4" x-data="{amount : @entangle('amount')}">
                @foreach ($airtime_bundles as $bundle)

                <button wire:click="$set('amount', {{$bundle['amount']}})" type="button" class=" hover:bg-blue-50 hover:text-primary   p-4 rounded-lg border border-gray-300 text-lg font-medium" :class="amount == {{$bundle['amount']}} ? ' bg-primary text-white' : 'bg-slate-100'">
                    <x-naira-symbol />{{number_format($bundle['label'])}}
                </button>

                @endforeach
            </div>

            <div>
                <x-input-label for="email" :value="__('Amount')" />
                <x-text-input id="email" class="block mt-1 w-full placeholder:text-gray-400" type="number" placeholder="500" wire:model='amount' required />
                <p class=" text-sm mt-1 lg:text-base">Current balance: <b>
                        <x-naira-symbol /> {{formatNumber(auth()->user()->wallet_balance)}}
                    </b></p>
                <x-input-error :messages="$errors->get('amount')" class="mt-2" />
            </div>



            <x-primary-button class="  w-fit mx-auto block px-12 rounded-full lg:w-80">
                {{-- <span wire:loading wire:target='buyAirtime()' class="fa fa-spinner fa-spin"></span> --}}
                Buy Airtime
            </x-primary-button>
        </form>


        <div x-show="showPreviewForm" x-cloak x-transition class="fixed top-0 left-0 z-30 bg-black/50 w-full h-full flex items-end lg:justify-center lg:items-center overflow-hidden">

            <div class=" bg-white w-full  rounded-t-2xl p-4 lg:max-w-2xl lg:rounded-2xl lg:p-6 max-h-[90%] overflow-y-auto ">

                <div class=" flex justify-between items-center pb-3 border-b-2">
                    <h3 class=" text-lg font-medium md:text-xl lg:font-semibold">
                        Confirm Purchase
                    </h3>

                    <button class=" text-2xl lg:text-3xl" @click="showPreviewForm = false">
                        <span class="fa fa-close text-red-700"></span>
                    </button>
                </div>

                <div class=" text-center space-y-1 mt-2 lg:mt-4">
                    <h2 class=" text-3xl lg:text-4xl font-bold">
                        <x-naira-symbol /> {{formatNumber($cost)}}
                    </h2>
                </div>

                <div class=" mt-4 space-y-2 md:text-lg lg:space-y-3">
                    <div class=" flex justify-between items-center">
                        <label for="">Phone Number</label>
                        <p class="">
                            {{$phone}}
                        </p>
                    </div>

                    <div class=" flex justify-between items-center">
                        <label for="">Amount</label>
                        <p class=" font-medium">
                            {{formatNumber($amount)}} NGN
                        </p>
                    </div>


                    <div class=" flex justify-between items-center">
                        <label for="">Cashback bonus</label>
                        <p class=" font-medium text-green-600">
                            {{formatNumber($total_cashback)}} NGN
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

                <x-enter-pin id="airtime" />

                <x-primary-button type="button" class="   mt-6 mb-4" wire:click='buyAirtime'>
                    {{ __('Pay') }}
                </x-primary-button>

            </div>

        </div>


        {{-- history --}}
        <div class=" mt-6">
            <h3 class=" font-semibold md:text-lg lg:text-xl">
                Recent Purchases
            </h3>

            <div class=" space-y-3 mt-2">
                @foreach ($recent_history as $historyItem)
                <div class=" border border-gray-200 p-2 rounded-md flex items-center justify-between hover:bg-primary/5 cursor-pointer md:p-4" wire:click="setHistoryDetails({{$historyItem->id}})">
                    <p class=" font-medium text-black lg:text-lg">
                        {{$historyItem->phone_number}}
                    </p>
                    <div class=" text-right">
                        <p class=" text-sm lg:text-base">
                            {{$historyItem->network_name}}
                        </p>
                        <p class=" text-xs text-gray-500 mt-1 lg:text-sm">
                            {{$historyItem->updated_at->diffForHumans() }}
                        </p>
                    </div>
                </div>

                @endforeach
            </div>
        </div>
    </div>
</div>