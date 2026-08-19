<div class="space-y-4">
    <h4 class="text-xl font-semibold mb-4 border-b pb-2">Address Details</h4>

    <div>
        <x-input-label for="city" :value="__('City')" />
        <x-text-input id="city" class="block w-full mt-1" type="text" wire:model="city" required placeholder="e.g., Lagos" />
        <x-input-error :messages="$errors->get('city')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="state" :value="__('State')" />
        <x-text-input id="state" class="block w-full mt-1" type="text" wire:model="state" required placeholder="e.g., Lagos State" />
        <x-input-error :messages="$errors->get('state')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="lga" :value="__('Local Government Area (LGA)')" />
        <x-text-input id="lga" class="block w-full mt-1" type="text" wire:model="lga" required placeholder="e.g., Ikeja" />
        <x-input-error :messages="$errors->get('lga')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="postal_code" :value="__('Postal Code')" />
        <x-text-input id="postal_code" class="block w-full mt-1" type="text" wire:model="postal_code" required placeholder="e.g., 100001" />
        <x-input-error :messages="$errors->get('postal_code')" class="mt-2" />
    </div>
</div>