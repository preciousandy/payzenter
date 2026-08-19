<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Symfony\Component\HttpFoundation\Response;

class IfOwingAjoContribution
{
    use LivewireAlert;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if ($request->user()->is_owing_ajo_contribution) {
            return $this->flash('error', 'You are owing an Ajo Contribution, please pay up to use this feature', [], route('home'));
        }
        return $next($request);
    }
}
