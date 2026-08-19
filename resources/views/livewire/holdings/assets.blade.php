<div class="px-4 py-6 max-w-5xl ">
    <div>

        @if ($isLoading)
        <div class=" fixed z-50 left-0 top-0 w-full h-full bg-white/60 flex  justify-center items-center">
            <div class="lds-dual-ring">
                <div></div>
                <div></div>
            </div>
        </div>
        @endif

        {{-- <div class="flex items-center space-x-2 bg-white p-2 border-2 border-gray-300 rounded-xl">
            <span class="fa fa-search text-xl text-gray-500"></span>
            <input wire:model='query' type="text" class="py-2 px-4 w-full text-sm bg-inherit border-none focus:outline-0 focus:ring focus:ring-transparent">
            <button type="button" wire:click="$set('query', '')" class="p-2 h-8 w-8 inline-flex justify-center items-center bg-gray-100 rounded-full"><span class="fa fa-close"></span></button>
        </div> --}}

        <div x-data="assetTabs" class=" ">
            <div class="  grid grid-cols-2 gap-x-4     mb-4 max-w-sm lg:text-lg lg:mb-6">
                <template x-for="(tab, tabIndex) in tabMenu" :key="tabIndex">
                    <button class=" p-2 " :class="currentTab == tab && ' text-primary font-semibold border-b-4 border-primary'" @click="currentTab = tab">
                        <span x-text="tab"></span>
                    </button>
                </template>
            </div>

            <div x-show="currentTab == 'Fiat'">

                @livewire('fiats-list')

            </div>

            <div x-show="currentTab == 'Metals'">

                @livewire('metals-list')

            </div>


        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('assetTabs', () => ({
            currentTab: 'Fiat',
            tabMenu: [
                "Fiat",
                "Metals",
            ],
            changeTab(tab) {
                this.currentTab = tab
            }
        }))
    })
</script>
@endpush