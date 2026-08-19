<?php

namespace App\Services;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoginNotification;
use Illuminate\Support\Facades\Request;
use Stevebauman\Location\Facades\Location;
use Jenssegers\Agent\Agent;

class SendLoginNotificationService
{
  /**
   * Handle the event.
   */
  public function handle(): void
  {
    // Get the user
    $user = auth()->user();

    // Gather login information
    $data = [
      'time' => now()->format('Y-m-d H:i:s'),
      'ip' => Request::ip(),
      'device' => $this->getDeviceInfo(),
      'browser' => $this->getBrowserInfo(),
      'location' => $this->getLocationInfo(Request::ip()),
    ];

    // Send the email notification
    Mail::to($user->email)->queue(new LoginNotification($user, $data));
  }

  /**
   * Get device information
   */
  private function getDeviceInfo(): string
  {
    $agent = new Agent();
    $device = $agent->device();
    $platform = $agent->platform();

    return $device . ' (' . $platform . ' ' . $agent->version($platform) . ')';
  }

  /**
   * Get browser information
   */
  private function getBrowserInfo(): string
  {
    $agent = new Agent();
    $browser = $agent->browser();

    return $browser . ' ' . $agent->version($browser);
  }

  /**
   * Get location information from IP
   */
  private function getLocationInfo($ip): string
  {
    // Skip for localhost testing
    if ($ip == '127.0.0.1' || $ip == '::1') {
      return 'localhost';
    }

    // Get location data
    $location = Location::get($ip);

    if ($location) {
      return $location->cityName . ', ' . $location->regionName . ', ' . $location->countryName;
    }

    return 'Unknown location';
  }
}
