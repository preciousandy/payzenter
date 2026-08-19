<div class=" px-4 py-6 lg:p-6 max-w-5xl">

    <x-loading-spinner wire:target="setUser, transfer, confirmTransfer" />

    <div>
        <x-blank-page-header title="Transfer to a SwiftPay account">
            <a href="{{route('transaction-history', ['source' => 'swiftpay_transfer'])}}" class=" inline-block text-sm md:text-base text-primary lg:text-lg font-medium">
                History
            </a>
        </x-blank-page-header>
    </div>

    <div class=" bg-blue-50 text-primary flex items-center p-3 text-sm md:text-base lg:text-lg md:font-medium rounded-xl gap-2 mt-8 lg:mt-10">
        <p class="w-6 h-6 rounded-full flex items-center justify-center  bg-primary">
            <span class=" fa fa-bolt text-white"></span>
        </p>
        <p>
            Transfer to SwiftPay accounts are FREE
        </p>
    </div>


    <div class="  mt-6" x-data="{showPreviewForm: @entangle('showPreviewForm'), showSuccessfullModal: @entangle('showSuccessfullModal')}">
        <h3 class=" font-semibold md:text-lg lg:text-xl">
            Transaction Details
        </h3>
        <form class="space-y-4  lg:space-y-6 mt-6 " wire:submit.prevent='confirmTransfer'>
            @if ($user)
            <img src="{{$user->profile_photo}}" alt="" class=" w-24 h-24 lg:h-28 lg:w-28 rounded-full mx-auto object-cover">
            @endif
            <div>
                <x-input-label for="username" :value="__('SwiftPay Tag')" />
                @if ($user)
                {{-- <x-text-input id="username" type="text" class="mt-2 block w-full  " wire:model='username' required disabled /> --}}
                <div class="flex mt-2">
                    <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-50 border border-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                        <x-lucide-at-sign class=" h-5 lg:h-7 text-gray-700" />
                    </span>
                    <input type="text" id="website-admin" class="rounded-none rounded-e-lg bg-white border border-gray-300 text-gray-900 focus:ring-1 focus:ring-primary outline-none focus:border-primary block flex-1 min-w-0 w-full text-sm md:text-base px-2.5 py-3 lg:py-4 dark:focus:ring-primary focus:border-l-0 disabled:bg-gray-100 disabled:cursor-not-allowed" wire:model='username' disabled />
                </div>
                @else


                <div class="flex mt-2">
                    <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-50 border border-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                        <x-lucide-at-sign class=" h-5 lg:h-7 text-gray-700" />
                    </span>
                    <input type="text" id="website-admin" class="rounded-none rounded-e-lg bg-white border border-gray-300 text-gray-900 focus:ring-1 focus:ring-primary outline-none focus:border-primary block flex-1 min-w-0 w-full text-sm md:text-base px-2.5 py-3 lg:py-4 dark:focus:ring-primary focus:border-l-0" wire:model='username' placeholder="johndoe" />
                </div>

                @endif

                <x-input-error class="mt-2 " :messages="$errors->get('username')" />
            </div>

            @if ($user)
            <div>
                <x-input-label for="name" :value="__('Account Name')" />
                <p class=" font-medium text-primary bg-blue-100 text-sm p-2 rounded-md md:text-base md:p-3 mt-1 md:mt-2">
                    <span class="fa fa-check-circle mr-1"></span> {{$name}}
                </p>
            </div>

            <div>
                <x-input-label for="username" :value="__('Amount')" />
                <x-text-input id="username" type="number" class="mt-1 block w-full" wire:model='amount' required />
                <x-input-error class="mt-2 " :messages="$errors->get('amount')" />
            </div>

            <div class=" mt-6">
                <x-input-label for="pin" :value="__('Remark')" />
                <x-text-input id="pin" type="text" class="mt-1 block w-full" wire:model='description' />
                <x-input-error class="mt-2 " :messages="$errors->get('description')" />
            </div>


            @endif




            @if ($user)
            <x-primary-button class=" w-full block rounded-lg lg:py-4 lg:text-base lg:w-72 lg:mx-auto" type="submit">
                {{ __('Next') }}
            </x-primary-button>
            @else

            <x-secondary-button type="button" class="py-3 w-full  block  bg-indigo-100 lg:py-4 lg:text-base lg:w-72 lg:mx-auto" wire:click='setUser'>
                <span class=" fa fa-user mr-2"></span> {{ __('Confirm User') }}
            </x-secondary-button>
            @endif

        </form>

        {{-- order Preview --}}
        <div x-show="showPreviewForm" x-cloak x-transition class=" fixed top-0 left-0 z-30 bg-black/50 w-full h-full flex items-end lg:justify-center lg:items-center overflow-hidden">

            <div class=" bg-white w-full  rounded-t-2xl p-4 lg:max-w-2xl lg:rounded-2xl lg:p-6 max-h-[90%] overflow-y-auto ">

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

                <div class=" mt-6 bg-blue-50 p-2 lg:p-3 rounded-xl lg:text-lg ">
                    <h4 class=" font-medium mb-2">Payment Method</h4>
                    <label for="">SwiftPay Balance</label> <br>
                    <p class=" text-2xl font-semibold text-primary lg:text-3xl lg:mt-1">
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


    <div class=" bg-gray-50 rounded-xl py-2 mt-6" x-data="tabs">
        <div class="flex items-center gap-x-4 mb-6">
            <template x-for="(tab, tabIndex) in tabMenu" :key="tabIndex">
                <button class=" px-4 py-2 text-sm md:text-base" :class="currentTab == tab && ' text-primary font-semibold border-b-4 border-primary'" @click="currentTab = tab">
                    <span x-text="tab"></span>
                </button>
            </template>
        </div>

        <div class=" px-3" x-show="currentTab === 'Recent'" x-cloak x-transition>

            @if (count($recentTransfers) > 0)
            <div class=" space-y-3">
                @foreach ($recentTransfers as $userItem)
                <a href="{{route('transfer.swiftpay.user', array_merge(['hashId' => $userItem->hash_id], request()->query()))}}" class=" flex items-center gap-4">
                    <img src="{{asset($userItem->profile_photo)}}" alt="" class=" h-10 w-10 lg:h-12 lg:w-12 rounded-full object-cover">

                    <div class="">
                        <p class=" font-semibold lg:text-lg">{{$userItem->name}}</p>
                        <p class=" text-sm text-gray-600 mt-1 lg:text-base">{{'@'}}{{$userItem->username}}</p>
                    </div>
                </a>
                @endforeach
            </div>
            <a href="{{route('transfer.swiftpay.beneficiaries')}}" class=" block p-3 text-center text-sm  md:text-base mt-4">
                <p class=" text-center text-primary  font-medium">
                    View all
                    <span class=" fa fa-chevron-right ml-2"></span>
                </p>
            </a>

            @else
            <p class=" text-center text-gray-500 py-8 px-4">No recent transfers</p>
            @endif



        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('tabs', () => ({
            currentTab: 'Recent',
            tabMenu: [
                "Recent",
                "Favourites",
            ],
            changeTab(tab) {
                this.currentTab = tab
            }
        }))
    })
</script>
@endpush