<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsBvnVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()->is_bvn_verified && !$request->user()->hasRole('admin')) {
            return redirect()->route('bvn.verify')->with('error', 'Please verify your BVN to continue');
        }

        return $next($request);
    }
}
