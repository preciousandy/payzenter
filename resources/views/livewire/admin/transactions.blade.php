<div class="space-y-6">
    <style>
        .btn-action-primary {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            padding: 6px 12px !important;
            border-radius: 8px !important;
            font-size: 12px !important;
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
        .btn-action-primary:hover {
            background-color: #c7d2fe !important;
            color: #312e81 !important;
            border-color: #a5b4fc !important;
        }
        .btn-action-secondary {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 6px 10px !important;
            border-radius: 8px !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #334155 !important;
            background-color: #f1f5f9 !important;
            border: 1px solid #cbd5e1 !important;
            cursor: pointer !important;
            transition: all 0.15s ease-in-out !important;
            outline: none !important;
            white-space: nowrap !important;
        }
        .btn-action-secondary:hover {
            background-color: #e2e8f0 !important;
            color: #0f172a !important;
            border-color: #94a3b8 !important;
        }
    </style>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Transfers & Withdrawals</h1>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-800">
                    Outgoing
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-1">
                Monitor and audit outgoing payouts, bank transfers, and merchant withdrawals sent into individual or business bank accounts.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.payment-transactions') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                <i class="fa fa-arrow-down text-emerald-600"></i> View Incoming (Payment Tx)
            </a>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Payout Live
            </span>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
            <button type="button"
                wire:click="setTab('transfers')"
                @class([
                    'flex items-center gap-2 py-3 px-1 border-b-2 text-sm font-bold transition-all whitespace-nowrap',
                    'border-indigo-600 text-indigo-600' => $activeTab === 'transfers',
                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' => $activeTab !== 'transfers',
                ])>
                <i class="fa fa-paper-plane text-sm"></i>
                <span>Bank Transfers & Withdrawals (Merchant Payouts)</span>
                <span @class([
                    'text-xs px-2 py-0.5 rounded-full font-bold',
                    'bg-indigo-100 text-indigo-700' => $activeTab === 'transfers',
                    'bg-gray-100 text-gray-600' => $activeTab !== 'transfers',
                ])>{{ number_format($transfersTotalCount) }}</span>
            </button>

            <button type="button"
                wire:click="setTab('debits')"
                @class([
                    'flex items-center gap-2 py-3 px-1 border-b-2 text-sm font-bold transition-all whitespace-nowrap',
                    'border-indigo-600 text-indigo-600' => $activeTab === 'debits',
                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' => $activeTab !== 'debits',
                ])>
                <i class="fa fa-list-check text-sm"></i>
                <span>All Outgoing User Debits (Ledger)</span>
                <span @class([
                    'text-xs px-2 py-0.5 rounded-full font-bold',
                    'bg-indigo-100 text-indigo-700' => $activeTab === 'debits',
                    'bg-gray-100 text-gray-600' => $activeTab !== 'debits',
                ])>{{ number_format($debitsTotalCount) }}</span>
            </button>
        </nav>
    </div>

    @if ($activeTab === 'transfers')
        <!-- TRANSFERS TAB -->

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <span>Successful Payouts</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-normal">
                        <i class="fa fa-check-circle"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-gray-900 mt-2">₦{{ number_format($transferStats['totalVolume'], 2) }}</div>
                <div class="text-xs text-gray-400 mt-1">{{ number_format($transferStats['successCount']) }} completed transfers</div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <span>Pending Processing</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-normal">
                        <i class="fa fa-clock"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-amber-600 mt-2">₦{{ number_format($transferStats['totalPendingVolume'], 2) }}</div>
                <div class="text-xs text-gray-400 mt-1">{{ number_format($transferStats['pendingCount']) }} pending confirmation</div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <span>Failed Transfers</span>
                    <div class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center text-sm font-normal">
                        <i class="fa fa-circle-xmark"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-red-600 mt-2">{{ number_format($transferStats['failedCount']) }}</div>
                <div class="text-xs text-gray-400 mt-1">Declined or provider error</div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <span>Transfer Fees</span>
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-normal">
                        <i class="fa fa-receipt"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-gray-900 mt-2">₦{{ number_format($transferStats['totalFee'], 2) }}</div>
                <div class="text-xs text-gray-400 mt-1">Total revenue collected on transfers</div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white rounded-2xl p-4 border border-gray-200/80 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                <!-- Search -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Search</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fa fa-search text-xs"></i>
                        </span>
                        <input type="text"
                            wire:model.debounce.400ms="search"
                            placeholder="Account, Bank, Name, Ref..."
                            class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" />
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                    <select wire:model="status" class="w-full py-2 px-3 text-xs rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition bg-white">
                        <option value="">All Statuses</option>
                        <option value="success">Successful</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>

                <!-- Provider Filter -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Provider</label>
                    <select wire:model="provider" class="w-full py-2 px-3 text-xs rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition bg-white">
                        <option value="">All Providers</option>
                        <option value="flutterwave">Flutterwave</option>
                        <option value="palmpay">PalmPay</option>
                        <option value="dot">Dot MFB</option>
                    </select>
                </div>

                <!-- From Date -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">From Date</label>
                    <input type="date"
                        wire:model="from_date"
                        class="w-full py-2 px-3 text-xs rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" />
                </div>

                <!-- To Date -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">To Date</label>
                    <input type="date"
                        wire:model="to_date"
                        class="w-full py-2 px-3 text-xs rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" />
                </div>
            </div>

            @if ($search || $status || $provider || $from_date || $to_date)
                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                    <span class="text-gray-500">Filters active</span>
                    <button type="button" wire:click="clearFilters" class="text-indigo-600 hover:text-indigo-800 font-semibold inline-flex items-center gap-1">
                        <i class="fa fa-times-circle"></i> Clear all filters
                    </button>
                </div>
            @endif
        </div>

        <!-- Transfers Table (Desktop) & Cards (Mobile) -->
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Transfers & Withdrawals</h3>
                    <p class="text-xs text-gray-500">Showing outgoing payments to merchant bank accounts</p>
                </div>
                <div class="text-xs font-bold text-gray-500">
                    Total: {{ $transfers->total() }} records
                </div>
            </div>

            @if ($transfers->count() === 0)
                <div class="p-12 text-center">
                    <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3 text-lg">
                        <i class="fa fa-paper-plane"></i>
                    </div>
                    <div class="text-sm font-bold text-gray-700">No transfers found</div>
                    <p class="text-xs text-gray-400 mt-1">No outgoing transactions matched your search or filters.</p>
                </div>
            @else
                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-xs text-gray-600">
                        <thead class="bg-gray-50/75 text-gray-500 font-bold uppercase tracking-wider text-[11px] border-b border-gray-200/60">
                            <tr>
                                <th class="px-5 py-3">ID / Ref</th>
                                <th class="px-5 py-3">Merchant</th>
                                <th class="px-5 py-3">Destination (Account)</th>
                                <th class="px-5 py-3">Amount</th>
                                <th class="px-5 py-3">Provider</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Date</th>
                                <th class="px-5 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($transfers as $tx)
                                @php
                                    $st = strtolower((string)$tx->status);
                                    $isSuccess = in_array($st, ['success', 'successful', 'completed', 'succeeded'], true);
                                    $isFailed = in_array($st, ['failed'], true);
                                    $isPending = in_array($st, ['pending', 'running', 'new', 'processing'], true);
                                    $prov = strtolower((string) data_get($tx->response_data, 'provider', ''));
                                    if ($prov === '') {
                                        if (str_starts_with((string)$tx->reference, 'flw_')) $prov = 'flutterwave';
                                        elseif (str_starts_with((string)$tx->reference, 'plm_')) $prov = 'palmpay';
                                        elseif (str_starts_with((string)$tx->reference, 'dot_')) $prov = 'dot';
                                        else $prov = 'bank';
                                    }
                                @endphp
                                <tr class="hover:bg-indigo-50/30 transition">
                                    <td class="px-5 py-3.5">
                                        <div class="font-bold text-gray-900">#{{ $tx->id }}</div>
                                        <div class="font-mono text-[11px] text-gray-400 truncate max-w-[130px]" title="{{ $tx->reference }}">
                                            {{ $tx->reference }}
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="font-semibold text-gray-900">
                                            {{ $tx->user ? trim($tx->user->first_name . ' ' . $tx->user->last_name) : 'User #' . $tx->user_id }}
                                        </div>
                                        <div class="text-[11px] text-gray-400 truncate max-w-[150px]">
                                            {{ $tx->user?->email }}
                                        </div>
                                        @if ($tx->user?->merchantKyb?->business_name)
                                            <span class="inline-block mt-0.5 px-1.5 py-0.2 rounded text-[10px] bg-slate-100 text-slate-600 font-medium truncate max-w-[150px]">
                                                {{ $tx->user->merchantKyb->business_name }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="font-bold text-gray-900">{{ $tx->account_name ?: 'N/A' }}</div>
                                        <div class="text-[11px] text-gray-500 font-medium flex items-center gap-1.5 mt-0.5">
                                            <span class="font-bold text-indigo-700">{{ $tx->bank_name ?: 'Bank' }}</span>
                                            <span>•</span>
                                            <span class="font-mono">{{ $tx->account_number }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        <div class="font-black text-gray-900 text-sm">
                                            ₦{{ number_format((float) $tx->amount, 2) }}
                                        </div>
                                        @if ((float) $tx->fee > 0)
                                            <div class="text-[11px] text-gray-400">
                                                Fee: ₦{{ number_format((float) $tx->fee, 2) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        @if ($prov === 'flutterwave')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Flutterwave
                                            </span>
                                        @elseif ($prov === 'palmpay')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                PalmPay
                                            </span>
                                        @elseif ($prov === 'dot')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                Dot MFB
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-gray-100 text-gray-600">
                                                {{ ucfirst($prov) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        @if ($isSuccess)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <i class="fa fa-check text-[10px]"></i> Successful
                                            </span>
                                        @elseif ($isFailed)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-50 text-red-700 border border-red-200" title="{{ $tx->getFailureReason() }}">
                                                <i class="fa fa-times text-[10px]"></i> Failed
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <i class="fa fa-spinner fa-spin text-[10px]"></i> Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap text-gray-500">
                                        <div>{{ optional($tx->created_at)->format('d M Y') }}</div>
                                        <div class="text-[11px] text-gray-400">{{ optional($tx->created_at)->format('H:i:s') }}</div>
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if ($isPending)
                                                <button type="button"
                                                    wire:click="checkStatus({{ $tx->id }})"
                                                    wire:loading.attr="disabled"
                                                    class="btn-action-primary text-xs"
                                                    title="Query provider to sync current status">
                                                    <span wire:loading.remove wire:target="checkStatus({{ $tx->id }})">
                                                        <i class="fa fa-rotate"></i>
                                                    </span>
                                                    <span wire:loading wire:target="checkStatus({{ $tx->id }})">
                                                        <i class="fa fa-spinner fa-spin"></i>
                                                    </span>
                                                    Check
                                                </button>
                                            @endif
                                            <button type="button"
                                                wire:click="openDetailModal({{ $tx->id }})"
                                                class="btn-action-secondary"
                                                title="View transfer audit details">
                                                <i class="fa fa-eye"></i> Details
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View (Optimized for Small Screens) -->
                <div class="block md:hidden divide-y divide-gray-100">
                    @foreach ($transfers as $tx)
                        @php
                            $st = strtolower((string)$tx->status);
                            $isSuccess = in_array($st, ['success', 'successful', 'completed', 'succeeded'], true);
                            $isFailed = in_array($st, ['failed'], true);
                            $isPending = in_array($st, ['pending', 'running', 'new', 'processing'], true);
                            $prov = strtolower((string) data_get($tx->response_data, 'provider', ''));
                            if ($prov === '') {
                                if (str_starts_with((string)$tx->reference, 'flw_')) $prov = 'flutterwave';
                                elseif (str_starts_with((string)$tx->reference, 'plm_')) $prov = 'palmpay';
                                elseif (str_starts_with((string)$tx->reference, 'dot_')) $prov = 'dot';
                                else $prov = 'bank';
                            }
                        @endphp
                        <div class="p-4 space-y-3 hover:bg-gray-50/50 transition">
                            <!-- Top row: ID, Amount, Status -->
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-gray-900 text-sm">#{{ $tx->id }}</span>
                                        <span class="text-[10px] font-mono text-gray-400 truncate max-w-[120px]">{{ $tx->reference }}</span>
                                    </div>
                                    <div class="text-[11px] text-gray-400 mt-0.5">
                                        {{ optional($tx->created_at)->format('d M Y, H:i') }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-base font-black text-gray-900">
                                        ₦{{ number_format((float)$tx->amount, 2) }}
                                    </div>
                                    @if ($isSuccess)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 mt-1">
                                            <i class="fa fa-check text-[9px]"></i> Success
                                        </span>
                                    @elseif ($isFailed)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200 mt-1">
                                            <i class="fa fa-times text-[9px]"></i> Failed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 mt-1">
                                            <i class="fa fa-spinner fa-spin text-[9px]"></i> Pending
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Destination Box -->
                            <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 space-y-1">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-gray-400 text-[11px]">Beneficiary:</span>
                                    <span class="font-bold text-gray-900">{{ $tx->account_name ?: 'N/A' }}</span>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-gray-400 text-[11px]">Bank:</span>
                                    <span class="font-semibold text-indigo-700">{{ $tx->bank_name ?: 'Bank' }} ({{ $tx->account_number }})</span>
                                </div>
                                <div class="flex items-center justify-between text-xs pt-1 border-t border-gray-200/50">
                                    <span class="text-gray-400 text-[11px]">Sender:</span>
                                    <span class="text-gray-700">{{ $tx->user ? trim($tx->user->first_name . ' ' . $tx->user->last_name) : 'User #' . $tx->user_id }}</span>
                                </div>
                            </div>

                            <!-- Actions Row -->
                            <div class="flex items-center justify-between gap-2 pt-1">
                                <span class="text-[11px] font-bold uppercase text-gray-400">
                                    via {{ ucfirst($prov) }}
                                </span>
                                <div class="flex items-center gap-2">
                                    @if ($isPending)
                                        <button type="button"
                                            wire:click="checkStatus({{ $tx->id }})"
                                            wire:loading.attr="disabled"
                                            class="btn-action-primary text-xs py-1 px-2.5">
                                            <i class="fa fa-rotate"></i> Sync
                                        </button>
                                    @endif
                                    <button type="button"
                                        wire:click="openDetailModal({{ $tx->id }})"
                                        class="btn-action-secondary text-xs py-1 px-2.5">
                                        <i class="fa fa-eye"></i> Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="p-4 border-t border-gray-100">
                    {{ $transfers->links() }}
                </div>
            @endif
        </div>

    @else
        <!-- DEBITS TAB (USER TRANSACTIONS LEDGER) -->

        <!-- Summary Cards for Debits -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Debits Volume</div>
                <div class="text-2xl font-black text-gray-900 mt-2">₦{{ number_format($debitStats['totalVolume'], 2) }}</div>
                <div class="text-xs text-gray-400 mt-1">{{ number_format($debitStats['count']) }} total debit records</div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Successful Debits</div>
                <div class="text-2xl font-black text-emerald-600 mt-2">{{ number_format($debitStats['successCount']) }}</div>
                <div class="text-xs text-gray-400 mt-1">Processed successfully</div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pending Debits</div>
                <div class="text-2xl font-black text-amber-600 mt-2">{{ number_format($debitStats['pendingCount']) }}</div>
                <div class="text-xs text-gray-400 mt-1">Awaiting confirmation</div>
            </div>
        </div>

        <!-- Debits Filter Bar -->
        <div class="bg-white rounded-2xl p-4 border border-gray-200/80 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Search</label>
                    <input type="text"
                        wire:model.debounce.400ms="debitSearch"
                        placeholder="Reference, Message, User name/email..."
                        class="w-full py-2 px-3 text-xs rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Source</label>
                    <select wire:model="debitSource" class="w-full py-2 px-3 text-xs rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition bg-white">
                        <option value="">All Sources</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="swiftpay_transfer">SwiftPay Transfer</option>
                        <option value="multiple_bank_transfer">Multiple Bank Transfer</option>
                        <option value="international_transfer">International Transfer</option>
                        <option value="airtime">Airtime</option>
                        <option value="data">Data</option>
                        <option value="electricity">Electricity</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">From Date</label>
                    <input type="date"
                        wire:model="debitFromDate"
                        class="w-full py-2 px-3 text-xs rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">To Date</label>
                    <input type="date"
                        wire:model="debitToDate"
                        class="w-full py-2 px-3 text-xs rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition" />
                </div>
            </div>

            @if ($debitSearch || $debitSource || $debitStatus || $debitFromDate || $debitToDate)
                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                    <span class="text-gray-500">Filters active</span>
                    <button type="button" wire:click="clearFilters" class="text-indigo-600 hover:text-indigo-800 font-semibold inline-flex items-center gap-1">
                        <i class="fa fa-times-circle"></i> Clear filters
                    </button>
                </div>
            @endif
        </div>

        <!-- Debits Table -->
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Outgoing Debits Ledger</h3>
                    <p class="text-xs text-gray-500">All debit ledger transactions from user wallets</p>
                </div>
                <div class="text-xs font-bold text-gray-500">
                    Total: {{ $debits->total() }} records
                </div>
            </div>

            @if ($debits->count() === 0)
                <div class="p-12 text-center text-gray-400">
                    <i class="fa fa-list text-2xl mb-2"></i>
                    <div class="text-sm font-bold text-gray-700">No debits found</div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-gray-600">
                        <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-[11px] border-b border-gray-200/60">
                            <tr>
                                <th class="px-5 py-3">ID / Ref</th>
                                <th class="px-5 py-3">User</th>
                                <th class="px-5 py-3">Amount</th>
                                <th class="px-5 py-3">Source</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Message</th>
                                <th class="px-5 py-3">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($debits as $deb)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-5 py-3.5">
                                        <div class="font-bold text-gray-900">#{{ $deb->id }}</div>
                                        <div class="font-mono text-[11px] text-gray-400 truncate max-w-[130px]" title="{{ $deb->reference }}">
                                            {{ $deb->reference }}
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="font-semibold text-gray-900">
                                            {{ $deb->user ? trim($deb->user->first_name . ' ' . $deb->user->last_name) : 'User #' . $deb->user_id }}
                                        </div>
                                        <div class="text-[11px] text-gray-400">{{ $deb->user?->email }}</div>
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap font-black text-gray-900 text-sm">
                                        ₦{{ number_format((float) $deb->amount, 2) }}
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-gray-700 capitalize">
                                            {{ str_replace('_', ' ', $deb->source) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        @if (in_array(strtolower((string)$deb->status), ['successful', 'success'], true))
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Successful
                                            </span>
                                        @elseif (in_array(strtolower((string)$deb->status), ['failed'], true))
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-red-50 text-red-700 border border-red-200">
                                                Failed
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 max-w-[240px] truncate text-gray-600" title="{{ $deb->message }}">
                                        {{ $deb->message ?: 'N/A' }}
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap text-gray-400">
                                        {{ optional($deb->created_at)->format('d M Y, H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-100">
                    {{ $debits->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- TRANSFER DETAILS MODAL -->
    @if ($showDetailModal && $selectedTransfer)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl border border-gray-100 overflow-hidden transform transition-all animate-in fade-in zoom-in duration-150">
                <!-- Modal Header -->
                <div class="px-6 py-5 bg-gradient-to-r from-slate-900 to-indigo-950 text-white flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-black tracking-tight">Transfer Details #{{ $selectedTransfer['id'] }}</h3>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-white/20 text-white">
                                {{ $selectedTransfer['provider'] }}
                            </span>
                        </div>
                        <p class="text-xs text-indigo-200 font-mono mt-0.5">Ref: {{ $selectedTransfer['reference'] }}</p>
                    </div>
                    <button type="button" wire:click="closeDetailModal" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition">
                        <i class="fa fa-times text-sm"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-5 max-h-[80vh] overflow-y-auto text-xs">
                    <!-- Financial Summary Banner -->
                    <div class="bg-indigo-50/70 border border-indigo-100 rounded-2xl p-4 flex items-center justify-between">
                        <div>
                            <span class="text-gray-500 text-[11px] font-semibold uppercase tracking-wider">Transfer Amount</span>
                            <div class="text-2xl font-black text-indigo-950 mt-0.5">₦{{ number_format($selectedTransfer['amount'], 2) }}</div>
                            @if ($selectedTransfer['fee'] > 0)
                                <div class="text-gray-500 text-[11px] mt-0.5">Fee: ₦{{ number_format($selectedTransfer['fee'], 2) }}</div>
                            @endif
                        </div>
                        <div class="text-right">
                            <span class="text-gray-500 text-[11px] font-semibold uppercase tracking-wider">Current Status</span>
                            <div class="mt-1">
                                @if (in_array(strtolower((string)$selectedTransfer['status']), ['success', 'successful', 'completed', 'succeeded'], true))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fa fa-check text-[10px]"></i> Successful
                                    </span>
                                @elseif (in_array(strtolower((string)$selectedTransfer['status']), ['failed'], true))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                        <i class="fa fa-times text-[10px]"></i> Failed
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        <i class="fa fa-spinner fa-spin text-[10px]"></i> Pending
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Failure reason alert if failed -->
                    @if ($selectedTransfer['failure_reason'])
                        <div class="bg-red-50 border border-red-200 rounded-2xl p-4 text-red-800">
                            <div class="flex items-start gap-2.5">
                                <i class="fa fa-triangle-exclamation text-red-500 mt-0.5"></i>
                                <div>
                                    <div class="font-bold text-xs">Failure Reason Reported:</div>
                                    <div class="text-xs text-red-700 mt-0.5">{{ $selectedTransfer['failure_reason'] }}</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Two-column info grids -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Beneficiary Account (Destination) -->
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 space-y-2">
                            <div class="text-xs font-bold text-gray-900 flex items-center gap-1.5 border-b border-gray-200/60 pb-2">
                                <i class="fa fa-building-columns text-indigo-600"></i> Beneficiary Account (Payout Destination)
                            </div>
                            <div class="space-y-1.5 pt-1">
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Account Name:</span>
                                    <span class="font-bold text-gray-900 text-right">{{ $selectedTransfer['account_name'] ?: 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Account Number:</span>
                                    <span class="font-mono font-bold text-indigo-700">{{ $selectedTransfer['account_number'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Bank Name:</span>
                                    <span class="font-semibold text-gray-900">{{ $selectedTransfer['bank_name'] }}</span>
                                </div>
                                @if ($selectedTransfer['bank_code'])
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Bank Code:</span>
                                        <span class="font-mono text-gray-600">{{ $selectedTransfer['bank_code'] }}</span>
                                    </div>
                                @endif
                                @if ($selectedTransfer['description'])
                                    <div class="flex justify-between pt-1 border-t border-gray-200/40">
                                        <span class="text-gray-400">Narration:</span>
                                        <span class="text-gray-700 italic">{{ $selectedTransfer['description'] }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Merchant (Sender) -->
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 space-y-2">
                            <div class="text-xs font-bold text-gray-900 flex items-center gap-1.5 border-b border-gray-200/60 pb-2">
                                <i class="fa fa-user text-indigo-600"></i> Merchant / Sender
                            </div>
                            <div class="space-y-1.5 pt-1">
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Merchant Name:</span>
                                    <span class="font-bold text-gray-900 text-right">{{ $selectedTransfer['merchant_name'] }}</span>
                                </div>
                                @if ($selectedTransfer['business_name'])
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Business Name:</span>
                                        <span class="font-semibold text-indigo-700">{{ $selectedTransfer['business_name'] }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Email:</span>
                                    <span class="text-gray-700">{{ $selectedTransfer['merchant_email'] }}</span>
                                </div>
                                @if ($selectedTransfer['merchant_phone'])
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Phone:</span>
                                        <span class="text-gray-700">{{ $selectedTransfer['merchant_phone'] }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between pt-1 border-t border-gray-200/40">
                                    <span class="text-gray-400">Initiated At:</span>
                                    <span class="text-gray-700">{{ $selectedTransfer['created_at'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Technical Provider Info -->
                    <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 space-y-2">
                        <div class="text-xs font-bold text-gray-900 flex items-center gap-1.5 border-b border-gray-200/60 pb-2">
                            <i class="fa fa-server text-indigo-600"></i> Provider & Gateway Metadata
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-1 text-[11px]">
                            <div>
                                <span class="text-gray-400 block">Gateway Provider:</span>
                                <span class="font-bold text-gray-900 uppercase">{{ $selectedTransfer['provider'] }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block">Provider Request ID:</span>
                                <span class="font-mono text-gray-700">{{ $selectedTransfer['transfer_id'] ?: 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block">Last Status Check:</span>
                                <span class="text-gray-700">{{ $selectedTransfer['last_status_check_at'] ?: 'Never' }}</span>
                            </div>
                        </div>

                        @if (!empty($selectedTransfer['response_data']))
                            <details class="pt-2">
                                <summary class="cursor-pointer text-[11px] font-bold text-indigo-600 hover:text-indigo-800">
                                    View Raw Response Payload JSON
                                </summary>
                                <pre class="mt-2 p-3 bg-slate-900 text-emerald-400 rounded-xl text-[10px] font-mono overflow-x-auto max-h-48 leading-relaxed">{{ json_encode($selectedTransfer['response_data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            </details>
                        @endif
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                    <div>
                        <button type="button"
                            wire:click="checkStatus({{ $selectedTransfer['id'] }})"
                            wire:loading.attr="disabled"
                            class="btn-action-primary text-xs">
                            <span wire:loading.remove wire:target="checkStatus({{ $selectedTransfer['id'] }})">
                                <i class="fa fa-rotate"></i>
                            </span>
                            <span wire:loading wire:target="checkStatus({{ $selectedTransfer['id'] }})">
                                <i class="fa fa-spinner fa-spin"></i>
                            </span>
                            Query Provider Real-Time Status
                        </button>
                    </div>
                    <button type="button" wire:click="closeDetailModal" class="px-4 py-2 bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 rounded-xl font-bold text-xs transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>