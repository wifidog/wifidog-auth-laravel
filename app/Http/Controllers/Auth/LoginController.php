<?php

namespace App\Http\Controllers\Auth;

use Random;
use sinkcup\LaravelUiSocialite\Socialite\Controllers\SocialiteLoginController;

class LoginController extends SocialiteLoginController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $data = [];
        $request = request();
        if ($request->has('gw_address') && $request->has('gw_port')) {
            $data = [
                'gw_address' => $request->input('gw_address'),
                'gw_port' => $request->input('gw_port'),
            ];
        }
        if ($request->has('url')) {
            $data['url'] = $request->input('url');
        }
        if (!empty($data)) {
            session($data);
        }
        $this->middleware('guest')->except('logout', 'redirectToProvider', 'handleProviderCallback');
    }

    /**
     * Where to redirect users after login.
     *
     * @return string
     */
    public function redirectTo()
    {
        $uri = '/home';
        $user = auth()->user();
        $token = bin2hex(random_bytes(80));
        $user->forceFill([
            'api_token' => $token,
        ])->save();
        if (session('gw_address') && session('gw_port')) {
            $uri = 'http://' . session('gw_address') . ':' . session('gw_port') . '/wifidog/auth?token=' . $token;
        }
        return $uri;
    }
}
