<?php

namespace App\Http\Controllers\Api\Gateway;

use App\Http\Controllers\Controller;
use App\Models\UserVirtualAccount;
use App\Models\VirtualAccount;
use App\Services\UserService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VirtualAccountController extends Controller
{
    use ApiResponseTrait;

    public function create(Request $request, UserService $userService)
    {
        $palmpayEnabled = \App\Helpers\SettingHelper::isPalmPayEnabled();
        $udaraEnabled = \App\Helpers\SettingHelper::isUdara360Enabled();
        $flwEnabled = \App\Helpers\SettingHelper::isFlutterwaveEnabled();

        $enabledProviders = [];
        if ($palmpayEnabled) $enabledProviders[] = 'palmpay';
        if ($udaraEnabled) $enabledProviders[] = 'udara360';
        if ($flwEnabled) $enabledProviders[] = 'flutterwave';

        if (empty($enabledProviders)) {
            return $this->error('Virtual account generation is currently disabled by administrator', 503);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:15',
            'email' => 'nullable|email|max:100',
            'provider' => 'nullable|string|in:' . implode(',', $enabledProviders),
        ], [
            'provider.in' => 'The selected provider is invalid or currently disabled.',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $user = $request->user();

        if ($request->filled('provider')) {
            $provider = strtolower((string) $request->input('provider'));
            if (!in_array($provider, $enabledProviders, true)) {
                return $this->error('The selected provider is currently disabled', 422);
            }
        } else {
            $defaultProvider = strtolower((string) \App\Helpers\SettingHelper::get(\App\Helpers\SettingHelper::DEFAULT_VIRTUAL_ACCOUNT_PROVIDER, 'palmpay'));
            if (in_array($defaultProvider, $enabledProviders, true)) {
                $provider = $defaultProvider;
            } else {
                $provider = $enabledProviders[0];
            }
        }

        $phone = $request->input('phone');
        $merchantRef = $phone; // Use phone as the unique reference for the merchant's customer
        
        // Use merchant's BVN or static default BVN
        $bvn = $user->bvn ?? ($user->merchantKyb->bvn ?? null) ?? '22325148306';

        // Check if this phone number already has an account for this specific provider & merchant
        $existing = VirtualAccount::where('user_id', $user->id)
            ->where('customer_phone', $phone)
            ->where('provider', $provider)
            ->first();

        if ($existing) {
            return $this->success([
                'account_number' => $existing->account_number,
                'bank_name' => $existing->bank_name,
                'bank_code' => $existing->bank_code,
                'account_name' => $existing->account_name,
                'provider' => $existing->provider,
                'phone' => $existing->customer_phone,
            ], 'Existing virtual account retrieved');
        }

        $customerDetails = [
            'name' => $request->input('name'),
            'email' => $request->input('email') ?? $user->email, // fallback to merchant email if not provided
            'phone' => $phone,
            'merchant_ref' => $merchantRef,
        ];

        try {
            if ($provider === 'flutterwave') {
                $res = $userService->generateAccountNumber($user, $bvn, $customerDetails);
            } elseif ($provider === 'udara360') {
                $res = $userService->generateUdara360VirtualAccount($user, $bvn, $customerDetails);
            } else {
                $res = $userService->generatePalmPayVirtualAccount($user, $bvn, $customerDetails);
            }

            if (($res['status'] ?? null) !== 'success') {
                return $this->error($res['message'] ?? 'Unable to generate virtual account', 400);
            }

            $data = $res['data'] ?? [];
            $accountNumber = $data['account_number'] ?? $data['accountNo'] ?? $data['accountnumber'] ?? $data['virtualAccountNo'] ?? null;
            
            $defaultBankName = match ($provider) {
                'udara360' => (new \App\Services\Udara360Service())->getBankName(),
                'flutterwave' => 'Wema Bank',
                default => 'PalmPay',
            };
            $bankName = $data['bank_name'] ?? $data['bankName'] ?? ($data['bank']['name'] ?? null) ?? $defaultBankName;

            $defaultBankCode = match ($provider) {
                'udara360' => (new \App\Services\Udara360Service())->getBankCode(),
                'flutterwave' => '035',
                default => '000000',
            };
            $bankCode = $data['bank_code'] ?? $data['bankCode'] ?? ($data['bank']['code'] ?? null) ?? $defaultBankCode;
            
            if (!is_string($bankCode) || trim($bankCode) === '') {
                $bankCode = '000000';
            }

            if (!is_string($accountNumber) || $accountNumber === '' || !is_string($bankName) || $bankName === '') {
                return $this->error('Virtual account response from provider is missing required fields', 500);
            }

            $abbr = getCompanyAbbreviation();
            $fallbackName = $customerDetails['name'] ?? ($user->first_name . ' ' . $user->last_name);
            if (!str_starts_with($fallbackName, $abbr)) {
                $fallbackName = $abbr . " " . $fallbackName;
            }

            $virtualAccount = VirtualAccount::create([
                'user_id' => $user->id,
                'customer_email' => $customerDetails['email'],
                'customer_phone' => $customerDetails['phone'],
                'account_number' => $accountNumber,
                'bank_name' => $bankName,
                'bank_code' => $bankCode,
                'account_name' => $data['account_name'] ?? $data['virtualAccountName'] ?? $fallbackName,
                'account_reference' => $data['accountReference'] ?? $data['order_ref'] ?? $data['tx_ref'] ?? $merchantRef,
                'provider' => $provider,
                'merchant_ref' => $merchantRef,
            ]);

            return $this->success([
                'account_number' => $virtualAccount->account_number,
                'bank_name' => $virtualAccount->bank_name,
                'bank_code' => $virtualAccount->bank_code,
                'account_name' => $virtualAccount->account_name,
                'provider' => $provider,
                'phone' => $phone,
            ], 'Virtual account created successfully');

        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    public function show(Request $request)
    {
        $palmpayEnabled = \App\Helpers\SettingHelper::isPalmPayEnabled();
        $udaraEnabled = \App\Helpers\SettingHelper::isUdara360Enabled();
        $flwEnabled = \App\Helpers\SettingHelper::isFlutterwaveEnabled();

        $user = $request->user();
        $phone = $request->query('phone');
        $provider = $request->query('provider');

        if ($provider) {
            $p = strtolower($provider);
            if ($p === 'udara360' && !$udaraEnabled) {
                return $this->error('The selected provider is currently disabled', 422);
            }
            if ($p === 'flutterwave' && !$flwEnabled) {
                return $this->error('The selected provider is currently disabled', 422);
            }
            if ($p === 'palmpay' && !$palmpayEnabled) {
                return $this->error('The selected provider is currently disabled', 422);
            }
        }

        $query = VirtualAccount::where('user_id', $user->id)
            ->when(!$palmpayEnabled, fn($q) => $q->where('provider', '!=', 'palmpay'))
            ->when(!$udaraEnabled, fn($q) => $q->where('provider', '!=', 'udara360'))
            ->when(!$flwEnabled, fn($q) => $q->where('provider', '!=', 'flutterwave'));

        if ($provider) {
            $query->where('provider', strtolower($provider));
        }

        if ($phone) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
            $query->where(function ($q) use ($phone, $cleanPhone) {
                $q->where('customer_phone', $phone);
                if ($cleanPhone !== '') {
                    $q->orWhere('customer_phone', 'LIKE', '%' . substr($cleanPhone, -10));
                }
            });
        }

        $virtualAccount = $query->latest('id')->first();

        if (!$virtualAccount && !$phone) {
            $virtualAccount = $user->virtualAccount;
        }

        if (!$virtualAccount) {
            return $this->error('No virtual account found for the specified criteria', 404);
        }

        return $this->success([
            'account_number' => $virtualAccount->account_number,
            'bank_name' => $virtualAccount->bank_name,
            'bank_code' => $virtualAccount->bank_code,
            'account_name' => $virtualAccount->account_name,
            'provider' => $virtualAccount->provider ?? ($provider ?: 'palmpay'),
            'phone' => $virtualAccount->customer_phone ?? null,
        ], 'Virtual account retrieved successfully');
    }

    public function index(Request $request)
    {
        $palmpayEnabled = \App\Helpers\SettingHelper::isPalmPayEnabled();
        $udaraEnabled = \App\Helpers\SettingHelper::isUdara360Enabled();
        $flwEnabled = \App\Helpers\SettingHelper::isFlutterwaveEnabled();

        $provider = $request->query('provider');

        if ($provider) {
            $p = strtolower($provider);
            if ($p === 'udara360' && !$udaraEnabled) {
                return $this->error('The selected provider is currently disabled', 422);
            }
            if ($p === 'flutterwave' && !$flwEnabled) {
                return $this->error('The selected provider is currently disabled', 422);
            }
            if ($p === 'palmpay' && !$palmpayEnabled) {
                return $this->error('The selected provider is currently disabled', 422);
            }
        }

        $query = VirtualAccount::where('user_id', $request->user()->id)
            ->when(!$palmpayEnabled, fn($q) => $q->where('provider', '!=', 'palmpay'))
            ->when(!$udaraEnabled, fn($q) => $q->where('provider', '!=', 'udara360'))
            ->when(!$flwEnabled, fn($q) => $q->where('provider', '!=', 'flutterwave'));

        if ($provider) {
            $query->where('provider', strtolower($provider));
        }

        $accounts = $query->latest('id')->paginate(20);

        return $this->success($accounts, 'Virtual accounts retrieved successfully');
    }
}
