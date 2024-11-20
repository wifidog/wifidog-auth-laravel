<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
        if (session('gw_address') && session('gw_port')) {
            $token = $request->user()->createToken('wifidog', ['internet:access'])->plainTextToken;
            session(['token' => $token]);
            $uri = 'http://' . session('gw_address') . ':' . session('gw_port') . '/wifidog/auth?token=' . $token;
            return redirect($uri);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $uri = '/';
        if (session('gw_address') && session('gw_port') && session('token')) {
            $uri = 'http://' . session('gw_address') . ':' . session('gw_port') . '/wifidog/auth?logout=1&token='
                . session('token');
        }
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect($uri);
    }
}
