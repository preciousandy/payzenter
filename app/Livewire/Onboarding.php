<?php

namespace App\Livewire;

use App\Helpers\SettingHelper;
use App\Models\Kyc;
use App\Models\MerchantKyb;
use App\Models\User;
use App\Models\UserReferral;
use App\Services\HashIdService;
use App\Services\TransferService;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class Onboarding extends Component
{
    use WithFileUploads;
    use LivewireAlert;

    public $currentStep = 1;
    public $kybTableMissing = false;

    // Step 1: Personal Info
    public $surname;
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $country = 'Nigeria';
    public $state;
    public $lga;
    public $address;
    public $pep_declaration = false;
    public $social_media_handle;
    public $identity_document_upload;
    public $proof_of_address_upload;
    public $identity_document_path;
    public $proof_of_address_path;

    // Step 2: Business Info
    public $business_name;
    public $business_description;
    public $business_email;
    public $business_phone;
    public $business_website;
    public $business_address;
    public $business_type;
    public $bvn;
    public $tin;
    public $business_social_media_handles;
    public $business_logo_upload;
    public $business_logo_path;

    // Step 3: Settlement Bank
    public $banks = [];
    public $settlement_bank_id;
    public $settlement_bank_name;
    public $settlement_account_number;
    public $settlement_account_name;
    public $settlement_verified_at;
    public $settlement_verification_unavailable = false;

    // Step 4: Company Docs
    public $cac_registration_number;
    public $business_resolution_upload;
    public $company_profile_upload;
    public $status_report_upload;
    public $business_address_verification_upload;
    public $business_resolution_path;
    public $company_profile_path;
    public $status_report_path;
    public $business_address_verification_path;

    // Step 5: PIN
    public $pinStage = 1;
    public $pin1;
    public $pin2;
    public $pin = [];

    public $sampleUrls = [
        'business_resolution' => null,
        'company_profile' => null,
        'status_report' => null,
        'business_address_verification' => null,
    ];

    protected $messages = [
        'identity_document_upload.required' => 'Please attach your Means of Identity document before proceeding.',
        'proof_of_address_upload.required' => 'Please attach your Proof of Address document before proceeding.',
        'business_resolution_upload.required' => 'Please attach your Business Resolution document before proceeding.',
        'company_profile_upload.required' => 'Please attach your Company Profile document before proceeding.',
        'status_report_upload.required' => 'Please attach your CAC Status Report document before proceeding.',
        'business_address_verification_upload.required' => 'Please attach your Address Verification document before proceeding.',
        '*.max' => 'The selected file is too large. Maximum allowed size is 10MB.',
        '*.mimes' => 'Unsupported file format. Please upload a PDF, PNG, JPG or JPEG file.',
    ];

    public function mount()
    {
        $user = auth()->user();

        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->phone = $user->phone;

        if (!Schema::hasTable('merchant_kybs')) {
            $this->kybTableMissing = true;
            return;
        }

        $kyb = MerchantKyb::firstOrCreate(['user_id' => $user->id]);

        $this->surname = $kyb->surname;
        $this->country = $kyb->country ?? $this->country;
        $this->state = $kyb->state;
        $this->lga = $kyb->lga;
        $this->address = $kyb->address;
        $this->pep_declaration = (bool) $kyb->pep_declaration;
        $this->social_media_handle = $kyb->social_media_handle;
        $this->identity_document_path = $kyb->identity_document_path;
        $this->proof_of_address_path = $kyb->proof_of_address_path;

        $this->business_name = $kyb->business_name ?? $user->company_name;
        $this->business_description = $kyb->business_description;
        $this->business_email = $kyb->business_email ?? $user->email;
        $this->business_phone = $kyb->business_phone ?? $user->phone;
        $this->business_website = $kyb->business_website ?? $user->website_link;
        $this->business_address = $kyb->business_address;
        $this->business_type = $kyb->business_type ?? $user->business_type;
        $this->bvn = $kyb->bvn ?? $user->bvn;
        $this->tin = $kyb->tin;
        $this->business_social_media_handles = $kyb->business_social_media_handles;
        $this->business_logo_path = $kyb->business_logo_path;

        $this->settlement_bank_id = $kyb->settlement_bank_id;
        $this->settlement_bank_name = $kyb->settlement_bank_name;
        $this->settlement_account_number = $kyb->settlement_account_number;
        $this->settlement_account_name = $kyb->settlement_account_name;
        $this->settlement_verified_at = $kyb->settlement_verified_at;

        $this->cac_registration_number = $kyb->cac_registration_number;
        $this->business_resolution_path = $kyb->business_resolution_path;
        $this->company_profile_path = $kyb->company_profile_path;
        $this->status_report_path = $kyb->status_report_path;
        $this->business_address_verification_path = $kyb->business_address_verification_path;

        $this->sampleUrls = [
            'business_resolution' => env('KYB_SAMPLE_BUSINESS_RESOLUTION_URL'),
            'company_profile' => env('KYB_SAMPLE_COMPANY_PROFILE_URL'),
            'status_report' => env('KYB_SAMPLE_STATUS_REPORT_URL'),
            'business_address_verification' => env('KYB_SAMPLE_BUSINESS_ADDRESS_VERIFICATION_URL'),
        ];

        $cachedBanks = cache()->get('dot_banks');
        if (empty($cachedBanks) || !is_array($cachedBanks)) {
            $this->banks = getDotBanks();
            if (!empty($this->banks)) {
                cache()->put('dot_banks', $this->banks, 60 * 60 * 24 * 2);
            }
        } else {
            $this->banks = $cachedBanks;
        }

        $this->currentStep = $this->resolveCurrentStep($kyb, $user);
    }

    private function resolveCurrentStep(MerchantKyb $kyb, User $user): int
    {
        if ($kyb->submitted_at && $user->pin) {
            return 5;
        }

        $hasDocs = $kyb->business_resolution_path && $kyb->company_profile_path && $kyb->status_report_path && $kyb->business_address_verification_path && $kyb->cac_registration_number;
        if ($hasDocs) {
            return 5;
        }

        $hasSettlement = $kyb->settlement_account_number && $kyb->settlement_bank_id && $kyb->settlement_account_name;
        if ($hasSettlement) {
            return 4;
        }

        $hasBusiness = $kyb->business_name && $kyb->business_email && $kyb->business_phone && $kyb->business_type && $kyb->bvn && $kyb->business_address;
        if ($hasBusiness) {
            return 3;
        }

        $hasPersonal = $kyb->surname && $kyb->first_name && $kyb->last_name && $kyb->email && $kyb->phone && $kyb->country && $kyb->state && $kyb->lga && $kyb->address && $kyb->identity_document_path && $kyb->proof_of_address_path;
        if ($hasPersonal) {
            return 2;
        }

        return 1;
    }

    public function goToStep($step)
    {
        if ($step >= 1 && $step <= 5) {
            $this->currentStep = (int) $step;
        }
    }

    public function prevStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    // Real-time hooks to immediately persist files upon upload completion
    public function updatedIdentityDocumentUpload()
    {
        $this->validateOnly('identity_document_upload', [
            'identity_document_upload' => 'required|file|max:10240|mimes:jpg,jpeg,png,pdf',
        ]);
        try {
            $path = $this->identity_document_upload->store('kyb', ['disk' => 'public']);
            $this->identity_document_path = $path;
            MerchantKyb::where('user_id', auth()->id())->update(['identity_document_path' => $path]);
            $this->alert('success', 'Means of identity attached and saved!');
        } catch (\Throwable $e) {
            $this->alert('error', 'Failed to save document. Please try again.');
        }
    }

    public function updatedProofOfAddressUpload()
    {
        $this->validateOnly('proof_of_address_upload', [
            'proof_of_address_upload' => 'required|file|max:10240|mimes:jpg,jpeg,png,pdf',
        ]);
        try {
            $path = $this->proof_of_address_upload->store('kyb', ['disk' => 'public']);
            $this->proof_of_address_path = $path;
            MerchantKyb::where('user_id', auth()->id())->update(['proof_of_address_path' => $path]);
            $this->alert('success', 'Proof of address attached and saved!');
        } catch (\Throwable $e) {
            $this->alert('error', 'Failed to save document. Please try again.');
        }
    }

    public function updatedBusinessLogoUpload()
    {
        $this->validateOnly('business_logo_upload', [
            'business_logo_upload' => 'required|image|max:10240|mimes:jpg,jpeg,png,webp,svg',
        ]);
        try {
            $path = $this->business_logo_upload->store('kyb', ['disk' => 'public']);
            $this->business_logo_path = $path;
            MerchantKyb::where('user_id', auth()->id())->update(['business_logo_path' => $path]);
            $this->alert('success', 'Business logo uploaded!');
        } catch (\Throwable $e) {
            $this->alert('error', 'Failed to save logo. Please try again.');
        }
    }

    public function updatedBusinessResolutionUpload()
    {
        $this->validateOnly('business_resolution_upload', [
            'business_resolution_upload' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png',
        ]);
        try {
            $path = $this->business_resolution_upload->store('kyb', ['disk' => 'public']);
            $this->business_resolution_path = $path;
            MerchantKyb::where('user_id', auth()->id())->update(['business_resolution_path' => $path]);
            $this->alert('success', 'Business resolution attached and saved!');
        } catch (\Throwable $e) {
            $this->alert('error', 'Failed to save document. Please try again.');
        }
    }

    public function updatedCompanyProfileUpload()
    {
        $this->validateOnly('company_profile_upload', [
            'company_profile_upload' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png',
        ]);
        try {
            $path = $this->company_profile_upload->store('kyb', ['disk' => 'public']);
            $this->company_profile_path = $path;
            MerchantKyb::where('user_id', auth()->id())->update(['company_profile_path' => $path]);
            $this->alert('success', 'Company profile attached and saved!');
        } catch (\Throwable $e) {
            $this->alert('error', 'Failed to save document. Please try again.');
        }
    }

    public function updatedStatusReportUpload()
    {
        $this->validateOnly('status_report_upload', [
            'status_report_upload' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png',
        ]);
        try {
            $path = $this->status_report_upload->store('kyb', ['disk' => 'public']);
            $this->status_report_path = $path;
            MerchantKyb::where('user_id', auth()->id())->update(['status_report_path' => $path]);
            $this->alert('success', 'Status report attached and saved!');
        } catch (\Throwable $e) {
            $this->alert('error', 'Failed to save document. Please try again.');
        }
    }

    public function updatedBusinessAddressVerificationUpload()
    {
        $this->validateOnly('business_address_verification_upload', [
            'business_address_verification_upload' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png',
        ]);
        try {
            $path = $this->business_address_verification_upload->store('kyb', ['disk' => 'public']);
            $this->business_address_verification_path = $path;
            MerchantKyb::where('user_id', auth()->id())->update(['business_address_verification_path' => $path]);
            $this->alert('success', 'Address verification document attached and saved!');
        } catch (\Throwable $e) {
            $this->alert('error', 'Failed to save document. Please try again.');
        }
    }

    public function addDigit($digit)
    {
        if (count($this->pin) < 4) {
            $this->pin[] = $digit;
        }
    }

    public function clearDigit()
    {
        array_pop($this->pin);
    }

    public function nextPinStage()
    {
        if ($this->currentStep !== 5) {
            return;
        }

        if (count($this->pin) < 4) {
            $this->alert('error', 'PIN must be 4 digits');
            return;
        }

        $this->pin1 = implode('', $this->pin);
        $this->pinStage = 2;
        $this->reset('pin');
    }

    public function submitStep1()
    {
        $rules = [
            'surname' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:25',
            'country' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'lga' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'pep_declaration' => 'boolean',
            'social_media_handle' => 'nullable|string|max:100',
            'identity_document_upload' => $this->identity_document_path ? 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf' : 'required|file|max:10240|mimes:jpg,jpeg,png,pdf',
            'proof_of_address_upload' => $this->proof_of_address_path ? 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf' : 'required|file|max:10240|mimes:jpg,jpeg,png,pdf',
        ];

        $data = $this->validate($rules);

        $kyb = MerchantKyb::firstOrCreate(['user_id' => auth()->id()]);

        if ($this->identity_document_upload) {
            $this->identity_document_path = $this->identity_document_upload->store('kyb', ['disk' => 'public']);
        }
        if ($this->proof_of_address_upload) {
            $this->proof_of_address_path = $this->proof_of_address_upload->store('kyb', ['disk' => 'public']);
        }

        $kyb->update([
            'surname' => $data['surname'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'country' => $data['country'],
            'state' => $data['state'],
            'lga' => $data['lga'],
            'address' => $data['address'],
            'pep_declaration' => $data['pep_declaration'],
            'social_media_handle' => $data['social_media_handle'] ?? null,
            'identity_document_path' => $this->identity_document_path,
            'proof_of_address_path' => $this->proof_of_address_path,
        ]);

        auth()->user()->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'],
        ]);

        $this->currentStep = 2;
    }

    public function submitStep2()
    {
        $data = $this->validate([
            'business_name' => 'required|string|max:150',
            'business_description' => 'required|string|max:500',
            'business_email' => 'required|email|max:255',
            'business_phone' => 'required|string|max:25',
            'business_website' => 'nullable|string|max:255',
            'business_address' => 'required|string|max:500',
            'business_type' => 'required|string|max:100',
            'bvn' => 'required|string|max:30',
            'tin' => 'nullable|string|max:50',
            'business_social_media_handles' => 'nullable|string|max:255',
            'business_logo_upload' => 'nullable|image|max:10240|mimes:jpg,jpeg,png,webp,svg',
        ]);

        $kyb = MerchantKyb::firstOrCreate(['user_id' => auth()->id()]);

        if ($this->business_logo_upload) {
            $this->business_logo_path = $this->business_logo_upload->store('kyb', ['disk' => 'public']);
        }

        $kyb->update([
            'business_name' => $data['business_name'],
            'business_description' => $data['business_description'],
            'business_email' => $data['business_email'],
            'business_phone' => $data['business_phone'],
            'business_website' => $data['business_website'] ?? null,
            'business_address' => $data['business_address'],
            'business_type' => $data['business_type'],
            'bvn' => $data['bvn'],
            'tin' => $data['tin'] ?? null,
            'business_social_media_handles' => $data['business_social_media_handles'] ?? null,
            'business_logo_path' => $this->business_logo_path,
        ]);

        auth()->user()->update([
            'company_name' => $data['business_name'],
            'website_link' => $data['business_website'] ?? auth()->user()->website_link,
            'business_type' => $data['business_type'],
            'bvn' => $data['bvn'],
        ]);

        $this->currentStep = 3;
    }

    public function verifySettlementAccount(TransferService $transferService)
    {
        $data = $this->validate([
            'settlement_bank_id' => 'required|string',
            'settlement_account_number' => 'required|numeric|digits:10',
        ]);

        $bank = collect($this->banks)->first(function ($item) use ($data) {
            $code = (string) ($item['code'] ?? $item['bankCode'] ?? '');
            return $code !== '' && $code === (string) $data['settlement_bank_id'];
        });
        if (!$bank) {
            $this->alert('error', 'Invalid bank selected');
            return;
        }

        $this->settlement_bank_name = $bank['name'] ?? $bank['bankName'] ?? null;

        try {
            $res = $transferService->getDotAccount($data['settlement_bank_id'], $data['settlement_account_number']);
        } catch (\Throwable $e) {
            $this->settlement_verification_unavailable = true;
            $this->settlement_verified_at = null;
            $this->settlement_account_name = null;
            $this->alert('warning', 'Unable to auto-verify account details. Please enter your account name manually.');
            return;
        }

        if (!$res || ($res['status'] ?? null) !== 'success') {
            $this->settlement_verification_unavailable = true;
            $this->settlement_verified_at = null;
            $this->settlement_account_name = null;
            $this->alert('warning', 'Unable to auto-verify account details. Please enter your account name manually below.');
            return;
        }

        $this->settlement_account_name = $res['data']['accountName'] ?? $res['data']['account_name'] ?? null;
        $this->settlement_verified_at = now();
        $this->settlement_verification_unavailable = false;
    }

    public function submitStep3()
    {
        $this->validate([
            'settlement_bank_id' => 'required|string',
            'settlement_bank_name' => 'required|string|max:150',
            'settlement_account_number' => 'required|numeric|digits:10',
            'settlement_account_name' => 'required|string|max:255',
        ]);

        $kyb = MerchantKyb::firstOrCreate(['user_id' => auth()->id()]);

        $kyb->update([
            'settlement_bank_id' => $this->settlement_bank_id,
            'settlement_bank_name' => $this->settlement_bank_name,
            'settlement_account_number' => $this->settlement_account_number,
            'settlement_account_name' => $this->settlement_account_name,
            'settlement_verified_at' => $this->settlement_verified_at,
        ]);

        $account = auth()->user()->bankAccounts()->updateOrCreate(
            [
                'account_number' => $this->settlement_account_number,
                'bank_name' => $this->settlement_bank_name,
            ],
            [
                'account_name' => $this->settlement_account_name,
                'is_default' => true,
            ]
        );

        auth()->user()->bankAccounts()
            ->where('id', '!=', $account->id)
            ->update(['is_default' => false]);

        $this->currentStep = 4;
    }

    public function submitStep4()
    {
        $rules = [
            'cac_registration_number' => 'required|string|max:100',
            'business_resolution_upload' => $this->business_resolution_path ? 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png' : 'required|file|max:10240|mimes:pdf,jpg,jpeg,png',
            'company_profile_upload' => $this->company_profile_path ? 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png' : 'required|file|max:10240|mimes:pdf,jpg,jpeg,png',
            'status_report_upload' => $this->status_report_path ? 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png' : 'required|file|max:10240|mimes:pdf,jpg,jpeg,png',
            'business_address_verification_upload' => $this->business_address_verification_path ? 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png' : 'required|file|max:10240|mimes:pdf,jpg,jpeg,png',
        ];

        $data = $this->validate($rules);

        $kyb = MerchantKyb::firstOrCreate(['user_id' => auth()->id()]);

        if ($this->business_resolution_upload) {
            $this->business_resolution_path = $this->business_resolution_upload->store('kyb', ['disk' => 'public']);
        }
        if ($this->company_profile_upload) {
            $this->company_profile_path = $this->company_profile_upload->store('kyb', ['disk' => 'public']);
        }
        if ($this->status_report_upload) {
            $this->status_report_path = $this->status_report_upload->store('kyb', ['disk' => 'public']);
        }
        if ($this->business_address_verification_upload) {
            $this->business_address_verification_path = $this->business_address_verification_upload->store('kyb', ['disk' => 'public']);
        }

        $kyb->update([
            'cac_registration_number' => $data['cac_registration_number'],
            'business_resolution_path' => $this->business_resolution_path,
            'company_profile_path' => $this->company_profile_path,
            'status_report_path' => $this->status_report_path,
            'business_address_verification_path' => $this->business_address_verification_path,
        ]);

        $this->currentStep = 5;
    }

    public function completeOnboarding()
    {
        if ($this->currentStep !== 5) {
            return;
        }

        if (count($this->pin) < 4) {
            $this->alert('error', 'PIN must be 4 digits');
            return;
        }

        $this->pin2 = implode('', $this->pin);

        if ($this->pin1 !== $this->pin2) {
            $this->alert('error', 'PIN does not match');
            return;
        }

        auth()->user()->update([
            'pin' => Hash::make($this->pin1)
        ]);

        MerchantKyb::where('user_id', auth()->id())->update([
            'submitted_at' => now(),
        ]);

        auth()->user()->update([
            'kyc_status' => Kyc::STATUS_PENDING,
        ]);

        if (Cookie::has('referral')) {
            $user = (new HashIdService())->getRecord(Cookie::get('referral'), User::class);

            UserReferral::create([
                'user_id' => auth()->id(),
                'referred_by' => $user->id,
                'status' => UserReferral::STATUS_PENDING,
                'amount' => SettingHelper::get(SettingHelper::REFERRAL_BONUS)
            ]);
        }

        return $this->flash('success', 'Details submitted successfully! Welcome to your dashboard.', [], route('home'));
    }

    public function render()
    {
        if (empty($this->banks)) {
            $this->banks = getDotBanks();
        }

        return view('livewire.onboarding', [
            'currentStep' => $this->currentStep,
            'kybTableMissing' => $this->kybTableMissing,
            'surname' => $this->surname,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'country' => $this->country,
            'state' => $this->state,
            'lga' => $this->lga,
            'address' => $this->address,
            'pep_declaration' => $this->pep_declaration,
            'social_media_handle' => $this->social_media_handle,
            'identity_document_path' => $this->identity_document_path,
            'proof_of_address_path' => $this->proof_of_address_path,
            'business_name' => $this->business_name,
            'business_description' => $this->business_description,
            'business_email' => $this->business_email,
            'business_phone' => $this->business_phone,
            'business_website' => $this->business_website,
            'business_address' => $this->business_address,
            'business_type' => $this->business_type,
            'bvn' => $this->bvn,
            'tin' => $this->tin,
            'business_social_media_handles' => $this->business_social_media_handles,
            'business_logo_path' => $this->business_logo_path,
            'banks' => $this->banks,
            'settlement_bank_id' => $this->settlement_bank_id,
            'settlement_bank_name' => $this->settlement_bank_name,
            'settlement_account_number' => $this->settlement_account_number,
            'settlement_account_name' => $this->settlement_account_name,
            'settlement_verification_unavailable' => $this->settlement_verification_unavailable,
            'cac_registration_number' => $this->cac_registration_number,
            'business_resolution_path' => $this->business_resolution_path,
            'company_profile_path' => $this->company_profile_path,
            'status_report_path' => $this->status_report_path,
            'business_address_verification_path' => $this->business_address_verification_path,
            'sampleUrls' => $this->sampleUrls,
            'pinStage' => $this->pinStage,
            'pin' => $this->pin,
        ])->extends('layouts.auth')->title('Activate Your Business');
    }
}
