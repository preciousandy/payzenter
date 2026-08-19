<div class=" bg-white">
    <div class=" px-4 py-6 bg-white min-h-screen max-w-5xl">


        <div x-data="menu" class=" ">
            <div class=" mb-6 grid grid-cols-2 gap-x-2">
                <template x-for="(tab, tabIndex) in tabMenu" :key="tabIndex">
                    <button class=" py-3 px-10 text-xs md:text-base lg:text-lg lg:py-4   rounded-t-xl whitespace-nowrap" :class="currentTab == tab && '  font-semibold  text-primary  text-sm border-b-4 border-primary'" @click="currentTab = tab">
                        <span x-text="tab"></span>
                    </button>

                </template>
            </div>
            <div x-show="currentTab == 'To SwiftPay Accounts'" x-cloak x-transition>

                @livewire('transfer.multiple.to-swiftpay-accounts')
            </div>


            <div x-show="currentTab == 'To Bank Accounts'" x-cloak x-transition>
                @livewire('transfer.multiple.to-bank-accounts')
            </div>
        </div>



    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('menu', () => ({
            currentTab: 'To Bank Accounts',
            tabMenu: [
                'To SwiftPay Accounts',
                'To Bank Accounts',
              
            ],
            changeTab(tab) {
                this.currentTab = tab
            }
        }))
    })
</script>
@endpush