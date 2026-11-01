<div class="p-4 sm:p-6 bg-slate-50 min-h-screen text-slate-800 font-sans" style="background-color: #f8fafc; color: #0f172a;">
    <!-- Header Banner -->
    <div class="rounded-2xl p-6 mb-6 shadow-xl border border-slate-800" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%); color: #ffffff;">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider mb-2 border" style="background-color: rgba(99, 102, 241, 0.2); color: #a5b4fc; border-color: rgba(99, 102, 241, 0.3);">
                    <i class="fa fa-shield-alt text-emerald-400"></i> Compliance & Identity Verification
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight" style="color: #ffffff;">
                    KYC & Merchant KYB Verifications
                </h1>
                <p class="mt-1 text-xs sm:text-sm max-w-2xl" style="color: #cbd5e1;">
                    Review submitted business registrations (CAC), government IDs, BVNs, and settlement details. Directly approve or reject compliance requests below.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold transition border" style="background-color: rgba(255, 255, 255, 0.1); border-color: rgba(255, 255, 255, 0.2); color: #ffffff;">
                    <i class="fa fa-arrow-left mr-1.5"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Submissions -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Submissions</div>
                <div class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($totalSubmitted) }}</div>
                <div class="text-xs text-slate-500 mt-1">Users & Merchants with KYC/KYB</div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
                <i class="fa fa-id-card"></i>
            </div>
        </div>

        <!-- Pending Verification -->
        <div class="p-5 rounded-2xl bg-white border border-amber-200 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Pending Review</div>
                <div class="text-2xl font-extrabold text-amber-600 mt-1">{{ number_format($pendingCount) }}</div>
                <div class="text-xs text-amber-700 mt-1">Awaiting Admin Action</div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                <i class="fa fa-clock"></i>
            </div>
        </div>

        <!-- Approved / Verified -->
        <div class="p-5 rounded-2xl bg-white border border-emerald-200 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Approved & Verified</div>
                <div class="text-2xl font-extrabold text-emerald-600 mt-1">{{ number_format($verifiedCount) }}</div>
                <div class="text-xs text-emerald-700 mt-1">Live & Settlement-Active</div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                <i class="fa fa-check-circle"></i>
            </div>
        </div>

        <!-- Rejected -->
        <div class="p-5 rounded-2xl bg-white border border-rose-200 shadow-xs flex items-center justify-between">
            <div>
                <div class="text-xs font-semibold text-rose-700 uppercase tracking-wider">Rejected Submissions</div>
                <div class="text-2xl font-extrabold text-rose-600 mt-1">{{ number_format($rejectedCount) }}</div>
                <div class="text-xs text-rose-700 mt-1">Requires Re-submission</div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg">
                <i class="fa fa-times-circle"></i>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 mb-4 border-b border-slate-100">
            <div>
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa fa-users text-indigo-600"></i> Submitted KYC & KYB Records
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Click <strong>Details</strong> to inspect uploaded CAC/ID documents, or use <strong>Approve</strong> / <strong>Reject</strong> directly.
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <livewire:kyc-table />
        </div>
    </div>
</div>