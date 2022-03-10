<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\PetfinderController;
use App\Http\Controllers\RecaptchaController;
use App\Http\Controllers\MobileAuthController;
use App\Http\Controllers\FacebookAdsController;
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
Route::get('petfinder-token', [PetfinderController::class, 'requestToken']);
Route::post('recaptcha-token', [RecaptchaController::class, 'checkToken']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('mobile/logout', [MobileAuthController::class, 'destroyToken']);
    Route::get('user', [UserController::class, 'getUser']);
    Route::post('paypal-create-order', [PaypalController::class, 'createOrder']);
    Route::post('paypal-capture-payment', [PaypalController::class, 'capturePayment']);

    //Testing route - will be removed
    Route::get('/auth-tests', function (Request $request) {
        dd(config('cors.allowed_origins'));
    });
});

Route::post('/tests', function (Request $request) {
    return response()->json(['ok']);
});

Route::post('ad-preview', [FacebookAdsController::class, 'adPreview']);
Route::post('list-ad-sets', [FacebookAdsController::class, 'listAdSets']);
Route::post('list-ads', [FacebookAdsController::class, 'listAds']);
Route::post('create-ad', [FacebookAdsController::class, 'createAd']);
