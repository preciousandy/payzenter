<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'CSRF token mismatch. Session expired.'], 419);
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
                return redirect()->route('admin.login')->with('error', 'Your session has expired. Please log in again.');
            }

            return redirect()->route('login')->with('error', 'Your session has expired. Please log in again.');
        });
    }
}
