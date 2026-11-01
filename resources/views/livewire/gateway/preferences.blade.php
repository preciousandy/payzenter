<div class="p-4 lg:p-6 max-w-5xl mx-auto">
    <x-loading-spinner wire:target="savePreferences" />

    <div class="mb-8">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Gateway Preferences</h1>
        <p class="text-gray-500 mt-2">Choose your default provider for the Gateway Payments Initiate API when you don’t pass a provider in the request.</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="font-semibold text-gray-900">Default Provider</div>
            <div class="text-sm text-gray-500 mt-1">This preference applies to POST /api/gateway/payments/initiate.</div>
        </div>

        <div class="p-6 space-y-6">
            <div>
                <x-input-label value="Preferred Provider" />
                <select wire:model.defer="preferred_provider" class="w-full mt-1 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Automatic (System Default)</option>
                    @if($enableFlutterwave)
                        <option value="flutterwave">Flutterwave</option>
                    @endif
                    @if($enablePalmPay)
                        <option value="palmpay">PalmPay</option>
                    @endif
                </select>
                <x-input-error :messages="$errors->get('preferred_provider')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($enableFlutterwave)
                <div class="p-4 rounded-2xl border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="font-semibold text-gray-900">Flutterwave</div>
                        <span class="text-xs font-semibold bg-green-100 text-green-700 px-3 py-1 rounded-full">Enabled</span>
                    </div>
                    <div class="text-sm text-gray-500 mt-2">Card, USSD, and bank transfer via Flutterwave.</div>
                </div>
                @endif

                @if($enablePalmPay)
                <div class="p-4 rounded-2xl border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="font-semibold text-gray-900">PalmPay</div>
                        <span class="text-xs font-semibold bg-green-100 text-green-700 px-3 py-1 rounded-full">Enabled</span>
                    </div>
                    <div class="text-sm text-gray-500 mt-2">Checkout via PalmPay.</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="font-semibold text-gray-900">Gateway Fee Payer</div>
            <div class="text-sm text-gray-500 mt-1">Choose who pays the gateway processing fees.</div>
        </div>

        <div class="p-6">
            <div>
                <x-input-label value="Who pays the fees?" />
                <select wire:model.defer="gateway_charge_mode" class="w-full mt-1 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="merchant">Merchant (you pay the fees)</option>
                    <option value="customer">Customer (customer pays the fees)</option>
                </select>
                <x-input-error :messages="$errors->get('gateway_charge_mode')" class="mt-2" />
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end mt-6">
        <x-primary-button wire:click="savePreferences" class="rounded-xl px-6 py-3 font-semibold">
            Save Preferences
        </x-primary-button>
    </div>

    <x-slot:headerTitle>
        Gateway Preferences
    </x-slot:headerTitle>
</div>
