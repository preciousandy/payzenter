<?php

namespace App\Services;

use App\Models\OTP;
use App\Mail\OTPMail;
use App\Models\User;
use App\Notifications\SendOtp;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class OTPService
{
  public function generateOTP($email)
  {
    // Generate 6 digit OTP
    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    // Delete any existing OTP for this email
    OTP::where('email', $email)->delete();

    try {
      // Create new OTP record
      OTP::create([
        'email' => $email,
        'otp' => $otp,
        'expires_at' => Carbon::now()->addMinutes(User::OTP_VERIFICATION_TIMEOUT)
      ]);

      $user = User::where('email', $email)->first();

      if (!$user) {
        return [
          'status' => 'error',
          'message' => 'User not found'
        ];
      }

      // Send OTP email
      $user->notify(new SendOtp($otp));

      return [
        'status' => 'success',
        'message' => 'OTP sent successfully'
      ];
    } catch (\Throwable $th) {
      return [
        'status' => 'error',
        'message' => 'Failed to send OTP'
      ];
    }
  }

  public function verifyOTP($email, $otp)
  {
    $otpRecord = OTP::where('email', $email)
      ->where('otp', $otp)
      ->where('expires_at', '>', Carbon::now())
      ->first();

    if (!$otpRecord) {
      return [
        'status' => 'error',
        'message' => 'Invalid OTP'
      ];
    }

    // Delete the OTP after successful verification
    $otpRecord->delete();

    return [
      'status' => 'success',
      'message' => 'OTP verified successfully'
    ];
  }
}
