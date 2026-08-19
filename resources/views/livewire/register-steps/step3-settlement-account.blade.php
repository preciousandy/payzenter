<div class="space-y-4">
    <h4 class="text-xl font-semibold mb-4 border-b pb-2">Settlement Account Details</h4>

    <div>
        <x-input-label for="settlement_account_name" :value="__('Account Name')" />
        <x-text-input
            id="settlement_account_name"
            class="block w-full mt-1"
            type="text"
            wire:model="settlement_account_name"
            required
            placeholder="John Doe Enterprises" />
        <x-input-error :messages="$errors->get('settlement_account_name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="bank" :value="__('Bank Name')" />
        <x-text-input
            id="bank"
            class="block w-full mt-1"
            type="text"
            wire:model="bank"
            required
            placeholder="First Bank of Nigeria" />
        <x-input-error :messages="$errors->get('bank')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="settlement_account_number" :value="__('Account Number')" />
        <x-text-input
            id="settlement_account_number"
            class="block w-full mt-1"
            type="text"
            wire:model="settlement_account_number"
            required
            maxlength="10"
            placeholder="0123456789" />
        <x-input-error :messages="$errors->get('settlement_account_number')" class="mt-2" />
    </div>

    <p class="text-sm text-center text-gray-600 dark:text-gray-400 lg:text-base pt-4">
        We will use these details to process your settlements.
    </p>
</div>