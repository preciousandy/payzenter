<div class="p-4 max-w-6xl mx-auto">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <div class="text-xl font-bold text-gray-900">Payment Links</div>
        <div class="text-sm text-gray-500 mt-1">Create a shareable link to collect payments from your customers</div>

        <div class="mt-5 grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="border border-gray-200 rounded-xl p-4 lg:col-span-2">
                <div class="font-semibold text-gray-900">Create Payment Link</div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Amount (NGN)" />
                        <x-text-input wire:model.defer="amount" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label value="Method" />
                        <select wire:model.defer="method" class="w-full mt-1 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="card">Card</option>
                            <option value="transfer">Bank Transfer</option>
                            <option value="ussd">USSD</option>
                        </select>
                        <x-input-error :messages="$errors->get('method')" class="mt-2" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label value="Return URL (after payment)" />
                        <x-text-input wire:model.defer="return_url" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('return_url')" class="mt-2" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label value="Webhook URL (optional)" />
                        <x-text-input wire:model.defer="merchant_webhook_url" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('merchant_webhook_url')" class="mt-2" />
                        <div class="mt-2 text-xs text-gray-500">Defaults to your saved webhook URL from the Webhook Events page.</div>
                    </div>
                </div>

                <div class="mt-4">
                    <x-primary-button type="button" wire:click="createLink" class="rounded-xl px-6 py-3">
                        Create Link
                    </x-primary-button>
                </div>
            </div>

            <div class="border border-gray-200 rounded-xl p-4">
                <div class="font-semibold text-gray-900">Latest Link</div>
                @if($createdLink)
                    <div class="mt-3">
                        <div class="text-xs text-gray-500">Share this link:</div>
                        <div class="mt-2 bg-gray-50 border border-gray-200 rounded-xl p-3 text-xs break-all">
                            {{ $createdLink }}
                        </div>
                    </div>
                @else
                    <div class="mt-3 text-sm text-gray-500">Create a payment link to see it here.</div>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mt-4 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs uppercase bg-gray-50 text-gray-700">
                    <tr>
                        <th class="px-4 py-3">Reference</th>
                        <th class="px-4 py-3">Amount</th>
                        <th class="px-4 py-3">Method</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Link</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($links as $l)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $l->reference }}</td>
                            <td class="px-4 py-3">₦{{ formatNumber(($l->amount ?? 0) / 100) }}</td>
                            <td class="px-4 py-3">{{ strtoupper($l->method) }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1
                                    @if(in_array($l->status, ['success', 'successful'])) bg-emerald-100 text-emerald-700
                                    @elseif(in_array($l->status, ['failed', 'closed', 'canceled', 'cancelled', 'fail'])) bg-rose-100 text-rose-700
                                    @else bg-amber-100 text-amber-700 @endif
                                ">
                                    @if(in_array($l->status, ['success', 'successful']))
                                        SUCCESS
                                    @elseif(in_array($l->status, ['failed', 'closed', 'canceled', 'cancelled', 'fail']))
                                        FAILED
                                    @else
                                        PROCESSING
                                    @endif
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a class="text-[#1400FB] font-semibold hover:underline" href="{{ route('gateway.pay', ['reference' => $l->reference]) }}" target="_blank">
                                    Open
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-gray-500">No payment links yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4">
            {{ $links->links() }}
        </div>
    </div>

    <x-slot:headerTitle>
        Payment Links
    </x-slot:headerTitle>
</div>
