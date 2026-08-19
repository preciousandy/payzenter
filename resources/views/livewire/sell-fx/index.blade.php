<div class=" bg-white">
    <div class=" px-4 py-6 bg-white min-h-screen max-w-4xl lg:p-6">

        <h2 class=" text-xl lg:text-2xl font-semibold text-center">
            Bureau De Change
        </h2>

        <div x-data="assetTabs" class=" mt-6 lg:mt-8">
            <div class=" grid grid-cols-2 gap-3 lg:gap-4 mb-4 lg:mb-6 max-w-lg w-full ">
                <template x-for="(tab, tabIndex) in tabMenu" :key="tabIndex">
                    <button class=" p-2 lg:p-4 rounded-lg" :class="currentTab == tab ? ' text-white font-semibold  bg-primary' : ' bg-gray-100'" @click="currentTab = tab">
                        <span x-text="tab"></span>
                    </button>
                </template>
            </div>

            {{-- sell crypto --}}
            <div x-show="currentTab ==  tabMenu[0]" x-cloak x-transition>
                <div class=" space-y-3 lg:space-y-5">
                    @foreach ($currencies as $currency)
                    <div wire:click="buyCurrency({{$currency}})" class=" block cursor-pointer lg:shadow-sm">
                        <div class=" bg-gray-50 p-4 rounded-xl space-y-2 lg:space-y-3 lg:p-6">
                            <div class=" flex justify-between items-center">
                                <div class=" flex items-center gap-x-2 lg:gap-4">
                                    <img src="{{$currency->logo_url}}" alt="" class=" h-10 w-10 lg:h-12 lg:w-12 rounded-full object-cover">
                                    <h3 class=" text-lg font-medium lg:text-xl">
                                        {{$currency->code}}
                                    </h3>
                                </div>

                                <p class=" font-medium text-sm {{$currency->sell_status}} lg:text-base">
                                    {{$currency->sell_status}}
                                </p>
                            </div>

                            <p class="text-2xl lg:text-3xl font-semibold">
                                {{formatNumber($currency->sell_price)}} NGN
                            </p>

                            <div class="text-sm md:text-base lg:space-y-2 space-y-1">
                                <p>
                                    Qunatity: {{$currency->currency_symbol}} {{$currency->volume ?? 0}}
                                </p>
                                <p>
                                    Limit: {{$currency->currency_symbol}} {{$currency->limit ?? "0"}}
                                </p>
                                <p class=" border-l-2 border-primary px-2 ">
                                    SwiftPay Balance
                                </p>
                            </div>

                        </div>
                    </div>

                    @endforeach
                </div>
            </div>

            <div x-show="currentTab == tabMenu[1]" x-cloak x-transition>
                <div class=" space-y-4 lg:space-y-6">
                    @forelse ($history as $order)
                    <div class=" bg-white border rounded-xl p-4 lg:p-6">
                        <div class=" flex items-center justify-between">
                            <div>
                                <p class=" text-xl lg:text-2xl text-green-600 font-semibold">Sell {{$order->currency_code}}</p>
                                <p class=" text-sm text-gray-500 lg:mt-1 lg:text-base">{{$order->created_at->diffForHumans()}}</p>
                            </div>

                            <div class=" flex items-center gap-2 md:gap-4">
                                <p class=" font-medium text-sm lg:text-base {{$order->status}}">
                                    {{$order->status}}
                                </p>

                                @if ($order->status === 'failed')
                                <div class=" relative" x-data="{showRejectMessage: false}">
                                    <button @click="showRejectMessage = true">
                                        <span class=" fa-regular fa-message"></span>
                                    </button>

                                    <div x-show="showRejectMessage" x-transition x-cloak class=" fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4 overflow-y-auto ">

                                        <div class=" bg-gray-50 p-4 lg:p-6 rounded-lg w-full  max-w-lg space-y-5 overflow-y-auto">
                                            <div class=" flex justify-between items-center ">
                                                <h2 class=" text-lg font-medium lg:text-xl">Rejection Message</h2>
                                                <button @click="showRejectMessage = false" class=" text-2xl lg:text-3xl text-gray-700">
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
                                <label for="">Amount</label>
                                <p class=" text-lg font-semibold">
                                    {{formatNumber($order->amount)}} {{$order->currency_code}}
                                </p>
                            </div>
                            <div class=" flex justify-between items-center">
                                <label for="">Price in NGN</label>
                                <p class=" ">
                                    {{formatNumber($order->naira_amount)}} NGN
                                </p>
                            </div>
                            <div class=" flex justify-between items-center">
                                <label for="">Order No.</label>
                                <div class=" flex items-center gap-1  lg:gap-2">
                                    <p class=" ">
                                        {{$order->order_number}}
                                    </p>

                                    <button onclick="copyToClipboard('{{$order->order_number}}')">
                                        <span class=" fa fa-copy"></span>
                                    </button>
                                </div>
                            </div>

                        </div>

                    </div>

                    @empty
                    <div class=" text-center py-6 lg:text-lg">
                        <p>
                            No history available
                        </p>
                    </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
        Alpine.data('assetTabs', () => ({
            currentTab: 'Sell',
            tabMenu: [
                "Sell",
                "History",
            ],
            changeTab(tab) {
                this.currentTab = tab
            }
        }))
    })
    </script>
    @endpush