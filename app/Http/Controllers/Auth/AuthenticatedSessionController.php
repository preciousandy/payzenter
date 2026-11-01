<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

use App\Utils\Roles;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $referer = $request->headers->get('referer');
        $refererPath = $referer ? parse_url($referer, PHP_URL_PATH) : '';
        $isAdminPath = str_contains($refererPath, '/powerhouse') || str_contains($refererPath, '/admin');

        $isAdmin = ($user && ($user->hasRole('admin') || $user->hasRole(Roles::ADMIN))) || $isAdminPath;

        Auth::guard('web')->logout();

        session()->put('login_verified', false);

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($isAdmin) {
            return redirect()->route('admin.login');
        }

        return redirect()->route('login');
    }
}
