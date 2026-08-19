<div class="bg-white">
    <div class=" px-4 py-6  lg:py-10 min-h-screen max-w-4xl" x-data="{showPreviewForm: @entangle('showPreviewForm')}">

        <x-loading-spinner wire:target='verifyCustomer, buyCable, test' />


        <h2 class=" text-2xl  lg:text-3xl font-bold text-center" wire:click='test'>TV & Internet</h2>

        <form action="" class=" space-y-6 lg:space-y-8  mt-10 lg:mt-14" wire:submit.prevent='confirmPurchase'>

            <div class="whitespace-nowrap overflow-x-auto py-2 flex items-center space-x-4" x-data="{cableId : @entangle('cableId')}">
                @foreach ($cables as $cable_provider)
                <div wire:click="$set('cableId', {{$cable_provider->id}})" class="p-3 rounded-lg border border-gray-300 text-sm font-medium flex items-center gap-2 flex-shrink-0 cursor-pointer lg:p-4 lg:px-10" :class="cableId == {{$cable_provider->id}} ? 'bg-primary text-white' : 'bg-slate-100'">
                    <img src="{{asset($cable_provider->img_url)}}" alt="" class="h-8 w-8 rounded-md">
                    <span class="whitespace-nowrap">{{$cable_provider->name}}</span>
                </div>
                @endforeach
            </div>


            @if ($cable && $cable->code == 'showmax')
            {{-- phone --}}
            <div>
                <x-input-label for="phone" :value="__('Phone Number')" />
                <x-text-input id="phone" class="block mt-1 w-full placeholder:text-gray-400" type="tel" placeholder="0803 *** ****" wire:model='phone' />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>
            @else
            <div>
                <x-input-label for="email" :value="__('Smart Card number/ IUC Number')" />
                <x-text-input id="email" class="block mt-1 w-full placeholder:text-gray-400" type="number" placeholder="1232 *** ****" wire:model='card_number' />
                <x-input-error :messages="$errors->get('card_number')" class="mt-2" />
            </div>
            @endif



            @if (!$customer && $cable->code !== 'showmax')
            <x-primary-button class="  w-fit mx-auto block px-8 rounded-full" type="button" wire:click='verifyCustomer'>
                Verify Customer
            </x-primary-button>
            @endif



            @if ($customer)
            <div class=" bg-indigo-100 p-6 rounded-xl space-y-2 text-primary lg:text-lg">
                <p><b>Customer Name:</b> {{$customer["customer_name"] ?? '-'}} </p>
            </div>
            @endif


            @if ($customer || $cable->code == 'showmax')

            <div>
                <x-input-label for="phone" :value="__('Select Plan')" />

                <select wire:model="package_code" id="" class=" p-3 lg:py-4 w-full rounded-lg border-gray-300 mt-2">
                    <option value="">Select Plan</option>
                    @foreach ($packages as $plan)
                    <option value="{{$plan->package_code}}">{{$plan->name}} (N{{formatNumber($plan->price)}})</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('package_code')" />

                <p class=" text-sm mt-1 lg:text-base">Current balance: <b>
                        <x-naira-symbol /> {{formatNumber(auth()->user()->wallet_balance)}}
                    </b>
                </p>
            </div>




            <x-primary-button class="  w-fit mx-auto block px-12 rounded-full">
                Buy Cable plan
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
                        <x-naira-symbol /> {{formatNumber($price)}}
                    </h2>
                </div>

                <div class=" mt-4 space-y-2 md:text-lg lg:space-y-3">

                    <div class=" flex justify-between items-center gap-4">
                        <label for="">Phone Number</label>
                        <p class=" ">
                            {{$phone}}
                        </p>
                    </div>
                    <div class=" flex justify-between items-center gap-4">
                        <label for="">Provider</label>
                        <p class=" ">
                            {{$cable?->name ?? ""}}
                        </p>
                    </div>


                    <div class=" flex justify-between items-center gap-4">
                        <label for="">Package</label>
                        <p class=" ">
                            {{$package?->name ?? ""}}
                        </p>
                    </div>


                    <div class=" flex justify-between items-center gap-4">
                        <label for="">Smart Card number</label>
                        <p class=" ">
                            {{$card_number ?? ""}}
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

                <x-primary-button type="button" class="   mt-6 mb-4" wire:click='buyCable'>
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
                <div class=" border border-gray-200 p-2 rounded-md flex items-center justify-between hover:bg-primary/5 cursor-pointer md:p-4" wire:click="setHistoryDetails({{$historyItem->id}})">
                    <div>
                        <p class=" font-medium text-black lg:text-lg">
                            {{$historyItem->customer_name ?? $historyItem->phone ?? 'Unknown Customer'}}
                        </p>

                        <p class=" text-sm lg:text-base text-gray-600 mt-1">
                            {{$historyItem->card_number}}
                        </p>

                    </div>
                    <div class=" text-right">
                        <p class=" text-sm lg:text-base font-medium">
                            {{ucfirst($historyItem->provider_code)}}
                        </p>
                        <p class=" text-xs text-gray-500 mt-1 lg:text-sm">
                            {{$historyItem->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>

                @empty
                <p class=" text-sm text-gray-500">No recent purchases found.</p>

                @endforelse
            </div>
        </div>
    </div>
</div>