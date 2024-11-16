<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();
        $data = [];
        if (session('gw_address') && session('gw_port') && session('token')) {
            $data['wifidog_uri'] = "http://" . session('gw_address')
                 . ":" . session('gw_port')
                  . "/wifidog/auth?token=" . session('token');
        }
        return view('dashboard', $data);
    }
}
