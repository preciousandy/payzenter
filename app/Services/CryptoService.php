<?php

namespace App\Services;

use App\Models\ErrorLog;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class CryptoService
{
  private function getSwapRateNaira($symbol, $type = "buy")
  {
    // Convert symbol to lowercase first
    $symbol = strtolower($symbol);

    // Define a reasonable amount to use for each
    $buy_swap_rates = [
      'btc' => 100000,
      'eth' => 50000,
      // 'litecoin' => 10000,
      'ltc' => 10000,
      'xrp' => 1000,
      'usdt' => 1000,
      'dash' => 10,
      'trx' => 1000,
      'doge' => 1000,
      'bnb' => 50000,
      'axs' => 10,
      'safemoon' => 100000,
      'cake' => 1000,
      'xlm' => 1000,
      'aave' => 10000,
      'link' => 1000,
      'usdc' => 1000,
      'sol' => 20000,
      'bch' => 20000,
      'ada' => 1000,
      'matic' => 1000,
      'dot' => 5000

    ];

    $sell_swap_rates = [
      'btc' => 1,
      'eth' => 1,
      'ltc' => 10,
      'xrp' => 100,
      'usdt' => 100,
      'dash' => 10,
      'trx' => 1000,
      'doge' => 1000,
      'bnb' => 1,
      'axs' => 10,
      'safemoon' => 100000,
      'cake' => 10,
      'xlm' => 100,
      'aave' => 1,
      'link' => 10,
      'usdc' => 100,
      'sol' => 2,
      'bch' => 2,
      'ada' => 1000,
      'matic' => 1000,
      'dot' => 500
    ];

    if (!array_key_exists($symbol, $buy_swap_rates) || !array_key_exists($symbol, $sell_swap_rates)) {
      return "Cryptocurrency not supported.";
    }


    return $type === "buy" ? $buy_swap_rates[$symbol] : $sell_swap_rates[$symbol];
  }


  public function getCurrencySwapRate($to_currency_code, $from_currency = 'ngn')
  {

    // dd($to_currency_code, $this->getSwapRateNaira($to_currency_code, "buy"));

    $url = env("QUIDAX_API_URL") . "/users/me/temporary_swap_quotation";

    try {
      $res = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . env("QUIDAX_SECRET_API_KEY")
      ])->post($url, [
        "from_currency" => $from_currency,
        "to_currency" => $to_currency_code,
        'from_amount' => $to_currency_code  === 'ngn' ? $this->getSwapRateNaira($from_currency, "sell") : $this->getSwapRateNaira($to_currency_code, "buy")
      ]);

      $data = $res->json();
      // dd($data);


      if ($res->successful() && $data['status'] == "success") {
        return  [
          'status' => 'success',
          'data' => $data['data']
        ];
      } else {

        throw new \Exception($data['data']['message'] ?? 'An error occurred');
      }
    } catch (\Throwable $th) {
      ErrorLog::logError(
        ErrorLog::SOURCE_QUIDAX_API,
        "Error getting swap rate: " . $th->getMessage(),
        [
          'request_url' => $url
        ]
      );
      return [
        'status' => 'error',
        'message' => $th->getMessage()
      ];
    }
  }

  public function generateSwapQuotation($currency_code, $amount)
  {
    $url = env("QUIDAX_API_URL") . "/users/me/swap_quotation";

    try {
      $res = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . env("QUIDAX_SECRET_API_KEY")
      ])->post($url, [
        "from_currency" => "ngn",
        "to_currency" => $currency_code,
        'from_amount' => $amount
      ]);

      $data = $res->json();

      // dd($data);


      if ($res->successful() && $data['status'] == "success") {
        return  [
          'status' => 'success',
          'data' => $data['data']
        ];
      } else {
        throw new \Exception($data['data']['message'] ?? 'An error occurred');
      }
    } catch (\Throwable $th) {
      ErrorLog::logError(
        ErrorLog::SOURCE_QUIDAX_API,
        "Error generating swap quotation: " . $th->getMessage(),
        [
          'request_url' => $url
        ]
      );
      return [
        'status' => 'error',
        'message' => $th->getMessage()
      ];
    }
  }


  public function confirmSwapOrder($quotation_id)
  {

    $url = env("QUIDAX_API_URL") . "/users/me/swap_quotation/$quotation_id/confirm";
    try {
      $res = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . env("QUIDAX_SECRET_API_KEY")
      ])->post($url);

      $data = $res->json();

      // dd($data);

      if ($res->successful() && $data['status'] == "success") {
        return  [
          'status' => 'success',
          'data' => $data['data']
        ];
      } else {
        throw new \Exception($data['data']['message'] ?? 'An error occurred');
      }
    } catch (\Throwable $th) {
      ErrorLog::logError(
        ErrorLog::SOURCE_QUIDAX_API,
        "Error confirming swap order: " . $th->getMessage(),
        [
          'request_url' => $url
        ]
      );
      return [
        'status' => 'error',
        'message' => $th->getMessage()
      ];
    }
  }

  public function sendCryptoToUser($currency_code, $amount, $address, $network, $quotation_id, $destination_tag = null)
  {

    $url = env("QUIDAX_API_URL") . "/users/me/withdraws";

    try {
      $res = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . env("QUIDAX_SECRET_API_KEY")
      ])->post($url, [
        "currency" => $currency_code,
        "amount" => $amount,
        'fund_uid' => $address,
        'transaction_note' => $quotation_id,
        'narration' => 'buy-crypto',
        'network' => $network,
        'fund_uid2' => $destination_tag
      ]);

      $data = $res->json();


      if ($res->successful() && $data['status'] == "success") {
        return  [
          'status' => 'success',
          'data' => $data['data']
        ];
      } else {
        throw new \Exception($data['data']['message'] ?? 'An error occurred');
      }
    } catch (\Throwable $th) {
      ErrorLog::logError(
        ErrorLog::SOURCE_QUIDAX_API,
        "Error sending crypto to user: " . $th->getMessage(),
        [
          'request_url' => $url
        ]
      );
      return [
        'status' => 'error',
        'message' => $th->getMessage()
      ];
    }
  }

  public function getCryptoFees($currency_code, $network, $amount)
  {
    $url = env("QUIDAX_API_URL") . "/fee";

    try {
      $res = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . env("QUIDAX_SECRET_API_KEY")
      ])->get($url, [
        "currency" => $currency_code,
        "network" => $network
      ]);

      $data = $res->json();

      // dd($data['data']);


      if ($res->successful() && $data['status'] == "success") {
        return  [
          'status' => 'success',
          // 'data' => is_numeric($data['data']) ? 1 : 7
          'data' => $data['data']['type'] == 'flat' ? $data['data']['fee'] : $this->getCryptoTransactionFee($amount, $data['data']['fee'])
        ];
      } else {
        throw new \Exception($data['data']['message'] ?? 'An error occurred');
      }
    } catch (\Throwable $th) {
      ErrorLog::logError(
        ErrorLog::SOURCE_QUIDAX_API,
        "Error getting crypto fees: " . $th->getMessage(),
        [
          'request_url' => $url
        ]
      );
      return [
        'status' => 'error',
        'message' => $th->getMessage()
      ];
    }
  }

  private function getCryptoTransactionFee($amount, $feeBrackets)
  {
    foreach ($feeBrackets as $bracket) {
      if ($amount >= $bracket['min'] && $amount < $bracket['max']) {
        return $bracket['value']; // flat fee
      }
    }

    return null; // fallback if no bracket is found
  }

  public function generateUserSwapQuotation($user_quidax_id, $currency_code, $amount)
  {

    $url = env("QUIDAX_API_URL") . "/users/" . $user_quidax_id . "/swap_quotation";

    try {
      $res = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . env("QUIDAX_SECRET_API_KEY")
      ])->post($url, [
        "from_currency" => $currency_code,
        "to_currency" => "ngn",
        'from_amount' => $amount
      ]);

      $data = $res->json();


      if ($res->successful() && $data['status'] == "success") {
        return  [
          'status' => 'success',
          'data' => $data['data']
        ];
      } else {
        throw new \Exception($data['data']['message'] ?? 'An error occurred');
      }
    } catch (\Throwable $th) {
      ErrorLog::logError(
        ErrorLog::SOURCE_QUIDAX_API,
        "Error generating user swap quotation: " . $th->getMessage(),
        [
          'request_url' => $url
        ]
      );
      return [
        'status' => 'error',
        'message' => $th->getMessage()
      ];
    }
  }


  public function confirmUserSwapOrder($user_quidax_id, $quotation_id)
  {
    $url = env("QUIDAX_API_URL") . "/users/" . $user_quidax_id . "/swap_quotation/$quotation_id/confirm";
    try {

      $res = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . env("QUIDAX_SECRET_API_KEY")
      ])->post($url);

      $data = $res->json();


      if ($res->successful() && $data['status'] == "success") {
        return  [
          'status' => 'success',
          'data' => $data['data']
        ];
      } else {
        throw new \Exception($data['data']['message'] ?? 'An error occurred');
      }
    } catch (\Throwable $th) {
      ErrorLog::logError(
        ErrorLog::SOURCE_QUIDAX_API,
        "Error confirming user swap order: " . $th->getMessage(),
        [
          'request_url' => $url
        ]
      );
      return [
        'status' => 'error',
        'message' => $th->getMessage()
      ];
    }
  }

  public function getCryptoFromUser($user_quidax_id, $currency_code = 'ngn', $amount, $quotation_id)
  {

    $url = env("QUIDAX_API_URL") . "/users/$user_quidax_id/withdraws";

    try {
      $res = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . env("QUIDAX_SECRET_API_KEY")
      ])->post($url, [
        "currency" => $currency_code,
        "amount" => $amount,
        'fund_uid' => env('SWIFTPAY_QUIDAX_ID'),
        'transaction_note' => $quotation_id,
        'narration' => 'sell-crypto',
      ]);

      $data = $res->json();


      if ($res->successful() && $data['status'] == "success") {
        return  [
          'status' => 'success',
          'data' => $data['data']
        ];
      } else {
        throw new \Exception($data['data']['message'] ?? 'An error occurred');
      }
    } catch (\Throwable $th) {
      ErrorLog::logError(
        ErrorLog::SOURCE_QUIDAX_API,
        "Error getting crypto from user: " . $th->getMessage(),
        [
          'request_url' => $url
        ]
      );
      return [
        'status' => 'error',
        'message' => $th->getMessage()
      ];
    }
  }

  public function getAllSubAccounts()
  {
    $url = env("QUIDAX_API_URL") . "/users";
    try {
      $res = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . env("QUIDAX_SECRET_API_KEY")
      ])->get($url);

      $data = $res->json();


      if ($res->successful() && $data['status'] == "success") {
        return  [
          'status' => 'success',
          'data' => $data['data']
        ];
      } else {
        throw new \Exception($data['data']['message'] ?? 'An error occurred');
      }
    } catch (\Throwable $th) {
      ErrorLog::logError(
        ErrorLog::SOURCE_QUIDAX_API,
        "Error getting all sub accounts: " . $th->getMessage(),
        [
          'request_url' => $url
        ]
      );
      return [
        'status' => 'error',
        'message' => $th->getMessage()
      ];
    }
  }
}
