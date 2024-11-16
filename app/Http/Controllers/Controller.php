<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function __construct()
    {
        $request = request();
        if (!empty($request->all())) {
            session($request->all());
        }
    }
}
