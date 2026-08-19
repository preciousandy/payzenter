<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class OTPVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('otp_verified') && !session('login_verified')) {
            // Store the intended URL in the session
            Session::put('url.intended', $request->url());


            // Redirect to OTP verification page
            return redirect()->route('otp.verify');
        }

        return $next($request);
    }
}
