<?php

namespace App\Http\Controllers;

class PortalController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $uri = session('url') ? session('url') : config('wifidog.portal_redirect_uri');
        return redirect($uri);
    }
}
