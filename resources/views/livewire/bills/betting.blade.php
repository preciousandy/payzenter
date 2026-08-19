<div class=" p-4 py-6 lg:py-10 max-w-4xl" x-data="{showPreviewForm: @entangle('showPreviewForm'), showSuccessModal: @entangle('showSuccessModal')}">
    <h2 class=" text-xl md:text-2xl lg:text-3xl  font-bold text-center"> Betting </h2>


    <x-loading-spinner wire:target='verifyCustomer, submitOrder, confirmPurchase' />

    {{-- success modal --}}
    <div x-cloak x-show="showSuccessModal" x-transition class=" bg-black/60 fixed h-full w-full left-0 top-0 z-[999] flex justify-center items-center p-4">

        <div class=" bg-white w-full rounded-2xl p-4 max-w-lg lg:p-6 ">
            <img src="{{asset('checked.png')}}" alt="" class=" h-16 mx-auto">

            <h4 class=" text-xl font-medium text-center mt-3 lg:text-2xl">
                Purchase Successful
            </h4>

            <p class=" text-gray-700 text-center mt-1 text-sm lg:mt-3 lg:text-lg">
                {{$success_message}}
            </p>

            <div class=" mt-2">
                <button wire:click='closeSuccessModal' class=" pr-btn w-full rounded-full mt-4 bg-red-700 border-red-700 disabled:bg-opacity-20 disabled:cursor-not-allowed " style="border-radius: 9999px">
                    Close
                </button>

            </div>
        </div>
    </div>

    <form action="" class=" space-y-6 md:space-y-8  mt-6 lg:mt-12" wire:submit.prevent='confirmPurchase'>

        <div>
            <x-input-label for="" :value="__('Select Provider')" />

            <select wire:model="provider" id="" class=" p-3 lg:py-4 w-full rounded-lg border-gray-300 mt-2" required>
                <option value="">Select Provider</option>
                @foreach ($providers as $providerItem)
                <option value="{{$providerItem->name}}">{{$providerItem->name}}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('provider')" />
        </div>


        <div>
            <x-input-label for="customer_id" :value="__('User ID')" />
            <x-text-input id="customer_id" class="block mt-1 w-full placeholder:text-gray-400" type="number" wire:model='customer_id' required />
            <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
        </div>

        @if (!$customer)
        <x-primary-button class="  w-fit mx-auto block px-12 rounded-full" type="button" wire:click='verifyCustomer'>
            Verify Customer
        </x-primary-button>
        @endif

        @if ($customer)
        <div class=" bg-white p-4 rounded-xl space-y-2 text-gray-600 lg:text-lg lg:p-6 lg:shadow">
            <p><b>Customer Name:</b> {{$customer["customer_name"] ?? '-'}} </p>
            <p><b>Customer Address:</b> {{$customer["customer_email"] ?? '-'}}</p>
        </div>



        <h3 class=" md:text-lg lg:text-xl font-semibold">Choose Amount</h3>

        <div class=" grid grid-cols-3 gap-4" x-data="{amount : @entangle('amount')}">
            @foreach ($bundles as $bundle)

            <button wire:click="$set('amount', {{$bundle}})" type="button" class="   p-3 lg:p-5 rounded-lg border border-gray-300 md:text-lg font-medium lg:hover:bg-blue-50" :class="amount == {{$bundle}} ? ' bg-primary text-white' : 'bg-slate-100'">
                <x-naira-symbol /> {{number_format($bundle)}}
            </button>

            @endforeach
        </div>

        <div>
            <x-input-label for="email" :value="__('Amount')" />
            <x-text-input id="email" class="block mt-1 w-full placeholder:text-gray-400" type="number" placeholder="500" wire:model='amount' required />
            <p class=" text-sm">Current balance: <b>
                    <x-naira-symbol /> {{formatNumber(auth()->user()->wallet_balance)}}
                </b></p>
            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
        </div>



        <x-primary-button class="   mx-auto block px-14 rounded-full lg:w-80">
            Confirm Purchase
        </x-primary-button>

        @endif
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
                    <x-naira-symbol /> {{formatNumber($amount + $total_fee)}}
                </h2>
            </div>

            <div class=" mt-4 space-y-2 md:text-lg lg:space-y-3">

                <div class=" flex justify-between items-center">
                    <label for="">Amount</label>
                    <p class=" font-medium text-green-700 ">
                        {{formatNumber($amount)}} NGN
                    </p>
                </div>
                <div class=" flex justify-between items-center">
                    <label for="">Provider</label>
                    <p class=" ">
                        {{ucfirst($provider)}}
                    </p>
                </div>
                <div class=" flex justify-between items-center">
                    <label for="">Customer ID</label>
                    <p class=" ">
                        {{$customer_id}}
                    </p>
                </div>

                @if ($customer)
                <div class=" flex justify-between items-center">
                    <label for="">Customer Name</label>
                    <p class=" ">
                        {{ $customer['customer_name'] ?? '-'}}
                    </p>
                </div>
                <div class=" flex justify-between items-center">
                    <label for="">Customer Address</label>
                    <p class=" ">
                        {{ $customer['customer_email'] ?? '-'}}
                    </p>
                </div>
                @endif

            </div>

            <div class=" mt-6 bg-blue-50 p-2 lg:p-3 rounded-xl lg:text-lg ">
                <h4 class=" font-medium mb-2">Payment Method</h4>
                <label for="">SwiftPay Balance</label> <br>
                <p class=" text-2xl font-semibold text-primary lg:text-3xl lg:mt-1">
                    <x-naira-symbol /> {{formatNumber(auth()->user()->wallet_balance)}}
                </p>
            </div>

            <x-enter-pin />

            <x-primary-button type="button" class="   mt-6 mb-4" wire:click='submitOrder'>
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
            @forelse ($recent_history as $historyItem)
            <div class=" border border-gray-200 p-2 rounded-md  hover:bg-primary/5 cursor-pointer md:p-4" wire:click="setHistoryDetails({{$historyItem->id}})">
                <div>
                    <p class=" text-sm font-medium text-black lg:text-lg">
                        {{$historyItem->customer_name ?  $historyItem->customer_name : $historyItem->provider . ' Account'}}
                    </p>

                    <div class=" flex items-center gap-2">
                        <p class=" text-xs lg:text-base text-gray-600 mt-1">
                            {{$historyItem->provider}}
                        </p>
                        <p class=" text-xs lg:text-base text-gray-600 mt-1">
                            {{$historyItem->customer_id ?? '-'}}
                        </p>
                        {{-- <p class=" text-xs lg:text-base text-gray-600 mt-1">
                            {{$historyItem->package_code}}
                        </p> --}}
                    </div>

                </div>

            </div>

            @empty
            <div class=" text-center text-gray-500">
                <p class=" text-sm lg:text-base">
                    No recent purchases found.
                </p>
            </div>
            @endforelse

        </div>
    </div>
</div>