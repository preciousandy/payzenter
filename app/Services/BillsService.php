<?php

namespace App\Services;

use App\Models\ErrorLog;
use App\Models\UserTransaction;
use Illuminate\Support\Facades\Http;

class BillsService
{

  public function fetchEbillsAccessToken()
  {
    try {
      $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
      ])
        ->post(env("E_BILLS_API_ENDPOINT") . "/jwt-auth/v1/token", [
          'username' => env("E_BILLS_USERNAME"),
          "password" => env("E_BILLS_PASSWORD")
        ]);

      $responseData = $response->json();

      if ($response->successful()) {
        return [
          'status' => 'success',
          'data' => $responseData['token'],
        ];
      } else {
        throw new \Exception($responseData['message']);
      }
    } catch (\Throwable $th) {

      ErrorLog::logError(
        ErrorLog::SOURCE_EBILLS_API,
        "Error fetching E-bills access token: " . $th->getMessage(),
      );

      return [
        'status' => 'error',
        'message' => 'Unable to process your request at the moment. Please try again later.'
      ];
    }
  }


  public function fetchEbillsWalletBalance()
  {

    $accessToken = getEbillsAccessToken();

    if (!$accessToken) {
      return [
        'status' => 'error',
        'message' => 'Unable to process your request at the moment. Please try again later.'
      ];
    }

    try {
      $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'Authorization' => 'Bearer ' . $accessToken
      ])
        ->get(env("E_BILLS_API_ENDPOINT") . "/api/v2/balance",);

      $responseData = $response->json();

      if ($response->successful()) {
        return [
          'status' => 'success',
          'data' => $responseData
        ];
      } else {
        throw new \Exception($responseData['message']);
      }
    } catch (\Throwable $th) {
      ErrorLog::logError(
        ErrorLog::SOURCE_EBILLS_API,
        "Error fetching E-bills wallet balance: " . $th->getMessage(),
      );

      return [
        'status' => 'error',
        'message' => 'Unable to process your request at the moment. Please try again later.'
      ];
    }
  }

  public function purchaseAirtime($network_code, $amount, $phone)
  {

    $accessToken = getEbillsAccessToken();

    if (!$accessToken) {
      return [
        'status' => 'error',
        'message' => 'Unable to process your request at the moment. Please try again later.'
      ];
    }

    try {
      $reference = generateTransactionReference(UserTransaction::SOURCE_AIRTIME) . random_int(100000, 999999);

      $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'Authorization' => 'Bearer ' . $accessToken
      ])
        ->post(env("E_BILLS_API_ENDPOINT") . "/api/v2/airtime", [
          "request_id" => $reference,
          'service_id' => $network_code,
          'amount' => $amount,
          'phone' => $phone,
        ]);

      $responseData = $response->json();

      if ($response->successful() && $responseData['code'] == 'success') {
        return [
          'status' => 'success',
          'reference' => $reference,
          'data' => $responseData
        ];
      } else {
        throw new \Exception($responseData['message']);
      }
    } catch (\Throwable $th) {
      ErrorLog::logError(
        ErrorLog::SOURCE_EBILLS_API,
        "Error purchasing airtime: " . $th->getMessage(),
      );

      return [
        'status' => 'error',
        'message' => 'Airtime purchase failed. Please try again later.'
      ];
    }
  }

  public function fetchEbillsDataPlans()
  {
    try {
      $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
      ])
        ->get(env("E_BILLS_API_ENDPOINT") . "/api/v2/variations/data",);

      $responseData = $response->json();

      if ($response->successful() && $responseData['code'] == 'success') {
        return [
          'status' => 'success',
          'data' => $responseData['data']
        ];
      } else {
        throw new \Exception($responseData['message']);
      }
    } catch (\Throwable $th) {
      ErrorLog::logError(
        ErrorLog::SOURCE_EBILLS_API,
        "Error fetching E-bills  data plans: " . $th->getMessage(),
      );

      return [
        'status' => 'error',
        'message' => 'Unable to fetch data plans at the moment. Please try again later.'
      ];
    }
  }

  public function purchaseDataPlan($network_code, $data_plan, $phone)
  {

    $accessToken = getEbillsAccessToken();

    if (!$accessToken) {
      return [
        'status' => 'error',
        'message' => 'Unable to process your request at the moment. Please try again later.'
      ];
    }

    try {

      $reference = generateTransactionReference(UserTransaction::SOURCE_DATA) . random_int(100000, 999999);

      $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'Authorization' => 'Bearer ' . $accessToken
      ])
        ->post(env("E_BILLS_API_ENDPOINT") . "/api/v2/data", [
          "request_id" => $reference,
          'service_id' => $network_code,
          'variation_id' => $data_plan,
          'phone' => $phone,
        ]);

      $responseData = $response->json();

      if ($response->successful() && $responseData['code'] == 'success') {
        return [
          'status' => 'success',
          'reference' => $reference,
          'data' => $responseData
        ];
      } else {
        throw new \Exception($responseData['message']);
      }
    } catch (\Throwable $th) {

      ErrorLog::logError(
        ErrorLog::SOURCE_EBILLS_API,
        "Error purchasing data plan: " . $th->getMessage(),
      );

      return [
        'status' => 'error',
        'message' => 'Data purchase failed. Please try again later.'
      ];
    }
  }

  public function fetchEbillsCustomer($customer_id, $service_code, $variation_id = 'prepaid')
  {
    $accessToken = getEbillsAccessToken();

    if (!$accessToken) {
      return [
        'status' => 'error',
        'message' => 'Unable to process your request at the moment. Please try again later.'
      ];
    }

    try {
      $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'Authorization' => 'Bearer ' . $accessToken
      ])
        ->post(env("E_BILLS_API_ENDPOINT") . "/api/v2/verify-customer", [
          'service_id' => $service_code,
          'variation_id' => $variation_id,
          'customer_id' => $customer_id,
        ]);

      $responseData = $response->json();

      if ($response->successful() && $responseData['code'] == 'success') {
        return [
          'status' => 'success',
          'data' => $responseData['data']
        ];
      } else {
        throw new \Exception($responseData['message']);
      }
    } catch (\Throwable $th) {
      ErrorLog::logError(
        ErrorLog::SOURCE_EBILLS_API,
        "Error fetching E-bills customer: " . $th->getMessage(),
      );
      return [
        'status' => 'error',
        'message' => 'Unable to verify customer at the moment. Please try again later.'
      ];
    }
  }

  public function purchaseElectricity($customer_id, $service_code, $amount,  $variation_id = 'prepaid')
  {
    $accessToken = getEbillsAccessToken();

    if (!$accessToken) {
      return [
        'status' => 'error',
        'message' => 'Unable to process your request at the moment. Please try again later.'
      ];
    }

    $reference = generateTransactionReference(UserTransaction::SOURCE_ELECTRICITY) . random_int(100000, 999999);

    try {
      $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'Authorization' => 'Bearer ' . $accessToken
      ])
        ->post(env("E_BILLS_API_ENDPOINT") . "/api/v2/electricity", [
          'service_id' => $service_code,
          'variation_id' => $variation_id,
          'customer_id' => $customer_id,
          'amount' => $amount,
          'request_id' => $reference,
        ]);

      $responseData = $response->json();

      if ($response->successful() && $responseData['code'] == 'success') {
        return [
          'status' => 'success',
          'reference' => $reference,
          'data' => $responseData['data']
        ];
      } else {
        throw new \Exception($responseData['message']);
      }
    } catch (\Throwable $th) {
      ErrorLog::logError(
        ErrorLog::SOURCE_EBILLS_API,
        "Error purchasing electricity: " . $th->getMessage(),
      );
      return [
        'status' => 'error',
        'message' => 'Electricity purchase failed. Please try again later.'
      ];
    }
  }

  public function fetchEbillsTvPlans()
  {
    try {
      $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
      ])
        ->get(env("E_BILLS_API_ENDPOINT") . "/api/v2/variations/tv",);

      $responseData = $response->json();

      if ($response->successful() && $responseData['code'] == 'success') {
        return [
          'status' => 'success',
          'data' => $responseData['data']
        ];
      } else {
        throw new \Exception($responseData['message']);
      }
    } catch (\Throwable $th) {
      ErrorLog::logError(
        ErrorLog::SOURCE_EBILLS_API,
        "Error fetching E-bills TV plans: " . $th->getMessage(),
      );
      return [
        'status' => 'error',
        'message' => 'Unable to fetch TV plans at the moment. Please try again later.'
      ];
    }
  }

  public function purchaseTvCable($smart_card_number, $provider_code,  $package_code)
  {
    $accessToken = getEbillsAccessToken();

    if (!$accessToken) {
      return [
        'status' => 'error',
        'message' => 'Unable to process your request at the moment. Please try again later.'
      ];
    }

    $reference = generateTransactionReference(UserTransaction::SOURCE_CABLE) . random_int(100000, 999999);

    try {
      $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'Authorization' => 'Bearer ' . $accessToken
      ])
        ->post(env("E_BILLS_API_ENDPOINT") . "/api/v2/tv", [
          'service_id' => $provider_code,
          'variation_id' => $package_code,
          'customer_id' => $smart_card_number,
          'request_id' => $reference,
        ]);

      $responseData = $response->json();

      if ($response->successful() && $responseData['code'] == 'success') {
        return [
          'status' => 'success',
          'reference' => $reference,
          'data' => $responseData['data']
        ];
      } else {
        throw new \Exception($responseData['message']);
      }
    } catch (\Throwable $th) {
      ErrorLog::logError(
        ErrorLog::SOURCE_EBILLS_API,
        "Error purchasing cable subscription: " . $th->getMessage(),
      );
      return [
        'status' => 'error',
        'message' => 'Cable subscription failed. Please try again later.'
      ];
    }
  }

  public function fundBettingAccount($customer_id, $service_code, $amount,  $variation_id = 'prepaid')
  {
    $accessToken = getEbillsAccessToken();

    if (!$accessToken) {
      return [
        'status' => 'error',
        'message' => 'Unable to process your request at the moment. Please try again later.'
      ];
    }

    $reference = generateTransactionReference(UserTransaction::SOURCE_BETTING) . random_int(100000, 999999);

    try {
      $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'Authorization' => 'Bearer ' . $accessToken
      ])
        ->post(env("E_BILLS_API_ENDPOINT") . "/api/v2/betting", [
          'service_id' => $service_code,
          'variation_id' => $variation_id,
          'customer_id' => $customer_id,
          'amount' => $amount,
          'request_id' => $reference,
        ]);

      $responseData = $response->json();

      if ($response->successful() && $responseData['code'] == 'success') {
        return [
          'status' => 'success',
          'reference' => $reference,
          'data' => $responseData['data']
        ];
      } else {
        throw new \Exception($responseData['message']);
      }
    } catch (\Throwable $th) {
      ErrorLog::logError(
        ErrorLog::SOURCE_EBILLS_API,
        "Error funding betting account: " . $th->getMessage(),
      );

      return [
        'status' => 'error',
        'message' => 'Betting account funding failed. Please try again later.'
      ];
    }
  }


  public function checkOrderStatus($transaction_reference)
  {
    $accessToken = getEbillsAccessToken();

    if (!$accessToken) {
      return [
        'status' => 'error',
        'message' => 'Unable to process your request at the moment. Please try again later.'
      ];
    }

    try {
      $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'Authorization' => 'Bearer ' . $accessToken

      ])
        ->post(env("E_BILLS_API_ENDPOINT") . "/api/v2/requery", [
          "request_id" => $transaction_reference,
        ]);

      $responseData = $response->json();

      if ($response->successful() && $responseData['code'] == 'success') {
        return [
          'status' => 'success',
          'data' => $responseData['data']
        ];
      } else {
        throw new \Exception($responseData['message']);
      }
    } catch (\Throwable $th) {
      ErrorLog::logError(
        ErrorLog::SOURCE_EBILLS_API,
        "Error checking order status: " . $th->getMessage(),
      );
      return [
        'status' => 'error',
        'message' => 'Unable to check order status at the moment. Please try again later.'
      ];
    }
  }
}
