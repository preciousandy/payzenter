<div class="p-3 sm:p-5 lg:p-6 bg-slate-100 min-h-screen font-sans text-slate-800" style="background-color: #f8fafc; color: #0f172a;">
    @can('view_dashboard')

    <!-- TOP HERO BANNER -->
    <div class="rounded-2xl p-5 sm:p-6 mb-6 shadow-xl border border-slate-800" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%); color: #ffffff;">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider mb-2 border" style="background-color: rgba(99, 102, 241, 0.2); color: #a5b4fc; border-color: rgba(99, 102, 241, 0.3);">
                    <span class="w-2 h-2 rounded-full" style="background-color: #10b981;"></span>
                    PayZenter Payment Gateway
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight" style="color: #ffffff;">
                    Gateway Executive Overview & Analytics
                </h1>
                <p class="mt-1 text-xs sm:text-sm max-w-2xl" style="color: #cbd5e1;">
                    Real-time payment gateway monitoring, virtual bank accounts, merchant settlements & transaction fee analytics.
                </p>
            </div>

            <!-- Quick Action Stat Badges -->
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.kyc-verification') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl border transition-all" style="background-color: rgba(255, 255, 255, 0.1); border-color: rgba(255, 255, 255, 0.15); color: #ffffff;">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center text-lg" style="background-color: rgba(245, 158, 11, 0.2); color: #fbbf24;">
                        <i class="fa fa-id-card"></i>
                    </div>
                    <div>
                        <div class="text-xs" style="color: #cbd5e1;">KYC Verified</div>
                        <div class="text-sm font-bold" style="color: #ffffff;">{{ number_format($kycVerifiedUsers) }} / {{ number_format($totalUsers) }}</div>
                    </div>
                </a>

                <div class="flex items-center gap-3 px-4 py-3 rounded-xl border" style="background-color: rgba(255, 255, 255, 0.1); border-color: rgba(255, 255, 255, 0.15); color: #ffffff;">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center text-lg" style="background-color: rgba(16, 185, 129, 0.2); color: #34d399;">
                        <i class="fa fa-wallet"></i>
                    </div>
                    <div>
                        <div class="text-xs" style="color: #cbd5e1;">Total Wallet Pool</div>
                        <div class="text-sm font-bold" style="color: #34d399;">₦{{ number_format($totalWalletBalance, 2) }}</div>
                    </div>
                </div>

                <div class="flex items-center gap-3 px-4 py-3 rounded-xl border" style="background-color: rgba(255, 255, 255, 0.1); border-color: rgba(255, 255, 255, 0.15); color: #ffffff;">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center text-lg" style="background-color: rgba(99, 102, 241, 0.2); color: #a5b4fc;">
                        <i class="fa fa-university"></i>
                    </div>
                    <div>
                        <div class="text-xs" style="color: #cbd5e1;">Virtual Accounts</div>
                        <div class="text-sm font-bold" style="color: #ffffff;">{{ number_format($totalVirtualAccounts) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DASHBOARD METRICS GRID -->
    <div class="space-y-6">

        <!-- 1. FEE REVENUE CARDS -->
        <div>
            <h2 class="text-lg font-bold mb-3 flex items-center gap-2" style="color: #0f172a;">
                <i class="fa fa-money-bill-wave text-indigo-600"></i> Transaction Fee Revenues
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- All Time Fees -->
                <div class="p-5 rounded-2xl shadow-lg relative overflow-hidden" style="background: linear-gradient(135deg, #3730a3 0%, #1e1b4b 100%); color: #ffffff;">
                    <div class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #c7d2fe;">All Time Fee Revenue</div>
                    <div class="text-2xl font-extrabold tracking-tight my-1" style="color: #ffffff;">
                        ₦{{ number_format($all_time_fees, 2) }}
                    </div>
                    <div class="text-xs flex items-center gap-1 mt-2 pt-2 border-t" style="color: #a5b4fc; border-color: rgba(199, 210, 254, 0.2);">
                        <i class="fa fa-chart-line text-emerald-300"></i> Platform lifetime earnings
                    </div>
                </div>

                <!-- Today's Fees -->
                <div class="p-5 rounded-2xl shadow-sm border" style="background-color: #ffffff; color: #0f172a; border-color: #e2e8f0;">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider" style="color: #64748b;">Today's Fees</span>
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: #fef3c7; color: #d97706;">
                            <i class="fa fa-sun text-sm"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-extrabold my-1" style="color: #0f172a;">
                        ₦{{ number_format($todays_fees, 2) }}
                    </div>
                    <div class="text-xs" style="color: #64748b;">
                        Collected in the last 24 hours
                    </div>
                </div>

                <!-- This Week's Fees -->
                <div class="p-5 rounded-2xl shadow-sm border" style="background-color: #ffffff; color: #0f172a; border-color: #e2e8f0;">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider" style="color: #64748b;">This Week's Fees</span>
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: #d1fae5; color: #059669;">
                            <i class="fa fa-calendar-week text-sm"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-extrabold my-1" style="color: #0f172a;">
                        ₦{{ number_format($this_weeks_fees, 2) }}
                    </div>
                    <div class="text-xs" style="color: #64748b;">
                        Accumulated this week
                    </div>
                </div>

                <!-- This Month's Fees -->
                <div class="p-5 rounded-2xl shadow-sm border" style="background-color: #ffffff; color: #0f172a; border-color: #e2e8f0;">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider" style="color: #64748b;">This Month's Fees</span>
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: #dbeafe; color: #2563eb;">
                            <i class="fa fa-calendar-alt text-sm"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-extrabold my-1" style="color: #0f172a;">
                        ₦{{ number_format($this_months_fees, 2) }}
                    </div>
                    <div class="text-xs" style="color: #64748b;">
                        Accumulated this month
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. PAYMENT GATEWAY CORE METRICS -->
        <div>
            <h2 class="text-lg font-bold mb-3 flex items-center gap-2" style="color: #0f172a;">
                <i class="fa fa-credit-card text-indigo-600"></i> Payment Gateway Processing
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Gateway Volume -->
                <div class="p-5 rounded-2xl shadow-sm border" style="background-color: #ffffff; color: #0f172a; border-color: #e2e8f0;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg" style="background-color: #e0e7ff; color: #4338ca;">
                            <i class="fa fa-money-check-alt"></i>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold" style="background-color: #e0e7ff; color: #3730a3;">
                            Processed
                        </span>
                    </div>
                    <div class="text-xs font-semibold uppercase" style="color: #64748b;">Total Gateway Volume</div>
                    <div class="text-2xl font-extrabold mt-1" style="color: #0f172a;">
                        ₦{{ number_format($totalGatewayVolume, 2) }}
                    </div>
                    <div class="mt-2 text-xs pt-2 border-t flex justify-between" style="color: #64748b; border-color: #f1f5f9;">
                        <span>Gateway Fees: <strong style="color: #0f172a;">₦{{ number_format($totalGatewayFees, 2) }}</strong></span>
                    </div>
                </div>

                <!-- Total Gateway Transactions -->
                <div class="p-5 rounded-2xl shadow-sm border" style="background-color: #ffffff; color: #0f172a; border-color: #e2e8f0;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg" style="background-color: #d1fae5; color: #059669;">
                            <i class="fa fa-exchange-alt"></i>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold" style="background-color: #d1fae5; color: #065f46;">
                            {{ $gatewaySuccessRate }}% success
                        </span>
                    </div>
                    <div class="text-xs font-semibold uppercase" style="color: #64748b;">Total Gateway Requests</div>
                    <div class="text-2xl font-extrabold mt-1" style="color: #0f172a;">
                        {{ number_format($totalGatewayTransactions) }}
                    </div>
                    <div class="w-full rounded-full h-1.5 mt-3 overflow-hidden" style="background-color: #f1f5f9;">
                        <div class="h-1.5 rounded-full" style="background-color: #059669; width: {{ min($gatewaySuccessRate, 100) }}%;"></div>
                    </div>
                </div>

                <!-- Successful Collections -->
                <div class="p-5 rounded-2xl shadow-sm border" style="background-color: #ffffff; color: #0f172a; border-color: #e2e8f0;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg" style="background-color: #ccfbf1; color: #0d9488;">
                            <i class="fa fa-check-circle"></i>
                        </div>
                        <span class="text-xs font-bold" style="color: #0f766e;">Paid</span>
                    </div>
                    <div class="text-xs font-semibold uppercase" style="color: #64748b;">Successful Collections</div>
                    <div class="text-2xl font-extrabold mt-1" style="color: #0f172a;">
                        {{ number_format($successfulGatewayTransactions) }}
                    </div>
                    <div class="mt-2 text-xs pt-2 border-t" style="color: #64748b; border-color: #f1f5f9;">
                        Successful merchant pay-ins
                    </div>
                </div>

                <!-- Pending & Failed Sessions -->
                <div class="p-5 rounded-2xl shadow-sm border" style="background-color: #ffffff; color: #0f172a; border-color: #e2e8f0;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg" style="background-color: #fee2e2; color: #dc2626;">
                            <i class="fa fa-clock"></i>
                        </div>
                        <span class="text-xs font-bold" style="color: #64748b;">In-Progress / Expired</span>
                    </div>
                    <div class="text-xs font-semibold uppercase" style="color: #64748b;">Pending & Failed</div>
                    <div class="flex items-center justify-between mt-2 pt-1">
                        <div>
                            <span class="text-xs block" style="color: #94a3b8;">Pending</span>
                            <span class="text-lg font-bold" style="color: #d97706;">{{ number_format($pendingGatewayTransactions) }}</span>
                        </div>
                        <div class="h-6 w-px" style="background-color: #e2e8f0;"></div>
                        <div>
                            <span class="text-xs block" style="color: #94a3b8;">Failed</span>
                            <span class="text-lg font-bold" style="color: #dc2626;">{{ number_format($failedGatewayTransactions) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. VIRTUAL ACCOUNTS ECOSYSTEM (PALMPAY & UDARA360) -->
        <div>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                <h2 class="text-lg font-bold flex items-center gap-2" style="color: #0f172a;">
                    <i class="fa fa-university text-indigo-600"></i> Virtual Bank Accounts
                </h2>
                <a href="{{ route('admin.settings') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold flex items-center gap-1">
                    Manage Providers & API Settings <i class="fa fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Virtual Accounts -->
                <div class="p-5 rounded-2xl border shadow-sm" style="background-color: #ffffff; color: #0f172a; border-color: #e2e8f0;">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-base" style="background-color: #e0e7ff; color: #4338ca;">
                            <i class="fa fa-layer-group"></i>
                        </div>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full" style="background-color: #e0e7ff; color: #3730a3;">Total Accounts</span>
                    </div>
                    <div class="text-xs font-medium" style="color: #64748b;">Generated Accounts</div>
                    <div class="text-2xl font-extrabold mt-0.5" style="color: #0f172a;">{{ number_format($totalVirtualAccounts) }}</div>
                    <div class="mt-3 pt-2 border-t text-xs flex justify-between" style="border-color: #f1f5f9; color: #64748b;">
                        <span>Dedicated Primary:</span>
                        <strong style="color: #0f172a;">{{ number_format($dedicatedMerchantAccounts) }}</strong>
                    </div>
                </div>

                <!-- PalmPay Virtual Accounts -->
                <div class="p-5 rounded-2xl border shadow-sm" style="background-color: #ffffff; color: #0f172a; border-color: #e2e8f0;">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-base" style="background-color: #d1fae5; color: #059669;">
                            <i class="fa fa-shield-alt"></i>
                        </div>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full" style="background-color: #d1fae5; color: #065f46;">PalmPay</span>
                    </div>
                    <div class="text-xs font-medium" style="color: #64748b;">PalmPay Accounts</div>
                    <div class="text-2xl font-extrabold mt-0.5" style="color: #059669;">{{ number_format($palmpayVirtualAccounts) }}</div>
                    <div class="mt-3 pt-2 border-t text-xs flex justify-between" style="border-color: #f1f5f9; color: #64748b;">
                        <span>Provider Status:</span>
                        <span class="font-bold text-emerald-600">Active</span>
                    </div>
                </div>

                <!-- Udara360 Virtual Accounts -->
                <div class="p-5 rounded-2xl border shadow-sm" style="background-color: #ffffff; color: #0f172a; border-color: #e2e8f0;">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-base" style="background-color: #ede9fe; color: #7c3aed;">
                            <i class="fa fa-bolt"></i>
                        </div>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full" style="background-color: #ede9fe; color: #5b21b6;">Udara360</span>
                    </div>
                    <div class="text-xs font-medium" style="color: #64748b;">Udara360 Accounts</div>
                    <div class="text-2xl font-extrabold mt-0.5" style="color: #7c3aed;">{{ number_format($udara360VirtualAccounts) }}</div>
                    <div class="mt-3 pt-2 border-t text-xs flex justify-between" style="border-color: #f1f5f9; color: #64748b;">
                        <span>Provider Status:</span>
                        <span class="font-bold text-indigo-600">Integrated</span>
                    </div>
                </div>

                <!-- Flutterwave Accounts -->
                <div class="p-5 rounded-2xl border shadow-sm" style="background-color: #ffffff; color: #0f172a; border-color: #e2e8f0;">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-base" style="background-color: #fef3c7; color: #d97706;">
                            <i class="fa fa-globe"></i>
                        </div>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full" style="background-color: #fef3c7; color: #92400e;">Flutterwave</span>
                    </div>
                    <div class="text-xs font-medium" style="color: #64748b;">Flutterwave Accounts</div>
                    <div class="text-2xl font-extrabold mt-0.5" style="color: #b45309;">{{ number_format($flutterwaveVirtualAccounts) }}</div>
                    <div class="mt-3 pt-2 border-t text-xs flex justify-between" style="border-color: #f1f5f9; color: #64748b;">
                        <span>Fallback Provider:</span>
                        <span class="font-bold text-slate-700">Wema Bank</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. SETTLEMENTS & PAYOUTS -->
        <div>
            <h2 class="text-lg font-bold mb-3 flex items-center gap-2" style="color: #0f172a;">
                <i class="fa fa-share-square text-indigo-600"></i> Merchant Settlements & Payouts
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Total Payouts Settled -->
                <div class="p-5 rounded-2xl border shadow-sm" style="background-color: #ffffff; color: #0f172a; border-color: #e2e8f0;">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-base" style="background-color: #ecfdf5; color: #047857;">
                            <i class="fa fa-check-double"></i>
                        </div>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full" style="background-color: #ecfdf5; color: #065f46;">Completed Settlements</span>
                    </div>
                    <div class="text-xs font-medium" style="color: #64748b;">Total Payout Volume</div>
                    <div class="text-2xl font-extrabold mt-0.5" style="color: #0f172a;">₦{{ number_format($totalPayoutVolume, 2) }}</div>
                    <div class="mt-3 pt-2 border-t text-xs flex justify-between" style="border-color: #f1f5f9; color: #64748b;">
                        <span>Successful Transfers: <strong style="color: #0f172a;">{{ number_format($successfulPayouts) }}</strong></span>
                        <span>Total Requests: <strong style="color: #0f172a;">{{ number_format($totalPayoutTransactions) }}</strong></span>
                    </div>
                </div>

                <!-- Pending Payout Queue -->
                <div class="p-5 rounded-2xl border shadow-sm" style="background-color: #ffffff; color: #0f172a; border-color: #e2e8f0;">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-base" style="background-color: #fffbeb; color: #b45309;">
                            <i class="fa fa-spinner"></i>
                        </div>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full" style="background-color: #fffbeb; color: #92400e;">Settlement Queue</span>
                    </div>
                    <div class="text-xs font-medium" style="color: #64748b;">Pending Payout Transfers</div>
                    <div class="text-2xl font-extrabold mt-0.5" style="color: #b45309;">{{ number_format($pendingPayouts) }}</div>
                    <div class="mt-3 pt-2 border-t text-xs flex justify-between" style="border-color: #f1f5f9; color: #64748b;">
                        <span>Processing Channel:</span>
                        <strong style="color: #0f172a;">NIP / PalmPay Transfer API</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. DAILY GATEWAY ACTIVITY CHARTS -->
        <div class="p-5 sm:p-6 rounded-2xl border shadow-sm" style="background-color: #ffffff; color: #0f172a; border-color: #e2e8f0;">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b mb-4" style="border-color: #f1f5f9;">
                <div>
                    <h3 class="text-lg font-bold flex items-center gap-2" style="color: #0f172a;">
                        <i class="fa fa-chart-line text-indigo-600"></i> Daily Gateway Activity Charts (Last 14 Days)
                    </h3>
                    <p class="text-xs mt-0.5" style="color: #64748b;">
                        Processed Volume: <strong style="color: #0f172a;">₦{{ number_format($totalGatewayVolume, 2) }}</strong>
                        | Transactions: <strong style="color: #0f172a;">{{ number_format($totalGatewayTransactions) }}</strong>
                        | Success Rate: <strong style="color: #0f172a;">{{ $gatewaySuccessRate }}%</strong>
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-xs font-bold uppercase mb-2" style="color: #475569;">Daily Transaction Requests</h4>
                    <div class="p-3 rounded-xl border" style="background-color: #f8fafc; border-color: #f1f5f9;">
                        <x-display-chart :chartLabels="$transactionDates" :chartData="$dailyTransactions" title="Daily Count" />
                    </div>
                </div>

                <div>
                    <h4 class="text-xs font-bold uppercase mb-2" style="color: #475569;">Daily Processed Volume (₦)</h4>
                    <div class="p-3 rounded-xl border" style="background-color: #f8fafc; border-color: #f1f5f9;">
                        <x-display-chart :chartLabels="$transactionDates" :chartData="$dailyTransactionsVolume" title="Daily Volume" />
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. USER & MERCHANT SECURITY MATRIX -->
        <div>
            <h2 class="text-lg font-bold mb-3 flex items-center gap-2" style="color: #0f172a;">
                <i class="fa fa-users text-indigo-600"></i> User & Security Analytics
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Registered Users -->
                <div class="p-5 rounded-2xl shadow-sm border" style="background-color: #ffffff; color: #0f172a; border-color: #e2e8f0;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg" style="background-color: #e0e7ff; color: #4338ca;">
                            <i class="fa fa-users"></i>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold" style="background-color: #e0e7ff; color: #3730a3;">
                            +{{ number_format($usersThisMonth) }} mo
                        </span>
                    </div>
                    <div class="text-xs font-semibold uppercase" style="color: #64748b;">Total System Users</div>
                    <div class="text-2xl font-extrabold mt-1" style="color: #0f172a;">
                        {{ number_format($totalUsers) }}
                    </div>
                    <div class="mt-2 text-xs pt-2 border-t flex justify-between" style="color: #64748b; border-color: #f1f5f9;">
                        <span>This Week: <strong style="color: #0f172a;">+{{ number_format($usersThisWeek) }}</strong></span>
                    </div>
                </div>

                <!-- KYC Verified Rate -->
                <div class="p-5 rounded-2xl shadow-sm border" style="background-color: #ffffff; color: #0f172a; border-color: #e2e8f0;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg" style="background-color: #ccfbf1; color: #0d9488;">
                            <i class="fa fa-id-card"></i>
                        </div>
                        @php $kycRate = $totalUsers > 0 ? round(($kycVerifiedUsers / $totalUsers) * 100, 1) : 0; @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold" style="background-color: #ccfbf1; color: #115e59;">
                            {{ $kycRate }}% verified
                        </span>
                    </div>
                    <div class="text-xs font-semibold uppercase" style="color: #64748b;">KYC Verified Users</div>
                    <div class="text-2xl font-extrabold mt-1" style="color: #0f172a;">
                        {{ number_format($kycVerifiedUsers) }}
                    </div>
                    <div class="w-full rounded-full h-1.5 mt-3 overflow-hidden" style="background-color: #f1f5f9;">
                        <div class="h-1.5 rounded-full" style="background-color: #0d9488; width: {{ min($kycRate, 100) }}%;"></div>
                    </div>
                </div>

                <!-- OTP Verified Rate -->
                <div class="p-5 rounded-2xl shadow-sm border" style="background-color: #ffffff; color: #0f172a; border-color: #e2e8f0;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg" style="background-color: #dbeafe; color: #2563eb;">
                            <i class="fa fa-shield-alt"></i>
                        </div>
                        @php $otpRate = $totalUsers > 0 ? round(($OtpVerifiedUsers / $totalUsers) * 100, 1) : 0; @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold" style="background-color: #dbeafe; color: #1e40af;">
                            {{ $otpRate }}% active
                        </span>
                    </div>
                    <div class="text-xs font-semibold uppercase" style="color: #64748b;">OTP Verified Users</div>
                    <div class="text-2xl font-extrabold mt-1" style="color: #0f172a;">
                        {{ number_format($OtpVerifiedUsers) }}
                    </div>
                    <div class="w-full rounded-full h-1.5 mt-3 overflow-hidden" style="background-color: #f1f5f9;">
                        <div class="h-1.5 rounded-full" style="background-color: #2563eb; width: {{ min($otpRate, 100) }}%;"></div>
                    </div>
                </div>

                <!-- Client Platform Split -->
                <div class="p-5 rounded-2xl shadow-sm border" style="background-color: #ffffff; color: #0f172a; border-color: #e2e8f0;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg" style="background-color: #f3e8ff; color: #9333ea;">
                            <i class="fa fa-laptop-house"></i>
                        </div>
                        <span class="text-xs font-bold" style="color: #64748b;">Client Split</span>
                    </div>
                    <div class="text-xs font-semibold uppercase" style="color: #64748b;">Web & Mobile Users</div>
                    <div class="flex items-center justify-between mt-2 pt-1">
                        <div>
                            <span class="text-xs block" style="color: #94a3b8;">Web</span>
                            <span class="text-lg font-bold" style="color: #0f172a;">{{ number_format($usersOnWeb) }}</span>
                        </div>
                        <div class="h-6 w-px" style="background-color: #e2e8f0;"></div>
                        <div>
                            <span class="text-xs block" style="color: #94a3b8;">Mobile App</span>
                            <span class="text-lg font-bold" style="color: #7e22ce;">{{ number_format($usersOnMobile) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. RECENT GATEWAY TRANSACTIONS -->
        <div class="bg-white rounded-2xl border shadow-sm overflow-hidden" style="border-color: #e2e8f0;">
            <div class="p-5 border-b flex flex-col sm:flex-row sm:items-center justify-between gap-2" style="border-color: #f1f5f9;">
                <div>
                    <h3 class="text-base font-bold flex items-center gap-2" style="color: #0f172a;">
                        <i class="fa fa-list text-indigo-600"></i> Recent Payment Gateway Transactions
                    </h3>
                    <p class="text-xs" style="color: #64748b;">Latest transactions received across checkout sessions and virtual accounts</p>
                </div>
                <a href="{{ route('admin.payment-transactions') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold flex items-center gap-1">
                    View All Payment Transactions <i class="fa fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                    <thead class="bg-gray-50 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Reference</th>
                            <th class="px-6 py-3">Merchant</th>
                            <th class="px-6 py-3">Customer</th>
                            <th class="px-6 py-3">Amount</th>
                            <th class="px-6 py-3">Provider / Method</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white font-medium text-gray-800">
                        @forelse($recentGatewayTransactions as $tx)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-xs font-bold text-gray-900">
                                    {{ $tx->reference }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs font-bold text-gray-900">{{ $tx->merchant?->name ?? 'Merchant #' . $tx->merchant_id }}</div>
                                    <div class="text-[11px] text-gray-400">{{ $tx->merchant?->email ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs font-semibold text-gray-800">{{ $tx->customer_name ?: ($tx->customer_email ?: 'N/A') }}</div>
                                    <div class="text-[11px] text-gray-400 font-mono">{{ $tx->customer_phone ?: '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-sm text-gray-900">
                                    ₦{{ number_format($tx->amount / 100, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs">
                                    @php $prov = strtolower($tx->provider ?? ''); @endphp
                                    @if($prov === 'palmpay')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            PalmPay
                                        </span>
                                    @elseif($prov === 'udara360')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            Udara360
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                            {{ ucfirst($tx->provider ?: $tx->method) }}
                                        </span>
                                    @endif
                                    <span class="text-gray-400 text-[11px] block mt-0.5">{{ ucfirst($tx->method) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($tx->status === 'success')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                            Success
                                        </span>
                                    @elseif(in_array($tx->status, ['pending', 'paying', 'unpaid', 'processing']))
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Processing
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                            Failed
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 font-mono">
                                    {{ $tx->created_at ? $tx->created_at->format('M d, Y H:i') : 'N/A' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-400">
                                    No payment gateway transactions recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    @endcan
</div>
