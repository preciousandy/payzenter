<div class="p-4 max-w-6xl mx-auto">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
                <div class="text-xl font-bold text-gray-900">Transactions</div>
                <div class="text-sm text-gray-500">All payment transactions for your merchant account</div>
            </div>

            <div class="flex gap-2">
                <x-secondary-button wire:click="clearFilters" class="rounded-xl">
                    Clear Filters
                </x-secondary-button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-3 mt-4">
            <div>
                <x-input-label value="Status" />
                <select wire:model.live="status" class="w-full mt-1 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All</option>
                    <option value="unpaid">Unpaid</option>
                    <option value="paying">Paying</option>
                    <option value="success">Success</option>
                    <option value="failed">Failed</option>
                    <option value="closed">Closed</option>
                </select>
            </div>
            <div>
                <x-input-label value="Method" />
                <select wire:model.live="method" class="w-full mt-1 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All</option>
                    <option value="card">Card</option>
                    <option value="transfer">Transfer</option>
                    <option value="ussd">USSD</option>
                </select>
            </div>
            <div>
                <x-input-label value="Provider" />
                <select wire:model.live="provider" class="w-full mt-1 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All</option>
                    @if($enablePalmPay)
                        <option value="palmpay">PalmPay</option>
                    @endif
                    @if($enableUdara360)
                        <option value="udara360">Udara360</option>
                    @endif
                    @if($enableFlutterwave)
                        <option value="flutterwave">Flutterwave</option>
                    @endif
                </select>
            </div>
            <div>
                <x-input-label value="From" />
                <x-text-input wire:model.live="from_date" type="date" class="w-full mt-1" />
            </div>
            <div>
                <x-input-label value="To" />
                <x-text-input wire:model.live="to_date" type="date" class="w-full mt-1" />
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
                        <th class="px-4 py-3">Provider</th>
                        <th class="px-4 py-3">Customer</th>
                        <th class="px-4 py-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $tx->reference }}</td>
                            <td class="px-4 py-3">₦{{ formatNumber($tx->amount / 100) }}</td>
                            <td class="px-4 py-3">{{ strtoupper($tx->method) }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    @if($tx->status === 'success') bg-green-100 text-green-700
                                    @elseif($tx->status === 'failed') bg-red-100 text-red-700
                                    @elseif(in_array($tx->status, ['unpaid','paying'])) bg-yellow-100 text-yellow-700
                                    @else bg-gray-100 text-gray-700 @endif
                                ">
                                    {{ strtoupper($tx->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if(strtolower((string) $tx->provider) === 'flutterwave' && !$enableFlutterwave)
                                    GATEWAY
                                @elseif(strtolower((string) $tx->provider) === 'udara360' && !$enableUdara360)
                                    VIRTUAL BANK
                                @else
                                    {{ strtoupper((string) ($tx->provider ?: 'GATEWAY')) }}
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-gray-900">{{ $tx->customer_email }}</div>
                                @if($tx->customer_phone)
                                    <div class="text-xs text-gray-500">{{ $tx->customer_phone }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $tx->created_at->format('d M Y, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-500">No transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4">
            {{ $transactions->links() }}
        </div>
    </div>

    <x-slot:headerTitle>
        Transactions
    </x-slot:headerTitle>
</div>

