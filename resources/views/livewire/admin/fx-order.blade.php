<div>

    <div>
        <h2 class=" text-xl font-semibold">
            Bureau Da Change
        </h2>
    </div>

    <div x-data="{
        currentTab: @entangle('currentTab'),
        
    }" class=" mt-8">
        <div class=" flex items-end mb-4">
            @foreach ($tabMenu as $tab)
            <button class=" py-3 px-6 bg-indigo-100 text-primary rounded-t-xl whitespace-nowrap" :class="currentTab == '{{ $tab }}' && '  font-semibold  bg-primary text-white text-lg'" wire:click="$set('currentTab', '{{ $tab }}')">
                <span x-text="'{{ $tab }}'"></span>
            </button>
            @endforeach
        </div>

        {{-- fx Buying Orders --}}
        @if ($currentTab == 'Buy Fx Orders')
        <livewire:buy-fx-order-table :key="'buy-fx-order-table'" />
        @endif


        {{-- FX Sales Tab --}}
        @if ($currentTab == 'Sell Fx Orders')
        <livewire:sell-fx-order-table :key="'sell-fx-order-table'" />
        @endif



        @if ($currentTab == 'Settings')
        <livewire:admin.fx-order-settings :key="'fx-order-settings'" />
        @endif


    </div>



</div>