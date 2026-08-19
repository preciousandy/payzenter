<div class="space-y-4">
    <h4 class="text-xl font-semibold mb-4 border-b pb-2">Business Details</h4>

    <div>
        <x-input-label for="company_name" :value="__('Company Name')" />
        <x-text-input id="company_name" class="block w-full mt-1" type="text" wire:model="company_name" required />
        <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="website_link" :value="__('Website Link (Optional)')" />
        <x-text-input id="website_link" class="block w-full mt-1" type="url" wire:model="website_link" />
        <x-input-error :messages="$errors->get('website_link')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="business_type" :value="__('Business Type')" />
        <select id="business_type" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm" wire:model="business_type" required>
            <option value="">Select Business Type</option>
            <option value="Sole Proprietor">Sole Proprietor</option>
            <option value="Limited Liability">Limited Liability</option>
            <option value="Non-Profit">Non-Profit</option>
        </select>
        <x-input-error :messages="$errors->get('business_type')" class="mt-2" />
    </div>

    <h4 class="text-xl font-semibold mb-4 border-b pt-4 pb-2">Compliance Documents</h4>

    <div>
        <x-input-label for="nin_image" :value="__('NIN Document Image')" />
        <input type="file" id="nin_image" wire:model="nin_image" class="block w-full mt-1 p-2 border border-gray-300 rounded-md">
        @error('nin_image') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        @if ($nin_image)
            <p class="text-green-600 text-sm mt-1">File Selected: {{ $nin_image->getClientOriginalName() }}</p>
        @endif
    </div>

    <div>
        <x-input-label for="cac_image" :value="__('CAC Document Image')" />
        <input type="file" id="cac_image" wire:model="cac_image" class="block w-full mt-1 p-2 border border-gray-300 rounded-md">
        @error('cac_image') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
    </div>

    <div>
        <x-input-label for="utility_bill_image" :value="__('Utility Bill Image (Proof of Address)')" />
        <input type="file" id="utility_bill_image" wire:model="utility_bill_image" class="block w-full mt-1 p-2 border border-gray-300 rounded-md">
        @error('utility_bill_image') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
    </div>
</div>