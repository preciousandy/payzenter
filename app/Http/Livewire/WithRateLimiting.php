<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

trait WithRateLimiting
{
  /**
   * Rate limit a Livewire method
   *
   * @param string $method Method name to rate limit
   * @param int $maxAttempts Maximum number of attempts allowed
   * @param int $decaySeconds Number of seconds until the rate limit resets
   * @throws ValidationException
   */
  public function rateLimit($method, $maxAttempts = 1, $decaySeconds = 60)
  {
    $key = $this->getRateLimitKey($method);

    if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
      $seconds = RateLimiter::availableIn($key);

      throw ValidationException::withMessages([
        $method => "Please wait {$seconds} seconds before trying again.",
      ]);
    }

    RateLimiter::hit($key, $decaySeconds);
  }

  /**
   * Generate a unique key for rate limiting
   *
   * @param string $method
   * @return string
   */
  protected function getRateLimitKey($method)
  {
    $user = auth()->user();
    return sha1($user ? $user->id : request()->ip()) . ':' . $method;
  }
}
