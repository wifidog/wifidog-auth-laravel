<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('user', 'API\UserController@show');
    Route::apiResource('users', 'API\UserController');
});

Route::get('/auth', 'API\AuthController@index')->middleware('auth.wifidog');
Route::post('/ping', 'API\GatewayController@ping');
Route::get('/ping', 'API\GatewayController@ping'); // wifidog-gateway is wrong, it should use post not get.
