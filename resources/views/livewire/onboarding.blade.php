<div x-data="{ showSampleModal: false, sampleUrl: null }" class="bg-white min-h-screen px-4 py-6">
    <x-loading-spinner wire:target="submitStep1,submitStep2,verifySettlementAccount,submitStep3,submitStep4,nextPinStage,completeOnboarding,identity_document_upload,proof_of_address_upload,business_logo_upload,business_resolution_upload,company_profile_upload,status_report_upload,business_address_verification_upload" />

    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold">Merchant Onboarding</h2>
            <p class="text-sm text-gray-500">Step {{ $currentStep }} of 5</p>
        </div>

        @if ($kybTableMissing)
            <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl p-4">
                <div class="font-semibold">Onboarding setup incomplete</div>
                <div class="text-sm mt-1">Database migrations have not been applied yet. Run: php artisan migrate</div>
            </div>
        @else
        @php
            $steps = [
                1 => 'Personal Info',
                2 => 'Business Info',
                3 => 'Settlement Account',
                4 => 'Company Docs',
                5 => 'Finish',
            ];
        @endphp

        <div class="flex justify-center space-x-2 mb-8 mt-4">
            @foreach($steps as $stepNum => $label)
                <div class="flex items-center">
                    <div class="@if($currentStep >= $stepNum) bg-[#1400FB] @else bg-gray-300 @endif w-9 h-9 rounded-full flex items-center justify-center text-white font-semibold shadow-md transition duration-300">
                        {{ $stepNum }}
                    </div>
                    <span class="absolute mt-14 text-xs font-medium @if($currentStep >= $stepNum) text-[#1400FB] @else text-gray-500 @endif hidden sm:block">{{ $label }}</span>
                    @if($stepNum < 5)
                        <div class="@if($currentStep > $stepNum) bg-[#1400FB] @else bg-gray-300 @endif w-16 h-1 transition duration-300"></div>
                    @endif
                </div>
            @endforeach
        </div>

        @if ($currentStep === 1)
            <form wire:submit.prevent="submitStep1" class="bg-gray-50 rounded-xl p-4 lg:p-6 space-y-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Surname" />
                        <x-text-input wire:model.defer="surname" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('surname')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label value="First Name" />
                        <x-text-input wire:model.defer="first_name" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label value="Last Name" />
                        <x-text-input wire:model.defer="last_name" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label value="Email" />
                        <x-text-input wire:model.defer="email" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label value="Phone Number" />
                        <x-text-input wire:model.defer="phone" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label value="Country" />
                        <x-text-input wire:model.defer="country" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('country')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label value="State" />
                        <x-text-input wire:model.defer="state" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('state')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label value="LGA" />
                        <x-text-input wire:model.defer="lga" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('lga')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label value="Address" />
                    <textarea wire:model.defer="address" class="w-full mt-1 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model.defer="pep_declaration" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                    <span class="text-sm text-gray-700">PEP declaration</span>
                </div>
                <x-input-error :messages="$errors->get('pep_declaration')" class="mt-2" />

                <div>
                    <x-input-label value="Social Media Handle" />
                    <x-text-input wire:model.defer="social_media_handle" class="w-full mt-1" />
                    <x-input-error :messages="$errors->get('social_media_handle')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Means of Identity (upload)" />
                        <input type="file" wire:model="identity_document_upload" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('identity_document_upload')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label value="Proof of Address (upload)" />
                        <input type="file" wire:model="proof_of_address_upload" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('proof_of_address_upload')" class="mt-2" />
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <x-primary-button class="rounded-xl px-6 py-3">
                        Next: Business Info
                    </x-primary-button>
                </div>
            </form>
        @endif

        @if ($currentStep === 2)
            <form wire:submit.prevent="submitStep2" class="bg-gray-50 rounded-xl p-4 lg:p-6 space-y-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Business Name" />
                        <x-text-input wire:model.defer="business_name" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('business_name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label value="Business Type" />
                        <x-text-input wire:model.defer="business_type" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('business_type')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label value="Description" />
                    <textarea wire:model.defer="business_description" class="w-full mt-1 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    <x-input-error :messages="$errors->get('business_description')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Business Email" />
                        <x-text-input wire:model.defer="business_email" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('business_email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label value="Business Phone" />
                        <x-text-input wire:model.defer="business_phone" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('business_phone')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label value="Business Website" />
                        <x-text-input wire:model.defer="business_website" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('business_website')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label value="BVN" />
                        <x-text-input wire:model.defer="bvn" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('bvn')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label value="TIN" />
                        <x-text-input wire:model.defer="tin" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('tin')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label value="Business Social Handles" />
                        <x-text-input wire:model.defer="business_social_media_handles" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('business_social_media_handles')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label value="Business Address" />
                    <textarea wire:model.defer="business_address" class="w-full mt-1 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    <x-input-error :messages="$errors->get('business_address')" class="mt-2" />
                </div>

                <div>
                    <x-input-label value="Business Logo (upload)" />
                    <input type="file" wire:model="business_logo_upload" class="w-full mt-1" />
                    <x-input-error :messages="$errors->get('business_logo_upload')" class="mt-2" />
                </div>

                <div class="flex justify-end pt-2">
                    <x-primary-button class="rounded-xl px-6 py-3">
                        Next: Settlement Account
                    </x-primary-button>
                </div>
            </form>
        @endif

        @if ($currentStep === 3)
            <div class="bg-gray-50 rounded-xl p-4 lg:p-6 space-y-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Bank" />
                        <select wire:model.defer="settlement_bank_id" class="w-full mt-1 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select bank</option>
                            @foreach($banks as $bank)
                                <option value="{{ $bank['code'] ?? $bank['bankCode'] }}">{{ $bank['name'] ?? $bank['bankName'] }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('settlement_bank_id')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label value="Account Number" />
                        <x-text-input wire:model.defer="settlement_account_number" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('settlement_account_number')" class="mt-2" />
                    </div>
                </div>

                <div class="flex gap-3 items-center">
                    <x-primary-button type="button" wire:click="verifySettlementAccount" class="rounded-xl px-6 py-3">
                        Verify Account
                    </x-primary-button>
                    @if($settlement_account_name)
                        <span class="text-sm text-gray-700">Account Name: <span class="font-semibold">{{ $settlement_account_name }}</span></span>
                    @endif
                </div>

                @if ($settlement_verification_unavailable || !$settlement_account_name)
                    <div>
                        <x-input-label value="Account Name" />
                        <x-text-input wire:model.defer="settlement_account_name" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('settlement_account_name')" class="mt-2" />
                    </div>
                @endif

                <div class="flex justify-end pt-2">
                    <x-primary-button type="button" wire:click="submitStep3" class="rounded-xl px-6 py-3">
                        Next: Company Docs
                    </x-primary-button>
                </div>
            </div>
        @endif

        @if ($currentStep === 4)
            <form wire:submit.prevent="submitStep4" class="bg-gray-50 rounded-xl p-4 lg:p-6 space-y-4">
                <div>
                    <x-input-label value="CAC Registration Number" />
                    <x-text-input wire:model.defer="cac_registration_number" class="w-full mt-1" />
                    <x-input-error :messages="$errors->get('cac_registration_number')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <div class="flex items-center justify-between">
                            <x-input-label value="Business Resolution (upload)" />
                            <button type="button" class="text-sm text-[#1400FB] font-semibold" @click="showSampleModal = true; sampleUrl = @js($sampleUrls['business_resolution']);">See sample</button>
                        </div>
                        <input type="file" wire:model="business_resolution_upload" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('business_resolution_upload')" class="mt-2" />
                    </div>
                    <div>
                        <div class="flex items-center justify-between">
                            <x-input-label value="Company Profile (upload)" />
                            <button type="button" class="text-sm text-[#1400FB] font-semibold" @click="showSampleModal = true; sampleUrl = @js($sampleUrls['company_profile']);">See sample</button>
                        </div>
                        <input type="file" wire:model="company_profile_upload" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('company_profile_upload')" class="mt-2" />
                    </div>
                    <div>
                        <div class="flex items-center justify-between">
                            <x-input-label value="Status Report (upload)" />
                            <button type="button" class="text-sm text-[#1400FB] font-semibold" @click="showSampleModal = true; sampleUrl = @js($sampleUrls['status_report']);">See sample</button>
                        </div>
                        <input type="file" wire:model="status_report_upload" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('status_report_upload')" class="mt-2" />
                    </div>
                    <div>
                        <div class="flex items-center justify-between">
                            <x-input-label value="Business Address Verification (upload)" />
                            <button type="button" class="text-sm text-[#1400FB] font-semibold" @click="showSampleModal = true; sampleUrl = @js($sampleUrls['business_address_verification']);">See sample</button>
                        </div>
                        <input type="file" wire:model="business_address_verification_upload" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('business_address_verification_upload')" class="mt-2" />
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <x-primary-button class="rounded-xl px-6 py-3">
                        Next: Finish
                    </x-primary-button>
                </div>
            </form>
        @endif

        @if ($currentStep === 5)
            <div class="bg-gray-50 rounded-xl p-4 lg:p-6 space-y-4">
                <div class="text-center">
                    <h3 class="text-xl font-semibold">
                        @if($pinStage === 1)
                            Create Transaction PIN
                        @else
                            Confirm Transaction PIN
                        @endif
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">
                        @if($pinStage === 1)
                            Enter a 4-digit PIN
                        @else
                            Re-enter your 4-digit PIN
                        @endif
                    </p>
                </div>

                <div class="flex justify-center space-x-4">
                    @for($i = 1; $i <= 4; $i++)
                        <div class="w-6 h-6 lg:w-8 lg:h-8 @if(isset($pin[$i-1])) bg-[#1400FB] @else bg-gray-300 @endif rounded-full"></div>
                    @endfor
                </div>

                <div class="grid grid-cols-3 gap-x-4 gap-y-6 w-4/5 mx-auto">
                    @for($i = 1; $i <= 9; $i++)
                        <button type="button" wire:click="addDigit({{ $i }})" class="flex items-center justify-center h-16 w-16 lg:w-20 lg:h-20 rounded-full mx-auto cursor-pointer border-2 border-gray-500 focus:bg-[#1400FB] focus:border-[#1400FB] focus:text-white">
                            <span class="text-2xl font-semibold">{{ $i }}</span>
                        </button>
                    @endfor
                    <button type="button" wire:click="clearDigit" class="flex items-center justify-center h-16 w-16 lg:w-20 lg:h-20 rounded-full mx-auto border-2 border-red-600 text-red-600 cursor-pointer">
                        <span class="text-2xl fa fa-delete-left"></span>
                    </button>
                    <button type="button" wire:click="addDigit(0)" class="flex items-center justify-center h-16 w-16 lg:w-20 lg:h-20 rounded-full mx-auto border-2 border-gray-500 focus:bg-[#1400FB] focus:border-[#1400FB] cursor-pointer">
                        <span class="text-2xl font-semibold">0</span>
                    </button>
                    @if ($pinStage === 1)
                        <button type="button" wire:click="nextPinStage" class="flex items-center justify-center h-16 w-16 lg:w-20 lg:h-20 rounded-full mx-auto border-2 border-[#1400FB] text-white bg-[#1400FB] cursor-pointer">
                            <span class="text-2xl fa fa-chevron-right"></span>
                        </button>
                    @else
                        <button type="button" wire:click="completeOnboarding" class="flex items-center justify-center h-16 w-16 lg:w-20 lg:h-20 rounded-full mx-auto border-2 border-[#1400FB] text-white bg-[#1400FB] cursor-pointer">
                            <span class="text-2xl fa fa-check"></span>
                        </button>
                    @endif
                </div>
            </div>
        @endif
        @endif
    </div>

    <div x-show="showSampleModal" x-transition x-cloak class="fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4 overflow-y-auto">
        <div class="bg-white p-4 lg:p-6 rounded-lg w-full max-w-3xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Sample Document</h3>
                <button type="button" @click="showSampleModal = false; sampleUrl = null;">
                    <span class="fa fa-close fa-lg"></span>
                </button>
            </div>

            <template x-if="sampleUrl">
                <iframe :src="sampleUrl" class="w-full h-[70vh] rounded-xl border"></iframe>
            </template>
            <template x-if="!sampleUrl">
                <div class="text-sm text-gray-600">Sample not available yet.</div>
            </template>
        </div>
    </div>
</div>
