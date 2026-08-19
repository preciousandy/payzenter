<div class=" p-4 lg:p-6 lg:max-w-5xl ">
    <div class=" flex items-center justify-between">
        <h2 class=" text-lg lg:text-xl font-semibold">
            Transaction History
        </h2>


    </div>

    <div class=" grid grid-cols-2 gap-x-4 mt-4 lg:mt-6 max-w-md">

        {{-- filter modal --}}
        <div x-data="{showFilterBySourceModal: @entangle('showFilterBySourceModal')}">
            <x-secondary-button @click="showFilterBySourceModal = true" class=" border-slate-300 text-slate-800 rounded w-full bg-white">
                <span class="fa fa-filter mr-2"></span> Filter
            </x-secondary-button>

            <div x-show='showFilterBySourceModal' x-cloak x-transition class=" fixed bg-black/60 top-0 left-0 w-full h-full z-[999999] flex items-end lg:items-center lg:p-4 lg:justify-center">

                <div class=" bg-white  rounded-t-lg p-4 w-full lg:max-w-xl md:p-6 lg:rounded-lg">
                    <div class=" flex items-center justify-between">
                        <h3 class=" font-semibold md:text-lg text-gray-700">
                            Filter By Category
                        </h3>

                        <button @click="showFilterBySourceModal = false">
                            <span class=" fa fa-close text-2xl lg:text-3xl"></span>
                        </button>
                    </div>
                    <div class=" flex gap-4 flex-wrap mb-6 mt-4 md:mt-6">
                        <button class="  px-3 py-2 rounded-md text-sm lg:text-base  {{$source == '' ? 'active' : 'bg-gray-100' }}" wire:click="filterBySource('')">
                            All Categories
                        </button>
                        @foreach (collect($sources)->sort() as $sourceItem)
                        <button class="  px-3 py-2 rounded-md text-sm  lg:text-base {{$source == $sourceItem ? 'active' : 'bg-gray-100' }}" wire:click="filterBySource('{{$sourceItem}}')">
                            {{ ucfirst(str_replace('_', ' ', $sourceItem))}}
                        </button>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        {{-- sort modal --}}
        <div x-data="{showSortModal: @entangle('showSortModal')}">
            <x-secondary-button @click="showSortModal = true" class=" w-full border-slate-300 text-slate-800 rounded bg-white">
                <span class="fa fa-sort mr-2"></span> Sort
            </x-secondary-button>

            <div x-show='showSortModal' x-cloak x-transition class=" fixed bg-black/60 top-0 left-0 w-full h-full z-[999999] flex items-end lg:items-center lg:p-4 lg:justify-center">

                <div class=" bg-white  rounded-t-lg p-4 w-full lg:max-w-xl md:p-6 lg:rounded-lg">
                    <div class=" flex items-center justify-end mb-6 lg:mb-8">


                        <button @click="showSortModal = false">
                            <span class=" fa fa-close text-2xl lg:text-3xl"></span>
                        </button>
                    </div>

                    <div>
                        <h3 class=" font-semibold text-gray-600 lg:text-lg">
                            Sort By Date
                        </h3>
                        <div class=" grid grid-cols-2 gap-x-4 mt-2">
                            <button class="  px-3 py-2 rounded-md text-sm lg:text-base lg:p-4  {{$order == 'asc' ? 'active font-medium' : 'bg-gray-100' }}" wire:click="sortByDate('asc')">
                                <span class="fa fa-caret-up mr-2"></span> Ascending
                            </button>
                            <button class="  px-3 py-2 rounded-md text-sm lg:text-base lg:p-4  {{$order == 'desc' ? 'active font-medium' : 'bg-gray-100' }}" wire:click="sortByDate('desc')">
                                <span class="fa fa-caret-down mr-2"></span> Descending
                            </button>

                        </div>
                    </div>

                    <div class=" mt-6 mb-6 lg:mt-8">
                        <h3 class=" font-semibold text-gray-600 lg:text-lg">
                            Sort By Amount
                        </h3>
                        <div class=" grid grid-cols-2 gap-x-4 mt-2">
                            <button class="  px-3 py-2 rounded-md text-sm lg:text-base lg:p-4  {{$amount == 'asc' ? 'active font-medium' : 'bg-gray-100' }}" wire:click="sortByAmount('asc')">
                                <span class="fa fa-caret-up mr-2"></span> Ascending
                            </button>
                            <button class="  px-3 py-2 rounded-md text-sm lg:text-base lg:p-4  {{$amount == 'desc' ? 'active font-medium' : 'bg-gray-100' }}" wire:click="sortByAmount('desc')">
                                <span class="fa fa-caret-down mr-2"></span> Descending
                            </button>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- leave here if not tailwind will ignore the classes --}}
    {{-- <div class="successful-border failed-border pending-border verified pending completed active unverified">

    </div> --}}

    <div class=" space-y-4 lg:space-y-6 mt-6 lg:mt-8">
        @forelse ($transactions as $transaction)
        <x-user-transaction-item :transaction="$transaction" />

        @empty

        <div class=" p-4 rounded-md">
            <p class=" font-medium md:text-lg lg:text-xl">
                You have no transactions yet.
            </p>
        </div>

        @endforelse
    </div>

    @if ($transactions->hasMorePages())
    <div class=" p-4 lg:p-8 w-full flex justify-center items-center mt-8 ">
        <x-primary-button wire:click='loadMore' class="lg:w-80 mx-auto">
            Load More Transactions
        </x-primary-button>
    </div>
    @endif
</div>