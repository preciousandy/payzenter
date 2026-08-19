<div class="p-4 max-w-6xl mx-auto">
    <x-loading-spinner wire:target="repush,saveWebhookUrl" />

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <div class="text-xl font-bold text-gray-900">Webhook Events</div>
                <div class="text-sm text-gray-500">Save your default endpoint, review delivery history, and repush failed events.</div>
            </div>

            <div class="w-full lg:max-w-2xl">
                <div class="grid grid-cols-1 md:grid-cols-[minmax(0,1fr)_auto] gap-3">
                    <div>
                        <x-input-label value="Default Webhook URL" />
                        <x-text-input
                            wire:model.defer="merchant_webhook_url"
                            type="url"
                            placeholder="https://yourapp.com/webhooks/payments"
                            class="w-full mt-1"
                        />
                        <x-input-error :messages="$errors->get('merchant_webhook_url')" class="mt-2" />
                        <div class="mt-2 text-xs text-gray-500">
                            Used for payment notifications unless a request-specific webhook URL is supplied, and for virtual account funding events.
                        </div>
                    </div>

                    <div class="md:self-end">
                        <x-primary-button type="button" wire:click="saveWebhookUrl" class="rounded-xl px-6 py-3 w-full md:w-auto">
                            Save URL
                        </x-primary-button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mt-4 p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label value="Status" />
                <select wire:model.live="status" class="w-full mt-1 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All statuses</option>
                    <option value="delivered">Delivered</option>
                    <option value="failed">Failed</option>
                </select>
            </div>

            <div>
                <x-input-label value="Event Type" />
                <select wire:model.live="event" class="w-full mt-1 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All events</option>
                    <option value="payment.success">Payment Success</option>
                    <option value="virtual_account.funding.success">Virtual Account Funding</option>
                </select>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mt-4 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs uppercase bg-gray-50 text-gray-700">
                    <tr>
                        <th class="px-4 py-3">Event</th>
                        <th class="px-4 py-3">Reference</th>
                        <th class="px-4 py-3">URL</th>
                        <th class="px-4 py-3">Attempts</th>
                        <th class="px-4 py-3">Last Status</th>
                        <th class="px-4 py-3">Delivered</th>
                        <th class="px-4 py-3">Last Attempt</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($deliveries as $d)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $d->event ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ str_replace('_', ' ', $d->payload['type'] ?? 'event') }}</div>
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900">
                                {{ $d->reference ?? $d->transaction?->reference ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="max-w-md truncate" title="{{ $d->url }}">{{ $d->url }}</div>
                                @if($d->error_message)
                                    <div class="text-xs text-red-600 mt-1">{{ $d->error_message }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $d->attempts }}</td>
                            <td class="px-4 py-3">
                                @if($d->response_status)
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ ($d->response_status >= 200 && $d->response_status < 300) ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $d->response_status }}
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($d->delivered_at)
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">YES</span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">NO</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                {{ $d->last_attempt_at ? $d->last_attempt_at->format('d M Y, H:i') : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <x-primary-button type="button" wire:click="repush({{ $d->id }})" class="rounded-xl px-4 py-2">
                                    Repush
                                </x-primary-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-gray-500">No webhook deliveries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4">
            {{ $deliveries->links() }}
        </div>
    </div>

    <x-slot:headerTitle>
        Webhook Events
    </x-slot:headerTitle>
</div>
