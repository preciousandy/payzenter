<?php

namespace App\Services;

use App\Models\OTP;
use App\Mail\OTPMail;
use App\Models\User;
use App\Notifications\SendOtp;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class GiftCardsService
{
  public function fetchReloadlyGiftcardsAccessToken()
  {
    try {
      $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
      ])
        ->post("https://auth.reloadly.com/oauth/token", [
          'client_id' => env("RELOADLY_CLIENT_ID"),
          'client_secret' => env("RELOADLY_CLIENT_SECRET"),
          "grant_type" => "client_credentials",
          "audience" => "https://giftcards.reloadly.com"
        ]);

      // Or get as JSON if the response is JSON
      $responseData = $response->json();

      // Check if request was successful
      if ($response->successful()) {
        // Handle success

        return [
          'status' => 'success',
          'data' => $responseData['access_token'],
        ];
      } else {
        // Handle error
        throw new \Exception($responseData['message']);
      }
    } catch (\Throwable $th) {
      // Handle exceptions
      return [
        'status' => 'error',
        'message' => $th->getMessage()
      ];
    }
  }
}
