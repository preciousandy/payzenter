<?php

namespace App\Livewire\FundWallet;

use App\Helpers\SettingHelper;
use App\Models\PaymentTransaction;
use App\Models\UserTransaction;
use App\Services\PaystackService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;

class PaystackCardPay extends Component
{
    public $amount = 5000;
    public $cardNumber = '';
    public $cardHolder = '';
    public $expiryDate = ''; // MM/YY
    public $cvv = '';
    
    public $isCardPaymentEnabled = true;
    public $isLoading = false;
    public $requiresOtp = false;
    public $requiresPin = false;
    public $requires3Ds = false;
    public $threeDsUrl = '';
    
    public $otpCode = '';
    public $pinCode = '';
    public $displayText = '';
    
    public $reference = '';
    public $errorMessage = null;
    public $successMessage = null;

    protected $rules = [
        'amount' => 'required|numeric|min:100',
        'cardNumber' => 'required|string|min:12',
        'expiryDate' => 'required|string|min:4',
        'cvv' => 'required|string|min:3|max:4',
    ];

    public function mount()
    {
        $this->isCardPaymentEnabled = (bool) SettingHelper::get(SettingHelper::ENABLE_PAYSTACK_CARD, '1');
        if (auth()->check()) {
            $this->cardHolder = auth()->user()->name ?? 'CARDHOLDER NAME';
        }
    }

    public function selectAmount($preset)
    {
        $this->amount = $preset;
    }

    public function getCardBrandProperty()
    {
        $clean = preg_replace('/\D/', '', $this->cardNumber);
        if (str_starts_with($clean, '4')) {
            return 'visa';
        }
        if (preg_match('/^(5[1-5]|222[1-9]|22[3-9]|2[3-6]|27[0-1]|2720)/', $clean)) {
            return 'mastercard';
        }
        if (preg_match('/^(506|507|650|5061)/', $clean)) {
            return 'verve';
        }
        if (str_starts_with($clean, '34') || str_starts_with($clean, '37')) {
            return 'american-express';
        }
        return 'card';
    }

    public function processCardPayment(PaystackService $paystack)
    {
        $this->errorMessage = null;
        $this->successMessage = null;

        $this->isCardPaymentEnabled = (bool) SettingHelper::get(SettingHelper::ENABLE_PAYSTACK_CARD, '1');
        if (!$this->isCardPaymentEnabled) {
            $this->errorMessage = 'Card payment via Paystack is currently disabled by administrator. Please use Bank Transfer.';
            return;
        }

        $this->validate();

        $cleanCardNumber = preg_replace('/\D/', '', $this->cardNumber);
        $expParts = explode('/', str_replace(' ', '', $this->expiryDate));
        $expiryMonth = $expParts[0] ?? '';
        $expiryYear = $expParts[1] ?? '';

        if (strlen($expiryYear) === 2) {
            $expiryYear = '20' . $expiryYear;
        }

        if (empty($expiryMonth) || empty($expiryYear)) {
            $this->errorMessage = 'Please enter a valid expiry date (MM/YY)';
            return;
        }

        $this->isLoading = true;

        $user = auth()->user();
        $this->reference = generateIncomingReference();

        Log::info('PaystackCardPay: Processing card payment', [
            'user_id'   => $user->id ?? null,
            'email'     => $user->email ?? null,
            'amount'    => $this->amount,
            'reference' => $this->reference,
        ]);

        $cardDetails = [
            'number' => $cleanCardNumber,
            'cvv' => $this->cvv,
            'expiry_month' => $expiryMonth,
            'expiry_year' => $expiryYear,
        ];

        $response = $paystack->chargeCard(
            $user->email,
            (float) $this->amount,
            $cardDetails,
            $this->reference,
            ['user_id' => $user->id]
        );

        if (!empty($response['status'])) {
            $dataStatus = strtolower((string) ($response['data']['status'] ?? ''));
            if (in_array($dataStatus, ['send_pin', 'send_otp', 'open_url', 'success', 'pending'])) {
                $this->isLoading = false;
                return $this->handleChargeResponse($response, $paystack);
            }
        }

        // Direct card charge not accepted by Paystack API on live mode, fallback to standard initialize
        $callbackUrl = route('fund-wallet');
        $initResult = $paystack->initializeTransaction((float) $this->amount, $user->email, $this->reference, $callbackUrl, ['user_id' => $user->id]);

        $this->isLoading = false;
        if (!empty($initResult['status']) && !empty($initResult['data']['authorization_url'])) {
            return redirect()->away($initResult['data']['authorization_url']);
        }

        $this->handleChargeResponse($response, $paystack);
    }

    public function submitOtp(PaystackService $paystack)
    {
        if (empty($this->otpCode)) {
            $this->errorMessage = 'Please enter the OTP sent to your phone/email.';
            return;
        }

        $this->isLoading = true;
        $this->errorMessage = null;

        Log::info('PaystackCardPay: Submitting OTP', [
            'user_id'   => auth()->id(),
            'reference' => $this->reference,
        ]);

        $response = $paystack->submitOtp($this->otpCode, $this->reference);
        $this->isLoading = false;
        
        $this->handleChargeResponse($response, $paystack);
    }

    public function submitPin(PaystackService $paystack)
    {
        if (empty($this->pinCode)) {
            $this->errorMessage = 'Please enter your card PIN.';
            return;
        }

        $this->isLoading = true;
        $this->errorMessage = null;

        Log::info('PaystackCardPay: Submitting PIN', [
            'user_id'   => auth()->id(),
            'reference' => $this->reference,
        ]);

        $response = $paystack->submitPin($this->pinCode, $this->reference);
        $this->isLoading = false;

        $this->handleChargeResponse($response, $paystack);
    }

    protected function handleChargeResponse(array $response, PaystackService $paystack)
    {
        $data = $response['data'] ?? [];
        $status = strtolower($data['status'] ?? '');

        Log::info('PaystackCardPay: Handling charge response', [
            'reference'       => $this->reference,
            'response_status' => $response['status'] ?? false,
            'data_status'     => $status,
            'message'         => $response['message'] ?? null,
        ]);

        if (empty($response['status'])) {
            $msg = $response['message'] ?? 'Payment failed. Please check your card details.';
            if ($msg === 'Charge attempted') {
                $msg = 'Card authorization failed. Please check your card details or PIN and try again.';
            }
            $this->errorMessage = $msg;
            return;
        }

        if ($status === 'send_otp') {
            $this->requiresOtp = true;
            $this->requiresPin = false;
            $this->displayText = $data['display_text'] ?? 'An OTP has been sent to your registered phone number or email.';
            return;
        }

        if ($status === 'send_pin') {
            $this->requiresPin = true;
            $this->requiresOtp = false;
            $this->displayText = 'Please enter your 4-digit card PIN to authorize the transaction.';
            return;
        }

        if ($status === 'open_url') {
            $this->requires3Ds = true;
            $this->threeDsUrl = $data['url'] ?? '';
            return;
        }

        if ($status === 'success') {
            $this->completePayment($data);
            return;
        }

        if ($status === 'pending') {
            // Check status via verify endpoint
            $verify = $paystack->verifyTransaction($this->reference);
            if (!empty($verify['status']) && ($verify['data']['status'] ?? '') === 'success') {
                $this->completePayment($verify['data']);
            } else {
                $this->errorMessage = 'Payment processing. If debited, your wallet will be updated automatically.';
            }
            return;
        }

        $rawMsg = $data['gateway_response'] ?? ($response['message'] ?? 'Payment failed.');
        if ($rawMsg === 'Charge attempted') {
            $rawMsg = 'Card authorization required. Please enter your Card PIN / OTP and try again.';
        }
        $this->errorMessage = $rawMsg;
    }

    protected function completePayment(array $data)
    {
        $amountNgn = ($data['amount'] ?? ($this->amount * 100)) / 100;
        $user = auth()->user();

        DB::beginTransaction();
        try {
            $existingTx = PaymentTransaction::where('transaction_reference', $this->reference)->first();
            if (!$existingTx) {
                PaymentTransaction::create([
                    'transaction_id' => $data['id'] ?? $this->reference,
                    'transaction_reference' => $this->reference,
                    'user_id' => $user->id,
                    'amount' => $amountNgn,
                    'currency' => $data['currency'] ?? 'NGN',
                    'charge_fee' => ($data['fees'] ?? 0) / 100,
                    'payment_method' => 'paystack_card',
                ]);

                $user->lockForUpdate();
                $user->wallet_balance += $amountNgn;
                $user->save();

                $user->transactions()->create([
                    'amount' => $amountNgn,
                    'type' => UserTransaction::TYPE_CREDIT,
                    'message' => "Funded ₦" . number_format($amountNgn, 2) . " via Paystack Card",
                    'status' => UserTransaction::STATUS_SUCCESSFUL,
                    'source' => UserTransaction::SOURCE_WALLET_FUNDED,
                    'reference' => $this->reference,
                ]);
            }
            DB::commit();

            Log::info('PaystackCardPay: Payment completed successfully & wallet credited', [
                'user_id'   => $user->id,
                'reference' => $this->reference,
                'amount'    => $amountNgn,
            ]);

            $this->requiresOtp = false;
            $this->requiresPin = false;
            $this->requires3Ds = false;
            $this->successMessage = "Payment Successful! ₦" . number_format($amountNgn, 2) . " has been added to your wallet.";
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PaystackCardPay: Error completing payment & wallet credit', [
                'user_id'   => $user->id,
                'reference' => $this->reference,
                'error'     => $e->getMessage(),
            ]);
            $this->errorMessage = "Error updating wallet: " . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.fund-wallet.paystack-card-pay');
    }
}
