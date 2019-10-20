<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class AuthenticateWithWifidog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::guard('api')->guest()) {
            $user_status = 0;
            $status = 401;
            return response()->txt('Auth: ' . $user_status, $status);
        }
        return $next($request);
    }
}
