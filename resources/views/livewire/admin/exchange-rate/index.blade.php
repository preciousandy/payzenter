<div class=" ">

    <x-loading-spinner wire:target='refreshRates' />
    <div class=" mb-6 flex justify-between items-center ">
        <h2 class=" text-xl font-semibold ">
            All Exchange Rates
        </h2>
        <x-primary-button wire:click='refreshRates' class=" rounded-xl w-40">
            <span class=" fa fa-rotate-left mr-1"></span> Refresh rates
        </x-primary-button>

    </div>

    <div x-data="assetTabs" class=" mt-8">
        <div class=" flex items-end mb-4">
            <template x-for="(tab, tabIndex) in tabMenu" :key="tabIndex">
                <button class=" py-3 px-6 bg-indigo-100 text-primary rounded-t-xl whitespace-nowrap" :class="currentTab == tab && '  font-semibold  bg-primary text-white text-lg'" @click="currentTab = tab">
                    <span x-text="tab"></span>
                </button>
            </template>
        </div>

        {{-- fx Buying Orders --}}
        <div x-show="currentTab == tabMenu[0]" x-cloak x-transition>
            <livewire:buy-fx-tab />
        </div>


        {{-- FX Sales Tab --}}
        <div x-show="currentTab == tabMenu[1]" x-cloak x-transition>
            <livewire:sell-fx-tab />

        </div>
        {{-- Banks Tab --}}
        <div x-show="currentTab == tabMenu[2]" x-cloak x-transition>
            <livewire:domiciliary-banks-tab />

        </div>



    </div>



</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('assetTabs', () => ({
            currentTab: 'Buy Fx',
            tabMenu: [
                "Buy Fx",
                "Sell Fx",
                "Banks",
            ],
            changeTab(tab) {
                this.currentTab = tab
            }
        }))
    })
</script>
@endpush