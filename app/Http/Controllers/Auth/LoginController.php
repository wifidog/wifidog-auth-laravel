<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use JWTAuth;
use Auth;
use Socialite;
use App\SocialUser;
use App\User;

class LoginController extends Controller
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

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $data = [];
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
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function redirectTo()
    {
        $uri = '/home';
        $user = Auth::user();
        $token = JWTAuth::fromUser($user);
        if (session('gw_address') && session('gw_port')) {
            $uri = 'http://' . session('gw_address') . ':' . session('gw_port') . '/wifidog/auth?token=' . $token;
        }
        return $uri;
    }

    public function showLoginForm()
    {
        return view('auth.login', ['social_login_providers' => config('auth.social_login.providers')]);
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider($driver)
    {
        return Socialite::driver($driver)->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback($driver)
    {
        try {
            $remote_user = Socialite::driver($driver)->user();
        } catch (RequestException $e) {
            $context = [
                'method' => __METHOD__,
                'driver' => $driver,
            ];
            if ($e->hasResponse()) {
                $body = $e->getResponse()->getBody();
                $context = array_merge($context, json_decode($body, true));
            }
            Log::warning('Socialite Login failed', $context);
            return redirect()->to('/login');
        } catch (\Exception $e) {
            Log::warning('Socialite Login failed: ' . $e->getMessage(), [
                'method' => __METHOD__,
                'driver' => $driver,
            ]);
            return redirect()->to('/login');
        }

        $social_user = SocialUser::firstOrNew([
            'provider' => 'facebook',
            'provider_user_id' => $remote_user->getId(),
        ]);
        if (!empty($social_user->user)) {
            $user = $social_user->user;
        } else {
            $user = User::firstOrCreate([
                'email' => $driver . $remote_user->getId() . '@example.com', // faker for email unique in db
                'name' => $remote_user->getName(),
            ]);
            $social_user->user()->associate($user);
        }
        $social_user->access_token = $remote_user->token;
        $social_user->refresh_token = $remote_user->refreshToken; // not always provided
        $social_user->expires_in = $remote_user->expiresIn;
        $social_user->save();
        auth()->login($user);
        return redirect()->to($this->redirectTo());
    }
}
