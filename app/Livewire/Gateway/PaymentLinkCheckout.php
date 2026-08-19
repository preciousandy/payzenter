<?php

namespace App\Livewire\Gateway;

use App\Helpers\SettingHelper;
use App\Models\GatewayTransaction;
use App\Models\UserTransaction;
use App\Services\GatewayWebhookService;
use App\Services\PalmPayService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use KingFlamez\Rave\Facades\Rave as Flutterwave;
use Livewire\Component;

class PaymentLinkCheckout extends Component
{
    public $reference;
    public $customer_email;
    public $customer_phone;
    public $customer_name;
    public $step = 'info';
    public $payment_method;
    public $timeLeft = 1800;
    public $enable_flutterwave = true;
    public $enable_palmpay = true;
    public $verificationMessage;

    public $card_number;
    public $card_cvv;
    public $card_expiry_month;
    public $card_expiry_year;
    public $card_name;
    public $card_pin;
    public $otp;
    public $flw_ref;
    public $auth_mode;
    public $auth_hint;
    public $auth_url;

    public $cardError;
    public $otpError;
    public $ussdError;
    public $ussd_bank;
    public $ussd_code;
    public $ussd_ref;
    public $processing = false;

    public function mount(string $reference)
    {
        $this->reference = $reference;
        $tx = $this->getTransaction();
        $this->loadGatewaySettings();

        if ($tx->status === 'success') {
            $this->step = 'success';
        } elseif ($tx->customer_email) {
            $this->customer_email = $tx->customer_email;
            $this->customer_name = $tx->customer_name;
            $this->customer_phone = $tx->customer_phone;

            if ($tx->method === 'card') {
                if ($this->enable_palmpay) {
                    return $this->payWithPalmPayCard($tx);
                }
                $this->step = 'card_form';
                $this->payment_method = 'card';
            } elseif ($tx->method === 'ussd') {
                if ($this->enable_palmpay) {
                    return $this->payWithPalmPayUssd($tx);
                }
                $this->step = 'ussd_form';
                $this->payment_method = 'ussd';
            } elseif ($tx->bank_transfer_account && $tx->method === 'transfer') {
                $this->step = 'payment';
                $this->payment_method = 'transfer';
                $this->calculateTimeLeft($tx);
            } else {
                $this->step = 'method';
            }
        }
    }

    private function loadGatewaySettings()
    {
        $this->enable_flutterwave = (bool) SettingHelper::get(SettingHelper::ENABLE_FLUTTERWAVE, '1');
        $this->enable_palmpay = (bool) SettingHelper::get(SettingHelper::ENABLE_PALMPAY, '1');
    }

    private function getTransaction()
    {
        $tx = GatewayTransaction::query()
            ->with('merchant')
            ->where('reference', $this->reference)
            ->first();

        if (!$tx) {
            abort(404);
        }

        return $tx;
    }

    private function calculateTimeLeft($tx)
    {
        if ($tx->expires_at) {
            $diff = now()->diffInSeconds($tx->expires_at, false);
            $this->timeLeft = $diff > 0 ? $diff : 0;
            if ($this->timeLeft === 0 && $tx->status === 'unpaid') {
                $tx->update(['status' => 'closed']);
            }
        }
    }

    public function setInfo()
    {
        $this->validate([
            'customer_email' => 'required|email',
            'customer_phone' => 'nullable|string|max:25',
            'customer_name' => 'nullable|string|max:100',
        ]);

        $tx = $this->getTransaction();
        $tx->update([
            'customer_email' => $this->customer_email,
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
        ]);

        $this->step = 'method';
    }

    public function selectMethod($method)
    {
        $this->loadGatewaySettings();
        $this->cardError = null;
        $this->otpError = null;
        $this->ussdError = null;

        if (($method === 'card' && !$this->enable_flutterwave && !$this->enable_palmpay) ||
            ($method === 'ussd' && !$this->enable_flutterwave && !$this->enable_palmpay) ||
            ($method === 'palmpay' && !$this->enable_palmpay)) {
            $this->addError('payment_method', 'This payment method is currently disabled');
            return;
        }

        $this->payment_method = $method;
        $tx = $this->getTransaction();

        if ($method === 'card') {
            if ($this->enable_palmpay) {
                return $this->payWithPalmPayCard($tx);
            }
            return $this->initCardForm($tx);
        } elseif ($method === 'ussd') {
            if ($this->enable_palmpay) {
                return $this->payWithPalmPayUssd($tx);
            }
            return $this->initUssdForm($tx);
        } elseif ($method === 'transfer') {
            return $this->payWithTransfer($tx);
        } elseif ($method === 'palmpay') {
            return $this->payWithPalmPay($tx);
        }
    }

    private function initCardForm($tx)
    {
        $this->step = 'card_form';
    }

    private function initUssdForm($tx)
    {
        $this->step = 'ussd_form';
        $this->ussdError = null;
    }

    public function getUssdBanksProperty()
    {
        return [
            ['code' => '058', 'name' => 'GTBank'],
            ['code' => '057', 'name' => 'Zenith Bank'],
            ['code' => '033', 'name' => 'United Bank for Africa (UBA)'],
            ['code' => '044', 'name' => 'Access Bank'],
            ['code' => '011', 'name' => 'First Bank of Nigeria'],
            ['code' => '232', 'name' => 'Sterling Bank'],
            ['code' => '214', 'name' => 'First City Monument Bank (FCMB)'],
            ['code' => '070', 'name' => 'Fidelity Bank'],
            ['code' => '050', 'name' => 'Ecobank'],
            ['code' => '035', 'name' => 'Wema Bank'],
            ['code' => '032', 'name' => 'Union Bank'],
            ['code' => '039', 'name' => 'Stanbic IBTC Bank'],
            ['code' => '082', 'name' => 'Keystone Bank'],
            ['code' => '215', 'name' => 'Unity Bank'],
            ['code' => '101', 'name' => 'Providus Bank'],
            ['code' => '301', 'name' => 'Jaiz Bank'],
        ];
    }

    public function submitUssd()
    {
        $this->validate([
            'ussd_bank' => 'required|string',
        ]);

        $this->processing = true;
        $this->ussdError = null;

        $tx = $this->getTransaction();

        try {
            $flwBaseUrl = rtrim((string) env('FLW_API_URL', ''), '/');
            $flwSecret = (string) env('FLW_LIVE_SECRET_KEY', '');
            if ($flwSecret === '') {
                $flwSecret = (string) env('FLW_SECRET_KEY', '');
            }

            if ($flwBaseUrl === '' || $flwSecret === '') {
                throw new \Exception('USSD payment is not configured');
            }

            $redirectUrl = route('gateway.flutterwave.callback', ['reference' => $tx->reference], true);

            $payload = [
                'tx_ref' => $tx->reference . '_u_' . time(),
                'amount' => ((float) ($tx->charged_amount ?? $tx->amount) / 100),
                'email' => $this->customer_email,
                'phone_number' => $this->customer_phone,
                'currency' => $tx->currency ?? 'NGN',
                'redirect_url' => $redirectUrl,
                'account_bank' => $this->ussd_bank,
            ];

            $charge = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $flwSecret,
            ])->post($flwBaseUrl . '/charges?type=ussd', $payload);

            $json = $charge->json();

            \Illuminate\Support\Facades\Log::info('Flutterwave USSD charge response', [
                'status' => $charge->status(),
                'json' => $json,
            ]);

            if (!$charge->successful() || ($json['status'] ?? null) !== 'success') {
                $msg = $json['message'] ?? 'USSD charge failed';
                if (isset($json['data']['processor_response'])) {
                    $msg .= ': ' . $json['data']['processor_response'];
                }
                throw new \Exception($msg);
            }

            $ussdCode = $json['data']['meta']['authorization']['note']
                ?? $json['data']['meta']['authorization']['ussd_code']
                ?? $json['data']['meta']['authorization']['instruction']
                ?? $json['data']['payment_code']
                ?? $json['data']['ussd_code']
                ?? $json['meta']['authorization']['note']
                ?? $json['data']['note']
                ?? null;

            if (!$ussdCode && !empty($json['data']['payment_code'])) {
                $code = (string) $json['data']['payment_code'];
                $ussdCode = str_starts_with($code, '*') ? $code : ('*' . $code . '#');
            }

            if (!$ussdCode) {
                $providerMsg = $json['message'] ?? ($json['data']['processor_response'] ?? 'USSD code not generated by bank');
                throw new \Exception('Unable to retrieve USSD dial code: ' . $providerMsg);
            }

            $this->ussd_code = $ussdCode;
            $this->ussd_ref = $json['data']['flw_ref'] ?? null;

            $tx->update([
                'provider' => 'flutterwave',
                'provider_reference' => $json['data']['id'] ?? $tx->reference,
                'raw_provider_response' => $json,
                'status' => 'paying',
            ]);

            $this->step = 'ussd_code';
            $this->processing = false;
        } catch (\Throwable $e) {
            $this->ussdError = $e->getMessage();
            $this->processing = false;
        }
    }

    private function payWithPalmPay($tx)
    {
        if ($tx->bank_transfer_account && $tx->provider === 'palmpay') {
            $this->payment_method = 'transfer';
            $this->step = 'payment';
            $this->calculateTimeLeft($tx);
            return;
        }

        try {
            $palmPay = new \App\Services\PalmPayService();
            $orderId = bin2hex(random_bytes(16));
            $notifyUrl = route('webhook.palmpay', [], true);

            $email = $this->customer_email;
            $phone = $this->customer_phone ?? null;
            $txMerchant = $tx->merchant;
            $bvn = $txMerchant?->bvn ?? ($txMerchant?->merchantKyb?->bvn ?? null) ?? '22325148306';

            $orderPayload = [
                'amount' => $tx->charged_amount ?? $tx->amount,
                'currency' => $tx->currency ?? 'NGN',
                'notifyUrl' => $notifyUrl,
                'orderId' => $orderId,
                'title' => config('app.name') . ' Payment',
                'description' => 'Payment for ' . $tx->reference,
                'callBackUrl' => $tx->return_url ?: config('app.url'),
                'productType' => 'bank_transfer',
                'userId' => $email,
            ];

            if ($phone !== null && $phone !== '') {
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
            $accountName = (($this->customer_name ?? 'PayZenter Customer') . ' ' . $tx->reference);

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

            if (empty($accountNo)) {
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
                'provider_reference' => $accountNo,
                'status' => 'paying',
                'raw_provider_response' => [
                    'order' => $orderProviderData,
                ],
            ]);

            $this->payment_method = 'transfer';
            $this->step = 'payment';
            $this->timeLeft = 3600;
        } catch (\Throwable $e) {
            $this->addError('payment_method', $e->getMessage());
        }
    }

    private function payWithPalmPayUssd($tx)
    {
        try {
            $palmPay = new \App\Services\PalmPayService();
            $orderId = bin2hex(random_bytes(16));
            $notifyUrl = route('webhook.palmpay', [], true);

            $email = $this->customer_email;
            $phone = $this->customer_phone ?? null;

            $orderPayload = [
                'amount' => $tx->charged_amount ?? $tx->amount,
                'currency' => $tx->currency ?? 'NGN',
                'notifyUrl' => $notifyUrl,
                'orderId' => $orderId,
                'title' => config('app.name') . ' Payment',
                'description' => 'USSD Payment for ' . $tx->reference,
                'callBackUrl' => $tx->return_url ?: config('app.url'),
                'productType' => 'online_payment',
                'userId' => $email,
            ];

            if ($phone !== null && $phone !== '') {
                $orderPayload['userMobileNo'] = $phone;
            }

            $orderRes = $palmPay->createOrder($orderPayload);
            $orderProviderData = $orderRes['data'] ?? [];

            $checkoutUrl = $orderProviderData['checkoutUrl'] ?? null;

            if (empty($checkoutUrl)) {
                throw new \Exception('PalmPay did not return a checkout URL');
            }

            $tx->update([
                'palmpay_order_id' => $orderId,
                'palmpay_order_no' => $orderProviderData['orderNo'] ?? null,
                'provider' => 'palmpay',
                'provider_reference' => $orderProviderData['orderNo'] ?? $orderId,
                'status' => 'paying',
                'raw_provider_response' => [
                    'order' => $orderProviderData,
                ],
            ]);

            return redirect()->away($checkoutUrl);

        } catch (\Throwable $e) {
            $this->addError('payment_method', 'PalmPay USSD Payment error: ' . $e->getMessage());
        }
    }

    private function payWithPalmPayCard($tx)
    {
        try {
            $palmPay = new \App\Services\PalmPayService();
            $orderId = bin2hex(random_bytes(16));
            $notifyUrl = route('webhook.palmpay', [], true);

            $email = $this->customer_email;
            $phone = $this->customer_phone ?? null;

            $orderPayload = [
                'amount' => $tx->charged_amount ?? $tx->amount,
                'currency' => $tx->currency ?? 'NGN',
                'notifyUrl' => $notifyUrl,
                'orderId' => $orderId,
                'title' => config('app.name') . ' Payment',
                'description' => 'Card Payment for ' . $tx->reference,
                'callBackUrl' => $tx->return_url ?: config('app.url'),
                'productType' => 'online_payment',
                'userId' => $email,
            ];

            if ($phone !== null && $phone !== '') {
                $orderPayload['userMobileNo'] = $phone;
            }

            $orderRes = $palmPay->createOrder($orderPayload);
            $orderProviderData = $orderRes['data'] ?? [];

            $checkoutUrl = $orderProviderData['checkoutUrl'] ?? null;

            if (empty($checkoutUrl)) {
                throw new \Exception('PalmPay did not return a checkout URL');
            }

            $tx->update([
                'palmpay_order_id' => $orderId,
                'palmpay_order_no' => $orderProviderData['orderNo'] ?? null,
                'provider' => 'palmpay',
                'provider_reference' => $orderProviderData['orderNo'] ?? $orderId,
                'status' => 'paying',
                'raw_provider_response' => [
                    'order' => $orderProviderData,
                ],
            ]);

            return redirect()->away($checkoutUrl);

        } catch (\Throwable $e) {
            $this->addError('payment_method', 'PalmPay Card Payment error: ' . $e->getMessage());
        }
    }

    public function submitCard()
    {
        $tx = $this->getTransaction();

        if ($this->enable_palmpay) {
            return $this->payWithPalmPayCard($tx);
        }

        $this->validate([
            'card_number' => 'required|string|digits_between:13,19',
            'card_cvv' => 'required|string|digits_between:3,4',
            'card_expiry_month' => 'required|string|digits:2',
            'card_expiry_year' => 'required|string|digits:2',
            'card_name' => 'nullable|string|max:100',
            'card_pin' => 'nullable|string|digits:4',
        ]);

        $this->processing = true;
        $this->cardError = null;

        $tx = $this->getTransaction();

        try {
            $flwBaseUrl = rtrim((string) env('FLW_API_URL', ''), '/');
            $flwSecret = (string) env('FLW_LIVE_SECRET_KEY', '');
            if ($flwSecret === '') {
                $flwSecret = (string) env('FLW_SECRET_KEY', '');
            }

            if ($flwBaseUrl === '' || $flwSecret === '') {
                throw new \Exception('Card payment is not configured');
            }

            $expiry = $this->card_expiry_month . '/' . $this->card_expiry_year;
            $redirectUrl = route('gateway.flutterwave.callback', ['reference' => $tx->reference], true);

            $firstname = 'Customer';
            $lastname = $tx->reference;
            if ($this->card_name) {
                $parts = explode(' ', trim($this->card_name), 2);
                $firstname = $parts[0];
                $lastname = $parts[1] ?? $tx->reference;
            } elseif ($this->customer_name) {
                $parts = explode(' ', trim($this->customer_name), 2);
                $firstname = $parts[0];
                $lastname = $parts[1] ?? $tx->reference;
            }

            $payload = [
                'tx_ref' => $tx->reference . '_' . time(),
                'amount' => ((float) ($tx->charged_amount ?? $tx->amount) / 100),
                'email' => $this->customer_email,
                'phone_number' => $this->customer_phone,
                'currency' => $tx->currency ?? 'NGN',
                'redirect_url' => $redirectUrl,
                'card_number' => preg_replace('/\s+/', '', $this->card_number),
                'cvv' => $this->card_cvv,
                'expiry_month' => $this->card_expiry_month,
                'expiry_year' => $this->card_expiry_year,
                'expiry_date' => $expiry,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'meta' => [
                    'gateway_reference' => $tx->reference,
                    'merchant_id' => $tx->merchant_id,
                ],
            ];

            if ($this->card_pin) {
                $payload['pin'] = $this->card_pin;
                $payload['authorization'] = [
                    'mode' => 'pin',
                    'pin' => $this->card_pin,
                ];
            }

            // Encrypt the payload using Triple DES (3DES-24)
            $encKey = $this->getEncryptionKey($flwSecret);
            $encryptedData = $this->encryptPayload($encKey, $payload);

            $chargePayload = [
                'client' => $encryptedData,
            ];

            $charge = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $flwSecret,
            ])->post($flwBaseUrl . '/charges?type=card', $chargePayload);

            $json = $charge->json();

            if (!$charge->successful() || ($json['status'] ?? null) !== 'success') {
                $msg = $json['message'] ?? 'Card charge failed';
                if (isset($json['data']['processor_response'])) {
                    $msg .= ': ' . $json['data']['processor_response'];
                }
                throw new \Exception($msg);
            }

            $flwStatus = strtolower((string) ($json['data']['status'] ?? ''));
            $authMode = strtolower((string) ($json['data']['meta']['authorization']['mode'] ?? ''));

            $tx->update([
                'provider' => 'flutterwave',
                'provider_reference' => $json['data']['id'] ?? $tx->reference,
                'raw_provider_response' => $json,
                'status' => 'paying',
            ]);

            if ($flwStatus === 'successful') {
                $tx->update([
                    'provider_transaction_id' => (string) ($json['data']['id'] ?? ''),
                    'status' => 'success',
                    'paid_at' => $tx->paid_at ?? now(),
                ]);
                $this->handleSuccessInternal($tx);
                $this->processing = false;
                $this->step = 'success';
                return;
            }

            $this->flw_ref = $json['data']['flw_ref'] ?? null;
            $this->auth_mode = $authMode;
            $this->auth_hint = $json['data']['meta']['authorization']['note'] ?? null;
            $this->auth_url = $json['data']['meta']['authorization']['redirect'] ?? null;

            if ($this->auth_url && $authMode === 'redirect') {
                $this->processing = false;
                return redirect()->away($this->auth_url);
            }

            if ($authMode === 'otp') {
                $this->step = 'otp_form';
            } elseif ($authMode === 'pin') {
                $this->cardError = 'Please enter your card PIN';
            } elseif ($authMode === 'avs_noauth') {
                $this->cardError = 'Address verification required. Please try again with PIN.';
            }

            $this->processing = false;
        } catch (\Throwable $e) {
            $this->cardError = $e->getMessage();
            $this->processing = false;
        }
    }

    public function submitOtp()
    {
        $this->validate([
            'otp' => 'required|string|max:10',
        ]);

        $this->processing = true;
        $this->otpError = null;

        $tx = $this->getTransaction();

        try {
            $flwBaseUrl = rtrim((string) env('FLW_API_URL', ''), '/');
            $flwSecret = (string) env('FLW_LIVE_SECRET_KEY', '');
            if ($flwSecret === '') {
                $flwSecret = (string) env('FLW_SECRET_KEY', '');
            }

            if ($flwBaseUrl === '' || $flwSecret === '') {
                throw new \Exception('Card payment is not configured');
            }

            $flwRef = $this->flw_ref ?? ($tx->raw_provider_response['data']['flw_ref'] ?? null);
            if (!$flwRef) {
                throw new \Exception('Missing payment reference');
            }

            $payload = [
                'flw_ref' => $flwRef,
                'otp' => $this->otp,
                'reference' => $tx->reference,
            ];

            $validate = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $flwSecret,
            ])->post($flwBaseUrl . '/validate-charge', $payload);

            $json = $validate->json();

            if (!$validate->successful()) {
                $msg = $json['message'] ?? 'OTP validation failed';
                throw new \Exception($msg);
            }

            $flwStatus = strtolower((string) ($json['data']['status'] ?? ''));

            if ($flwStatus !== 'successful') {
                $tx->update([
                    'status' => 'failed',
                    'raw_provider_response' => $json,
                ]);
                throw new \Exception($json['message'] ?? 'Payment was not successful');
            }

            $tx->update([
                'provider' => 'flutterwave',
                'provider_transaction_id' => (string) ($json['data']['id'] ?? ''),
                'provider_reference' => $json['data']['tx_ref'] ?? $tx->reference,
                'status' => 'success',
                'paid_at' => $tx->paid_at ?? now(),
                'raw_provider_response' => $json,
            ]);

            $this->handleSuccessInternal($tx);
            $this->processing = false;
            $this->step = 'success';
        } catch (\Throwable $e) {
            $this->otpError = $e->getMessage();
            $this->processing = false;
        }
    }

    private function payWithTransfer($tx)
    {
        if ($tx->bank_transfer_account) {
            $this->step = 'payment';
            $this->calculateTimeLeft($tx);
            return;
        }

        if ($this->enable_palmpay) {
            try {
                $this->payWithPalmPay($tx);
                return;
            } catch (\Throwable $e) {
                $this->addError('payment_method', 'PalmPay Bank Transfer error: ' . $e->getMessage());
                return;
            }
        }

        return $this->payWithFlutterwaveTransfer($tx);
    }

    private function payWithFlutterwaveTransfer($tx)
    {
        $flwRedirectUrl = route('gateway.flutterwave.callback', ['reference' => $tx->reference], true);
        $customerName = $this->customer_name ?: 'Customer';
        $nameParts = explode(' ', trim($customerName), 2);
        $vaFirstname = $nameParts[0] ?? 'Customer';
        $vaLastname = $nameParts[1] ?? $tx->reference;
        $amountNgn = (float) (($tx->charged_amount ?? $tx->amount) / 100);

        $vaRes = createFlutterwaveVirtualAccount([
            'email' => $this->customer_email,
            'phone' => $this->customer_phone ?? '08000000000',
            'firstname' => $vaFirstname,
            'lastname' => $vaLastname,
            'merchant_ref' => 'TX' . $tx->id,
            'narration' => 'PayZenter ' . $tx->reference,
            'is_permanent' => false,
            'amount' => $amountNgn,
        ]);

        $vaSuccess = false;
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
                    'provider_reference' => $vaData['reference'] ?? $tx->reference,
                    'status' => 'paying',
                    'raw_provider_response' => $vaRes,
                ]);
                $vaSuccess = true;
            }
        }

        if ($vaSuccess) {
            $this->step = 'payment';
            $this->timeLeft = 1800;
            return;
        }

        $hostedRes = createFlutterwaveHostedPayment([
            'tx_ref' => $tx->reference . '_h_' . time(),
            'amount' => $amountNgn,
            'currency' => $tx->currency ?? 'NGN',
            'email' => $this->customer_email,
            'phone' => $this->customer_phone ?? null,
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
            $this->addError('payment_method', $hostedRes['message'] ?? 'Unable to generate transfer payment options');
            return;
        }

        $hostedData = $hostedRes['data'];
        $tx->update([
            'provider' => 'flutterwave',
            'provider_reference' => $hostedData['reference'] ?? $tx->reference,
            'status' => 'paying',
            'raw_provider_response' => [
                'virtual_account_failed' => $vaRes,
                'hosted_payment' => $hostedRes,
                'checkout_url' => $hostedData['link'] ?? null,
            ],
        ]);

        $hostedLink = $hostedData['link'] ?? null;
        if ($hostedLink) {
            return redirect()->away($hostedLink);
        }

        $this->addError('payment_method', 'Unable to retrieve hosted checkout link');
    }

    public function checkStatus()
    {
        $tx = $this->getTransaction();

        if ($tx->status === 'success') {
            $this->step = 'success';
            return;
        }

        if ($tx->expires_at && now()->isAfter($tx->expires_at)) {
            $tx->update(['status' => 'closed']);
            return;
        }

        if ($tx->provider === 'palmpay' && ($tx->palmpay_order_id || $tx->palmpay_order_no)) {
            try {
                $palmPay = new PalmPayService();
                $res = $palmPay->queryOrderStatus([
                    'orderId' => $tx->palmpay_order_id,
                    'orderNo' => $tx->palmpay_order_no,
                ]);

                $orderStatus = $res['data']['orderStatus'] ?? null;
                if ((string) $orderStatus === '2' || $orderStatus === 2) {
                    $tx->update([
                        'status' => 'success',
                        'paid_at' => $tx->paid_at ?? now(),
                        'raw_provider_response' => $res,
                    ]);
                    $this->handleSuccessInternal($tx);
                    $this->step = 'success';
                }
            } catch (\Throwable $e) {
                // background check silent fail
            }
        }
    }

    public function verifyPayment()
    {
        $this->verificationMessage = null;
        $tx = $this->getTransaction();

        if ($tx->status === 'success') {
            $this->step = 'success';
            return;
        }

        try {
            if ($tx->provider === 'palmpay' && ($tx->palmpay_order_id || $tx->palmpay_order_no)) {
                $palmPay = new PalmPayService();
                $queryParams = array_filter([
                    'orderId' => $tx->palmpay_order_id,
                    'orderNo' => $tx->palmpay_order_no,
                ]);
                $res = $palmPay->queryOrderStatus($queryParams);

                $orderStatus = $res['data']['orderStatus'] ?? $res['data']['status'] ?? null;
                $statusStr = strtoupper(trim((string) $orderStatus));

                if (in_array($statusStr, ['2', '00', '00000000', 'SUCCESS', 'SUCCESSFUL', 'PAY_SUCCESS', 'FINISHED', 'COMPLETED'], true)) {
                    $tx->update([
                        'status' => 'success',
                        'paid_at' => $tx->paid_at ?? now(),
                        'raw_provider_response' => $res,
                    ]);
                    $this->handleSuccessInternal($tx);
                    $this->step = 'success';
                    return;
                }
            } elseif ($tx->provider === 'flutterwave' && $tx->provider_transaction_id) {
                $flwRes = Flutterwave::verifyTransaction($tx->provider_transaction_id);
                $flwStatus = strtolower((string) ($flwRes['data']['status'] ?? ''));
                if ($flwStatus === 'successful') {
                    $tx->update([
                        'status' => 'success',
                        'paid_at' => $tx->paid_at ?? now(),
                        'raw_provider_response' => $flwRes,
                    ]);
                    $this->handleSuccessInternal($tx);
                    $this->step = 'success';
                    return;
                }
            }
        } catch (\Throwable $e) {
            // Ignore query exceptions and check fresh model
        }

        $freshTx = $tx->fresh();
        if ($freshTx && $freshTx->status === 'success') {
            $this->step = 'success';
            return;
        }

        $this->verificationMessage = 'Payment not yet confirmed by the bank. If you just made the transfer, please wait 1-2 minutes for bank network processing and click again.';
    }

    private function handleSuccessInternal(GatewayTransaction $tx): void
    {
        $alreadyProcessed = UserTransaction::query()
            ->where('reference', $tx->reference)
            ->where('type', UserTransaction::TYPE_CREDIT)
            ->exists();

        if ($alreadyProcessed || $tx->status !== 'success') {
            return;
        }

        DB::beginTransaction();

        try {
            $amountNgn = $tx->amount / 100;
            $chargeKobo = $tx->fee;
            $chargeNgn = $chargeKobo / 100;

            $merchant = $tx->merchant;
            $merchant->wallet_balance += $tx->amount;
            if ($tx->charge_mode === 'merchant') {
                $merchant->wallet_balance -= $chargeKobo;
            }
            $merchant->save();

            $merchant->transactions()->create([
                'amount' => $tx->amount,
                'type' => UserTransaction::TYPE_CREDIT,
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'source' => UserTransaction::SOURCE_BANK_TRANSFER,
                'message' => 'Gateway payment received: ₦' . number_format($amountNgn, 2),
                'reference' => $tx->reference,
            ]);

            if ($chargeKobo > 0 && $tx->charge_mode === 'merchant') {
                $merchant->transactions()->create([
                    'amount' => $chargeKobo,
                    'type' => UserTransaction::TYPE_DEBIT,
                    'status' => UserTransaction::STATUS_SUCCESSFUL,
                    'source' => UserTransaction::SOURCE_BANK_TRANSFER,
                    'message' => 'Gateway payment charge: ₦' . number_format($chargeNgn, 2),
                    'reference' => $tx->reference,
                    'fee' => $chargeKobo,
                ]);
            }

            DB::commit();

            (new GatewayWebhookService())->dispatch($tx, 'payment.success');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
        }
    }

    public function goBackToMethod()
    {
        $this->step = 'method';
        $this->cardError = null;
        $this->otpError = null;
        $this->ussdError = null;
        $this->card_number = null;
        $this->card_cvv = null;
        $this->card_expiry_month = null;
        $this->card_expiry_year = null;
        $this->card_name = null;
        $this->card_pin = null;
        $this->otp = null;
        $this->flw_ref = null;
        $this->auth_mode = null;
        $this->auth_hint = null;
        $this->auth_url = null;
        $this->ussd_bank = null;
        $this->ussd_code = null;
        $this->ussd_ref = null;
    }

    private function getEncryptionKey($secretKey)
    {
        $key = (string) env('FLW_ENCRYPTION_KEY', '');
        if ($key !== '') {
            return $key;
        }

        // Derive key from secret key
        $cleanSecret = str_replace('FLWSECK-', '', $secretKey);
        $cleanSecret = str_replace('FLWSECK_TEST-', '', $cleanSecret);
        $md5 = md5($cleanSecret);
        
        $part1 = substr($cleanSecret, 0, 12);
        $part2 = substr($md5, -12);
        
        return $part1 . $part2;
    }

    private function encryptPayload($key, $payload)
    {
        $json = json_encode($payload);
        $encrypted = openssl_encrypt($json, 'DES-EDE3', $key, OPENSSL_RAW_DATA);
        return base64_encode($encrypted);
    }

    public function render()
    {
        $tx = $this->getTransaction();
        return view('livewire.gateway.payment-link-checkout', [
            'tx' => $tx,
        ])->extends('layouts.guest')->title('Secure Payment');
    }
}
