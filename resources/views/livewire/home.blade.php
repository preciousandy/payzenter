<div class="p-4 lg:p-6 max-w-6xl mx-auto space-y-6" x-data="{showBalance: @entangle('showBalance')}">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-sm text-gray-600 mt-1">
                Welcome back, {{ auth()->user()->name }}{{ auth()->user()->company_name ? ' • ' . auth()->user()->company_name : '' }}
            </p>
        </div>

       {{-- <div class="bg-white border border-gray-200 rounded-xl p-3 flex items-center justify-between gap-3 max-w-md w-full sm:w-auto"
             x-data="{ merchantId: '{{ auth()->user()->merchant_id ?? '' }}', showCopied: false }"
        >
            <div class="min-w-0">
                <div class="text-xs text-gray-500">Merchant ID</div>
                <div class="font-semibold text-gray-900 truncate">{{ auth()->user()->merchant_id ?? 'N/A' }}</div>
            </div>
            <button
                type="button"
                class="px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 text-gray-700 hover:bg-gray-100"
                @click="navigator.clipboard.writeText(merchantId); showCopied = true; setTimeout(() => showCopied = false, 2000)"
                title="Copy Merchant ID"
            >
                <span class="fa fa-copy mr-1"></span>
                <span x-show="!showCopied" x-cloak>Copy</span>
                <span x-show="showCopied" x-cloak>Copied</span>
            </button>
        </div>--}}
    </div>

    @php
        $kybSubmitted = (bool) optional($kyb)->submitted_at;
        $hasSettlement = (bool) (optional($kyb)->settlement_account_number && optional($kyb)->settlement_bank_id && optional($kyb)->settlement_account_name);
    @endphp

    @if (!$kybSubmitted || !$hasSettlement)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="text-sm text-amber-900">
                <div class="font-semibold">
                    Complete your onboarding to unlock payouts and smooth operations.
                </div>
                <div class="mt-1 text-amber-800">
                    @if (!$kybSubmitted)
                        KYB not submitted.
                    @else
                        Settlement account not completed.
                    @endif
                </div>
            </div>
            <a href="{{ route('onboarding') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-amber-600 text-white font-semibold hover:bg-amber-700">
                Continue Onboarding <span class="fa fa-arrow-right ml-2"></span>
            </a>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm lg:col-span-2">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-sm text-gray-500 font-medium">Wallet Balance</div>
                    <div class="mt-2">
                        <div class="text-4xl lg:text-5xl font-extrabold text-gray-900 tracking-tight" x-show="showBalance" x-cloak>
                            <x-naira-symbol class="inline-block h-6 w-6 -mt-1" /> {{ formatNumber(auth()->user()->wallet_balance) }}
                        </div>
                        <div class="text-4xl lg:text-5xl font-extrabold text-gray-900 tracking-tight" x-show="!showBalance" x-cloak>
                            <x-naira-symbol class="inline-block h-6 w-6 -mt-1" /> ********
                        </div>
                    </div>
                </div>

                <button class="text-xl text-gray-500 hover:text-gray-800 transition" wire:click="toggleShowBalance" title="Toggle Balance Visibility">
                    <span class="fa fa-eye-slash" x-show="showBalance" x-cloak></span>
                    <span class="fa fa-eye" x-show="!showBalance" x-cloak></span>
                </button>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-6">
                <a href="{{ route('transfer.index') }}" class="p-3 rounded-xl border border-gray-200 hover:bg-gray-50 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-[#1400FB]/10 text-[#1400FB] flex items-center justify-center">
                        <span class="fa fa-download"></span>
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-gray-900">Withdraw</div>
                        <div class="text-xs text-gray-500 truncate">To settlement account</div>
                    </div>
                </a>

                <a href="{{ route('gateway.transactions') }}" class="p-3 rounded-xl border border-gray-200 hover:bg-gray-50 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-[#1400FB]/10 text-[#1400FB] flex items-center justify-center">
                        <span class="fa fa-receipt"></span>
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-gray-900">Transactions</div>
                        <div class="text-xs text-gray-500 truncate">Gateway payments</div>
                    </div>
                </a>

                <a href="{{ route('gateway.virtual-accounts') }}" class="p-3 rounded-xl border border-gray-200 hover:bg-gray-50 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-[#1400FB]/10 text-[#1400FB] flex items-center justify-center">
                        <span class="fa fa-building-columns"></span>
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-gray-900">Virtual Account</div>
                        <div class="text-xs text-gray-500 truncate">Collect via transfer</div>
                    </div>
                </a>

                <a href="{{ route('gateway.api-keys') }}" class="p-3 rounded-xl border border-gray-200 hover:bg-gray-50 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-[#1400FB]/10 text-[#1400FB] flex items-center justify-center">
                        <span class="fa fa-key"></span>
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-gray-900">API Keys</div>
                        <div class="text-xs text-gray-500 truncate">{{ $apiKeysCount }} active</div>
                    </div>
                </a>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-500 font-medium">Settlement Account</div>
                        <div class="mt-1 text-base font-semibold text-gray-900">
                            {{ $hasSettlement ? (optional($kyb)->settlement_bank_name ?? 'Settlement') : 'Not configured' }}
                        </div>
                    </div>
                    <div class="text-xs font-semibold px-2 py-1 rounded-full {{ $hasSettlement ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                        {{ $hasSettlement ? 'READY' : 'ACTION' }}
                    </div>
                </div>

                <div class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between gap-3">
                        <div class="text-gray-500">Account Name</div>
                        <div class="text-gray-900 font-medium text-right">{{ $hasSettlement ? (optional($kyb)->settlement_account_name ?? '-') : '-' }}</div>
                    </div>
                    <div class="flex justify-between gap-3">
                        <div class="text-gray-500">Account Number</div>
                        <div class="text-gray-900 font-medium text-right">{{ $hasSettlement ? (optional($kyb)->settlement_account_number ?? '-') : '-' }}</div>
                    </div>
                </div>

                <div class="mt-5">
                    <a href="{{ route('onboarding') }}" class="inline-flex w-full items-center justify-center px-4 py-2 rounded-lg bg-gray-900 text-white font-semibold hover:bg-black">
                        Update Settlement <span class="fa fa-arrow-right ml-2"></span>
                    </a>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-500 font-medium">Virtual Account</div>
                        <div class="mt-1 text-base font-semibold text-gray-900">
                            {{ $virtualAccount?->bank_name ?? 'Not generated' }}
                        </div>
                    </div>
                    <div class="text-xs font-semibold px-2 py-1 rounded-full {{ $virtualAccount ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                        {{ $virtualAccount ? 'ACTIVE' : 'NONE' }}
                    </div>
                </div>

                <div class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between gap-3">
                        <div class="text-gray-500">Account Name</div>
                        <div class="text-gray-900 font-medium text-right">{{ $virtualAccount?->account_name ?? '-' }}</div>
                    </div>
                    <div class="flex justify-between gap-3">
                        <div class="text-gray-500">Account Number</div>
                        <div class="text-gray-900 font-medium text-right">{{ $virtualAccount?->account_number ?? '-' }}</div>
                    </div>
                </div>

                <div class="mt-5">
                    <a href="{{ route('gateway.virtual-accounts') }}" class="inline-flex w-full items-center justify-center px-4 py-2 rounded-lg bg-[#1400FB] text-white font-semibold hover:bg-[#2E2380]">
                        Manage Virtual Account <span class="fa fa-arrow-right ml-2"></span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Recent Gateway Transactions</h3>
                <a href="{{ route('gateway.transactions') }}" class="text-sm font-semibold text-[#1400FB] hover:text-[#2E2380]">
                    View all <span class="fa fa-chevron-right ml-1"></span>
                </a>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs uppercase text-gray-500">
                        <tr class="border-b">
                            <th class="py-2 text-left">Reference</th>
                            <th class="py-2 text-left">Amount</th>
                            <th class="py-2 text-left">Status</th>
                            <th class="py-2 text-left">Method</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($recentGatewayTransactions as $tx)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 font-medium text-gray-900">{{ $tx->reference }}</td>
                                <td class="py-3">₦{{ formatNumber(($tx->amount ?? 0) / 100) }}</td>
                                <td class="py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                                        @if($tx->status === 'success') bg-green-100 text-green-700
                                        @elseif($tx->status === 'failed') bg-red-100 text-red-700
                                        @elseif(in_array($tx->status, ['unpaid','paying'])) bg-yellow-100 text-yellow-700
                                        @else bg-gray-100 text-gray-700 @endif
                                    ">
                                        {{ strtoupper($tx->status) }}
                                    </span>
                                </td>
                                <td class="py-3 text-gray-700">{{ strtoupper($tx->method) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-10 text-center text-gray-500">
                                    No gateway transactions yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Recent Withdrawals</h3>
                <a href="{{ route('transfer.index') }}" class="text-sm font-semibold text-[#1400FB] hover:text-[#2E2380]">
                    View all <span class="fa fa-chevron-right ml-1"></span>
                </a>
            </div>

            <div class="mt-4 space-y-3">
                @forelse ($recentWithdrawals as $w)
                    <a href="{{ route('transfer.receipt', $w->hash_id) }}" class="block p-4 rounded-xl border border-gray-200 hover:bg-gray-50">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-gray-900 truncate">
                                    {{ $w->bank_name ?? 'Bank' }} • {{ $w->account_number ?? '' }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1 truncate">
                                    {{ $w->account_name ?? '' }}{{ $w->description ? ' • ' . $w->description : '' }}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-bold text-gray-900">
                                    ₦{{ formatNumber(($w->amount ?? 0) + ($w->fee ?? 0)) }}
                                </div>
                                <div class="text-xs mt-1">
                                    <span class="px-2 py-1 rounded-full font-semibold
                                        @if($w->status === 'success') bg-green-100 text-green-700
                                        @elseif($w->status === 'failed') bg-red-100 text-red-700
                                        @else bg-yellow-100 text-yellow-700 @endif
                                    ">
                                        {{ strtoupper($w->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 mt-3">
                            {{ optional($w->created_at)->format('d M Y, h:i A') }}
                        </div>
                    </a>
                @empty
                    <div class="py-10 text-center text-gray-500">
                        No withdrawals yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<x-slot:headerTitle>Dashboard</x-slot:headerTitle>
