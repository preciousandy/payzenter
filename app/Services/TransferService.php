<?php

namespace App\Services;

use App\Helpers\SettingHelper;
use App\Models\ErrorLog;
use App\Models\Transfer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TransferService
{
  public function canUsePalmPayPayout(): bool
  {
    return rtrim((string) env('PALMPAY_BASE_URL', ''), '/') !== ''
      && trim((string) env('PALMPAY_APP_ID', '')) !== ''
      && trim((string) env('PALMPAY_MERCHANT_PRIVATE_KEY_BASE64', '')) !== '';
  }

  public function canUseFlutterwaveTransfer(): bool
  {
    return rtrim((string) env('FLW_API_URL', ''), '/') !== ''
      && trim((string) env('FLW_LIVE_SECRET_KEY', '')) !== '';
  }

  public function resolveTransferProvider(): string
  {
    if ($this->canUsePalmPayPayout()) {
      return 'palmpay';
    }

    $selected = strtolower(trim((string) SettingHelper::get('gateway_provider', 'palmpay')));

    if ($selected === 'palmpay' && $this->canUsePalmPayPayout()) {
      return 'palmpay';
    }

    if ($selected === 'flutterwave' && $this->canUseFlutterwaveTransfer()) {
      return 'flutterwave';
    }

    if ($this->canUseFlutterwaveTransfer()) {
      return 'flutterwave';
    }

    return 'palmpay';
  }

  public static function mapToFlutterwaveBankCode(string $code): string
  {
    $clean = trim($code);
    
    // Map of PalmPay/Common codes to Flutterwave codes
    $map = [
        '999032' => '100004', // OPay
        '090551' => '090405', // Moniepoint MFB
        '100004' => '100004', // OPay
        '090405' => '090405', // Moniepoint MFB
        '100033' => '100033', // PalmPay
        '090267' => '090267', // Kuda Bank
    ];

    return $map[$clean] ?? $clean;
  }

  public function confirmCustomer($bank_code, $bank_account_number)
  {
    try {
      $bankCode = self::mapToFlutterwaveBankCode((string) $bank_code);
      $tokens = array_values(array_filter([env("FLW_LIVE_SECRET_KEY"), env("FLW_SECRET_KEY")]));
      $apiUrl = rtrim((string) env("FLW_API_URL", "https://api.flutterwave.com/v3"), '/');

      $lastError = null;
      foreach ($tokens as $token) {
        $res = Http::withHeaders([
          'Content-Type' => 'application/json',
          'Authorization' => 'Bearer ' . $token
        ])->timeout(8)->post($apiUrl . "/accounts/resolve", [
          "account_number" => (string) $bank_account_number,
          "account_bank" => (string) $bankCode
        ]);

        $data = $res->json();

        if ($res->successful() && ($data['status'] ?? '') === "success") {
          return [
            'status' => 'success',
            'data' => $data['data']
          ];
        }

        $lastError = $data['message'] ?? 'Unable to resolve account';
      }

      throw new \Exception($lastError ?? 'Unable to confirm account details');
    } catch (\Throwable $th) {
      ErrorLog::logError(
        ErrorLog::SOURCE_DOT_API,
        "Error confirming customer: " . $th->getMessage(),
      );
      return [
        'status' => 'error',
        'message' =>  'Unable to confirm account details at the moment. Please try again later.'
      ];
    }
  }

  public function getDotAccount($bank_code, $bank_account_number)
  {
    $accessToken = getDotAccessToken();
    $baseUrl = rtrim((string) env("DOT_MFB_API_URL", ''), '/');

    if ($baseUrl === '' || !$accessToken) {
      return $this->confirmCustomer($bank_code, $bank_account_number);
    }

    try {
      $res = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $accessToken
      ])->get(env("DOT_MFB_API_URL") . "/banking/account-info", [
        "accountNo" => $bank_account_number,
        "bankCode" => $bank_code
      ]);

      $data = $res->json();

      if ($res->successful()) {
        return [
          'status' => 'success',
          'data' => $data
        ];
      } else {
        Log::channel('transfer-logs')->info('Check bank account error: ' . $res->body());
        throw new \Exception($data['description'] ?? 'Failed to fetch account details');
      }
    } catch (\Throwable $th) {
      ErrorLog::logError(
        ErrorLog::SOURCE_DOT_API,
        "Error confirming dot customer: " . $th->getMessage(),
      );
      return [
        'status' => 'error',
        'message' => 'Unable to verify account at the moment. Please try again later.'
      ];
    }
  }

  public function initaiteDotTransfer($receipent_bank_code, $receipent_account_number, $receipent_account_name, $amount, $reference, $description = '', $bank_name, $user = null)
  {
    $accessToken = getDotAccessToken();

    if (!$accessToken) {
      return [
        'state' => 'FAILED',
        'message' => 'Unable to connect to Dot MFB at the moment. Please try again later.'
      ];
    }

    try {
      $res = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $accessToken
      ])->post(env("DOT_MFB_API_URL") . "/transfers/submit", [
        "amount" => $amount,
        "channel" => "THIRD_PARTY",
        "remarks" => "PGNG/ " . $description,
        "transactionRef" => $reference,
        "sender" => [
          "accountNo" => 1006694412,
          "bankCode" => "090470",
          "bankName" => "DOTMFB",
          "fullName" => $user ? $user->name : auth()->user()->name,
          "kycLevel" => "1",
          "narration" => "transfer"
        ],
        "beneficiary" => [
          "accountNo" => $receipent_account_number,
          "bankCode" => $receipent_bank_code,
          "bankName" => $bank_name,
          "fullName" => $receipent_account_name,
          "kycLevel" => "3"
        ]
      ]);

      $data = $res->json();

      if ($res->successful() && $data['state'] !== "FAILED") {
        return $data;
      } else {
        Log::channel('transfer-logs')->info('transfer to bank error: ' . $res->body());
        throw new \Exception($data['description'] ?? 'Failed to initiate transfer');
      }
    } catch (\Throwable $th) {
      ErrorLog::logError(
        ErrorLog::SOURCE_DOT_API,
        "Error in initiating dot transfer: " . $th->getMessage(),
      );
      return [
        'state' => 'FAILED',
        'message' => 'Unable to process transfer at the moment. Please try again later.'
      ];
    }
  }

  public function initiateFlutterwaveTransfer($receipent_bank_code, $receipent_account_number, $receipent_account_name, $amount, $reference, $description = '')
  {
    $baseUrl = rtrim((string) env('FLW_API_URL', ''), '/');
    $secret = (string) env('FLW_LIVE_SECRET_KEY', '');

    if ($baseUrl === '' || $secret === '') {
      return [
        'state' => 'FAILED',
        'message' => 'Flutterwave is not configured. Please add your Flutterwave keys.'
      ];
    }

    try {
      $bankCode = self::mapToFlutterwaveBankCode((string) $receipent_bank_code);
      $res = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $secret,
      ])->post($baseUrl . '/transfers', [
        'account_bank' => $bankCode,
        'account_number' => $receipent_account_number,
        'amount' => $amount,
        'narration' => $description !== '' ? $description : 'Withdrawal',
        'currency' => 'NGN',
        'reference' => $reference,
        'beneficiary_name' => $receipent_account_name,
      ]);

      $data = $res->json();

      if ($res->successful() && ($data['status'] ?? null) === 'success') {
        return [
          'state' => Transfer::STATUS_PENDING,
          'receiptNumber' => $reference,
          'requestId' => (string) (($data['data']['id'] ?? null) ?? ''),
          'raw' => $data,
        ];
      }

      $message = $data['message'] ?? 'Unable to process withdrawal at the moment. Please try again later.';
      throw new \Exception($message);
    } catch (\Throwable $th) {
      ErrorLog::logError(
        ErrorLog::SOURCE_DOT_API,
        "Error in initiating flutterwave transfer: " . $th->getMessage(),
      );
      return [
        'state' => 'FAILED',
        'message' => 'Unable to process withdrawal at the moment. Please try again later.'
      ];
    }
  }

  public function initiatePalmPayTransfer($receipent_bank_code, $receipent_account_number, $receipent_account_name, $amount, $reference, $description = '')
  {
    if (!$this->canUsePalmPayPayout()) {
      return [
        'state' => 'FAILED',
        'message' => 'PalmPay payout is not configured. Please add your PalmPay details.'
      ];
    }

    try {
      $bankCode = PalmPayService::mapToPalmPayBankCode((string) $receipent_bank_code);

      $payload = [
        'orderId' => substr(preg_replace('/[^A-Za-z0-9]/', '', (string) $reference), 0, 32),
        'title' => 'Settlement Withdrawal',
        'description' => $description !== '' ? $description : 'Settlement withdrawal',
        'payeeName' => $receipent_account_name,
        'payeeBankCode' => $bankCode,
        'payeeBankAccNo' => (string) $receipent_account_number,
        'currency' => 'NGN',
        'amount' => (int) round(((float) $amount) * 100),
        'notifyUrl' => route('webhook.palmpay', [], true),
        'remark' => $description !== '' ? $description : 'Settlement withdrawal',
      ];

      $response = (new PalmPayService())->createPayout($payload);
      $data = $response['data'] ?? [];

      return [
        'state' => $this->mapPalmPayTransferState($data['orderStatus'] ?? null),
        'receiptNumber' => (string) ($data['orderId'] ?? $payload['orderId']),
        'requestId' => (string) ($data['orderNo'] ?? ''),
        'raw' => $response,
      ];
    } catch (\Throwable $th) {
      Log::error("Error in initiating PalmPay transfer: " . $th->getMessage());
      ErrorLog::logError(
        ErrorLog::SOURCE_DOT_API,
        "Error in initiating PalmPay transfer: " . $th->getMessage(),
      );

      return [
        'state' => 'FAILED',
        'message' => 'PalmPay payout failed: ' . $th->getMessage()
      ];
    }
  }

  public function checkFlutterwaveTransferStatus(?string $transferId = null, ?string $reference = null): array
  {
    $baseUrl = rtrim((string) env('FLW_API_URL', 'https://api.flutterwave.com/v3'), '/');
    if ($baseUrl === '') {
      $baseUrl = 'https://api.flutterwave.com/v3';
    }
    $secret = (string) (env('FLW_LIVE_SECRET_KEY') ?: env('FLW_SECRET_KEY', ''));

    if ($secret === '') {
      return [
        'status' => 'error',
        'message' => 'Flutterwave is not configured. Please add your Flutterwave keys.'
      ];
    }

    if (($transferId === null || trim($transferId) === '') && ($reference === null || trim($reference) === '')) {
      return [
        'status' => 'error',
        'message' => 'Transfer identifier is missing.'
      ];
    }

    try {
      if ($transferId !== null && trim($transferId) !== '') {
        $res = Http::withHeaders([
          'Content-Type' => 'application/json',
          'Authorization' => 'Bearer ' . $secret,
        ])->get($baseUrl . '/transfers/' . trim($transferId));
      } else {
        $res = Http::withHeaders([
          'Content-Type' => 'application/json',
          'Authorization' => 'Bearer ' . $secret,
        ])->get($baseUrl . '/transfers', [
          'reference' => trim((string) $reference),
        ]);
      }

      $data = $res->json();

      if (!$res->successful() || ($data['status'] ?? null) !== 'success') {
        $message = $data['message'] ?? 'Unable to check transfer status at the moment. Please try again later.';
        throw new \Exception($message);
      }

      $payload = $data['data'] ?? null;
      if (is_array($payload) && array_key_exists(0, $payload)) {
        $payload = $payload[0] ?? null;
      }

      $providerStatus = strtoupper((string) (($payload['status'] ?? null) ?? ''));
      $state = Transfer::STATUS_PENDING;
      if (in_array($providerStatus, ['SUCCESSFUL', 'COMPLETED'], true)) {
        $state = Transfer::STATUS_SUCCESS;
      } elseif ($providerStatus === 'FAILED') {
        $state = Transfer::STATUS_FAILED;
      }

      return [
        'status' => 'success',
        'state' => $state,
        'raw' => $data,
      ];
    } catch (\Throwable $th) {
      ErrorLog::logError(
        ErrorLog::SOURCE_DOT_API,
        "Error checking flutterwave transfer status: " . $th->getMessage(),
      );
      return [
        'status' => 'error',
        'message' => 'Unable to check transfer status at the moment. Please try again later.'
      ];
    }
  }

  public function checkPalmPayTransferStatus(?string $orderNo = null, ?string $reference = null): array
  {
    if (!$this->canUsePalmPayPayout()) {
      return [
        'status' => 'error',
        'message' => 'PalmPay payout is not configured. Please add your PalmPay details.'
      ];
    }

    if (($orderNo === null || trim($orderNo) === '') && ($reference === null || trim($reference) === '')) {
      return [
        'status' => 'error',
        'message' => 'Transfer identifier is missing.'
      ];
    }

    try {
      $response = (new PalmPayService())->queryPayoutStatus([
        'orderId' => $reference !== null && trim($reference) !== '' ? trim($reference) : null,
        'orderNo' => $orderNo !== null && trim($orderNo) !== '' ? trim($orderNo) : null,
      ]);

      $data = $response['data'] ?? [];
      $state = $this->mapPalmPayTransferState($data['orderStatus'] ?? null);

      return [
        'status' => match ($state) {
          Transfer::STATUS_SUCCESS => 'success',
          Transfer::STATUS_FAILED => 'failed',
          default => 'pending',
        },
        'state' => $state,
        'data' => $data,
        'raw' => $response,
      ];
    } catch (\Throwable $th) {
      ErrorLog::logError(
        ErrorLog::SOURCE_DOT_API,
        "Error checking PalmPay transfer status: " . $th->getMessage(),
      );

      return [
        'status' => 'error',
        'message' => 'Unable to check PalmPay transfer status at the moment. Please try again later.'
      ];
    }
  }

  public function fetchDotAccessToken()
  {
    try {
      $response = Http::asForm()
        ->withBasicAuth(env('DOT_LIVE_CLIENT_ID'), env('DOT_LIVE_CLIENT_SECRET'))
        ->timeout(30)
        ->post(env("DOT_MFB_API_URL") . "/oauth2/token", [
          'grant_type' => 'client_credentials'
        ]);

      $responseData = $response->json();

      if ($response->successful()) {
        return [
          'status' => 'success',
          'data' => $responseData['access_token'],
        ];
      } else {
        throw new \Exception($responseData['error_description']);
      }
    } catch (\Throwable $th) {
      ErrorLog::logError(
        ErrorLog::SOURCE_DOT_API,
        "Error getting dot access token: " . $th->getMessage(),
      );
      return [
        'status' => 'error',
        'message' => 'Unable to connect to the service at the moment. Please try again later.'
      ];
    }
  }

  public function checkDotTransferStatus($transfer_id, $transfer_reference)
  {
    $accessToken = getDotAccessToken();

    if (!$accessToken) {
      return [
        'status' => 'error',
        'message' => 'Unable to check transfer status at the moment. Please try again later.'
      ];
    }

    try {
      $res = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $accessToken
      ])->get(env("DOT_MFB_API_URL") . "/transfers/tsq", [
        'id' => $transfer_id,
        'reference' => $transfer_reference
      ]);

      $data = $res->json();

      if ($res->successful()) {
        if ($data && $data['state'] == Transfer::DOT_TRANSFER_STATUS_SUCCEEDED) {
          return  [
            'status' => 'success',
            'data' => $data
          ];
        }

        if ($data && $data['state'] == Transfer::DOT_TRANSFER_STATUS_FAILED) {
          return  [
            'status' => 'failed',
            'message' => 'Transfer was not successful. Please contact support if you have been debited.'
          ];
        }

        return  [
          'status' => 'pending',
          'message' => 'Transfer is still processing. Please check back later.'
        ];
      } else {
        throw new \Exception($data['description'] ?? 'Failed to check transfer status');
      }
    } catch (\Throwable $th) {
      ErrorLog::logError(
        ErrorLog::SOURCE_DOT_API,
        "Error checking dot transfer status: " . $th->getMessage(),
      );
      return [
        'status' => 'error',
        'message' => 'Unable to check transfer status at the moment. Please try again later.'
      ];
    }
  }

  private function mapPalmPayTransferState($orderStatus): string
  {
    if (!is_numeric($orderStatus)) {
      return Transfer::STATUS_PENDING;
    }

    return match ((int) $orderStatus) {
      2 => Transfer::STATUS_SUCCESS,
      3, 4 => Transfer::STATUS_FAILED,
      default => Transfer::STATUS_PENDING,
    };
  }
}
