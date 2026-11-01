<div 
    x-data="{ 
        activeUploads: 0, 
        showSampleModal: false, 
        sampleUrl: null, 
        sampleTitle: 'Sample Document' 
    }" 
    x-on:uploading-state-change.window="activeUploads = Math.max(0, activeUploads + $event.detail.delta)"
    x-on:open-sample.window="sampleUrl = $event.detail.url; sampleTitle = $event.detail.title; showSampleModal = true"
    class="bg-gray-50 min-h-screen px-4 py-8"
>
    <x-loading-spinner wire:target="submitStep1,submitStep2,verifySettlementAccount,submitStep3,submitStep4,nextPinStage,completeOnboarding,identity_document_upload,proof_of_address_upload,business_logo_upload,business_resolution_upload,company_profile_upload,status_report_upload,business_address_verification_upload" />

    <div class="max-w-3xl mx-auto">
        <!-- Top bar with Back to Dashboard link -->
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-[#1400FB] transition-colors py-1.5 px-3 rounded-xl hover:bg-white border border-transparent hover:border-gray-200">
                <i class="fa fa-arrow-left text-xs"></i>
                <span>Return to Dashboard</span>
            </a>
            <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full bg-blue-100 text-[#1400FB] border border-blue-200 shadow-sm">
                <i class="fa fa-shield-halved text-[11px]"></i>
                <span>Step {{ $currentStep }} of 5</span>
            </span>
        </div>

        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Activate Your Business</h1>
            <p class="text-sm text-gray-500 mt-1 max-w-lg mx-auto">
                Complete your merchant profile to start accepting live payments, creating virtual accounts, and withdrawing your funds.
            </p>
        </div>

        @if ($kybTableMissing)
            <div class="bg-red-50 border border-red-200 text-red-800 rounded-2xl p-5 shadow-sm">
                <div class="font-bold flex items-center gap-2">
                    <i class="fa fa-triangle-exclamation"></i>
                    <span>Onboarding setup incomplete</span>
                </div>
                <div class="text-sm mt-1">Database migrations have not been applied yet. Run: php artisan migrate</div>
            </div>
        @else

        @php
            $steps = [
                1 => 'Personal Info',
                2 => 'Business Profile',
                3 => 'Settlement Bank',
                4 => 'Company Docs',
                5 => 'Security PIN',
            ];
        @endphp

        <!-- Stepper Indicators -->
        <div class="bg-white rounded-2xl p-4 sm:p-6 border border-gray-200 shadow-sm mb-8">
            <div class="flex items-center justify-between relative">
                <!-- Background connecting track -->
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-gray-200 rounded-full z-0"></div>
                <!-- Active connecting track -->
                <div 
                    class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-[#1400FB] rounded-full z-0 transition-all duration-500"
                    style="width: {{ (($currentStep - 1) / 4) * 100 }}%"
                ></div>

                @foreach($steps as $stepNum => $label)
                    <div class="relative z-10 flex flex-col items-center">
                        <button 
                            type="button"
                            wire:click="goToStep({{ $stepNum }})"
                            @disabled($stepNum > $currentStep)
                            @class([
                                'w-9 h-9 sm:w-11 sm:h-11 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 shadow-sm',
                                'bg-[#1400FB] text-white ring-4 ring-blue-100 scale-105' => $currentStep === $stepNum,
                                'bg-emerald-500 text-white cursor-pointer hover:bg-emerald-600' => $currentStep > $stepNum,
                                'bg-gray-100 text-gray-400 border-2 border-gray-300 cursor-not-allowed' => $currentStep < $stepNum,
                            ])
                            title="{{ $label }}"
                        >
                            @if($currentStep > $stepNum)
                                <i class="fa fa-check text-xs"></i>
                            @else
                                {{ $stepNum }}
                            @endif
                        </button>
                        <span @class([
                            'text-[11px] sm:text-xs font-semibold mt-2 hidden sm:block',
                            'text-[#1400FB]' => $currentStep === $stepNum,
                            'text-emerald-700' => $currentStep > $stepNum,
                            'text-gray-400' => $currentStep < $stepNum,
                        ])>
                            {{ $label }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- STEP 1: Personal Info -->
        @if ($currentStep === 1)
            <form wire:submit.prevent="submitStep1" class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 space-y-6 shadow-sm">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-blue-100 text-[#1400FB] flex items-center justify-center text-xs font-extrabold">1</span>
                        <span>Personal Information</span>
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Provide your legal personal information as appearing on your official ID.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Surname" :required="true" />
                        <x-text-input wire:model.defer="surname" placeholder="e.g. Okon" class="w-full mt-1.5" />
                        <x-input-error :messages="$errors->get('surname')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label value="First Name" :required="true" />
                        <x-text-input wire:model.defer="first_name" placeholder="e.g. Emmanuel" class="w-full mt-1.5" />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label value="Last Name" :required="true" />
                        <x-text-input wire:model.defer="last_name" placeholder="e.g. Chukwu" class="w-full mt-1.5" />
                        <x-input-error :messages="$errors->get('last_name')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label value="Email Address" :required="true" />
                        <x-text-input wire:model.defer="email" type="email" class="w-full mt-1.5" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label value="Phone Number" :required="true" />
                        <x-text-input wire:model.defer="phone" placeholder="e.g. 08012345678" class="w-full mt-1.5" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label value="Country" :required="true" />
                        <x-text-input wire:model.defer="country" class="w-full mt-1.5" />
                        <x-input-error :messages="$errors->get('country')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label value="State of Residence" :required="true" />
                        <x-text-input wire:model.defer="state" placeholder="e.g. Lagos" class="w-full mt-1.5" />
                        <x-input-error :messages="$errors->get('state')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label value="LGA" :required="true" />
                        <x-text-input wire:model.defer="lga" placeholder="e.g. Ikeja" class="w-full mt-1.5" />
                        <x-input-error :messages="$errors->get('lga')" class="mt-1" />
                    </div>
                </div>

                <div>
                    <x-input-label value="Residential Address" :required="true" />
                    <textarea wire:model.defer="address" rows="2" placeholder="Full street address..." class="w-full mt-1.5 rounded-xl border-gray-300 focus:border-[#1400FB] focus:ring-[#1400FB] text-sm"></textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-1" />
                </div>

                <div class="flex items-start gap-3 p-3.5 bg-gray-50 rounded-2xl border border-gray-200">
                    <input type="checkbox" id="pep_declaration" wire:model.defer="pep_declaration" class="rounded border-gray-300 text-[#1400FB] shadow-sm focus:ring-[#1400FB] mt-0.5" />
                    <label for="pep_declaration" class="text-xs text-gray-700 leading-normal cursor-pointer">
                        <span class="font-bold">Politically Exposed Person (PEP) Declaration:</span> Check here if you or any immediate family member holds a prominent public function.
                    </label>
                </div>
                <x-input-error :messages="$errors->get('pep_declaration')" class="mt-1" />

                <div>
                    <x-input-label value="Personal Social Media Handle (Optional)" />
                    <x-text-input wire:model.defer="social_media_handle" placeholder="@yourhandle (Twitter, Instagram, LinkedIn)" class="w-full mt-1.5" />
                    <x-input-error :messages="$errors->get('social_media_handle')" class="mt-1" />
                </div>

                <!-- File Upload Palates for Step 1 -->
                <div class="pt-4 border-t border-gray-100">
                    <h4 class="text-sm font-bold text-gray-900 mb-1">Identity & Residential Verification</h4>
                    <p class="text-xs text-gray-500 mb-4">Please upload valid and legible copies of your documents.</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-file-upload-palate 
                            wireModel="identity_document_upload" 
                            :existingPath="$identity_document_path" 
                            label="Means of Identity" 
                            hint="NIN slip, International Passport, or Voter's Card (PDF, JPG, PNG up to 10MB)"
                            :required="empty($identity_document_path)"
                        />
                        <x-file-upload-palate 
                            wireModel="proof_of_address_upload" 
                            :existingPath="$proof_of_address_path" 
                            label="Proof of Address" 
                            hint="Utility bill, bank statement, or tenancy receipt dated within 3 months (PDF, JPG, PNG up to 10MB)"
                            :required="empty($proof_of_address_path)"
                        />
                    </div>
                </div>

                <!-- Footer with Next Button synchronized with Upload State -->
                <div class="flex items-center justify-end pt-4 border-t border-gray-100">
                    <button 
                        type="submit" 
                        :disabled="activeUploads > 0" 
                        wire:loading.attr="disabled"
                        class="px-8 py-3.5 rounded-2xl bg-[#1400FB] text-white font-extrabold hover:bg-blue-700 transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed transform hover:-translate-y-0.5"
                    >
                        <span x-show="activeUploads > 0" class="flex items-center gap-2">
                            <i class="fa fa-spinner fa-spin text-sm"></i>
                            <span>Attaching Document... Please wait</span>
                        </span>
                        <span x-show="activeUploads === 0" wire:loading.remove wire:target="submitStep1" class="flex items-center gap-2">
                            <span>Next: Business Profile</span>
                            <i class="fa fa-arrow-right text-xs"></i>
                        </span>
                        <span wire:loading wire:target="submitStep1" class="flex items-center gap-2">
                            <i class="fa fa-spinner fa-spin text-sm"></i>
                            <span>Saving details...</span>
                        </span>
                    </button>
                </div>
            </form>
        @endif

        <!-- STEP 2: Business Info -->
        @if ($currentStep === 2)
            <form wire:submit.prevent="submitStep2" class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 space-y-6 shadow-sm">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-blue-100 text-[#1400FB] flex items-center justify-center text-xs font-extrabold">2</span>
                        <span>Business Profile</span>
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Provide information about your business or venture.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Business Name" :required="true" />
                        <x-text-input wire:model.defer="business_name" placeholder="e.g. Acme Global Services" class="w-full mt-1.5" />
                        <x-input-error :messages="$errors->get('business_name')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label value="Business Type / Industry" :required="true" />
                        <x-text-input wire:model.defer="business_type" placeholder="e.g. E-commerce, Fintech, Retail" class="w-full mt-1.5" />
                        <x-input-error :messages="$errors->get('business_type')" class="mt-1" />
                    </div>
                </div>

                <div>
                    <x-input-label value="Business Description" :required="true" />
                    <textarea wire:model.defer="business_description" rows="2" placeholder="Briefly describe what your business offers..." class="w-full mt-1.5 rounded-xl border-gray-300 focus:border-[#1400FB] focus:ring-[#1400FB] text-sm"></textarea>
                    <x-input-error :messages="$errors->get('business_description')" class="mt-1" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Business Email" :required="true" />
                        <x-text-input wire:model.defer="business_email" type="email" placeholder="support@yourbusiness.com" class="w-full mt-1.5" />
                        <x-input-error :messages="$errors->get('business_email')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label value="Business Phone Number" :required="true" />
                        <x-text-input wire:model.defer="business_phone" placeholder="e.g. 08012345678" class="w-full mt-1.5" />
                        <x-input-error :messages="$errors->get('business_phone')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label value="Website URL (Optional)" />
                        <x-text-input wire:model.defer="business_website" placeholder="https://yourwebsite.com" class="w-full mt-1.5" />
                        <x-input-error :messages="$errors->get('business_website')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label value="BVN (Bank Verification Number)" :required="true" />
                        <x-text-input wire:model.defer="bvn" placeholder="11-digit BVN" maxlength="11" class="w-full mt-1.5" />
                        <x-input-error :messages="$errors->get('bvn')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label value="Tax Identification Number (TIN - Optional)" />
                        <x-text-input wire:model.defer="tin" placeholder="TIN Number" class="w-full mt-1.5" />
                        <x-input-error :messages="$errors->get('tin')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label value="Business Social Handles (Optional)" />
                        <x-text-input wire:model.defer="business_social_media_handles" placeholder="@business_handle" class="w-full mt-1.5" />
                        <x-input-error :messages="$errors->get('business_social_media_handles')" class="mt-1" />
                    </div>
                </div>

                <div>
                    <x-input-label value="Business Operating Address" :required="true" />
                    <textarea wire:model.defer="business_address" rows="2" placeholder="Full address of your shop, office or operational base..." class="w-full mt-1.5 rounded-xl border-gray-300 focus:border-[#1400FB] focus:ring-[#1400FB] text-sm"></textarea>
                    <x-input-error :messages="$errors->get('business_address')" class="mt-1" />
                </div>

                <!-- Business Logo Upload Palate -->
                <div class="pt-4 border-t border-gray-100">
                    <x-file-upload-palate 
                        wireModel="business_logo_upload" 
                        :existingPath="$business_logo_path" 
                        label="Business Brand Logo (Optional)" 
                        hint="Displayed on your payment checkout pages (PNG, JPG, SVG up to 10MB)"
                        accept=".png,.jpg,.jpeg,.svg,.webp"
                    />
                </div>

                <!-- Footer with Back and Next buttons -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <button 
                        type="button" 
                        wire:click="goToStep(1)" 
                        class="px-5 py-3 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-2xl hover:bg-gray-100 transition duration-150 flex items-center gap-2"
                    >
                        <i class="fa fa-arrow-left text-xs"></i>
                        <span>Back</span>
                    </button>

                    <button 
                        type="submit" 
                        :disabled="activeUploads > 0" 
                        wire:loading.attr="disabled"
                        class="px-8 py-3.5 rounded-2xl bg-[#1400FB] text-white font-extrabold hover:bg-blue-700 transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed transform hover:-translate-y-0.5"
                    >
                        <span x-show="activeUploads > 0" class="flex items-center gap-2">
                            <i class="fa fa-spinner fa-spin text-sm"></i>
                            <span>Attaching Document... Please wait</span>
                        </span>
                        <span x-show="activeUploads === 0" wire:loading.remove wire:target="submitStep2" class="flex items-center gap-2">
                            <span>Next: Settlement Account</span>
                            <i class="fa fa-arrow-right text-xs"></i>
                        </span>
                        <span wire:loading wire:target="submitStep2" class="flex items-center gap-2">
                            <i class="fa fa-spinner fa-spin text-sm"></i>
                            <span>Saving details...</span>
                        </span>
                    </button>
                </div>
            </form>
        @endif

        <!-- STEP 3: Settlement Account -->
        @if ($currentStep === 3)
            <div class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 space-y-6 shadow-sm">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-blue-100 text-[#1400FB] flex items-center justify-center text-xs font-extrabold">3</span>
                        <span>Settlement Bank Account</span>
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">This is the bank account where your business proceeds and customer payments will be settled.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Bank" :required="true" />
                        <select wire:model.defer="settlement_bank_id" class="w-full mt-1.5 rounded-xl border-gray-300 focus:border-[#1400FB] focus:ring-[#1400FB] text-sm">
                            <option value="">Select bank</option>
                            @foreach($banks as $bank)
                                <option value="{{ $bank['code'] ?? $bank['bankCode'] }}" @selected((string)$settlement_bank_id === (string)($bank['code'] ?? $bank['bankCode']))>
                                    {{ $bank['name'] ?? $bank['bankName'] }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('settlement_bank_id')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label value="Account Number" :required="true" />
                        <x-text-input wire:model.defer="settlement_account_number" placeholder="10-digit account number" maxlength="10" class="w-full mt-1.5" />
                        <x-input-error :messages="$errors->get('settlement_account_number')" class="mt-1" />
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 bg-blue-50/60 rounded-2xl border border-blue-200/80">
                    <button 
                        type="button" 
                        wire:click="verifySettlementAccount" 
                        wire:loading.attr="disabled"
                        class="px-5 py-2.5 rounded-xl bg-[#1400FB] text-white font-bold text-xs hover:bg-blue-700 transition shadow-sm flex items-center gap-2 flex-shrink-0"
                    >
                        <span wire:loading.remove wire:target="verifySettlementAccount"><i class="fa fa-magnifying-glass mr-1"></i> Verify Account</span>
                        <span wire:loading wire:target="verifySettlementAccount"><i class="fa fa-spinner fa-spin mr-1"></i> Resolving...</span>
                    </button>

                    @if($settlement_account_name)
                        <div class="flex items-center gap-2 text-sm text-gray-800">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs"><i class="fa fa-check"></i></span>
                            <span>Account Name: <strong class="text-gray-900 font-extrabold">{{ $settlement_account_name }}</strong></span>
                        </div>
                    @endif
                </div>

                @if ($settlement_verification_unavailable || !$settlement_account_name)
                    <div>
                        <x-input-label value="Account Name" :required="true" />
                        <x-text-input wire:model.defer="settlement_account_name" placeholder="Enter full name as on bank account" class="w-full mt-1.5" />
                        <x-input-error :messages="$errors->get('settlement_account_name')" class="mt-1" />
                    </div>
                @endif

                <!-- Footer with Back and Next buttons -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <button 
                        type="button" 
                        wire:click="goToStep(2)" 
                        class="px-5 py-3 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-2xl hover:bg-gray-100 transition duration-150 flex items-center gap-2"
                    >
                        <i class="fa fa-arrow-left text-xs"></i>
                        <span>Back</span>
                    </button>

                    <button 
                        type="button" 
                        wire:click="submitStep3" 
                        wire:loading.attr="disabled"
                        class="px-8 py-3.5 rounded-2xl bg-[#1400FB] text-white font-extrabold hover:bg-blue-700 transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl disabled:opacity-50 transform hover:-translate-y-0.5"
                    >
                        <span wire:loading.remove wire:target="submitStep3" class="flex items-center gap-2">
                            <span>Next: Company Docs</span>
                            <i class="fa fa-arrow-right text-xs"></i>
                        </span>
                        <span wire:loading wire:target="submitStep3" class="flex items-center gap-2">
                            <i class="fa fa-spinner fa-spin text-sm"></i>
                            <span>Saving settlement...</span>
                        </span>
                    </button>
                </div>
            </div>
        @endif

        <!-- STEP 4: Company Docs -->
        @if ($currentStep === 4)
            <form wire:submit.prevent="submitStep4" class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 space-y-6 shadow-sm">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-blue-100 text-[#1400FB] flex items-center justify-center text-xs font-extrabold">4</span>
                        <span>Company Verification Documents</span>
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">
                        Upload your registered company documents. Files attach automatically and are saved securely as soon as selected.
                    </p>
                </div>

                <div>
                    <x-input-label value="CAC Registration Number (RC / BN Number)" :required="true" />
                    <x-text-input wire:model.defer="cac_registration_number" placeholder="e.g. RC 1234567 or BN 9876543" class="w-full mt-1.5" />
                    <x-input-error :messages="$errors->get('cac_registration_number')" class="mt-1" />
                </div>

                <!-- 4 Document Upload Palates -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2">
                    <x-file-upload-palate 
                        wireModel="business_resolution_upload" 
                        :existingPath="$business_resolution_path" 
                        label="Business Resolution" 
                        hint="Board or proprietorship resolution authorizing account opening (PDF, JPG, PNG up to 10MB)"
                        :sampleUrl="$sampleUrls['business_resolution']"
                        :required="empty($business_resolution_path)"
                    />

                    <x-file-upload-palate 
                        wireModel="company_profile_upload" 
                        :existingPath="$company_profile_path" 
                        label="Company Profile" 
                        hint="Overview of your company operations and services (PDF, JPG, PNG up to 10MB)"
                        :sampleUrl="$sampleUrls['company_profile']"
                        :required="empty($company_profile_path)"
                    />

                    <x-file-upload-palate 
                        wireModel="status_report_upload" 
                        :existingPath="$status_report_path" 
                        label="CAC Status Report" 
                        hint="Official CAC status report or certificate of incorporation (PDF, JPG, PNG up to 10MB)"
                        :sampleUrl="$sampleUrls['status_report']"
                        :required="empty($status_report_path)"
                    />

                    <x-file-upload-palate 
                        wireModel="business_address_verification_upload" 
                        :existingPath="$business_address_verification_path" 
                        label="Business Address Verification" 
                        hint="Utility bill, lease agreement, or tax receipt for business address (PDF, JPG, PNG up to 10MB)"
                        :sampleUrl="$sampleUrls['business_address_verification']"
                        :required="empty($business_address_verification_path)"
                    />
                </div>

                <!-- Footer with Back and Next buttons synchronized with active uploads -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                    <button 
                        type="button" 
                        wire:click="goToStep(3)" 
                        class="px-5 py-3 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-2xl hover:bg-gray-100 transition duration-150 flex items-center gap-2"
                    >
                        <i class="fa fa-arrow-left text-xs"></i>
                        <span>Back</span>
                    </button>

                    <button 
                        type="submit" 
                        :disabled="activeUploads > 0" 
                        wire:loading.attr="disabled"
                        class="px-8 py-3.5 rounded-2xl bg-[#1400FB] text-white font-extrabold hover:bg-blue-700 transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed transform hover:-translate-y-0.5"
                    >
                        <span x-show="activeUploads > 0" class="flex items-center gap-2">
                            <i class="fa fa-spinner fa-spin text-sm"></i>
                            <span>Attaching Document... Please wait</span>
                        </span>
                        <span x-show="activeUploads === 0" wire:loading.remove wire:target="submitStep4" class="flex items-center gap-2">
                            <span>Next: Security PIN</span>
                            <i class="fa fa-arrow-right text-xs"></i>
                        </span>
                        <span wire:loading wire:target="submitStep4" class="flex items-center gap-2">
                            <i class="fa fa-spinner fa-spin text-sm"></i>
                            <span>Saving Documents...</span>
                        </span>
                    </button>
                </div>
            </form>
        @endif

        <!-- STEP 5: Security PIN -->
        @if ($currentStep === 5)
            <div class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 space-y-6 shadow-sm text-center">
                <div class="flex items-center justify-between">
                    <button 
                        type="button" 
                        wire:click="goToStep(4)" 
                        class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition flex items-center gap-1.5"
                    >
                        <i class="fa fa-arrow-left text-[10px]"></i>
                        <span>Back to Docs</span>
                    </button>
                    <span class="text-xs font-bold text-[#1400FB] uppercase tracking-wider">Final Step</span>
                </div>

                <div>
                    <div class="w-16 h-16 rounded-full bg-blue-100 text-[#1400FB] flex items-center justify-center mx-auto text-2xl shadow-inner mb-3">
                        <i class="fa fa-lock"></i>
                    </div>
                    <h3 class="text-2xl font-extrabold text-gray-900">
                        @if($pinStage === 1)
                            Create Transaction PIN
                        @else
                            Confirm Transaction PIN
                        @endif
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        @if($pinStage === 1)
                            Choose a secure 4-digit PIN for approving withdrawals and transfers.
                        @else
                            Re-enter your 4-digit PIN to confirm.
                        @endif
                    </p>
                </div>

                <!-- PIN Dots -->
                <div class="flex justify-center space-x-4 my-6">
                    @for($i = 1; $i <= 4; $i++)
                        <div class="w-5 h-5 sm:w-6 sm:h-6 @if(isset($pin[$i-1])) bg-[#1400FB] scale-110 shadow-md ring-4 ring-blue-100 @else bg-gray-200 @endif rounded-full transition-all duration-200"></div>
                    @endfor
                </div>

                <!-- PIN Keypad -->
                <div class="grid grid-cols-3 gap-3 sm:gap-4 max-w-xs mx-auto">
                    @for($i = 1; $i <= 9; $i++)
                        <button 
                            type="button" 
                            wire:click="addDigit({{ $i }})" 
                            class="h-16 w-16 sm:h-20 sm:w-20 rounded-2xl mx-auto flex items-center justify-center text-2xl font-bold bg-gray-50 hover:bg-[#1400FB] hover:text-white text-gray-800 transition duration-150 border border-gray-200 active:scale-95 shadow-sm"
                        >
                            {{ $i }}
                        </button>
                    @endfor
                    <button 
                        type="button" 
                        wire:click="clearDigit" 
                        class="h-16 w-16 sm:h-20 sm:w-20 rounded-2xl mx-auto flex items-center justify-center text-xl font-bold bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition duration-150 border border-red-200 active:scale-95"
                        title="Delete digit"
                    >
                        <i class="fa fa-delete-left"></i>
                    </button>
                    <button 
                        type="button" 
                        wire:click="addDigit(0)" 
                        class="h-16 w-16 sm:h-20 sm:w-20 rounded-2xl mx-auto flex items-center justify-center text-2xl font-bold bg-gray-50 hover:bg-[#1400FB] hover:text-white text-gray-800 transition duration-150 border border-gray-200 active:scale-95 shadow-sm"
                    >
                        0
                    </button>
                    @if ($pinStage === 1)
                        <button 
                            type="button" 
                            wire:click="nextPinStage" 
                            class="h-16 w-16 sm:h-20 sm:w-20 rounded-2xl mx-auto flex items-center justify-center text-xl font-bold bg-[#1400FB] text-white hover:bg-blue-700 transition duration-150 shadow-md active:scale-95"
                            title="Continue to confirmation"
                        >
                            <i class="fa fa-arrow-right"></i>
                        </button>
                    @else
                        <button 
                            type="button" 
                            wire:click="completeOnboarding" 
                            class="h-16 w-16 sm:h-20 sm:w-20 rounded-2xl mx-auto flex items-center justify-center text-xl font-bold bg-emerald-600 text-white hover:bg-emerald-700 transition duration-150 shadow-md active:scale-95"
                            title="Complete Activation"
                        >
                            <i class="fa fa-check"></i>
                        </button>
                    @endif
                </div>
            </div>
        @endif

        @endif
    </div>

    <!-- Sample Document Lightbox Modal -->
    <div 
        x-show="showSampleModal" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak 
        class="fixed inset-0 bg-black/75 z-50 flex items-center justify-center p-4 backdrop-blur-sm"
    >
        <div class="bg-white rounded-3xl w-full max-w-3xl overflow-hidden shadow-2xl border border-gray-100">
            <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-gray-50">
                <div class="flex items-center gap-2">
                    <i class="fa fa-file-lines text-[#1400FB]"></i>
                    <h3 class="text-base font-bold text-gray-900" x-text="sampleTitle || 'Sample Document'"></h3>
                </div>
                <button 
                    type="button" 
                    @click="showSampleModal = false; sampleUrl = null;"
                    class="w-8 h-8 rounded-full bg-white hover:bg-gray-200 text-gray-500 hover:text-gray-900 flex items-center justify-center transition"
                >
                    <i class="fa fa-xmark"></i>
                </button>
            </div>

            <div class="p-4 bg-gray-100">
                <template x-if="sampleUrl">
                    <iframe :src="sampleUrl" class="w-full h-[65vh] rounded-2xl bg-white border border-gray-300 shadow-inner"></iframe>
                </template>
                <template x-if="!sampleUrl">
                    <div class="p-12 text-center text-gray-500">
                        <i class="fa fa-file-circle-question text-4xl text-gray-300 mb-2"></i>
                        <p class="font-medium">No preview sample has been configured for this document yet.</p>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
