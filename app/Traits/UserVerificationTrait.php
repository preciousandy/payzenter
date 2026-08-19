<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait UserVerificationTrait
{

  public function sendOTP($channels = ["sms", "email", "whatsapp"])
  {
    try {
      $res = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . env("FLW_LIVE_SECRET_KEY")
      ])->post(env("FLW_API_URL") . "/otps/", [
        "length" => 6,
        "customer" => [
          "email" => $this->email,
          "phone" => $this->phone,
          "name" => $this->firstname . " " . $this->lastname
        ],
        "sender" => config("app.name"),
        "send" => true,
        "medium" => $channels,
        "expiry" => 10 // 5 minutes
      ]);

      $data = $res->json();

      if ($res->successful() && $data['status'] == "success") {
        session()->put('otp_reference', $data['data'][0]['reference']);
        return [
          'status' => 'success',
          'message' => $data['data'][0]['reference']
        ];
      } else {
        throw new \Exception($data['message']);
      }
    } catch (\Throwable $th) {
      return [
        'status' => 'error',
        'message' => "Failed to send OTP " . $th->getMessage()
      ];
    }
  }

  public function verifyOTP($otp)
  {
    try {
      $res = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . env("FLW_LIVE_SECRET_KEY")
      ])->post(env("FLW_API_URL") . "/otps/" . session('otp_reference') . "/validate", [
        "otp" => $otp,
      ]);

      $data = $res->json();

      if ($res->successful() && $data['status'] == "success") {
        return [
          'status' => 'success',
          'message' => $data['data']
        ];
      } else {
        throw new \Exception($data['message']);
      }
    } catch (\Throwable $th) {
      return [
        'status' => 'error',
        'message' => "Failed to verify OTP " . $th->getMessage()
      ];
    }
  }

  public function submitBVN($first_name, $last_name, $bvn)
  {
    try {
      $res = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . env("FLW_SECRET_KEY")
      ])->post(env("FLW_API_URL") . "/bvn/verifications", [
        "bvn" => $bvn,
        "firstname" => $first_name,
        "lastname" => $last_name,
        "redirect_url" => route('bvn.validate')
      ]);

      $data = $res->json();


      if ($res->successful() && $data['status'] == "success") {
        return $data['data'];
      } else {
        throw new \Exception($data['message']);
      }
    } catch (\Throwable $th) {
      throw new \Exception("Failed to verify BVN " . $th->getMessage());
    }
  }
}
