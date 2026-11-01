<div class="p-4 max-w-7xl mx-auto space-y-6">
    <x-loading-spinner wire:target="generate" />

    <!-- Top Card: Dedicated Merchant Virtual Account -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-100 pb-4 mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Virtual Bank Accounts</h2>
                <p class="text-sm text-gray-500 mt-0.5">Dedicated virtual bank accounts for accepting payments and bank transfers</p>
            </div>
            <div class="flex items-center gap-2">
                @if($palmpayEnabled)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        PalmPay Enabled
                    </span>
                @endif
                @if($udaraEnabled)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Udara360 Enabled
                    </span>
                @endif
                @if(!$palmpayEnabled && !$udaraEnabled && !$flwEnabled)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-rose-100 text-rose-800">
                        Virtual Accounts Disabled by Admin
                    </span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Current Main Merchant Account -->
            <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-5 relative overflow-hidden">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Merchant Primary Account</span>
                    @if($virtualAccount)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">Active</span>
                    @endif
                </div>

                @if($virtualAccount)
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
                            <span class="text-xs text-slate-500 font-medium">Bank</span>
                            <span class="text-sm font-bold text-slate-900">{{ $virtualAccount->bank_name }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
                            <span class="text-xs text-slate-500 font-medium">Account Number</span>
                            <div class="flex items-center gap-2">
                                <span class="text-base font-extrabold text-indigo-900 tracking-wide font-mono">{{ $virtualAccount->account_number }}</span>
                                <button onclick="navigator.clipboard.writeText('{{ $virtualAccount->account_number }}'); alert('Account number copied!');" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium bg-indigo-50 hover:bg-indigo-100 px-2 py-0.5 rounded transition">Copy</button>
                            </div>
                        </div>
                        <div class="flex justify-between items-center py-1">
                            <span class="text-xs text-slate-500 font-medium">Account Name</span>
                            <span class="text-sm font-semibold text-slate-800">{{ $virtualAccount->account_name }}</span>
                        </div>
                    </div>
                @else
                    <div class="py-6 text-center text-sm text-slate-500">
                        <svg class="w-8 h-8 mx-auto text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        No dedicated merchant virtual account generated yet.
                    </div>
                @endif
            </div>

            <!-- Generate New Virtual Account Form -->
            <div class="border border-gray-200 rounded-2xl p-5 bg-white">
                <div class="font-bold text-gray-900 text-base mb-3">Generate Dedicated Account</div>
                
                <div class="space-y-4">
                    <div>
                        <x-input-label value="Select Provider" class="text-xs font-semibold text-gray-700" />
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-1.5">
                            @if($palmpayEnabled)
                                <label class="flex items-center gap-2 p-3 border rounded-xl cursor-pointer transition {{ $provider === 'palmpay' ? 'border-emerald-600 bg-emerald-50/50 text-emerald-900 font-semibold' : 'border-gray-200 text-gray-700 hover:border-gray-300' }}">
                                    <input type="radio" wire:model.live="provider" value="palmpay" class="text-emerald-600 focus:ring-emerald-500">
                                    <span>PalmPay</span>
                                </label>
                            @endif
                            @if($udaraEnabled)
                                <label class="flex items-center gap-2 p-3 border rounded-xl cursor-pointer transition {{ $provider === 'udara360' ? 'border-indigo-600 bg-indigo-50/50 text-indigo-900 font-semibold' : 'border-gray-200 text-gray-700 hover:border-gray-300' }}">
                                    <input type="radio" wire:model.live="provider" value="udara360" class="text-indigo-600 focus:ring-indigo-500">
                                    <span>Udara360</span>
                                </label>
                            @endif
                            @if($flwEnabled)
                                <label class="flex items-center gap-2 p-3 border rounded-xl cursor-pointer transition {{ $provider === 'flutterwave' ? 'border-amber-600 bg-amber-50/50 text-amber-900 font-semibold' : 'border-gray-200 text-gray-700 hover:border-gray-300' }}">
                                    <input type="radio" wire:model.live="provider" value="flutterwave" class="text-amber-600 focus:ring-amber-500">
                                    <span>Flutterwave</span>
                                </label>
                            @endif
                        </div>
                        @if(!$palmpayEnabled && !$udaraEnabled && !$flwEnabled)
                            <div class="text-xs text-rose-500 mt-1 font-medium">All virtual account providers are currently disabled by administrator.</div>
                        @endif
                    </div>

                    <div>
                        <x-input-label value="BVN (Verification)" class="text-xs font-semibold text-gray-700" />
                        <x-text-input wire:model.defer="bvn" placeholder="Enter 11-digit BVN" class="w-full mt-1 font-mono text-sm" />
                        <x-input-error :messages="$errors->get('bvn')" class="mt-1" />
                    </div>

                    <div>
                        <x-primary-button wire:click="generate" :disabled="!$palmpayEnabled && !$udaraEnabled && !$flwEnabled" class="w-full justify-center rounded-xl py-3 text-sm font-semibold shadow-sm">
                            Generate {{ ucfirst($provider) }} Virtual Account
                        </x-primary-button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- All Customer & API Virtual Accounts Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="text-base font-bold text-gray-900">All Created Virtual Accounts</h3>
                <p class="text-xs text-gray-500">Accounts generated for customers via API or checkout</p>
            </div>
            <span class="text-xs font-medium text-gray-500 bg-gray-100 px-3 py-1 rounded-full w-fit">
                Total: {{ $allAccounts->total() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-gray-50 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3">Account Details</th>
                        <th class="px-6 py-3">Bank</th>
                        <th class="px-6 py-3">Customer / Email</th>
                        <th class="px-6 py-3">Provider</th>
                        <th class="px-6 py-3">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white font-medium text-gray-800">
                    @forelse($allAccounts as $acc)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-gray-900 text-sm tracking-wide">{{ $acc->account_number }}</span>
                                    <button onclick="navigator.clipboard.writeText('{{ $acc->account_number }}'); alert('Copied {{ $acc->account_number }}!');" class="text-gray-400 hover:text-indigo-600 transition" title="Copy Account Number">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    </button>
                                </div>
                                <div class="text-xs text-gray-500 font-normal mt-0.5">{{ $acc->account_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-semibold text-gray-900">{{ $acc->bank_name ?? 'Virtual Bank' }}</span>
                                @if($acc->bank_code)
                                    <div class="text-xs font-mono text-gray-400">{{ $acc->bank_code }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs font-semibold text-gray-900">{{ $acc->customer_email ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500 font-mono">{{ $acc->customer_phone ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(strtolower($acc->provider ?? '') === 'palmpay')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        PalmPay
                                    </span>
                                @elseif(strtolower($acc->provider ?? '') === 'udara360')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        Udara360
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ ucfirst($acc->provider ?? 'PalmPay') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                {{ $acc->created_at ? $acc->created_at->format('M d, Y H:i') : 'N/A' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-400">
                                No customer virtual accounts created yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($allAccounts->hasPages())
            <div class="px-6 py-3 border-t border-gray-100 bg-gray-50">
                {{ $allAccounts->links() }}
            </div>
        @endif
    </div>

    <x-slot:headerTitle>
        Virtual Accounts
    </x-slot:headerTitle>
</div>
