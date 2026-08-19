<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Symfony\Component\HttpFoundation\Response;

class isLoginVerified
{
    use LivewireAlert;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() &&  !Session::has('login_verified') && config('app.env') != 'local') {

            // Redirect to OTP verification page
            return $this->flash('error', 'Please verify your login first', [], route('verify-login'));
        }

        return $next($request);
    }
}
