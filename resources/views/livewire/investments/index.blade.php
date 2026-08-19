<div class="px-4 py-6 bg-white min-h-screen">
    <div class=" max-w-5xl">

        <div x-data="assetTabs" class=" ">
            <div class="  grid grid-cols-2 gap-x-4   lg:text-lg lg:max-w-md   mb-4 lg:mb-6">
                <template x-for="(tab, tabIndex) in tabMenu" :key="tabIndex">
                    <button class=" p-2 " :class="currentTab == tab && ' text-primary font-semibold border-b-4 border-primary'" @click="currentTab = tab">
                        <span x-text="tab"></span>
                    </button>
                </template>
            </div>


            <div x-show="currentTab == 'Stocks'">

                @livewire('stocks-list')

            </div>

            <div x-show="currentTab == 'Cryptocurrency'">

                @livewire('crypto-assets-list')

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('assetTabs', () => ({
            currentTab: 'Stocks',
            tabMenu: [
                "Stocks",
                "Cryptocurrency",
            ],
            changeTab(tab) {
                this.currentTab = tab
            }
        }))
    })
</script>
@endpush