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

    public $banks = [];
    public $settlement_bank_id;
    public $settlement_bank_name;
    public $settlement_account_number;
    public $settlement_account_name;
    public $settlement_verified_at;
    public $settlement_verification_unavailable = false;

    public $cac_registration_number;
    public $business_resolution_upload;
    public $company_profile_upload;
    public $status_report_upload;
    public $business_address_verification_upload;

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

        $this->settlement_bank_id = $kyb->settlement_bank_id;
        $this->settlement_bank_name = $kyb->settlement_bank_name;
        $this->settlement_account_number = $kyb->settlement_account_number;
        $this->settlement_account_name = $kyb->settlement_account_name;
        $this->settlement_verified_at = $kyb->settlement_verified_at;

        $this->cac_registration_number = $kyb->cac_registration_number;

        $this->sampleUrls = [
            'business_resolution' => env('KYB_SAMPLE_BUSINESS_RESOLUTION_URL'),
            'company_profile' => env('KYB_SAMPLE_COMPANY_PROFILE_URL'),
            'status_report' => env('KYB_SAMPLE_STATUS_REPORT_URL'),
            'business_address_verification' => env('KYB_SAMPLE_BUSINESS_ADDRESS_VERIFICATION_URL'),
        ];

        $this->banks = cache()->remember('dot_banks', 60 * 60 * 24 * 2, function () {
            return getDotBanks();
        });

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
        $data = $this->validate([
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
            'identity_document_upload' => 'required|file|max:5120|mimes:jpg,jpeg,png,pdf',
            'proof_of_address_upload' => 'required|file|max:5120|mimes:jpg,jpeg,png,pdf',
        ]);

        $kyb = MerchantKyb::firstOrCreate(['user_id' => auth()->id()]);

        $identityPath = $this->identity_document_upload->store('kyb', ['disk' => 'public']);
        $proofPath = $this->proof_of_address_upload->store('kyb', ['disk' => 'public']);

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
            'identity_document_path' => $identityPath,
            'proof_of_address_path' => $proofPath,
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
            'business_logo_upload' => 'nullable|image|max:5120',
        ]);

        $kyb = MerchantKyb::firstOrCreate(['user_id' => auth()->id()]);

        $logoPath = $kyb->business_logo_path;
        if ($this->business_logo_upload) {
            $logoPath = $this->business_logo_upload->store('kyb', ['disk' => 'public']);
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
            'business_logo_path' => $logoPath,
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
            $code = $item['code'] ?? $item['bankCode'] ?? null;
            return is_string($code) && $code === $data['settlement_bank_id'];
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
            $this->alert('error', 'Dot account verification is not configured yet. Please type the account name manually.');
            return;
        }

        if (!$res || ($res['status'] ?? null) !== 'success') {
            $this->settlement_verification_unavailable = true;
            $this->settlement_verified_at = null;
            $this->settlement_account_name = null;
            $this->alert('error', 'Unable to verify account details right now. Please type the account name manually.');
            return;
        }

        $this->settlement_account_name = $res['data']['accountName'] ?? null;
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
        $data = $this->validate([
            'cac_registration_number' => 'required|string|max:100',
            'business_resolution_upload' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png',
            'company_profile_upload' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png',
            'status_report_upload' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png',
            'business_address_verification_upload' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png',
        ]);

        $kyb = MerchantKyb::firstOrCreate(['user_id' => auth()->id()]);

        $kyb->update([
            'cac_registration_number' => $data['cac_registration_number'],
            'business_resolution_path' => $this->business_resolution_upload->store('kyb', ['disk' => 'public']),
            'company_profile_path' => $this->company_profile_upload->store('kyb', ['disk' => 'public']),
            'status_report_path' => $this->status_report_upload->store('kyb', ['disk' => 'public']),
            'business_address_verification_path' => $this->business_address_verification_upload->store('kyb', ['disk' => 'public']),
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

        return $this->flash('success', 'Details submitted successfully', [], route('home'));
    }

    public function render()
    {
        return view('livewire.onboarding')->extends('layouts.auth')->title('Onboarding');
    }
}
