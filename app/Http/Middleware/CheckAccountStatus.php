<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountStatus
{
    use LivewireAlert;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Check if suspended user's suspension has expired and auto-reactivate
            if ($user->account_status === User::ACCOUNT_STATUS_SUSPENDED) {
                if ($user->checkSuspensionExpiry()) {
                    // Account was reactivated, allow the request to continue
                    return $next($request);
                } else {
                    // Still suspended
                    auth()->logout();
                    return $this->flash('error', 'Your account is currently suspended', [], route('welcome'));
                }
            }

            // Check if account is blocked
            if ($user->account_status === User::ACCOUNT_STATUS_BLOCKED) {
                auth()->logout();
                return $this->flash('error', 'Your account has been blocked', [], route('welcome'));
            }
        }

        return $next($request);
    }
}
