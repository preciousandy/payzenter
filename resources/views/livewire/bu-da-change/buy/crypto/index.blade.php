<div class=" bg-white">
    <div class=" px-4 py-6 bg-white min-h-screen max-w-5xl lg:p-6 ">
        <h2 class=" text-xl lg:text-2xl font-semibold text-center">
            S2P Trading
        </h2>

        <div x-data="assetTabs" class=" mt-6 lg:mt-8">
            <div class=" grid grid-cols-2 gap-3 mb-4 lg:mb-6  max-w-xl">
                <template x-for="(tab, tabIndex) in tabMenu" :key="tabIndex">
                    <button class=" p-2 lg:p-3 rounded-lg" :class="currentTab == tab ? ' text-white font-semibold  bg-primary' : ' bg-gray-100'" @click="currentTab = tab">
                        <span x-text="tab"></span>
                    </button>
                </template>
            </div>

            {{-- buy crypto --}}
            <div x-show="currentTab == 'Buy'">
                <div class=" space-y-3 lg:space-y-5">
                    @foreach ($currencies as $currency)
                    <a href="{{route('bureau-da-change.buy-crypto.buy', $currency->uuid)}}" class=" block">
                        <div class=" bg-gray-50 p-4 lg:p-6 lg:shadow-sm rounded-xl space-y-2 lg:space-y-3">
                            <div class=" flex justify-between items-center">
                                <div class=" flex items-center gap-x-2 md:gap-4">
                                    <img src="{{$currency->icon}}" alt="" class=" h-8 rounded-full md:h-10 lg:h-12">
                                    <h3 class="  text-lg font-medium lg:text-xl">
                                        {{$currency->name}}
                                    </h3>
                                </div>


                                <span class="{{$currency->status}}">
                                    {{$currency->status}}
                                </span>
                            </div>

                            <p class=" text-2xl font-semibold">
                                $ {{formatNumber($currency->price_in_usd)}}
                            </p>
                            <p class=" text-sm lg:text-base text-gray-600 lg:font-medium ">
                                NGN {{formatNumber($currency->price_in_usd * $dollarRate)}}
                            </p>

                            <p class=" border-l-2 border-primary px-2 ">
                                SwiftPay Balance
                            </p>
                        </div>
                    </a>

                    @endforeach
                </div>
            </div>


            <div x-show="currentTab == 'History'">
                <div class=" space-y-6 lg:space-y-8">
                    @forelse ($history as $order)
                    <div class=" bg-white border rounded-xl p-4 lg:p-6 lg:shadow-sm">

                        <div class=" flex items-center justify-between">
                            <div>
                                <p class=" text-xl lg:text-2xl text-green-600 font-semibold">Buy {{$order->crypto_symbol}}</p>
                                <p class=" text-sm text-gray-500 lg:mt-1 lg:text-base">{{$order->created_at->diffForHumans()}}</p>
                            </div>

                            <div class=" flex items-center gap-2 lg:gap-4">
                                <p @class([' font-medium text-sm lg:text-base', 'text-gray-500'=> $order->status == 'pending','text-red-500' => $order->status == 'failed','text-green-500' => $order->status == 'completed', ]) >
                                    {{$order->status}}
                                </p>

                                @if ($order->status === 'failed')
                                <div class=" relative" x-data="{showRejectMessage: false}">
                                    <button @click="showRejectMessage = true">
                                        <span class=" fa-regular fa-message"></span>
                                    </button>

                                    <div x-show="showRejectMessage" x-transition x-cloak class=" fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4 overflow-y-auto ">

                                        <div class=" bg-gray-50 p-4 lg:p-6 rounded-lg w-full  max-w-lg space-y-5 overflow-y-auto">
                                            <div class=" flex justify-between items-center mb-6">
                                                <h2 class=" text-lg font-medium lg:text-xl">Rejection Message</h2>

                                                <button @click="showRejectMessage = false" class=" text-2xl">
                                                    <span class="fa fa-close"></span>
                                                </button>
                                            </div>

                                            <p class=" text-gray-600 leading-7 text-sm md:text-base lg:text-lg mt-4 lg:mt-6">
                                                {{$order->status_message}}
                                            </p>


                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                        </div>

                        <hr class=" my-3 lg:my-4">

                        <div class=" space-y-2 lg:space-y-3">

                            <div class=" flex justify-between items-center">
                                <label for=""> Crypto Name </label>
                                <p class=" ">
                                    {{$order->crypto_name }}
                                </p>
                            </div>

                            <div class=" flex justify-between items-center">
                                <label for="">Amount in NGN</label>
                                <p class=" text-lg font-semibold">
                                    {{formatNumber($order->amount_in_naira)}}
                                </p>
                            </div>
                            <div class=" flex justify-between items-center">
                                <label for="">Rate</label>
                                <p class=" ">
                                    <x-naira-symbol /> {{formatNumber($order->amount_in_naira / $order->amount_in_crypto)}}
                                </p>
                            </div>
                            <div class=" flex justify-between items-center">
                                <label for=""> Crypto amount </label>
                                <p class=" ">
                                    {{$order->amount_in_crypto }} {{$order->crypto_symbol}}
                                </p>
                            </div>
                            <div class=" flex justify-between items-center">
                                <label for=""> Wallet Address</label>
                                <p class=" ">
                                    {{truncateText($order->wallet_address, 15) }}
                                </p>
                            </div>
                        </div>

                    </div>

                    @empty

                    <div class=" bg-white  rounded-xl p-4">
                        <p class=" text-center text-gray-500">
                            No history
                        </p>
                    </div>
                    @endforelse
                </div>

            </div>
        </div>




    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('assetTabs', () => ({
            currentTab: 'Buy',
            tabMenu: [
                "Buy",
                "History",
            ],
            changeTab(tab) {
                this.currentTab = tab
            }
        }))
    })
</script>
@endpush