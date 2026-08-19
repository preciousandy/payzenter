<div>
    <x-loading-spinner wire:target='create' />
    <div class=" flex justify-between items-center border border-gray-200 rounded-lg p-3 text-sm shadow-sm">
        <p>
            <span class="fa fa-dot-circle text-amber-600 mr-1"></span> Create Crypto Wallet
        </p>

        <x-primary-button class=" px-3 py-2 text-xs w-fit shadow" wire:click='create'>
            Create Now
        </x-primary-button>
    </div>
</div>