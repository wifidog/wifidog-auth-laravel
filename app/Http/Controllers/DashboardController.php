<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data = [];
        if (session('gw_address') && session('gw_port') && session('token')) {
            $data['wifidog_uri'] = "http://" . session('gw_address')
                 . ":" . session('gw_port')
                  . "/wifidog/auth?token=" . session('token');
        }
        return view('dashboard', $data);
    }
}
