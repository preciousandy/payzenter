<?php

namespace App\Http\Controllers\Api\Gateway;

use App\Helpers\SettingHelper;
use App\Http\Controllers\Controller;
use App\Models\GatewayTransaction;
use App\Models\User;
use App\Models\UserTransaction;
use App\Services\GatewayPaymentStatusService;
use App\Services\PalmPayService;
use App\Services\GatewayWebhookService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use KingFlamez\Rave\Facades\Rave as Flutterwave;

class PaymentsController extends Controller
{
    use ApiResponseTrait;

    public function initiate(Request $request, PalmPayService $palmPay)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:100',
            'currency' => 'nullable|string|max:10',
            'method' => 'required|string|in:card,transfer,ussd',
            'title' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:200',
            'customer_email' => 'required|email',
            'customer_phone' => 'nullable|string|max:25',
            'customer_name' => 'nullable|string|max:100',
            'return_url' => 'required|url|max:500',
            'merchant_webhook_url' => 'nullable|url|max:500',
            'reference' => 'nullable|string|max:64',
            'goods_details' => 'nullable|array',
            'bvn' => 'nullable|string|digits:11',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $data = $validator->validated();

        $merchantUser = $request->user();
        $merchantBvn = $merchantUser->bvn ?? ($merchantUser->merchantKyb->bvn ?? null) ?? ($data['bvn'] ?? null) ?? '22325148306';

        $amountKobo = (int) round(((float) $data['amount']) * 100);
        if ($amountKobo < 10000) {
            return $this->error('Minimum amount is 100 NGN', 422);
        }

        // Calculate fee at initiation
        $amountNgn = $amountKobo / 100;
        $fixedFee = SettingHelper::get(SettingHelper::GATEWAY_PAYMENT_FIXED_FEE, 0);
        $percentageFee = SettingHelper::get(SettingHelper::GATEWAY_PAYMENT_PERCENTAGE_FEE, 0);
        $chargeNgn = getTotalFee($amountNgn, $fixedFee, $percentageFee);
        $chargeKobo = (int) round($chargeNgn * 100);

        // Get charge mode
        $chargeMode = $request->input('charge_mode', $request->user()->gateway_charge_mode ?? 'merchant');
        if (!in_array($chargeMode, ['merchant', 'customer'])) {
            $chargeMode = 'merchant';
        }

        // Calculate charged amount
        $chargedAmountKobo = $chargeMode === 'customer' ? $amountKobo + $chargeKobo : $amountKobo;

        $reference = !empty($data['reference']) ? $data['reference'] : generateIncomingReference();
        $existing = GatewayTransaction::query()
            ->where('merchant_id', $request->user()->id)
            ->where('reference', $reference)
            ->exists();
        if ($existing) {
            return $this->error('Reference already exists', 422);
        }

        $method = $data['method'];
        $callBackUrl = $data['return_url'];

        $enableFlutterwave = SettingHelper::isFlutterwaveEnabled();
        $enablePalmPay = SettingHelper::isPalmPayEnabled();

        $requestedProvider = strtolower((string) $request->input('provider', ''));
        $preferredProvider = strtolower((string) ($request->user()->preferred_gateway_provider ?? ''));

        if ($requestedProvider === 'udara360') {
            return $this->error('Udara360 is a dedicated virtual account provider. Use POST /api/gateway/virtual-accounts instead.', 422);
        }
        if ($requestedProvider === 'flutterwave' && !$enableFlutterwave) {
            return $this->error('Flutterwave is currently disabled', 422);
        }
        if ($requestedProvider === 'palmpay' && !$enablePalmPay) {
            return $this->error('PalmPay is currently disabled', 422);
        }

        $provider = $requestedProvider !== '' ? $requestedProvider : $preferredProvider;
        if (!in_array($provider, ['flutterwave', 'palmpay'], true)) {
            $provider = '';
        }

        if ($provider === 'flutterwave' && !$enableFlutterwave) {
            $provider = '';
        }
        if ($provider === 'palmpay' && !$enablePalmPay) {
            $provider = '';
        }

        if ($provider === '') {
            if ($enablePalmPay) {
                $provider = 'palmpay';
            } elseif ($enableFlutterwave) {
                $provider = 'flutterwave';
            } else {
                return $this->error('No payment providers are currently enabled', 503);
            }
        }

        $flwSecret = (string) env('FLW_LIVE_SECRET_KEY', '');
        if ($flwSecret === '') {
            $flwSecret = (string) env('FLW_SECRET_KEY', '');
        }
        $flwBaseUrl = rtrim((string) env('FLW_API_URL', ''), '/');

        $palmBaseUrl = (string) env('PALMPAY_BASE_URL', '');
        $palmAppId = (string) env('PALMPAY_APP_ID', '');

        if ($provider === 'palmpay' && ($palmBaseUrl === '' || $palmAppId === '')) {
            if ($enableFlutterwave && $flwBaseUrl !== '' && $flwSecret !== '') {
                $provider = 'flutterwave';
            } else {
                return $this->error('PalmPay is not configured and Flutterwave is unavailable', 503);
            }
        }
        if ($provider === 'flutterwave' && ($flwBaseUrl === '' || $flwSecret === '')) {
            if ($enablePalmPay && $palmBaseUrl !== '' && $palmAppId !== '') {
                $provider = 'palmpay';
            } else {
                return $this->error('Flutterwave is not configured and PalmPay is unavailable', 503);
            }
        }

        $merchantWebhookUrl = $data['merchant_webhook_url'] ?? $request->user()->merchant_webhook_url;

        $tx = GatewayTransaction::create([
            'merchant_id' => $request->user()->id,
            'reference' => $reference,
            'amount' => $amountKobo,
            'charged_amount' => $chargedAmountKobo,
            'fee' => $chargeKobo,
            'charge_mode' => $chargeMode,
            'currency' => $data['currency'] ?? 'NGN',
            'method' => $method,
            'status' => 'unpaid',
            'provider' => $provider,
            'provider_reference' => $reference,
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'] ?? null,
            'customer_name' => $data['customer_name'] ?? null,
            'return_url' => $callBackUrl,
            'merchant_webhook_url' => $merchantWebhookUrl,
        ]);

        try {
            $payzenterCheckoutUrl = route('gateway.pay', ['reference' => $tx->reference], true);
            $providerCheckoutData = null;

            if ($provider === 'flutterwave') {
                $flwBaseUrl = rtrim((string) env('FLW_API_URL', ''), '/');
                $flwSecret = (string) env('FLW_LIVE_SECRET_KEY', '');

                if ($flwBaseUrl === '' || $flwSecret === '') {
                    throw new \Exception('Flutterwave is not configured');
                }

                $providerReference = null;

                switch ($method) {
                    case 'transfer':
                        $flwRedirectUrl = route('gateway.flutterwave.callback', ['reference' => $tx->reference], true);
                        $customerName = $data['customer_name'] ?? 'Customer';
                        $nameParts = explode(' ', trim($customerName), 2);
                        $vaFirstname = $nameParts[0] ?? 'Customer';
                        $vaLastname = $nameParts[1] ?? $tx->reference;
                        $chargedAmountNgn = (float) $chargedAmountKobo / 100;

                        $vaRes = createFlutterwaveVirtualAccount([
                            'email' => $data['customer_email'],
                            'phone' => $data['customer_phone'] ?? '08000000000',
                            'firstname' => $vaFirstname,
                            'lastname' => $vaLastname,
                            'merchant_ref' => 'GTW' . $tx->id,
                            'narration' => 'PayZenter ' . $tx->reference,
                            'is_permanent' => false,
                            'amount' => $chargedAmountNgn,
                            'bvn' => $merchantBvn,
                        ]);

                        if ($vaRes['status'] === 'success') {
                            $vaData = $vaRes['data'];
                            $accountNo = $vaData['accountNo'] ?? null;
                            $bankName = $vaData['bankName'] ?? 'WEMA Bank';
                            $accountName = $vaData['accountName'] ?? ($vaFirstname . ' ' . $vaLastname);

                            if ($accountNo !== null && $accountNo !== '') {
                                $tx->update([
                                    'bank_transfer_account' => $accountNo,
                                    'bank_transfer_bank' => $bankName,
                                    'bank_transfer_name' => $accountName,
                                    'expires_at' => now()->addMinutes(30),
                                    'provider' => 'flutterwave',
                                    'provider_reference' => $vaData['reference'] ?? $reference,
                                    'status' => 'processing',
                                    'raw_provider_response' => $vaRes,
                                ]);
                                break;
                            }
                        }

                        $hostedRes = createFlutterwaveHostedPayment([
                            'tx_ref' => $reference . '_h_' . time(),
                            'amount' => $chargedAmountNgn,
                            'currency' => $data['currency'] ?? 'NGN',
                            'email' => $data['customer_email'],
                            'phone' => $data['customer_phone'] ?? null,
                            'firstname' => $vaFirstname,
                            'lastname' => $vaLastname,
                            'redirect_url' => $flwRedirectUrl,
                            'title' => config('app.name') . ' - Secure Payment',
                            'description' => 'Payment Reference: ' . $tx->reference,
                            'narration' => 'Payment ' . $tx->reference,
                            'payment_options' => 'card,account,ussd,banktransfer',
                            'meta' => [
                                'gateway_reference' => $tx->reference,
                                'merchant_id' => $tx->merchant_id,
                            ],
                        ]);

                        if ($hostedRes['status'] !== 'success') {
                            $errMsg = $hostedRes['message'] ?? 'Unable to generate transfer payment options';
                            throw new \Exception($errMsg);
                        }

                        $hostedData = $hostedRes['data'];
                        $tx->update([
                            'provider' => 'flutterwave',
                            'provider_reference' => $hostedData['reference'] ?? $reference,
                            'status' => 'processing',
                            'raw_provider_response' => [
                                'virtual_account_failed' => $vaRes,
                                'hosted_payment' => $hostedRes,
                                'checkout_url' => $hostedData['link'] ?? null,
                            ],
                        ]);

                        $providerCheckoutData = [
                            'checkout_url' => $hostedData['link'] ?? null,
                            'reference' => $hostedData['reference'] ?? $reference,
                        ];
                        break;

                    case 'ussd':
                        $flwRedirectUrl = route('gateway.flutterwave.callback', ['reference' => $tx->reference], true);
                        $accountBank = $data['account_bank'] ?? $request->input('account_bank', null);

                        if (!empty($accountBank)) {
                            $ussdPayload = [
                                'tx_ref' => $reference,
                                'amount' => ((float) $chargedAmountKobo / 100),
                                'email' => $data['customer_email'],
                                'phone_number' => $data['customer_phone'] ?? null,
                                'currency' => $data['currency'] ?? 'NGN',
                                'redirect_url' => $flwRedirectUrl,
                                'account_bank' => $accountBank,
                            ];

                            $ussdCharge = Http::withHeaders([
                                'Accept' => 'application/json',
                                'Content-Type' => 'application/json',
                                'Authorization' => 'Bearer ' . $flwSecret,
                            ])->post($flwBaseUrl . '/charges?type=ussd', $ussdPayload);

                            $ussdJson = $ussdCharge->json();
                            if (!$ussdCharge->successful() || ($ussdJson['status'] ?? null) !== 'success') {
                                throw new \Exception($ussdJson['message'] ?? 'USSD charge initiation failed');
                            }

                            $ussdCode = $ussdJson['data']['meta']['authorization']['note']
                                ?? $ussdJson['data']['meta']['authorization']['ussd_code']
                                ?? $ussdJson['data']['meta']['authorization']['instruction']
                                ?? $ussdJson['data']['payment_code']
                                ?? $ussdJson['data']['ussd_code']
                                ?? $ussdJson['meta']['authorization']['note']
                                ?? $ussdJson['data']['note']
                                ?? null;

                            $providerReference = $ussdJson['data']['id'] ?? null;
                            $providerCheckoutData = [
                                'ussd_code' => $ussdCode,
                                'reference' => $ussdJson['data']['flw_ref'] ?? null,
                            ];

                            $tx->update([
                                'provider_reference' => $providerReference ?? $reference,
                                'raw_provider_response' => $ussdJson,
                                'status' => 'processing',
                            ]);
                        } else {
                            $tx->update([
                                'status' => 'unpaid',
                                'raw_provider_response' => [
                                    'type' => 'ussd_awaited',
                                    'checkout_url' => $payzenterCheckoutUrl,
                                ],
                            ]);
                        }
                        break;

                    case 'card':
                    default:
                        $tx->update([
                            'status' => 'unpaid',
                            'raw_provider_response' => [
                                'type' => 'card_awaited',
                                'checkout_url' => $payzenterCheckoutUrl,
                            ],
                        ]);
                        break;
                }
            } else {
                $palmPayError = null;
                $isExplicitProvider = ($requestedProvider !== '');
                $allAttemptedIdentities = [];

                try {
                    $orderId = bin2hex(random_bytes(16));
                    $notifyUrl = route('webhook.palmpay', [], true);

                    $bvn = $merchantBvn;
                    $email = $data['customer_email'];
                    $phone = $data['customer_phone'] ?? null;

                    $orderPayload = [
                        'amount' => $chargedAmountKobo,
                        'currency' => $data['currency'] ?? 'NGN',
                        'notifyUrl' => $notifyUrl,
                        'orderId' => $orderId,
                        'title' => $data['title'] ?? (config('app.name') . ' Payment'),
                        'description' => $data['description'] ?? ('Payment ' . $tx->reference),
                        'callBackUrl' => $callBackUrl,
                        'productType' => 'bank_transfer',
                        'userId' => $email,
                    ];

                    if (!empty($data['goods_details'])) {
                        $orderPayload['goodsDetails'] = json_encode($data['goods_details'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    }

                    if (!empty($phone)) {
                        $orderPayload['userMobileNo'] = $phone;
                    }

                    $orderRes = null;
                    $orderProviderData = [];
                    try {
                        $orderRes = $palmPay->createOrder($orderPayload);
                        $orderProviderData = $orderRes['data'] ?? [];
                    } catch (\Throwable $th) {
                        $orderProviderData = ['error' => $th->getMessage()];
                    }

                    $accountNo = null;
                    $accountBank = 'PalmPay';
                    $accountName = (($data['customer_name'] ?? 'PayZenter Customer') . ' ' . $tx->reference);

                    if (!empty($orderProviderData)) {
                        if (!empty($orderProviderData['payerVirtualAccNo'])) {
                            $accountNo = $orderProviderData['payerVirtualAccNo'];
                        } elseif (!empty($orderProviderData['virtualAccountNo'])) {
                            $accountNo = $orderProviderData['virtualAccountNo'];
                        } elseif (!empty($orderProviderData['accountNo'])) {
                            $accountNo = $orderProviderData['accountNo'];
                        } elseif (!empty($orderProviderData['virtualAccount']) && is_array($orderProviderData['virtualAccount'])) {
                            $accountNo = $orderProviderData['virtualAccount']['virtualAccountNo'] 
                                ?? $orderProviderData['virtualAccount']['accountNo'] 
                                ?? null;
                        } elseif (!empty($orderProviderData['virtualAccount']) && is_string($orderProviderData['virtualAccount'])) {
                            $accountNo = $orderProviderData['virtualAccount'];
                        }

                        if (!empty($orderProviderData['payerBankName'])) {
                            $accountBank = $orderProviderData['payerBankName'];
                        } elseif (!empty($orderProviderData['virtualAccountBankName'])) {
                            $accountBank = $orderProviderData['virtualAccountBankName'];
                        } elseif (!empty($orderProviderData['bankName'])) {
                            $accountBank = $orderProviderData['bankName'];
                        } elseif (!empty($orderProviderData['virtualAccount']) && is_array($orderProviderData['virtualAccount'])) {
                            $accountBank = $orderProviderData['virtualAccount']['virtualAccountBankName'] 
                                ?? $orderProviderData['virtualAccount']['bankName'] 
                                ?? 'PalmPay';
                        }

                        if (!empty($orderProviderData['payerAccountName'])) {
                            $accountName = $orderProviderData['payerAccountName'];
                        } elseif (!empty($orderProviderData['virtualAccountName'])) {
                            $accountName = $orderProviderData['virtualAccountName'];
                        } elseif (!empty($orderProviderData['accountName'])) {
                            $accountName = $orderProviderData['accountName'];
                        } elseif (!empty($orderProviderData['virtualAccount']) && is_array($orderProviderData['virtualAccount'])) {
                            $accountName = $orderProviderData['virtualAccount']['virtualAccountName'] 
                                ?? $orderProviderData['virtualAccount']['accountName'] 
                                ?? $accountName;
                        }
                    }

                    $strategyUsed = 'official_pay_with_bank_transfer_order';
                    $lastStrategyError = null;

                    $checkoutUrl = $orderProviderData['checkoutUrl'] 
                        ?? $orderProviderData['paymentUrl'] 
                        ?? $orderProviderData['orderUrl'] 
                        ?? null;

                    if (empty($accountNo) && empty($checkoutUrl)) {
                        $errMsg = $orderProviderData['error'] ?? 'PalmPay did not return bank transfer details in createorder response';
                        throw new \Exception($errMsg);
                    }

                    $tx->update([
                        'palmpay_order_id' => $orderId,
                        'palmpay_order_no' => $orderProviderData['orderNo'] ?? null,
                        'bank_transfer_account' => $accountNo,
                        'bank_transfer_bank' => $accountBank,
                        'bank_transfer_name' => $accountName,
                        'expires_at' => now()->addMinutes(60),
                        'provider' => 'palmpay',
                        'provider_reference' => $orderProviderData['orderNo'] ?? $accountNo ?? $orderId,
                        'status' => 'processing',
                        'raw_provider_response' => [
                            'order' => $orderProviderData,
                            'checkout_url' => $checkoutUrl,
                        ],
                    ]);

                    if ($checkoutUrl) {
                        $providerCheckoutData = [
                            'checkout_url' => $checkoutUrl,
                            'reference' => $orderProviderData['orderNo'] ?? $orderId,
                        ];
                    }
                } catch (\Throwable $palmPayEx) {
                    $palmPayError = $palmPayEx->getMessage();
                    throw $palmPayEx;

                    $providerCheckoutData = null;
                    $flwRedirectUrl = route('gateway.flutterwave.callback', ['reference' => $tx->reference], true);
                    $customerName = $data['customer_name'] ?? 'Customer';
                    $nameParts = explode(' ', trim($customerName), 2);
                    $vaFirstname = $nameParts[0] ?? 'Customer';
                    $vaLastname = $nameParts[1] ?? $tx->reference;
                    $chargedAmountNgn = (float) $chargedAmountKobo / 100;

                    $fallbackUsed = 'hosted_payment';
                    $vaResFallback = null;

                    if ($method === 'transfer') {
                        try {
                            $vaResFallback = createFlutterwaveVirtualAccount([
                                'email' => $data['customer_email'],
                                'phone' => $data['customer_phone'] ?? '08000000000',
                                'firstname' => $vaFirstname,
                                'lastname' => $vaLastname,
                                'merchant_ref' => 'GTW' . $tx->id,
                                'narration' => 'PayZenter ' . $tx->reference,
                                'is_permanent' => false,
                                'amount' => $chargedAmountNgn,
                                'bvn' => $merchantBvn,
                            ]);

                            if ($vaResFallback['status'] === 'success') {
                                $vaDataFb = $vaResFallback['data'];
                                $accountNoFb = $vaDataFb['accountNo'] ?? null;
                                $bankNameFb = $vaDataFb['bankName'] ?? 'WEMA Bank';
                                $accountNameFb = $vaDataFb['accountName'] ?? ($vaFirstname . ' ' . $vaLastname);

                                if ($accountNoFb !== null && $accountNoFb !== '') {
                                    $tx->update([
                                        'bank_transfer_account' => $accountNoFb,
                                        'bank_transfer_bank' => $bankNameFb,
                                        'bank_transfer_name' => $accountNameFb,
                                        'expires_at' => now()->addMinutes(30),
                                        'provider' => 'flutterwave',
                                        'provider_reference' => $vaDataFb['reference'] ?? $reference,
                                        'status' => 'processing',
                                        'raw_provider_response' => [
                                            'palmpay_failed' => $palmPayError,
                                            'palmpay_identity_attempts' => $allAttemptedIdentities,
                                            'flutterwave_virtual_account' => $vaResFallback,
                                        ],
                                    ]);
                                    $fallbackUsed = 'virtual_account';
                                }
                            }
                        } catch (\Throwable $flwVaEx) {
                            $vaResFallback = ['error' => $flwVaEx->getMessage()];
                        }
                    }

                    if ($fallbackUsed === 'hosted_payment') {
                        $hostedRes = createFlutterwaveHostedPayment([
                            'tx_ref' => $reference . '_h_' . time(),
                            'amount' => $chargedAmountNgn,
                            'currency' => $data['currency'] ?? 'NGN',
                            'email' => $data['customer_email'],
                            'phone' => $data['customer_phone'] ?? null,
                            'firstname' => $vaFirstname,
                            'lastname' => $vaLastname,
                            'redirect_url' => $flwRedirectUrl,
                            'title' => config('app.name') . ' - Secure Payment',
                            'description' => 'Payment Reference: ' . $tx->reference,
                            'narration' => 'Payment ' . $tx->reference,
                            'payment_options' => 'card,account,ussd,banktransfer',
                            'meta' => [
                                'gateway_reference' => $tx->reference,
                                'merchant_id' => $tx->merchant_id,
                            ],
                        ]);

                        if ($hostedRes['status'] !== 'success') {
                            $errMsg = $hostedRes['message'] ?? ('PalmPay failed (' . $palmPayError . ') and Flutterwave fallback failed');
                            throw new \Exception($errMsg);
                        }

                        $hostedData = $hostedRes['data'];
                        $tx->update([
                            'provider' => 'flutterwave',
                            'provider_reference' => $hostedData['reference'] ?? $reference,
                            'status' => 'processing',
                            'raw_provider_response' => [
                                'palmpay_failed' => $palmPayError,
                                'palmpay_identity_attempts' => $allAttemptedIdentities,
                                'virtual_account_attempt' => $vaResFallback,
                                'hosted_payment' => $hostedRes,
                                'checkout_url' => $hostedData['link'] ?? null,
                            ],
                        ]);

                        $providerCheckoutData = [
                            'checkout_url' => $hostedData['link'] ?? null,
                            'reference' => $hostedData['reference'] ?? $reference,
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            $tx->update([
                'status' => 'failed',
                'raw_provider_response' => [
                    'error' => $e->getMessage(),
                ],
            ]);

            return $this->error($e->getMessage(), 400);
        }

        $payzenterCheckoutUrl = route('gateway.pay', ['reference' => $tx->reference], true);
        $responseData = [
            'reference' => $tx->reference,
            'amount' => $tx->amount,
            'charged_amount' => $tx->charged_amount,
            'fee' => $tx->fee,
            'charge_mode' => $tx->charge_mode,
            'currency' => $tx->currency,
            'method' => $tx->method,
            'status' => $tx->status,
            'checkout_url' => $payzenterCheckoutUrl,
            'provider' => [
                'name' => $tx->provider,
                'reference' => $tx->provider_reference,
                'transaction_id' => $tx->provider_transaction_id,
            ],
        ];

        if ($tx->bank_transfer_account) {
            $responseData['payment'] = [
                'type' => 'bank_transfer',
                'account_number' => $tx->bank_transfer_account,
                'bank_name' => $tx->bank_transfer_bank,
                'account_name' => $tx->bank_transfer_name,
                'expires_at' => $tx->expires_at ? ($tx->expires_at instanceof \DateTimeInterface ? $tx->expires_at->toIso8601String() : \Carbon\Carbon::parse($tx->expires_at)->toIso8601String()) : null,
            ];
        }

        if ($tx->method === 'ussd' && isset($providerCheckoutData)) {
            $responseData['payment'] = $providerCheckoutData;
        }

        if ($tx->method === 'transfer' && isset($providerCheckoutData) && !$tx->bank_transfer_account) {
            $responseData['payment'] = [
                'type' => 'hosted_checkout',
                'checkout_url' => $providerCheckoutData['checkout_url'] ?? null,
                'reference' => $providerCheckoutData['reference'] ?? null,
            ];
        }

        return $this->success($responseData, 'Payment initialized');
    }

    public function show(Request $request, string $reference)
    {
        $tx = GatewayTransaction::query()
            ->where('merchant_id', $request->user()->id)
            ->where('reference', $reference)
            ->first();

        if (!$tx) {
            return $this->error('Payment not found', 404);
        }

        $payzenterCheckoutUrl = route('gateway.pay', ['reference' => $tx->reference], true);

        $data = [
            'reference' => $tx->reference,
            'amount' => $tx->amount,
            'currency' => $tx->currency,
            'method' => $tx->method,
            'status' => $tx->status,
            'paid_at' => $tx->paid_at,
            'checkout_url' => $payzenterCheckoutUrl,
            'provider' => [
                'name' => $tx->provider,
                'reference' => $tx->provider_reference,
                'transaction_id' => $tx->provider_transaction_id,
            ],
        ];

        if ($tx->bank_transfer_account) {
            $data['payment'] = [
                'type' => 'bank_transfer',
                'account_number' => $tx->bank_transfer_account,
                'bank_name' => $tx->bank_transfer_bank,
                'account_name' => $tx->bank_transfer_name,
                'expires_at' => $tx->expires_at ? ($tx->expires_at instanceof \DateTimeInterface ? $tx->expires_at->toIso8601String() : \Carbon\Carbon::parse($tx->expires_at)->toIso8601String()) : null,
            ];
        }

        return $this->success($data, 'Payment fetched');
    }

    public function refreshStatus(Request $request, PalmPayService $palmPay, string $reference)
    {
        $tx = GatewayTransaction::query()
            ->where('merchant_id', $request->user()->id)
            ->where('reference', $reference)
            ->first();

        if (!$tx) {
            return $this->error('Payment not found', 404);
        }

        try {
            $service = app(GatewayPaymentStatusService::class);
            $res = $service->checkAndRefreshStatus($tx);

            return $this->success([
                'reference' => $tx->reference,
                'status' => $tx->status === 'paying' ? 'processing' : $tx->status,
                'paid_at' => $tx->paid_at,
                'message' => $res['message'],
            ], 'Payment status refreshed');
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    private function applyPalmPayStatusToTransaction(GatewayTransaction $tx, $orderStatus): void
    {
        $map = [
            0 => 'processing',
            1 => 'processing',
            2 => 'success',
            3 => 'failed',
            4 => 'failed',
            5 => 'failed',
        ];

        if (!is_numeric($orderStatus)) {
            return;
        }

        $next = $map[(int) $orderStatus] ?? null;
        if (!$next) {
            return;
        }

        $update = ['status' => $next];
        if ($next === 'success' && !$tx->paid_at) {
            $update['paid_at'] = now();
        }

        $tx->update($update);
    }

    public function flutterwaveCallback(Request $request, GatewayWebhookService $webhooks, string $reference)
    {
        $tx = GatewayTransaction::query()
            ->where('reference', $reference)
            ->first();

        if (!$tx) {
            return redirect()->to('/');
        }

        $status = strtolower((string) $request->query('status', ''));
        if ($status !== 'successful') {
            $tx->update([
                'status' => $status === 'cancelled' ? 'closed' : 'failed',
            ]);

            return redirect()->away((string) $tx->return_url);
        }

        $transactionID = Flutterwave::getTransactionIDFromCallback();
        $data = Flutterwave::verifyTransaction($transactionID);

        $verifiedStatus = strtolower((string) ($data['data']['status'] ?? ''));
        if ($verifiedStatus !== 'successful') {
            $tx->update([
                'status' => 'failed',
                'raw_provider_response' => $data,
            ]);

            return redirect()->away((string) $tx->return_url);
        }

        DB::beginTransaction();

        $amountNgn = $tx->amount / 100;
        $chargeKobo = $tx->fee;
        $chargeNgn = $chargeKobo / 100;

        $tx->update([
            'provider' => 'flutterwave',
            'provider_reference' => $reference,
            'provider_transaction_id' => (string) $transactionID,
            'status' => 'success',
            'paid_at' => $tx->paid_at ?? now(),
            'raw_provider_response' => $data,
        ]);

        // Update merchant's wallet
        $merchant = $tx->merchant;
        $merchant->wallet_balance += $amountNgn;
        if ($tx->charge_mode === 'merchant') {
            $merchant->wallet_balance -= $chargeNgn;
        }
        $merchant->save();

        // Create credit transaction for merchant
        $merchant->transactions()->create([
            'amount' => $amountNgn,
            'type' => UserTransaction::TYPE_CREDIT,
            'status' => UserTransaction::STATUS_SUCCESSFUL,
            'source' => UserTransaction::SOURCE_BANK_TRANSFER,
            'message' => 'Gateway payment received: ₦' . number_format($amountNgn, 2),
            'reference' => $tx->reference,
        ]);

        // Create debit transaction for charge if charge > 0 and merchant pays
        if ($chargeNgn > 0 && $tx->charge_mode === 'merchant') {
            $merchant->transactions()->create([
                'amount' => $chargeNgn,
                'type' => UserTransaction::TYPE_DEBIT,
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'source' => UserTransaction::SOURCE_BANK_TRANSFER,
                'message' => 'Gateway payment charge: ₦' . number_format($chargeNgn, 2),
                'reference' => $tx->reference,
                'fee' => $chargeNgn,
            ]);
        }

        DB::commit();

        $webhooks->dispatch($tx, 'payment.success');

        return redirect()->away((string) $tx->return_url);
    }

    public function chargeCard(Request $request, string $reference)
    {
        $validator = Validator::make($request->all(), [
            'card_number' => 'required|string|digits_between:13,19',
            'cvv' => 'required|string|digits_between:3,4',
            'expiry_month' => 'required|string|digits:2',
            'expiry_year' => 'required|string|digits:2|min:' . date('y'),
            'pin' => 'nullable|string|digits:4',
            'fullname' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $tx = GatewayTransaction::query()
            ->where('reference', $reference)
            ->whereIn('status', ['unpaid', 'processing', 'paying', 'failed'])
            ->first();

        if (!$tx) {
            return $this->error('Payment not found or already completed', 404);
        }

        try {
            $enablePalmPay = (bool) SettingHelper::get(SettingHelper::ENABLE_PALMPAY, '1');
            if ($enablePalmPay) {
                try {
                    $palmPay = new PalmPayService();
                    $orderId = bin2hex(random_bytes(16));
                    $notifyUrl = route('webhook.palmpay', [], true);

                    $orderPayload = [
                        'amount' => $tx->charged_amount ?? $tx->amount,
                        'currency' => $tx->currency ?? 'NGN',
                        'notifyUrl' => $notifyUrl,
                        'orderId' => $orderId,
                        'title' => config('app.name') . ' Payment',
                        'description' => 'Card Payment for ' . $tx->reference,
                        'callBackUrl' => $tx->return_url ?: config('app.url'),
                        'productType' => 'bank_card',
                        'userId' => $tx->customer_email,
                    ];

                    if ($tx->customer_phone) {
                        $orderPayload['userMobileNo'] = $tx->customer_phone;
                    }

                    $orderRes = $palmPay->createOrder($orderPayload);
                    $orderData = $orderRes['data'] ?? [];
                    $checkoutUrl = $orderData['checkoutUrl'] 
                        ?? $orderData['h5Url'] 
                        ?? $orderData['webUrl'] 
                        ?? $orderData['paymentUrl'] 
                        ?? $orderData['redirectUrl'] 
                        ?? null;

                    if (empty($checkoutUrl)) {
                        return $this->error('PalmPay did not return a checkout URL', 400);
                    }

                    $tx->update([
                        'palmpay_order_id' => $orderId,
                        'palmpay_order_no' => $orderData['orderNo'] ?? null,
                        'provider' => 'palmpay',
                        'provider_reference' => $orderData['orderNo'] ?? $orderId,
                        'status' => 'processing',
                        'raw_provider_response' => [
                            'order' => $orderData,
                        ],
                    ]);

                    return $this->success([
                        'status' => 'redirect',
                        'redirect_url' => $checkoutUrl,
                        'reference' => $tx->reference,
                    ], 'Redirecting to PalmPay checkout');
                } catch (\Throwable $th) {
                    return $this->error('PalmPay Card Payment error: ' . $th->getMessage(), 400);
                }
            }

            $enableFlutterwave = SettingHelper::isFlutterwaveEnabled();
            if (!$enableFlutterwave) {
                return $this->error('Flutterwave card charge is currently disabled by administrator', 422);
            }

            $flwBaseUrl = rtrim((string) env('FLW_API_URL', ''), '/');
            $flwSecret = (string) env('FLW_LIVE_SECRET_KEY', '');
            if ($flwSecret === '') {
                $flwSecret = (string) env('FLW_SECRET_KEY', '');
            }

            if ($flwBaseUrl === '' || $flwSecret === '') {
                throw new \Exception('Card payment is not configured');
            }

            $expiry = $request->expiry_month . '/' . $request->expiry_year;
            $redirectUrl = route('gateway.flutterwave.callback', ['reference' => $tx->reference], true);

            $payload = [
                'tx_ref' => $tx->reference . '_' . time(),
                'amount' => ((float) $tx->charged_amount / 100),
                'email' => $tx->customer_email,
                'phone_number' => $tx->customer_phone,
                'currency' => $tx->currency ?? 'NGN',
                'redirect_url' => $redirectUrl,
                'card_number' => $request->card_number,
                'cvv' => $request->cvv,
                'expiry_date' => $expiry,
                'firstname' => $tx->customer_name ? explode(' ', $tx->customer_name)[0] : 'Customer',
                'lastname' => $tx->customer_name ? (explode(' ', $tx->customer_name)[1] ?? $tx->reference) : $tx->reference,
                'meta' => [
                    'gateway_reference' => $tx->reference,
                    'merchant_id' => $tx->merchant_id,
                ],
            ];

            if ($request->filled('pin')) {
                $payload['authorization'] = [
                    'mode' => 'pin',
                    'pin' => $request->pin,
                ];
            }

            $charge = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $flwSecret,
            ])->post($flwBaseUrl . '/charges?type=card', $payload);

            $json = $charge->json();
            if (!$charge->successful() || ($json['status'] ?? null) !== 'success') {
                return $this->error($json['message'] ?? 'Card charge failed', 400);
            }

            $flwStatus = strtolower((string) ($json['data']['status'] ?? ''));
            $authMode = $json['data']['meta']['authorization']['mode'] ?? null;

            $tx->update([
                'provider' => 'flutterwave',
                'provider_reference' => $json['data']['id'] ?? $tx->reference,
                'raw_provider_response' => $json,
                'status' => 'processing',
            ]);

            $responseData = [
                'reference' => $tx->reference,
                'status' => $tx->status,
                'charge_status' => $flwStatus,
                'auth_required' => false,
            ];

            if ($flwStatus === 'successful') {
                $tx->update([
                    'provider_transaction_id' => (string) ($json['data']['id'] ?? ''),
                    'status' => 'success',
                    'paid_at' => $tx->paid_at ?? now(),
                ]);
                $this->handlePaymentSuccess($tx);
                $responseData['status'] = 'success';
                $responseData['charge_status'] = 'successful';
                return $this->success($responseData, 'Payment successful');
            }

            if ($authMode && in_array(strtolower($authMode), ['otp', 'pin', 'avs_noauth', 'redirect'])) {
                $responseData['auth_required'] = true;
                $responseData['auth_mode'] = strtolower($authMode);
                $responseData['auth_hint'] = $json['data']['meta']['authorization']['note'] ?? null;
                $responseData['auth_url'] = $json['data']['meta']['authorization']['redirect'] ?? null;
                $responseData['flw_ref'] = $json['data']['flw_ref'] ?? null;
            }

            return $this->success($responseData, 'Card charge initiated');
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    public function validateCardCharge(Request $request, string $reference)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|max:10',
            'flw_ref' => 'nullable|string|max:64',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $tx = GatewayTransaction::query()
            ->where('reference', $reference)
            ->whereIn('status', ['processing', 'paying', 'unpaid'])
            ->first();

        if (!$tx) {
            return $this->error('Payment not found or already completed', 404);
        }

        try {
            $enableFlutterwave = SettingHelper::isFlutterwaveEnabled();
            if (!$enableFlutterwave) {
                return $this->error('Flutterwave card validation is currently disabled by administrator', 422);
            }

            $flwBaseUrl = rtrim((string) env('FLW_API_URL', ''), '/');
            $flwSecret = (string) env('FLW_LIVE_SECRET_KEY', '');
            if ($flwSecret === '') {
                $flwSecret = (string) env('FLW_SECRET_KEY', '');
            }

            if ($flwBaseUrl === '' || $flwSecret === '') {
                throw new \Exception('Card payment is not configured');
            }

            $flwRef = $request->flw_ref ?? ($tx->raw_provider_response['data']['flw_ref'] ?? null);
            if (!$flwRef) {
                throw new \Exception('Missing Flutterwave reference for validation');
            }

            $payload = [
                'flw_ref' => $flwRef,
                'otp' => $request->otp,
                'reference' => $tx->reference,
            ];

            $validate = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $flwSecret,
            ])->post($flwBaseUrl . '/validate-charge', $payload);

            $json = $validate->json();
            if (!$validate->successful()) {
                return $this->error($json['message'] ?? 'OTP validation failed', 400);
            }

            $flwStatus = strtolower((string) ($json['data']['status'] ?? ''));
            if ($flwStatus !== 'successful') {
                $tx->update([
                    'status' => 'failed',
                    'raw_provider_response' => $json,
                ]);
                return $this->error($json['message'] ?? 'Payment not successful', 400, [
                    'charge_status' => $flwStatus,
                ]);
            }

            $tx->update([
                'provider' => 'flutterwave',
                'provider_transaction_id' => (string) ($json['data']['id'] ?? ''),
                'provider_reference' => $json['data']['tx_ref'] ?? $tx->reference,
                'status' => 'success',
                'paid_at' => $tx->paid_at ?? now(),
                'raw_provider_response' => $json,
            ]);

            $this->handlePaymentSuccess($tx);

            return $this->success([
                'reference' => $tx->reference,
                'status' => 'success',
                'charge_status' => 'successful',
                'paid_at' => $tx->paid_at,
            ], 'Payment successful');
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    private function handlePaymentSuccess(GatewayTransaction $tx): void
    {
        if ($tx->status !== 'success') {
            return;
        }

        $alreadyProcessed = UserTransaction::query()
            ->where('reference', $tx->reference)
            ->where('type', UserTransaction::TYPE_CREDIT)
            ->exists();

        if ($alreadyProcessed) {
            return;
        }

        DB::beginTransaction();

        try {
            $amountNgn = $tx->amount / 100;
            $chargeKobo = $tx->fee;
            $chargeNgn = $chargeKobo / 100;

            $merchant = $tx->merchant;
            $merchant->wallet_balance += $amountNgn;
            if ($tx->charge_mode === 'merchant') {
                $merchant->wallet_balance -= $chargeNgn;
            }
            $merchant->save();

            $merchant->transactions()->create([
                'amount' => $amountNgn,
                'type' => UserTransaction::TYPE_CREDIT,
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'source' => UserTransaction::SOURCE_BANK_TRANSFER,
                'message' => 'Gateway payment received: ₦' . number_format($amountNgn, 2),
                'reference' => $tx->reference,
            ]);

            if ($chargeNgn > 0 && $tx->charge_mode === 'merchant') {
                $merchant->transactions()->create([
                    'amount' => $chargeNgn,
                    'type' => UserTransaction::TYPE_DEBIT,
                    'status' => UserTransaction::STATUS_SUCCESSFUL,
                    'source' => UserTransaction::SOURCE_BANK_TRANSFER,
                    'message' => 'Gateway payment charge: ₦' . number_format($chargeNgn, 2),
                    'reference' => $tx->reference,
                    'fee' => $chargeNgn,
                ]);
            }

            DB::commit();

            (new GatewayWebhookService())->dispatch($tx, 'payment.success');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
