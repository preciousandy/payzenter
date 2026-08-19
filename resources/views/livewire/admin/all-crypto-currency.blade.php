<div>
    <div x-data="assetTabs" class=" mt-8">
        <div class=" grid grid-cols-3  mb-6 w-96">
            <template x-for="(tab, tabIndex) in tabMenu" :key="tabIndex">
                <button class=" p-3 bg-indigo-100 text-primary rounded-t-xl whitespace-nowrap" :class="currentTab == tab && '  font-semibold  bg-primary text-white text-lg'" @click="currentTab = tab">
                    <span x-text="tab"></span>
                </button>

            </template>
        </div>

        {{-- currencies tab --}}
        <div x-show="currentTab == tabMenu[0]">
            <div wire:loading.flex wire:target='refreshRates' class=" fixed z-50 left-0 top-0 w-full h-full bg-white/40  justify-center items-center">
                <svg class="w-20 h-20 mr-3 -ml-1 text-blue-600 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>


            <div class=" mb-6 flex justify-between items-center">
                <h2 class=" text-xl font-bold ">
                    All Cryptocurrencies
                </h2>

                <x-primary-button wire:click='refreshRates' class=" rounded-xl w-fit">
                    <span class=" fa fa-refresh mr-2"></span> Refresh Rates
                </x-primary-button>
            </div>

            <livewire:cryptocurrency-table :currencies="$currencies" :dollarRate="$dollarRate" />
        </div>


        {{-- Crypto Sales Tab --}}
        <div x-show="currentTab == tabMenu[1]">

            @can('manage_crypto_sell_orders')
            <div class=" mb-6 flex justify-between items-center">
                <h2 class=" text-xl font-bold ">
                    All Sale Orders
                </h2>
            </div>

            {{-- currencies table --}}
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-black  bg-gray-50 dark:bg-gray-700 dark:text-gray-400 uppercase">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                User Name
                            </th>

                            <th scope="col" class="px-6 py-3">
                                User email
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Crypto Currency
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Amount in NGN
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Amount in USD
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Amount in Crypto
                            </th>

                            <th scope="col" class="px-6 py-3">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-center">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($saleOrders as $order)

                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-sky-50 dark:hover:bg-gray-600 0">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{$order->user->name}}
                            </td>
                            <td class="px-6 py-4 ">
                                {{$order->user->email}}
                            </td>
                            <td class="px-6 py-4 ">
                                {{$order->crypto_name}}
                            </td>
                            <td class="px-6 py-4">
                                {{formatNumber($order->amount_in_naira)}}
                            </td>
                            <td class="px-6 py-4">
                                {{formatNumber($order->amount_in_usd)}}
                            </td>
                            <td class="px-6 py-4">
                                {{$order->amount_in_crypto}}
                            </td>


                            <td class="px-6 py-4 ">
                                {{$order->status}}
                            </td>

                            <td class="px-6 py-4 ">
                                @if ($order->status == 'pending')
                                <div x-data="{showFailedModal: false}">
                                    <x-secondary-button @click="showFailedModal = true" class="">
                                        View
                                    </x-secondary-button>

                                    <div x-show="showFailedModal" x-transition x-cloak class=" fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4 overflow-y-auto ">
                                        <div class=" bg-gray-50 p-4 lg:p-8 rounded-lg w-full md:w-full lg:w-1/3 xl:w-1/4 space-y-5 overflow-y-auto">
                                            <div class=" flex justify-between items-center mb-6">
                                                <h2 class=" text-2xl font-semibold">Sale Order Details</h2>
                                                <button @click="showFailedModal = false">
                                                    <span class="fa fa-close"></span>
                                                </button>
                                            </div>

                                            <div>
                                                <table class="  w-full ">
                                                    <tr>
                                                        <td class=" font-semibold py-2">User Name</td>
                                                        <td>{{$order->user->name}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class=" font-semibold py-2">User Email</td>
                                                        <td>{{$order->user->email}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class=" font-semibold py-2">Crypto Currency</td>
                                                        <td>{{$order->crypto_name}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class=" font-semibold py-2">Amount in NGN</td>
                                                        <td>{{formatNumber($order->amount_in_naira)}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class=" font-semibold py-2">Amount in USD</td>
                                                        <td>{{formatNumber($order->amount_in_usd)}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class=" font-semibold py-2">Amount in Crypto</td>
                                                        <td>{{$order->amount_in_crypto}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class=" font-semibold py-2">Status</td>
                                                        <td>{{$order->status}}</td>
                                                    </tr>

                                                    <tr>
                                                        <th>
                                                            Transaction Details
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <td class=" font-semibold py-2">Wallet Address</td>
                                                        <td>{{$order->wallet_address}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class=" font-semibold py-2">Transaction ID</td>
                                                        <td>{{$order->transaction_id}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class=" font-semibold py-2">Transaction Date</td>
                                                        <td>{{$order->created_at}}</td>
                                                    </tr>
                                                    @if ($order->screenshot)
                                                    <tr>
                                                        <td class=" font-semibold py-2">Screenshot</td>
                                                        <td>
                                                            <a href="{{$order->screenshot_url}}" target="_blank">
                                                                <x-secondary-button>
                                                                    View sceenshot
                                                                </x-secondary-button>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @endif
                                                </table>



                                            </div>

                                            <div x-data='{showRejectForm: false}'>
                                                <div class=" w-full grid  grid-cols-2 gap-6">
                                                    <x-secondary-button @click="showRejectForm = !showRejectForm" class=" w-full border-red-600 text-red-700">
                                                        Reject
                                                    </x-secondary-button>

                                                    <x-primary-button wire:click='approveSale({{$order->id}})' class=" w-full">
                                                        Approve
                                                    </x-primary-button>

                                                </div>

                                                <form action="" x-show="showRejectForm" x-transition x-cloak class=" space-y-4 mt-3" wire:submit.prevent="rejectSale({{$order->id}})">
                                                    <div>
                                                        <x-input-label for="name" :value="__('Reason:')" />
                                                        <textarea name="" id="" rows="4" class=" w-full rounded-md bg-white border mt-2" wire:model='rejectMessage' required></textarea>
                                                        <x-input-error class="mt-2 " :messages="$errors->get('rejectMessage')" />
                                                    </div>

                                                    <x-primary-button class=" w-full">
                                                        {{ __('Submit') }}
                                                    </x-primary-button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>


                                </div>

                                @endif
                            </td>



                        </tr>
                        @endforeach


                    </tbody>
                </table>


            </div>
            @endcan



        </div>

        {{-- Crypto Buying Orders --}}
        <div x-show="currentTab == tabMenu[2]">

            @can('manage_crypto_buy_orders')
            <div class=" mb-6 flex justify-between items-center">
                <h2 class=" text-xl font-semibold ">
                    All Pending Crypto Orders
                </h2>
            </div>

            {{-- currencies table --}}
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-black  bg-gray-50 dark:bg-gray-700 dark:text-gray-400 uppercase">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                User Name
                            </th>

                            <th scope="col" class="px-6 py-3">
                                User email
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Crypto Currency
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Amount in NGN
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Amount in USD
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Amount in Crypto
                            </th>

                            <th scope="col" class="px-6 py-3">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-center">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($buyOrders as $order)

                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-sky-50 dark:hover:bg-gray-600 0">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{$order->user->name}}
                            </td>
                            <td class="px-6 py-4 ">
                                {{$order->user->email}}
                            </td>
                            <td class="px-6 py-4 ">
                                {{$order->crypto_name}}
                            </td>
                            <td class="px-6 py-4">
                                {{formatNumber($order->amount_in_naira)}}
                            </td>
                            <td class="px-6 py-4">
                                {{formatNumber($order->amount_in_usd)}}
                            </td>
                            <td class="px-6 py-4">
                                {{rtrim(rtrim(number_format($order->amount_in_crypto, 15), '0'), '.')}}
                            </td>

                            <td class="px-6 py-4 ">
                                {{$order->status}}
                            </td>

                            <td class="px-6 py-4 ">
                                @if ($order->status == 'pending')
                                <div x-data="{showFailedModal: false}">
                                    <x-secondary-button @click="showFailedModal = true" class="">
                                        View
                                    </x-secondary-button>

                                    <div x-show="showFailedModal" x-transition x-cloak class=" fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4 overflow-y-auto ">
                                        <div class=" bg-gray-50 p-4 lg:p-8 rounded-lg w-full md:w-full lg:w-1/3 xl:w-1/4 space-y-5 overflow-y-auto">
                                            <div class=" flex justify-between items-center mb-6">
                                                <h2 class=" text-2xl font-semibold">Sale Order Details</h2>
                                                <button @click="showFailedModal = false" class=" text-2xl">
                                                    <span class="fa fa-close"></span>
                                                </button>
                                            </div>

                                            <div>
                                                <table class="  w-full ">
                                                    <tr>
                                                        <td class=" font-semibold py-2">User Name</td>
                                                        <td>{{$order->user->name}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class=" font-semibold py-2">User Email</td>
                                                        <td>{{$order->user->email}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class=" font-semibold py-2">Crypto Currency</td>
                                                        <td>{{$order->crypto_name}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class=" font-semibold py-2">Amount in NGN</td>
                                                        <td>{{formatNumber($order->amount_in_naira)}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class=" font-semibold py-2">Amount in USD</td>
                                                        <td>{{formatNumber($order->amount_in_usd)}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class=" font-semibold py-2">Amount in Crypto</td>
                                                        <td>{{rtrim(rtrim(number_format($order->amount_in_crypto, 15), '0'), '.')}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class=" font-semibold py-2">Status</td>
                                                        <td>{{$order->status}}</td>
                                                    </tr>

                                                </table>



                                            </div>

                                            <div x-data='{showRejectForm: false}'>
                                                <div class=" w-full grid  grid-cols-2 gap-6">
                                                    <x-secondary-button @click="showRejectForm = !showRejectForm" class=" w-full border-red-600 text-red-700">
                                                        Reject
                                                    </x-secondary-button>

                                                    <x-primary-button wire:click='approveBuyOrder({{$order->id}})' class=" w-full">
                                                        Approve
                                                    </x-primary-button>

                                                </div>

                                                <form action="" x-show="showRejectForm" x-transition x-cloak class=" space-y-4 mt-3" wire:submit.prevent="rejectBuyOrder({{$order->id}})">
                                                    <div>
                                                        <x-input-label for="name" :value="__('Reason:')" />
                                                        <textarea name="" id="" rows="4" class=" w-full rounded-md bg-white border mt-2" wire:model='rejectMessage' required></textarea>
                                                        <x-input-error class="mt-2 " :messages="$errors->get('rejectMessage')" />
                                                    </div>

                                                    <x-primary-button class=" w-full">
                                                        {{ __('Submit') }}
                                                    </x-primary-button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>


                                </div>

                                @endif
                            </td>



                        </tr>
                        @endforeach


                    </tbody>
                </table>


            </div>

            @endcan

        </div>
    </div>



</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('assetTabs', () => ({
            currentTab: 'Currencies',
            tabMenu: [
                "Currencies",
                
            ],
            changeTab(tab) {
                this.currentTab = tab
            }
        }))
    })
</script>
@endpush