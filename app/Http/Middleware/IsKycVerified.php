<?php

namespace App\Http\Middleware;

use App\Models\Kyc;
use Closure;
use Illuminate\Http\Request;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Symfony\Component\HttpFoundation\Response;

class IsKycVerified
{
    use LivewireAlert;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if user don't have kyc at all
        if ($request->user()->kyc_status === Kyc::STATUS_UNVERIFIED && !$request->user()->hasRole('admin')) {
            // return redirect()->route('kyc.verify')->with('error', 'Please complete your KYC verification');
            return $this->flash('error', 'Please complete your KYC verification', [], route('onboarding'));
        }
        if ($request->user()->kyc_status === Kyc::STATUS_REJECTED && !$request->user()->hasRole('admin')) {
            // return redirect()->route('kyc.verify')->with('error', 'Please complete your KYC verification');
            return $this->flash('error', 'Your previous KYC was rejected, please submit another one', [], route('onboarding'));
        }

        if ($request->user()->kyc_status == Kyc::STATUS_PENDING && !$request->user()->hasRole('admin')) {
            return $this->flash('warning', 'Your KYC verification is still pending', [], route('home'));
        }



        return $next($request);
    }
}
