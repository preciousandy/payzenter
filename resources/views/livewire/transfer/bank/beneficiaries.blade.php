<div class=" px-4 py-6 bg-white min-h-screen">
    <div>
        <x-blank-page-header title="Beneficiaries" />
    </div>

    <div class="flex items-center space-x-2 bg-white p-2 border border-gray-300 rounded-lg mt-6">
        <span class="fa fa-search text-base text-gray-500"></span>
        <input wire:model.live='query' type="text" class=" px-2 w-full text-sm bg-inherit border-none focus:outline-0 focus:ring focus:ring-transparent">
        <button type="button" wire:click="$set('query', '')" class=" h-4 w-4 inline-flex justify-center items-center bg-gray-100 rounded-full"><span class="fa fa-close text-sm"></span></button>
    </div>


    <div class="py-2 mt-4" x-data="tabs">
        <div class=" grid grid-cols-2 gap-x-4 mb-3">
            <template x-for="(tab, tabIndex) in tabMenu" :key="tabIndex">
                <button class=" px-4 py-2 text-sm " :class="currentTab == tab && ' text-green-600 font-semibold border-b-4 border-green-600'" @click="currentTab = tab">
                    <span x-text="tab"></span>
                </button>
            </template>
        </div>

        <div class=" px-3" x-show="currentTab === 'Recent'" x-cloak x-transition>
            @if (count($recentTransfers) > 0)
            <div class=" divide-y">
                @foreach ($recentTransfers as $transfer)
                <div class=" flex justify-between items-center gap-x-4">
                    <a href="{{route('transfer.bank', ['transfer' => $transfer->hash_id])}}" class=" flex items-center gap-4 py-3  flex-1">
                        <img src="{{asset(randomImage($transfer->account_name))}}" alt="" class=" h-10 w-10 rounded-full object-cover">

                        <div class="">
                            <p class=" font-semibold">{{$transfer->account_name}}</p>
                            <p class=" text-sm text-gray-600 mt-1">{{$transfer->account_number}} | {{$transfer->bank_name}}</p>
                        </div>
                    </a>
                    <x-dropdown align="right" width="60">
                        <x-slot name="trigger">
                            <button>
                                <span class="fa fa-ellipsis"></span>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div>
                                <button wire:click='toggleBeneficiary({{$transfer->id}})' class=" p-2 text-sm text-gray-700 text-left whitespace-nowrap">
                                    @if ($transfer->is_beneficiary)
                                    <span class=" fa fa-star mr-1 text-yellow-500"></span> Remove from Favourites
                                    @else
                                    <span class=" fa fa-star mr-1"></span> Add to Favourites

                                    @endif
                                </button>
                                {{-- <button class=" p-2 text-sm text-gray-700">
                                    <span class=" fa fa-trash mr-1"></span> Delete
                                </button> --}}

                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>
                @endforeach
            </div>
            @else
            <p class=" text-center text-gray-500 py-8 px-4">No recent transfers</p>
            @endif



        </div>

        <div class=" px-3" x-show="currentTab === 'Favourites'" x-cloak x-transition>
            @if (count($favorites) > 0)
            <div class=" divide-y">
                @foreach ($favorites as $beneficiary)
                <div class=" flex justify-between items-center gap-x-4">
                    <a href="{{route('transfer.bank', ['beneficiary' => $beneficiary->hash_id])}}" class=" flex items-center gap-4 py-3  flex-1">
                        <img src="{{asset(randomImage($beneficiary->acct_name))}}" alt="" class="h-10 w-10 rounded-full object-cover">

                        <div class="">
                            <p class=" font-semibold">{{$beneficiary->acct_name}}</p>
                            <p class=" text-sm text-gray-600 mt-1">{{$beneficiary->acct_number}} | {{$beneficiary->bank_name}}</p>
                        </div>
                    </a>

                </div>
                @endforeach
            </div>
            @else
            <p class=" text-center text-gray-500 py-8 px-4">
                No favourite beneficiaries
            </p>
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