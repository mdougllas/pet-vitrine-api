<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MobileAuthController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;

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

Route::post('mobile/register', [RegisteredUserController::class, 'store']);

Route::post('mobile/login', [MobileAuthController::class, 'requestToken']);

Route::middleware('auth:sanctum')->post('mobile/logout', [MobileAuthController::class, 'destroyToken']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
