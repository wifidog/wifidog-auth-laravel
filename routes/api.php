<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GatewayController;
use Illuminate\Support\Facades\Route;

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

Route::get('/auth', [AuthController::class, 'index'])->middleware('auth:sanctum');
Route::post('/ping', [GatewayController::class, 'ping']);
Route::get('/ping', [GatewayController::class, 'ping']); // wifidog-gateway is wrong, it should use post not get.
