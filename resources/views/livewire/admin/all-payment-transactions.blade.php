<div class="space-y-6">
    <style>
        .btn-repush-webhook {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            padding: 5px 11px !important;
            border-radius: 7px !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            color: #4338ca !important;
            background-color: #e0e7ff !important;
            border: 1px solid #c7d2fe !important;
            cursor: pointer !important;
            transition: all 0.15s ease-in-out !important;
            text-decoration: none !important;
            white-space: nowrap !important;
            outline: none !important;
        }
        .btn-repush-webhook:hover {
            background-color: #c7d2fe !important;
            color: #312e81 !important;
            border-color: #a5b4fc !important;
        }
        .btn-repush-webhook:disabled {
            opacity: 0.6 !important;
            cursor: not-allowed !important;
        }
        .btn-webhook-inspect {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 28px !important;
            height: 28px !important;
            border-radius: 7px !important;
            font-size: 11px !important;
            color: #475569 !important;
            background-color: #f1f5f9 !important;
            border: 1px solid #cbd5e1 !important;
            cursor: pointer !important;
            transition: all 0.15s ease-in-out !important;
            outline: none !important;
        }
        .btn-webhook-inspect:hover {
            background-color: #e2e8f0 !important;
            color: #1e293b !important;
            border-color: #94a3b8 !important;
        }
    </style>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Payment Transactions</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage and audit both merchant gateway collections and user wallet funding payments.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Live Monitoring
            </span>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-6" aria-label="Tabs">
            <button type="button"
                wire:click="setTab('gateway')"
                @class([
                    'flex items-center gap-2 py-3 px-1 border-b-2 text-sm font-bold transition-all',
                    'border-indigo-600 text-indigo-600' => $activeTab === 'gateway',
                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' => $activeTab !== 'gateway',
                ])>
                <i class="fa fa-layer-group text-sm"></i>
                <span>Gateway Transactions (Merchant & Virtual Accounts)</span>
                <span @class([
                    'text-xs px-2 py-0.5 rounded-full font-bold',
                    'bg-indigo-100 text-indigo-700' => $activeTab === 'gateway',
                    'bg-gray-100 text-gray-600' => $activeTab !== 'gateway',
                ])>{{ number_format($gatewayTotalCount) }}</span>
            </button>

            <button type="button"
                wire:click="setTab('wallet')"
                @class([
                    'flex items-center gap-2 py-3 px-1 border-b-2 text-sm font-bold transition-all',
                    'border-indigo-600 text-indigo-600' => $activeTab === 'wallet',
                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' => $activeTab !== 'wallet',
                ])>
                <i class="fa fa-wallet text-sm"></i>
                <span>User Wallet Payments (Card Funding & Paystack)</span>
                <span @class([
                    'text-xs px-2 py-0.5 rounded-full font-bold',
                    'bg-indigo-100 text-indigo-700' => $activeTab === 'wallet',
                    'bg-gray-100 text-gray-600' => $activeTab !== 'wallet',
                ])>{{ number_format($walletTotalCount) }}</span>
            </button>
        </nav>
    </div>

    @if ($activeTab === 'gateway')
        <!-- GATEWAY TAB -->

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <span>Transactions</span>
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-normal">
                        <i class="fa fa-receipt"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-gray-900 mt-2">{{ number_format($gatewayStats['count']) }}</div>
                <div class="text-xs text-gray-400 mt-1">Total gateway checkouts</div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <span>Successful</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-normal">
                        <i class="fa fa-check-circle"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-emerald-600 mt-2">{{ number_format($gatewayStats['successCount']) }}</div>
                <div class="text-xs text-gray-400 mt-1">Paid & settled transactions</div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <span>Successful Volume</span>
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-normal">
                        <i class="fa fa-coins"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-gray-900 mt-2">₦{{ number_format($gatewayStats['volume'], 2) }}</div>
                <div class="text-xs text-gray-400 mt-1">Total pay-in volume</div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <span>Fees Earned</span>
                    <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-sm font-normal">
                        <i class="fa fa-percent"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-indigo-600 mt-2">₦{{ number_format($gatewayStats['fees'], 2) }}</div>
                <div class="text-xs text-gray-400 mt-1">Platform fee share</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-3">
                <div class="sm:col-span-2 lg:col-span-2">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Search</label>
                    <input type="text" wire:model.live.debounce.350ms="search" placeholder="Reference, customer, merchant..."
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Status</label>
                    <select wire:model.live="status" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All statuses</option>
                        <option value="success">Success</option>
                        <option value="pending">Pending</option>
                        <option value="unpaid">Unpaid</option>
                        <option value="paying">Paying</option>
                        <option value="failed">Failed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Method</label>
                    <select wire:model.live="method" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All methods</option>
                        <option value="card">Card</option>
                        <option value="transfer">Transfer</option>
                        <option value="ussd">USSD</option>
                        <option value="virtual_account">Virtual Account</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Provider</label>
                    <select wire:model.live="provider" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All providers</option>
                        <option value="palmpay">PalmPay</option>
                        <option value="udara360">Udara360</option>
                        <option value="paystack">Paystack</option>
                        <option value="flutterwave">Flutterwave</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">From Date</label>
                    <input type="date" wire:model.live="from_date" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">To Date</label>
                    <div class="flex gap-2">
                        <input type="date" wire:model.live="to_date" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <button type="button" wire:click="clearFilters" title="Clear Filters"
                            class="px-3 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 flex items-center justify-center">
                            <i class="fa fa-times text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gateway Table -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider font-semibold">
                        <tr>
                            <th class="px-5 py-3.5">Reference</th>
                            <th class="px-5 py-3.5">Merchant</th>
                            <th class="px-5 py-3.5">Customer</th>
                            <th class="px-5 py-3.5">Amount</th>
                            <th class="px-5 py-3.5">Fee</th>
                            <th class="px-5 py-3.5">Provider / Method</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5">Date</th>
                            <th class="px-5 py-3.5 text-center">Webhook</th>
                            <th class="px-5 py-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-800">
                        @forelse ($gatewayTransactions as $tx)
                            <tr class="hover:bg-gray-50/80 transition" wire:key="gw-{{ $tx->id }}">
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="font-mono text-xs font-bold text-gray-900">{{ $tx->reference }}</div>
                                    @if($tx->provider_reference && $tx->provider_reference !== $tx->reference)
                                        <div class="font-mono text-[11px] text-gray-400 mt-0.5">{{ $tx->provider_reference }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="text-xs font-bold text-gray-900">{{ $tx->merchant?->name ?? 'Merchant #' . $tx->merchant_id }}</div>
                                    <div class="text-[11px] text-gray-400">{{ $tx->merchant?->email }}</div>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="text-xs font-semibold text-gray-900">{{ $tx->customer_name ?: ($tx->customer_email ?: 'N/A') }}</div>
                                    @if($tx->customer_phone)
                                        <div class="text-[11px] text-gray-400 font-mono">{{ $tx->customer_phone }}</div>
                                    @elseif($tx->customer_email)
                                        <div class="text-[11px] text-gray-400">{{ $tx->customer_email }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="font-extrabold text-sm text-gray-900">₦{{ number_format(($tx->amount ?? 0) / 100, 2) }}</span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-xs text-gray-600 font-medium">
                                    ₦{{ number_format(($tx->fee ?? 0) / 100, 2) }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-xs">
                                    @php $prov = strtolower($tx->provider ?? ''); @endphp
                                    @if($prov === 'palmpay')
                                        <span class="inline-flex px-2 py-0.5 rounded-full font-semibold text-xs bg-emerald-50 text-emerald-700 border border-emerald-200">PalmPay</span>
                                    @elseif($prov === 'udara360')
                                        <span class="inline-flex px-2 py-0.5 rounded-full font-semibold text-xs bg-indigo-50 text-indigo-700 border border-indigo-200">Udara360</span>
                                    @elseif($prov === 'paystack')
                                        <span class="inline-flex px-2 py-0.5 rounded-full font-semibold text-xs bg-sky-50 text-sky-700 border border-sky-200">Paystack</span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 rounded-full font-semibold text-xs bg-slate-100 text-slate-700">{{ ucfirst($tx->provider ?: 'N/A') }}</span>
                                    @endif
                                    <span class="block text-[11px] text-gray-400 uppercase mt-0.5 font-mono">{{ $tx->method }}</span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold
                                        @if($tx->status === 'success') bg-emerald-100 text-emerald-800
                                        @elseif(in_array($tx->status, ['pending', 'paying', 'unpaid'])) bg-amber-100 text-amber-800
                                        @else bg-rose-100 text-rose-800 @endif">
                                        {{ ucfirst($tx->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-xs text-gray-500">
                                    {{ optional($tx->created_at)->format('d M Y, H:i') }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-center">
                                    @php $deliv = $tx->latestWebhookDelivery; @endphp
                                    @if($deliv)
                                        @if($deliv->delivered_at)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"
                                                  title="Delivered at {{ $deliv->delivered_at->format('d M Y, H:i:s') }}">
                                                <i class="fa fa-check-circle text-[10px] text-emerald-600"></i>
                                                <span>{{ $deliv->response_status ?: 200 }} OK</span>
                                            </span>
                                        @elseif($deliv->response_status)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200"
                                                  title="HTTP {{ $deliv->response_status }}: {{ $deliv->error_message }}">
                                                <i class="fa fa-times-circle text-[10px] text-rose-600"></i>
                                                <span>HTTP {{ $deliv->response_status }}</span>
                                            </span>
                                        @elseif($deliv->last_attempt_at)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200"
                                                  title="{{ $deliv->error_message }}">
                                                <i class="fa fa-exclamation-triangle text-[10px] text-amber-600"></i>
                                                <span>Failed</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-gray-100 text-gray-600">
                                                <i class="fa fa-clock text-[10px]"></i>
                                                <span>Pending</span>
                                            </span>
                                        @endif
                                        <div class="text-[10px] text-gray-400 font-mono mt-0.5">
                                            {{ $deliv->attempts }} {{ \Illuminate\Support\Str::plural('attempt', $deliv->attempts) }}
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-gray-50 text-gray-400 border border-gray-200">
                                            <i class="fa fa-minus text-[10px]"></i>
                                            <span>Not Sent</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button type="button"
                                            wire:click="repushWebhook({{ $tx->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="repushWebhook({{ $tx->id }})"
                                            class="btn-repush-webhook"
                                            title="Repush webhook notification to merchant">
                                            <i class="fa fa-rotate-right" wire:loading.remove wire:target="repushWebhook({{ $tx->id }})"></i>
                                            <i class="fa fa-spinner fa-spin" wire:loading wire:target="repushWebhook({{ $tx->id }})"></i>
                                            <span>Repush Webhook</span>
                                        </button>

                                        <button type="button"
                                            wire:click="openWebhookModal({{ $tx->id }})"
                                            class="btn-webhook-inspect"
                                            title="Inspect webhook logs and payload">
                                            <i class="fa fa-code"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-3">
                                            <i class="fa fa-folder-open text-xl"></i>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-700">No gateway transactions found</p>
                                        <p class="text-xs text-gray-400 mt-1">Try resetting your filters or search term.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($gatewayTransactions && $gatewayTransactions->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $gatewayTransactions->links() }}
                </div>
            @endif
        </div>

    @else
        <!-- WALLET TAB -->

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <span>Funding Payments</span>
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-normal">
                        <i class="fa fa-wallet"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-gray-900 mt-2">{{ number_format($walletStats['count']) }}</div>
                <div class="text-xs text-gray-400 mt-1">Total wallet top-ups</div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <span>Total Amount Funded</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-normal">
                        <i class="fa fa-money-bill-wave"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-emerald-600 mt-2">₦{{ number_format($walletStats['volume'], 2) }}</div>
                <div class="text-xs text-gray-400 mt-1">Credited into user wallets</div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <span>Fees Collected</span>
                    <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-sm font-normal">
                        <i class="fa fa-hand-holding-dollar"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-indigo-600 mt-2">₦{{ number_format($walletStats['fees'], 2) }}</div>
                <div class="text-xs text-gray-400 mt-1">Funding processing fees</div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <span>Funded Users</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-normal">
                        <i class="fa fa-users"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-gray-900 mt-2">{{ number_format($walletStats['uniqueUsers']) }}</div>
                <div class="text-xs text-gray-400 mt-1">Unique customer accounts</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Search</label>
                    <input type="text" wire:model.live.debounce.350ms="walletSearch" placeholder="Reference, transaction ID, customer..."
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Payment Method</label>
                    <select wire:model.live="walletMethod" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All methods</option>
                        <option value="paystack_card">Paystack Card</option>
                        <option value="flutterwave">Flutterwave</option>
                        <option value="monnify">Monnify</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">From Date</label>
                    <input type="date" wire:model.live="walletFromDate" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">To Date</label>
                    <div class="flex gap-2">
                        <input type="date" wire:model.live="walletToDate" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <button type="button" wire:click="clearFilters" title="Clear Filters"
                            class="px-3 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 flex items-center justify-center">
                            <i class="fa fa-times text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wallet Payments Table -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider font-semibold">
                        <tr>
                            <th class="px-5 py-3.5">Reference / Tx ID</th>
                            <th class="px-5 py-3.5">User</th>
                            <th class="px-5 py-3.5">Amount</th>
                            <th class="px-5 py-3.5">Charge Fee</th>
                            <th class="px-5 py-3.5">Payment Method</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-800">
                        @forelse ($walletTransactions as $tx)
                            <tr class="hover:bg-gray-50/80 transition" wire:key="wp-{{ $tx->id }}">
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="font-mono text-xs font-bold text-gray-900">{{ $tx->transaction_reference }}</div>
                                    <div class="font-mono text-[11px] text-gray-400 mt-0.5">ID: {{ $tx->transaction_id }}</div>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="text-xs font-bold text-gray-900">{{ $tx->user?->name ?? 'User #' . $tx->user_id }}</div>
                                    <div class="text-[11px] text-gray-400">{{ $tx->user?->email }}</div>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap font-extrabold text-sm text-gray-900">
                                    ₦{{ number_format($tx->amount, 2) }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-xs text-gray-600 font-medium">
                                    ₦{{ number_format($tx->charge_fee, 2) }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-xs">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full font-semibold text-xs bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        <i class="fa fa-credit-card text-[10px]"></i>
                                        {{ ucwords(str_replace('_', ' ', $tx->payment_method)) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                        Successful
                                    </span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-xs text-gray-500">
                                    {{ optional($tx->created_at)->format('d M Y, H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-3">
                                            <i class="fa fa-wallet text-xl"></i>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-700">No user wallet funding payments found</p>
                                        <p class="text-xs text-gray-400 mt-1">Try resetting your filters or search term.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($walletTransactions && $walletTransactions->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $walletTransactions->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- Webhook Inspection & Repush Modal -->
    @if ($showWebhookModal && $selectedTxData)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" wire:click="closeWebhookModal"></div>

            <div class="min-h-full flex items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-3xl w-full border border-gray-100 overflow-hidden transform transition-all text-left">
                    <!-- Modal Header -->
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/60">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                                <i class="fa fa-network-wired text-base"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-gray-900 flex items-center gap-2">
                                    <span>Webhook Dispatch & Delivery</span>
                                    <span class="font-mono text-xs px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 font-semibold">{{ $selectedTxData['reference'] }}</span>
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">Merchant: <span class="font-semibold text-gray-700">{{ $selectedTxData['merchant_name'] }}</span> ({{ $selectedTxData['merchant_email'] }})</p>
                            </div>
                        </div>
                        <button type="button" wire:click="closeWebhookModal" class="w-8 h-8 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 flex items-center justify-center transition">
                            <i class="fa fa-times text-sm"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 space-y-6 max-h-[78vh] overflow-y-auto">
                        <!-- Target Endpoint URL Box -->
                        <div class="bg-indigo-50/70 border border-indigo-100 rounded-xl p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-indigo-950 uppercase tracking-wider">Target Webhook Endpoint URL</label>
                                @if(empty($selectedTxData['merchant_webhook_url']))
                                    <span class="text-[11px] font-semibold text-amber-700 flex items-center gap-1">
                                        <i class="fa fa-exclamation-triangle"></i> Merchant has not set a default webhook URL
                                    </span>
                                @endif
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <input type="url" wire:model.live.debounce.300ms="overrideWebhookUrl" placeholder="https://vtuloaded.com.ng/account/payzenter_webhook"
                                    class="w-full text-xs font-mono rounded-lg border-indigo-200 focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                <button type="button" wire:click="repushFromModal" wire:loading.attr="disabled"
                                    class="px-5 py-2.5 rounded-lg text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white flex items-center justify-center gap-2 whitespace-nowrap shadow-sm transition cursor-pointer">
                                    <i class="fa fa-paper-plane" wire:loading.remove wire:target="repushFromModal"></i>
                                    <i class="fa fa-spinner fa-spin" wire:loading wire:target="repushFromModal"></i>
                                    <span>Dispatch Webhook</span>
                                </button>
                            </div>

                            <!-- Quick Preset URL Buttons -->
                            <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                <span class="text-[10px] font-bold text-indigo-900 uppercase">Quick Presets:</span>
                                <button type="button" wire:click="setPresetUrl('vtuloaded_payzenter')"
                                    class="px-2 py-0.5 rounded text-[11px] font-semibold bg-white border border-indigo-200 text-indigo-700 hover:bg-indigo-100 transition cursor-pointer">
                                    VTULoaded (payzenter_webhook)
                                </button>
                                <button type="button" wire:click="setPresetUrl('vtuloaded_process')"
                                    class="px-2 py-0.5 rounded text-[11px] font-semibold bg-white border border-indigo-200 text-indigo-700 hover:bg-indigo-100 transition cursor-pointer">
                                    VTULoaded (process_webhook)
                                </button>
                                <button type="button" wire:click="setPresetUrl('vtuloaded_paymentpoint')"
                                    class="px-2 py-0.5 rounded text-[11px] font-semibold bg-white border border-indigo-200 text-indigo-700 hover:bg-indigo-100 transition cursor-pointer">
                                    AcctLoaded (paymentpoint_webhook)
                                </button>
                                @if(!empty($selectedTxData['merchant_webhook_url']))
                                    <button type="button" wire:click="setPresetUrl('merchant_default')"
                                        class="px-2 py-0.5 rounded text-[11px] font-semibold bg-white border border-indigo-200 text-gray-700 hover:bg-gray-100 transition cursor-pointer">
                                        Merchant Default
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Enriched Account & Customer Overrides for Delivery -->
                        <div class="bg-gray-50/80 border border-gray-200 rounded-xl p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fa fa-sliders text-indigo-600 text-xs"></i>
                                    <span class="text-xs font-bold text-gray-900 uppercase tracking-wider">Payload Enrichment & Overrides</span>
                                </div>
                                <span class="text-[10px] font-medium text-gray-500">Injected into receiver, virtual_account & customer objects</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-600 uppercase mb-1">
                                        Virtual Account #
                                    </label>
                                    <input type="text" wire:model.live.debounce.300ms="overrideAccountNumber" placeholder="e.g. 6668601363"
                                        class="w-full text-xs font-mono rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                </div>

                                <div>
                                    <label class="block text-[11px] font-bold text-gray-600 uppercase mb-1">
                                        Customer Phone
                                    </label>
                                    <input type="text" wire:model.live.debounce.300ms="overrideCustomerPhone" placeholder="e.g. 08077406889"
                                        class="w-full text-xs font-mono rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                </div>

                                <div>
                                    <label class="block text-[11px] font-bold text-gray-600 uppercase mb-1">
                                        Customer Email
                                    </label>
                                    <input type="email" wire:model.live.debounce.300ms="overrideCustomerEmail" placeholder="e.g. user@gmail.com"
                                        class="w-full text-xs font-mono rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                </div>

                                <div>
                                    <label class="block text-[11px] font-bold text-gray-600 uppercase mb-1">
                                        Amount (₦ Naira)
                                    </label>
                                    <input type="number" step="0.01" wire:model.live.debounce.300ms="overrideAmount" placeholder="e.g. 5000.00"
                                        class="w-full text-xs font-mono font-bold text-gray-900 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                </div>
                            </div>
                        </div>

                        <!-- Transaction Summary Chips -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-white rounded-xl p-3 border border-gray-200 text-xs shadow-xs">
                            <div>
                                <span class="text-gray-400 block text-[10px] uppercase font-bold">Original Amount</span>
                                <span class="font-extrabold text-gray-900">₦{{ number_format(($selectedTxData['amount'] ?? 0) / 100, 2) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block text-[10px] uppercase font-bold">Status</span>
                                <span class="font-bold text-emerald-600 uppercase">{{ $selectedTxData['status'] }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block text-[10px] uppercase font-bold">Provider / Method</span>
                                <span class="font-semibold text-gray-800">{{ ucfirst($selectedTxData['provider']) }} ({{ $selectedTxData['method'] }})</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block text-[10px] uppercase font-bold">Created</span>
                                <span class="font-mono text-gray-700">{{ $selectedTxData['created_at'] }}</span>
                            </div>
                        </div>

                        <!-- Live Payload Preview Box (Collapsible) -->
                        <div x-data="{ openPayloadPreview: false }" class="border border-gray-200 rounded-xl bg-gray-50/60 p-3">
                            <button type="button" @click="openPayloadPreview = !openPayloadPreview"
                                class="w-full flex items-center justify-between text-xs font-bold text-gray-700 hover:text-indigo-600 transition cursor-pointer">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa fa-code text-indigo-500"></i>
                                    <span>Preview Generated Universal Webhook Payload</span>
                                </span>
                                <span class="text-[11px] text-indigo-600 font-semibold flex items-center gap-1">
                                    <span x-text="openPayloadPreview ? 'Hide JSON' : 'Show JSON'"></span>
                                    <i class="fa text-[10px]" :class="openPayloadPreview ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                </span>
                            </button>
                            <div x-show="openPayloadPreview" x-cloak class="mt-3">
                                <pre class="bg-gray-950 text-emerald-400 p-3 rounded-lg text-[11px] font-mono overflow-x-auto max-h-56 leading-relaxed border border-gray-800">{{ $previewPayloadJson }}</pre>
                            </div>
                        </div>

                        <!-- Deliveries / Attempt Logs -->
                        <div>
                            <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-3 flex items-center justify-between">
                                <span>Delivery Attempts & Merchant Responses</span>
                                <span class="text-[11px] text-gray-500 font-normal">{{ count($selectedDeliveries) }} recorded</span>
                            </h4>

                            @if (empty($selectedDeliveries))
                                <div class="text-center py-8 border border-dashed border-gray-200 rounded-xl bg-gray-50/50">
                                    <i class="fa fa-bell-slash text-gray-300 text-2xl mb-2"></i>
                                    <p class="text-xs font-semibold text-gray-600">No webhook delivery recorded yet</p>
                                    <p class="text-[11px] text-gray-400 mt-0.5">Click "Dispatch Webhook" above to trigger delivery for this transaction.</p>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach ($selectedDeliveries as $del)
                                        @php
                                            $respLower = strtolower($del['response_body'] ?? '');
                                            $isRejectedAppLevel = str_contains($respLower, 'not found') 
                                                || str_contains($respLower, 'not successful') 
                                                || str_contains($respLower, 'invalid')
                                                || str_contains($respLower, 'failed')
                                                || !empty($del['error_message']);
                                        @endphp
                                        <div class="border rounded-xl p-4 bg-white shadow-xs space-y-3 @if($del['delivered_at']) border-emerald-200 @elseif($isRejectedAppLevel) border-amber-200 @else border-gray-200 @endif" wire:key="deliv-{{ $del['id'] }}">
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-100 pb-2.5">
                                                <div class="flex items-center gap-2">
                                                    @if($del['delivered_at'])
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                                            <i class="fa fa-check-circle"></i> HTTP {{ $del['response_status'] ?: 200 }} Delivered & Acknowledged
                                                        </span>
                                                    @elseif($del['response_status'])
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold @if($isRejectedAppLevel) bg-amber-100 text-amber-800 @else bg-rose-100 text-rose-800 @endif">
                                                            <i class="fa fa-exclamation-triangle"></i> HTTP {{ $del['response_status'] }} Rejected by Receiver
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                                            <i class="fa fa-times-circle"></i> Connection / Network Error
                                                        </span>
                                                    @endif
                                                    <span class="text-xs text-gray-400 font-mono">Attempt #{{ $del['attempts'] }}</span>
                                                </div>

                                                <div class="flex items-center gap-2 text-xs">
                                                    <span class="text-gray-400 font-mono">{{ $del['last_attempt_at'] ?: $del['created_at'] }}</span>
                                                    <button type="button" wire:click="repushDelivery({{ $del['id'] }})"
                                                        class="px-2.5 py-1 text-[11px] font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-md transition flex items-center gap-1 cursor-pointer">
                                                        <i class="fa fa-rotate-right text-[10px]"></i> Re-attempt
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="text-xs font-mono break-all text-gray-700 bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                                                <span class="font-bold text-gray-400 uppercase text-[10px] block mb-0.5">Target Endpoint:</span>
                                                {{ $del['url'] }}
                                            </div>

                                            @if($del['error_message'])
                                                <div class="bg-amber-50 border border-amber-200 text-amber-900 text-xs p-2.5 rounded-lg">
                                                    <span class="font-bold block mb-0.5 flex items-center gap-1">
                                                        <i class="fa fa-circle-exclamation text-amber-600"></i> Receiver Status Note:
                                                    </span>
                                                    {{ $del['error_message'] }}
                                                </div>
                                            @endif

                                            @if($del['response_body'])
                                                <div class="bg-gray-900 text-gray-100 p-3 rounded-lg text-xs font-mono overflow-x-auto">
                                                    <span class="font-bold text-gray-400 uppercase text-[10px] block mb-1">Receiver Response Body:</span>
                                                    <div class="@if(str_contains($respLower, 'successfully') || str_contains($respLower, 'processed')) text-emerald-400 @elseif($isRejectedAppLevel) text-amber-300 @else text-gray-200 @endif">
                                                        {{ $del['response_body'] }}
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Collapsible Sent Payload -->
                                            <div x-data="{ openPayload: false }" class="pt-1 text-xs">
                                                <button type="button" @click="openPayload = !openPayload" class="text-indigo-600 hover:text-indigo-800 font-semibold flex items-center gap-1 cursor-pointer">
                                                    <i class="fa text-[10px]" :class="openPayload ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                                    <span x-text="openPayload ? 'Hide Sent Payload JSON' : 'Inspect Sent Payload JSON'"></span>
                                                </button>

                                                <div x-show="openPayload" x-cloak class="mt-2">
                                                    <pre class="bg-gray-950 text-gray-200 p-3 rounded-lg text-[11px] font-mono overflow-x-auto max-h-48 leading-relaxed border border-gray-800">{{ json_encode($del['payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-xs text-gray-400">Transaction ID: {{ $selectedTxData['id'] }}</span>
                        <button type="button" wire:click="closeWebhookModal"
                            class="px-4 py-2 text-xs font-semibold text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition cursor-pointer">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>