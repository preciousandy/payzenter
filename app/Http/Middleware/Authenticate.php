<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        $referer = (string) $request->headers->get('referer', '');
        $refererPath = parse_url($referer, PHP_URL_PATH) ?? '';

        if (
            $request->is('powerhouse*') ||
            $request->is('admin*') ||
            $request->routeIs('admin.*') ||
            str_contains($refererPath, '/powerhouse') ||
            str_contains($refererPath, '/admin')
        ) {
            return route('admin.login');
        }

        return route('login');
    }
}
