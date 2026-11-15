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

    @if (!$isActivated)
        <div class="relative overflow-hidden rounded-3xl bg-[#1400FB] p-6 lg:p-8 text-white shadow-xl border border-blue-400/20"
             style="background: linear-gradient(135deg, #0b0075 0%, #1400FB 55%, #3a2cf5 100%);">
            <!-- Background Glow Circles -->
            <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full blur-3xl pointer-events-none" style="background: rgba(255, 255, 255, 0.1);"></div>
            <div class="absolute -left-16 -bottom-16 w-64 h-64 rounded-full blur-3xl pointer-events-none" style="background: rgba(99, 102, 241, 0.25);"></div>

            <div class="relative z-10">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div class="max-w-2xl">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider text-blue-100 border mb-3"
                             style="background-color: rgba(255, 255, 255, 0.15); border-color: rgba(255, 255, 255, 0.25);">
                            <i class="fa fa-bolt" style="color: #fcd34d;"></i>
                            <span>Business Activation • Step {{ $completedStepsCount }} of 5</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                            Activate your business
                        </h2>
                        <p class="text-blue-100 text-sm sm:text-base mt-2 leading-relaxed" style="color: #dbeafe;">
                            Welcome inside PayZenter! You're only a few quick details away from unlocking live customer collections, generating instant virtual accounts, and withdrawing your funds directly.
                        </p>
                    </div>

                    <div class="flex-shrink-0">
                        @if ($kybSubmitted && !$hasPin)
                            <a href="{{ route('onboarding') }}" class="inline-flex items-center justify-center gap-3 px-6 py-4 rounded-2xl bg-white text-[#1400FB] font-bold text-base hover:bg-blue-50 shadow-lg hover:shadow-2xl transition-all duration-200 transform hover:-translate-y-0.5">
                                <span>Set Transaction PIN</span>
                                <i class="fa fa-lock text-sm"></i>
                            </a>
                        @elseif ($kybSubmitted)
                            <div class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl border text-white font-semibold text-sm"
                                 style="background-color: rgba(255, 255, 255, 0.15); border-color: rgba(255, 255, 255, 0.25);">
                                <i class="fa fa-clock" style="color: #fcd34d;"></i>
                                <span>Verification in Review</span>
                            </div>
                        @else
                            <a href="{{ route('onboarding') }}" class="inline-flex items-center justify-center gap-3 px-7 py-4 rounded-2xl bg-white text-[#1400FB] font-extrabold text-base hover:bg-blue-50 shadow-lg hover:shadow-2xl transition-all duration-200 transform hover:-translate-y-0.5">
                                <span>Activate Your Business</span>
                                <i class="fa fa-arrow-right text-sm"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Progress Bar Section -->
                <div class="mt-6 pt-5 border-t" style="border-top-color: rgba(255, 255, 255, 0.18);">
                    <div class="flex items-center justify-between text-xs sm:text-sm font-semibold mb-2">
                        <span class="uppercase tracking-wider text-[11px]" style="color: #bfdbfe;">Activation Progress</span>
                        <span class="text-white font-bold">{{ $activationProgress }}% Complete</span>
                    </div>
                    <div class="w-full rounded-full h-3 p-0.5 overflow-hidden" style="background-color: rgba(0, 0, 0, 0.28);">
                        <div 
                            class="h-2 rounded-full transition-all duration-700 ease-out shadow-sm"
                            style="width: {{ $activationProgress }}%; background: linear-gradient(90deg, #34d399 0%, #2dd4bf 50%, #67e8f9 100%);"
                        ></div>
                    </div>

                    <!-- Step badges checklist -->
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 sm:gap-3 mt-4 text-xs font-medium">
                        <div class="flex items-center gap-2 px-3 py-2 rounded-xl border text-white"
                             style="background-color: rgba(255, 255, 255, 0.14); border-color: rgba(255, 255, 255, 0.2);">
                            <i class="fa fa-check-circle" style="color: #34d399;"></i>
                            <span class="truncate">Account Created</span>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-2 rounded-xl border"
                             style="{{ $hasPersonalInfo ? 'background-color: rgba(255, 255, 255, 0.14); border-color: rgba(255, 255, 255, 0.2); color: #ffffff;' : 'background-color: rgba(0, 0, 0, 0.22); border-color: rgba(255, 255, 255, 0.1); color: #bfdbfe;' }}">
                            <i class="fa {{ $hasPersonalInfo ? 'fa-check-circle' : 'fa-circle-dot' }}" style="color: {{ $hasPersonalInfo ? '#34d399' : 'rgba(147, 197, 253, 0.7)' }};"></i>
                            <span class="truncate">Personal Info</span>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-2 rounded-xl border"
                             style="{{ $hasBusinessInfo ? 'background-color: rgba(255, 255, 255, 0.14); border-color: rgba(255, 255, 255, 0.2); color: #ffffff;' : 'background-color: rgba(0, 0, 0, 0.22); border-color: rgba(255, 255, 255, 0.1); color: #bfdbfe;' }}">
                            <i class="fa {{ $hasBusinessInfo ? 'fa-check-circle' : 'fa-circle-dot' }}" style="color: {{ $hasBusinessInfo ? '#34d399' : 'rgba(147, 197, 253, 0.7)' }};"></i>
                            <span class="truncate">Business Profile</span>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-2 rounded-xl border"
                             style="{{ $hasSettlement ? 'background-color: rgba(255, 255, 255, 0.14); border-color: rgba(255, 255, 255, 0.2); color: #ffffff;' : 'background-color: rgba(0, 0, 0, 0.22); border-color: rgba(255, 255, 255, 0.1); color: #bfdbfe;' }}">
                            <i class="fa {{ $hasSettlement ? 'fa-check-circle' : 'fa-circle-dot' }}" style="color: {{ $hasSettlement ? '#34d399' : 'rgba(147, 197, 253, 0.7)' }};"></i>
                            <span class="truncate">Settlement Bank</span>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-2 rounded-xl border"
                             style="{{ ($kybSubmitted && $hasPin) ? 'background-color: rgba(255, 255, 255, 0.14); border-color: rgba(255, 255, 255, 0.2); color: #ffffff;' : 'background-color: rgba(0, 0, 0, 0.22); border-color: rgba(255, 255, 255, 0.1); color: #bfdbfe;' }}">
                            <i class="fa {{ ($kybSubmitted && $hasPin) ? 'fa-check-circle' : 'fa-circle-dot' }}" style="color: {{ ($kybSubmitted && $hasPin) ? '#34d399' : 'rgba(147, 197, 253, 0.7)' }};"></i>
                            <span class="truncate">Docs & PIN</span>
                        </div>
                    </div>
                </div>
            </div>
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

    <!-- THREE KEY GATEWAY METRICS WITH BOX SHADOW (USER / MERCHANT SIDE) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- 1. Transaction Count (Today) -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 transition duration-200 hover:translate-y-[-2px]" style="box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-full border border-indigo-100">
                    Today's Activity
                </span>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
                    <span class="fa fa-receipt"></span>
                </div>
            </div>
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Transaction Count</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight">
                    {{ number_format($todayTransactionsCount) }}
                </span>
                <span class="text-xs font-semibold text-gray-500">today</span>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100 text-xs text-gray-600 flex items-center justify-between">
                <span>Received today so far</span>
                <span class="font-bold text-emerald-600">{{ number_format($todaySuccessfulCount) }} successful</span>
            </div>
        </div>

        <!-- 2. Today's Payments (Amount Received) -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 transition duration-200 hover:translate-y-[-2px]" style="box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-100">
                    Live Today
                </span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                    <span class="fa fa-money-bill-wave"></span>
                </div>
            </div>
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Today's Payments</div>
            <div class="mt-2 flex items-baseline gap-1">
                <span class="text-3xl lg:text-4xl font-extrabold text-emerald-600 tracking-tight">
                    ₦{{ number_format($todayPaymentsAmount, 2) }}
                </span>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100 text-xs text-gray-600 flex items-center justify-between">
                <span>Exact amount received today on website</span>
                <span class="text-gray-400 font-mono text-[11px]">{{ now()->format('d M') }}</span>
            </div>
        </div>

        <!-- 3. Virtual Accounts (Generated / Volume) -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 transition duration-200 hover:translate-y-[-2px]" style="box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700 bg-blue-50 px-2.5 py-1 rounded-full border border-blue-100">
                    Virtual Accounts
                </span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                    <span class="fa fa-building-columns"></span>
                </div>
            </div>
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Virtual Accounts Generated</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight">
                    {{ number_format($totalVirtualAccounts) }}
                </span>
                <span class="text-xs font-semibold text-gray-500">accounts</span>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100 text-xs text-gray-600 flex items-center justify-between">
                <span>Used to collect via PayZenter</span>
                <span class="font-bold text-gray-900">₦{{ number_format($virtualAccountsVolume, 2) }}</span>
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
