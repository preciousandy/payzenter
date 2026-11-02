<div x-data="{showDetailsModal: false, showRejectModal: false, showRejectForm: false}" class="inline-flex items-center gap-1.5 flex-nowrap">
    <x-loading-spinner wire:target='approve' />
    <x-loading-spinner wire:target='reject' />

    <!-- 1. View Details Button -->
    <button type="button" @click="showDetailsModal = true" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg border border-slate-300 transition shadow-xs">
        <i class="fa fa-eye text-[11px] text-slate-500"></i>
        <span>Details</span>
    </button>

    @if (($user->kyc_status ?? '') !== 'verified')
        <!-- 2. Quick Approve Button -->
        <button type="button" wire:click="approve" wire:loading.attr="disabled" wire:confirm="Are you sure you want to approve KYC for {{ $user->name ?? 'this user' }}?" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition shadow-xs">
            <i class="fa fa-check text-[11px]"></i>
            <span>Approve</span>
        </button>
    @else
        <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-bold rounded-md">
            <i class="fa fa-check-circle"></i> Verified
        </span>
    @endif

    @if (($user->kyc_status ?? '') !== 'rejected')
        <!-- 3. Quick Reject Button -->
        <button type="button" @click="showRejectModal = true" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold rounded-lg border border-rose-200 transition shadow-xs">
            <i class="fa fa-times text-[11px]"></i>
            <span>Reject</span>
        </button>
    @else
        <span class="inline-flex items-center gap-1 px-2 py-1 bg-rose-50 text-rose-700 border border-rose-200 text-[11px] font-bold rounded-md">
            <i class="fa fa-ban"></i> Rejected
        </span>
    @endif

    <div x-show="showDetailsModal" x-transition x-cloak class="fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4 overflow-y-auto">
        <div class="bg-white p-6 lg:p-8 rounded-2xl w-full max-w-5xl overflow-y-auto max-h-[90vh] shadow-2xl border border-gray-100 text-left">
            <!-- Modal Header -->
            <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        @if ($merchantKyb)
                            Merchant KYB / KYC Verification
                        @else
                            Individual KYC Verification
                        @endif
                    </h2>
                    @if ($user)
                        <p class="text-sm text-gray-500 mt-1">
                            User: <strong class="text-gray-700">{{ $user->name }}</strong> ({{ $user->email }})
                            | Status: 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                @if(($user->kyc_status ?? '') === 'verified') bg-green-100 text-green-800
                                @elseif(($user->kyc_status ?? '') === 'pending') bg-yellow-100 text-yellow-800
                                @elseif(($user->kyc_status ?? '') === 'rejected') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($user->kyc_status ?? 'Unverified') }}
                            </span>
                        </p>
                    @endif
                </div>
                <button type="button" @click="showDetailsModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <span class="fa fa-close fa-2x"></span>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="mt-6 space-y-8 text-gray-800">

                @if ($merchantKyb)
                    <!-- MERCHANT KYB DETAILS -->
                    
                    <!-- Section 1: Business Overview -->
                    <div class="bg-indigo-50/60 p-5 rounded-xl border border-indigo-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-indigo-900 flex items-center gap-2">
                                <i class="fa fa-briefcase text-indigo-600"></i> Business Overview
                            </h3>
                            @if ($merchantKyb->submitted_at)
                                <span class="text-xs font-medium text-indigo-700 bg-indigo-100 px-3 py-1 rounded-full">
                                    Submitted: {{ $merchantKyb->submitted_at->format('d M Y, H:i') }}
                                </span>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">Business Name</span>
                                <span class="font-semibold text-gray-900 text-base">{{ $merchantKyb->business_name ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">Business Type</span>
                                <span class="font-semibold text-gray-800">{{ $merchantKyb->business_type ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">CAC Registration Number</span>
                                <span class="font-semibold text-gray-800">{{ $merchantKyb->cac_registration_number ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">Business Email</span>
                                <span class="font-medium text-gray-800">{{ $merchantKyb->business_email ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">Business Phone</span>
                                <span class="font-medium text-gray-800">{{ $merchantKyb->business_phone ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">Website</span>
                                @if($merchantKyb->business_website)
                                    <a href="{{ Str::startsWith($merchantKyb->business_website, 'http') ? $merchantKyb->business_website : 'https://' . $merchantKyb->business_website }}" target="_blank" class="text-indigo-600 hover:underline font-medium">
                                        {{ $merchantKyb->business_website }}
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">BVN</span>
                                <span class="font-semibold text-gray-800">{{ $merchantKyb->bvn ?? $user->bvn ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">TIN</span>
                                <span class="font-medium text-gray-800">{{ $merchantKyb->tin ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">Social Media Handles</span>
                                <span class="font-medium text-gray-800">{{ $merchantKyb->business_social_media_handles ?? '-' }}</span>
                            </div>
                            <div class="md:col-span-3">
                                <span class="text-xs text-gray-500 font-medium block">Business Address</span>
                                <span class="font-medium text-gray-800">{{ $merchantKyb->business_address ?? '-' }}</span>
                            </div>
                            <div class="md:col-span-3">
                                <span class="text-xs text-gray-500 font-medium block">Business Description</span>
                                <span class="font-normal text-gray-700 block bg-white p-3 rounded-lg border border-indigo-100 text-xs mt-1">
                                    {{ $merchantKyb->business_description ?? '-' }}
                                </span>
                            </div>
                        </div>

                        @if($merchantKyb->business_logo_path)
                            <div class="mt-4 pt-3 border-t border-indigo-100">
                                <span class="text-xs text-gray-500 font-medium block mb-2">Business Logo</span>
                                <a href="{{ $merchantKyb->business_logo_url }}" target="_blank" class="inline-block group">
                                    <img src="{{ $merchantKyb->business_logo_url }}" alt="Business Logo" class="h-20 w-auto rounded-lg border shadow-sm object-contain bg-white p-1 group-hover:ring-2 group-hover:ring-indigo-400 transition">
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Section 2: Director / Personal Information -->
                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fa fa-user-circle text-gray-600"></i> Director / Personal Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">Full Name</span>
                                <span class="font-semibold text-gray-900">{{ $merchantKyb->first_name }} {{ $merchantKyb->last_name }} {{ $merchantKyb->surname }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">Personal Email</span>
                                <span class="font-medium text-gray-800">{{ $merchantKyb->email ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">Personal Phone</span>
                                <span class="font-medium text-gray-800">{{ $merchantKyb->phone ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">Country / State / LGA</span>
                                <span class="font-medium text-gray-800">{{ $merchantKyb->country ?? '-' }}, {{ $merchantKyb->state ?? '-' }}, {{ $merchantKyb->lga ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">PEP Declaration (Politically Exposed)</span>
                                <span class="font-semibold {{ $merchantKyb->pep_declaration ? 'text-amber-600' : 'text-green-600' }}">
                                    {{ $merchantKyb->pep_declaration ? 'Yes (Politically Exposed)' : 'No' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">Personal Social Handle</span>
                                <span class="font-medium text-gray-800">{{ $merchantKyb->social_media_handle ?? '-' }}</span>
                            </div>
                            <div class="md:col-span-3">
                                <span class="text-xs text-gray-500 font-medium block">Residential Address</span>
                                <span class="font-medium text-gray-800">{{ $merchantKyb->address ?? '-' }}</span>
                            </div>
                        </div>

                        <!-- Director Documents -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 pt-4 border-t border-gray-200">
                            <!-- Identity Document -->
                            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center justify-between">
                                    <span class="flex items-center gap-1.5"><i class="fa fa-id-badge text-indigo-600"></i> Identity Document</span>
                                    @if($merchantKyb->identity_document_path)
                                        <a href="{{ $merchantKyb->identity_document_url }}" target="_blank" class="text-xs text-indigo-600 hover:text-indigo-800 hover:underline font-semibold flex items-center gap-1">
                                            <span>Open in New Tab</span> <i class="fa fa-external-link"></i>
                                        </a>
                                    @endif
                                </h4>
                                @if($merchantKyb->identity_document_path)
                                    @php $ext = strtolower(pathinfo($merchantKyb->identity_document_path, PATHINFO_EXTENSION)); @endphp
                                    @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
                                        <a href="{{ $merchantKyb->identity_document_url }}" target="_blank" class="block group relative overflow-hidden rounded-lg border bg-gray-50">
                                            <img src="{{ $merchantKyb->identity_document_url }}" alt="Identity Document" class="w-full max-h-60 object-contain p-1 group-hover:scale-102 transition duration-200">
                                            <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition text-white text-xs font-bold gap-1.5">
                                                <i class="fa fa-magnifying-glass-plus"></i> View Full Size
                                            </div>
                                        </a>
                                    @else
                                        <div class="p-5 bg-red-50/50 rounded-xl text-center border border-dashed border-red-200">
                                            <i class="fa fa-file-pdf text-red-600 fa-3x mb-2 block"></i>
                                            <span class="text-xs font-bold text-gray-800 block mb-1">PDF Document</span>
                                            <span class="text-[11px] text-gray-500 block mb-3 font-mono truncate max-w-xs mx-auto">{{ basename($merchantKyb->identity_document_path) }}</span>
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ $merchantKyb->identity_document_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#1400FB] text-white text-xs font-bold rounded-lg hover:bg-blue-700 shadow-sm transition">
                                                    <i class="fa fa-eye"></i> View PDF
                                                </a>
                                                <a href="{{ $merchantKyb->identity_document_url }}" download class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">
                                                    <i class="fa fa-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <p class="text-xs text-gray-400 italic py-4 text-center">No identity document uploaded</p>
                                @endif
                            </div>

                            <!-- Proof of Address -->
                            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center justify-between">
                                    <span class="flex items-center gap-1.5"><i class="fa fa-map-pin text-indigo-600"></i> Proof of Address</span>
                                    @if($merchantKyb->proof_of_address_path)
                                        <a href="{{ $merchantKyb->proof_of_address_url }}" target="_blank" class="text-xs text-indigo-600 hover:text-indigo-800 hover:underline font-semibold flex items-center gap-1">
                                            <span>Open in New Tab</span> <i class="fa fa-external-link"></i>
                                        </a>
                                    @endif
                                </h4>
                                @if($merchantKyb->proof_of_address_path)
                                    @php $ext = strtolower(pathinfo($merchantKyb->proof_of_address_path, PATHINFO_EXTENSION)); @endphp
                                    @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
                                        <a href="{{ $merchantKyb->proof_of_address_url }}" target="_blank" class="block group relative overflow-hidden rounded-lg border bg-gray-50">
                                            <img src="{{ $merchantKyb->proof_of_address_url }}" alt="Proof of Address" class="w-full max-h-60 object-contain p-1 group-hover:scale-102 transition duration-200">
                                            <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition text-white text-xs font-bold gap-1.5">
                                                <i class="fa fa-magnifying-glass-plus"></i> View Full Size
                                            </div>
                                        </a>
                                    @else
                                        <div class="p-5 bg-red-50/50 rounded-xl text-center border border-dashed border-red-200">
                                            <i class="fa fa-file-pdf text-red-600 fa-3x mb-2 block"></i>
                                            <span class="text-xs font-bold text-gray-800 block mb-1">PDF Document</span>
                                            <span class="text-[11px] text-gray-500 block mb-3 font-mono truncate max-w-xs mx-auto">{{ basename($merchantKyb->proof_of_address_path) }}</span>
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ $merchantKyb->proof_of_address_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#1400FB] text-white text-xs font-bold rounded-lg hover:bg-blue-700 shadow-sm transition">
                                                    <i class="fa fa-eye"></i> View PDF
                                                </a>
                                                <a href="{{ $merchantKyb->proof_of_address_url }}" download class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">
                                                    <i class="fa fa-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <p class="text-xs text-gray-400 italic py-4 text-center">No proof of address uploaded</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Settlement Bank Account -->
                    <div class="bg-emerald-50/60 p-5 rounded-xl border border-emerald-100">
                        <h3 class="text-lg font-bold text-emerald-900 mb-4 flex items-center gap-2">
                            <i class="fa fa-bank text-emerald-600"></i> Settlement Bank Account
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">Bank Name</span>
                                <span class="font-semibold text-gray-900">{{ $merchantKyb->settlement_bank_name ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">Account Number</span>
                                <span class="font-mono font-semibold text-gray-900">{{ $merchantKyb->settlement_account_number ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">Account Name</span>
                                <span class="font-semibold text-gray-900">{{ $merchantKyb->settlement_account_name ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">Verification Date</span>
                                <span class="font-medium text-gray-800">
                                    {{ $merchantKyb->settlement_verified_at ? $merchantKyb->settlement_verified_at->format('d M Y, H:i') : 'Unverified' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: CAC & Compliance Documents -->
                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fa fa-folder-open text-gray-600"></i> CAC & Compliance Documents
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Business Resolution -->
                            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold text-gray-700 flex items-center gap-1.5"><i class="fa fa-file-lines text-indigo-600"></i> 1. Business Resolution</span>
                                    @if($merchantKyb->business_resolution_path)
                                        <a href="{{ $merchantKyb->business_resolution_url }}" target="_blank" class="text-xs text-indigo-600 hover:underline font-semibold flex items-center gap-1">
                                            <span>Open</span> <i class="fa fa-external-link"></i>
                                        </a>
                                    @endif
                                </div>
                                @if($merchantKyb->business_resolution_path)
                                    @php $ext = strtolower(pathinfo($merchantKyb->business_resolution_path, PATHINFO_EXTENSION)); @endphp
                                    @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
                                        <a href="{{ $merchantKyb->business_resolution_url }}" target="_blank" class="block group relative overflow-hidden rounded-lg border bg-gray-50">
                                            <img src="{{ $merchantKyb->business_resolution_url }}" class="w-full max-h-48 object-contain p-1 group-hover:scale-102 transition duration-200">
                                            <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition text-white text-xs font-bold gap-1.5">
                                                <i class="fa fa-magnifying-glass-plus"></i> View Full Size
                                            </div>
                                        </a>
                                    @else
                                        <div class="p-4 bg-red-50/50 rounded-xl text-center border border-dashed border-red-200">
                                            <i class="fa fa-file-pdf text-red-600 fa-2x mb-1 block"></i>
                                            <span class="text-xs font-bold text-gray-800 block mb-1">PDF File</span>
                                            <span class="text-[11px] text-gray-500 block mb-2 font-mono truncate max-w-xs mx-auto">{{ basename($merchantKyb->business_resolution_path) }}</span>
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ $merchantKyb->business_resolution_url }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1 bg-[#1400FB] text-white text-xs font-bold rounded-lg hover:bg-blue-700 shadow-sm transition">
                                                    <i class="fa fa-eye"></i> View PDF
                                                </a>
                                                <a href="{{ $merchantKyb->business_resolution_url }}" download class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">
                                                    <i class="fa fa-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <p class="text-xs text-gray-400 italic py-3 text-center">Not uploaded</p>
                                @endif
                            </div>

                            <!-- Company Profile -->
                            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold text-gray-700 flex items-center gap-1.5"><i class="fa fa-file-lines text-indigo-600"></i> 2. Company Profile</span>
                                    @if($merchantKyb->company_profile_path)
                                        <a href="{{ $merchantKyb->company_profile_url }}" target="_blank" class="text-xs text-indigo-600 hover:underline font-semibold flex items-center gap-1">
                                            <span>Open</span> <i class="fa fa-external-link"></i>
                                        </a>
                                    @endif
                                </div>
                                @if($merchantKyb->company_profile_path)
                                    @php $ext = strtolower(pathinfo($merchantKyb->company_profile_path, PATHINFO_EXTENSION)); @endphp
                                    @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
                                        <a href="{{ $merchantKyb->company_profile_url }}" target="_blank" class="block group relative overflow-hidden rounded-lg border bg-gray-50">
                                            <img src="{{ $merchantKyb->company_profile_url }}" class="w-full max-h-48 object-contain p-1 group-hover:scale-102 transition duration-200">
                                            <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition text-white text-xs font-bold gap-1.5">
                                                <i class="fa fa-magnifying-glass-plus"></i> View Full Size
                                            </div>
                                        </a>
                                    @else
                                        <div class="p-4 bg-red-50/50 rounded-xl text-center border border-dashed border-red-200">
                                            <i class="fa fa-file-pdf text-red-600 fa-2x mb-1 block"></i>
                                            <span class="text-xs font-bold text-gray-800 block mb-1">PDF File</span>
                                            <span class="text-[11px] text-gray-500 block mb-2 font-mono truncate max-w-xs mx-auto">{{ basename($merchantKyb->company_profile_path) }}</span>
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ $merchantKyb->company_profile_url }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1 bg-[#1400FB] text-white text-xs font-bold rounded-lg hover:bg-blue-700 shadow-sm transition">
                                                    <i class="fa fa-eye"></i> View PDF
                                                </a>
                                                <a href="{{ $merchantKyb->company_profile_url }}" download class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">
                                                    <i class="fa fa-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <p class="text-xs text-gray-400 italic py-3 text-center">Not uploaded</p>
                                @endif
                            </div>

                            <!-- Status Report -->
                            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold text-gray-700 flex items-center gap-1.5"><i class="fa fa-file-lines text-indigo-600"></i> 3. Status Report / CAC Document</span>
                                    @if($merchantKyb->status_report_path)
                                        <a href="{{ $merchantKyb->status_report_url }}" target="_blank" class="text-xs text-indigo-600 hover:underline font-semibold flex items-center gap-1">
                                            <span>Open</span> <i class="fa fa-external-link"></i>
                                        </a>
                                    @endif
                                </div>
                                @if($merchantKyb->status_report_path)
                                    @php $ext = strtolower(pathinfo($merchantKyb->status_report_path, PATHINFO_EXTENSION)); @endphp
                                    @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
                                        <a href="{{ $merchantKyb->status_report_url }}" target="_blank" class="block group relative overflow-hidden rounded-lg border bg-gray-50">
                                            <img src="{{ $merchantKyb->status_report_url }}" class="w-full max-h-48 object-contain p-1 group-hover:scale-102 transition duration-200">
                                            <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition text-white text-xs font-bold gap-1.5">
                                                <i class="fa fa-magnifying-glass-plus"></i> View Full Size
                                            </div>
                                        </a>
                                    @else
                                        <div class="p-4 bg-red-50/50 rounded-xl text-center border border-dashed border-red-200">
                                            <i class="fa fa-file-pdf text-red-600 fa-2x mb-1 block"></i>
                                            <span class="text-xs font-bold text-gray-800 block mb-1">PDF File</span>
                                            <span class="text-[11px] text-gray-500 block mb-2 font-mono truncate max-w-xs mx-auto">{{ basename($merchantKyb->status_report_path) }}</span>
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ $merchantKyb->status_report_url }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1 bg-[#1400FB] text-white text-xs font-bold rounded-lg hover:bg-blue-700 shadow-sm transition">
                                                    <i class="fa fa-eye"></i> View PDF
                                                </a>
                                                <a href="{{ $merchantKyb->status_report_url }}" download class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">
                                                    <i class="fa fa-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <p class="text-xs text-gray-400 italic py-3 text-center">Not uploaded</p>
                                @endif
                            </div>

                            <!-- Business Address Verification -->
                            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold text-gray-700 flex items-center gap-1.5"><i class="fa fa-file-lines text-indigo-600"></i> 4. Business Address Verification</span>
                                    @if($merchantKyb->business_address_verification_path)
                                        <a href="{{ $merchantKyb->business_address_verification_url }}" target="_blank" class="text-xs text-indigo-600 hover:underline font-semibold flex items-center gap-1">
                                            <span>Open</span> <i class="fa fa-external-link"></i>
                                        </a>
                                    @endif
                                </div>
                                @if($merchantKyb->business_address_verification_path)
                                    @php $ext = strtolower(pathinfo($merchantKyb->business_address_verification_path, PATHINFO_EXTENSION)); @endphp
                                    @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
                                        <a href="{{ $merchantKyb->business_address_verification_url }}" target="_blank" class="block group relative overflow-hidden rounded-lg border bg-gray-50">
                                            <img src="{{ $merchantKyb->business_address_verification_url }}" class="w-full max-h-48 object-contain p-1 group-hover:scale-102 transition duration-200">
                                            <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition text-white text-xs font-bold gap-1.5">
                                                <i class="fa fa-magnifying-glass-plus"></i> View Full Size
                                            </div>
                                        </a>
                                    @else
                                        <div class="p-4 bg-red-50/50 rounded-xl text-center border border-dashed border-red-200">
                                            <i class="fa fa-file-pdf text-red-600 fa-2x mb-1 block"></i>
                                            <span class="text-xs font-bold text-gray-800 block mb-1">PDF File</span>
                                            <span class="text-[11px] text-gray-500 block mb-2 font-mono truncate max-w-xs mx-auto">{{ basename($merchantKyb->business_address_verification_path) }}</span>
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ $merchantKyb->business_address_verification_url }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1 bg-[#1400FB] text-white text-xs font-bold rounded-lg hover:bg-blue-700 shadow-sm transition">
                                                    <i class="fa fa-eye"></i> View PDF
                                                </a>
                                                <a href="{{ $merchantKyb->business_address_verification_url }}" download class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">
                                                    <i class="fa fa-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <p class="text-xs text-gray-400 italic py-3 text-center">Not uploaded</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if ($kyc)
                    <!-- INDIVIDUAL KYC DETAILS -->
                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fa fa-id-card text-gray-600"></i> Individual KYC Details
                        </h3>

                        @if ($kyc->face_cam_image_url)
                            <div class="mb-6 text-center">
                                <h4 class="text-xs font-semibold text-gray-500 mb-2">Selfie Photo</h4>
                                <a href="{{ $kyc->face_cam_image_url }}" target="_blank" class="inline-block group">
                                    <img src="{{ $kyc->face_cam_image_url }}" alt="Selfie" class="mx-auto rounded-xl max-h-48 border shadow-sm group-hover:ring-2 group-hover:ring-indigo-400 transition">
                                    <span class="text-xs text-indigo-600 block mt-1 font-semibold group-hover:underline">
                                        <i class="fa fa-external-link text-[10px]"></i> View Full Photo
                                    </span>
                                </a>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">First Name</span>
                                <span class="font-semibold text-gray-900">{{ $kyc->first_name }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">Last Name</span>
                                <span class="font-semibold text-gray-900">{{ $kyc->last_name }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">Other Names</span>
                                <span class="font-medium text-gray-800">{{ $kyc->other_names ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">Date of Birth</span>
                                <span class="font-medium text-gray-800">{{ $kyc->date_of_birth ? $kyc->date_of_birth->format('d M Y') : '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">BVN</span>
                                <span class="font-semibold text-gray-800">{{ $kyc->user->bvn ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">Gender</span>
                                <span class="font-medium text-gray-800">{{ $kyc->gender ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">Document Type</span>
                                <span class="font-medium text-gray-800">{{ ucfirst(str_replace('_', ' ', $kyc->document_type)) ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">Document Number</span>
                                <span class="font-semibold text-gray-800">{{ $kyc->document_number ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 font-medium block">Occupation</span>
                                <span class="font-medium text-gray-800">{{ $kyc->occupation ?? '-' }}</span>
                            </div>
                            <div class="md:col-span-3">
                                <span class="text-xs text-gray-500 font-medium block">Address</span>
                                <span class="font-medium text-gray-800">{{ $kyc->address ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 pt-4 border-t border-gray-200">
                            <div>
                                <h4 class="text-xs font-semibold text-gray-600 mb-2">Document Front Image</h4>
                                @if($kyc->document_front_image_url)
                                    <a href="{{ $kyc->document_front_image_url }}" target="_blank" class="block group">
                                        <img src="{{ $kyc->document_front_image_url }}" alt="Front" class="w-full max-h-52 object-contain rounded-xl border bg-gray-50 p-1 group-hover:ring-2 group-hover:ring-indigo-400 transition">
                                        <span class="text-xs text-indigo-600 block mt-1 font-semibold text-center group-hover:underline">
                                            <i class="fa fa-external-link text-[10px]"></i> Open in New Tab
                                        </span>
                                    </a>
                                @else
                                    <p class="text-xs text-gray-400 italic">No front image</p>
                                @endif
                            </div>
                            <div>
                                <h4 class="text-xs font-semibold text-gray-600 mb-2">Document Back Image</h4>
                                @if($kyc->document_back_image_url)
                                    <a href="{{ $kyc->document_back_image_url }}" target="_blank" class="block group">
                                        <img src="{{ $kyc->document_back_image_url }}" alt="Back" class="w-full max-h-52 object-contain rounded-xl border bg-gray-50 p-1 group-hover:ring-2 group-hover:ring-indigo-400 transition">
                                        <span class="text-xs text-indigo-600 block mt-1 font-semibold text-center group-hover:underline">
                                            <i class="fa fa-external-link text-[10px]"></i> Open in New Tab
                                        </span>
                                    </a>
                                @else
                                    <p class="text-xs text-gray-400 italic">No back image</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            <!-- Approve / Reject Actions -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Current Status:</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                            @if(($user->kyc_status ?? '') === 'verified') bg-emerald-100 text-emerald-800 border border-emerald-200
                            @elseif(($user->kyc_status ?? '') === 'pending') bg-amber-100 text-amber-800 border border-amber-200
                            @elseif(($user->kyc_status ?? '') === 'rejected') bg-rose-100 text-rose-800 border border-rose-200
                            @else bg-slate-100 text-slate-800 border border-slate-200 @endif">
                            {{ ucfirst($user->kyc_status ?? 'Unverified') }}
                        </span>
                    </div>

                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <button type="button" @click="showDetailsModal = false" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
                            Close
                        </button>

                        @if (($user->kyc_status ?? '') !== 'rejected')
                            <button type="button" @click="showRejectForm = !showRejectForm" class="px-5 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                                <i class="fa fa-times"></i>
                                <span>{{ ($user->kyc_status ?? '') === 'verified' ? 'Revoke / Reject KYC' : 'Reject KYC' }}</span>
                            </button>
                        @endif

                        @if (($user->kyc_status ?? '') !== 'verified')
                            <button type="button" wire:click="approve" wire:loading.attr="disabled" wire:confirm="Are you sure you want to approve KYC for {{ $user->name ?? 'this user' }}?" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-md flex items-center gap-1.5">
                                <i class="fa fa-check"></i>
                                <span>{{ ($user->kyc_status ?? '') === 'rejected' ? 'Re-Approve KYC' : 'Approve KYC' }}</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Inline Rejection Form in Modal -->
                <form x-show="showRejectForm" x-transition x-cloak class="space-y-4 mt-5 p-5 bg-rose-50/70 rounded-2xl border border-rose-200" wire:submit.prevent="reject()">
                    <div>
                        <x-input-label for="modal-reason-{{ $user->id ?? 0 }}" :value="__('Rejection Reason:')" class="text-rose-900 font-bold text-xs" />
                        <textarea id="modal-reason-{{ $user->id ?? 0 }}" rows="3" class="w-full rounded-xl bg-white border-rose-300 shadow-xs mt-1 text-sm focus:border-rose-500 focus:ring-rose-500" wire:model="reason" placeholder="Provide clear reason for rejection (e.g., blurry document, mismatching BVN, invalid address)..." required></textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('reason')" />
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showRejectForm = false" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 text-xs font-semibold rounded-lg">
                            Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="py-2 px-5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg text-xs transition shadow-sm">
                            Confirm & Send Rejection
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- Standalone Quick Rejection Modal (Triggered from Table Row) -->
    <div x-show="showRejectModal" x-transition x-cloak class="fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4">
        <div @click.away="showRejectModal = false" class="bg-white p-6 rounded-2xl w-full max-w-md shadow-2xl border border-gray-100 text-left">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <i class="fa fa-times-circle text-rose-600"></i> Reject KYC Verification
                </h3>
                <button type="button" @click="showRejectModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fa fa-close"></i>
                </button>
            </div>

            <p class="text-xs text-gray-600 mt-3 mb-4">
                You are rejecting KYC for <strong class="text-gray-900">{{ $user->name ?? 'User' }}</strong> ({{ $user->email ?? '' }}).
            </p>

            <form wire:submit.prevent="reject()">
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Reason for Rejection <span class="text-rose-500">*</span></label>
                    <textarea rows="3" class="w-full rounded-xl bg-white border-gray-300 shadow-xs text-xs focus:border-rose-500 focus:ring-rose-500 p-2.5" wire:model="reason" placeholder="State why KYC is rejected..." required></textarea>
                    @error('reason') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="showRejectModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg">
                        Cancel
                    </button>
                    <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg shadow-sm">
                        Submit Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>