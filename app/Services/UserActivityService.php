<?php

namespace App\Services;

use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Jenssegers\Agent\Agent;

class UserActivityService
{
  protected Agent $agent;

  public function __construct()
  {
    $this->agent = new Agent();
  }

  public function logActivity(string $action, ?array $additionalData = null): void
  {
    $request = request();
    $user = auth()->user();

    if (!$user) {
      return;
    }

    // Get location data using IP-API (free tier)
    $locationData = $this->getLocationData($request->ip());

    $activity = new UserActivity([
      'user_id' => $user->id,
      'action' => $action,
      'ip_address' => $request->ip(),
      'user_agent' => $request->userAgent(),
      'device_type' => $this->getDeviceType(),
      'device_name' => $this->agent->device(),
      'browser' => $this->agent->browser(),
      'platform' => $this->agent->platform(),
      'location_country' => $locationData['country'] ?? null,
      'location_city' => $locationData['city'] ?? null,
      'location_region' => $locationData['region'] ?? null,
      'latitude' => $locationData['lat'] ?? null,
      'longitude' => $locationData['lon'] ?? null,
      'url' => $request->fullUrl(),
      'method' => $request->method(),
      'input_data' => $additionalData ?? $this->sanitizeInput($request->input()),
    ]);

    $activity->save();
  }

  protected function getDeviceType(): string
  {
    if ($this->agent->isPhone()) {
      return 'phone';
    }
    if ($this->agent->isTablet()) {
      return 'tablet';
    }
    if ($this->agent->isRobot()) {
      return 'robot';
    }
    return 'desktop';
  }

  protected function getLocationData(string $ip): array
  {
    if (in_array($ip, ['127.0.0.1', 'localhost', '::1'])) {
      return [];
    }

    try {
      $response = Http::get("http://ip-api.com/json/{$ip}");
      if ($response->successful()) {
        return $response->json();
      }
    } catch (\Exception $e) {
      // Log error if needed
    }

    return [];
  }

  protected function sanitizeInput(array $input): array
  {
    // Remove sensitive data before logging
    $sensitiveFields = ['password', 'password_confirmation', 'token', 'credit_card'];

    return collect($input)->except($sensitiveFields)->toArray();
  }
}
