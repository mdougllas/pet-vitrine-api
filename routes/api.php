<?php

use App\Http\Controllers\AdController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\PetfinderController;
use App\Http\Controllers\RecaptchaController;
use App\Http\Controllers\MobileAuthController;
use App\Http\Controllers\FacebookAdsController;
use App\Http\Controllers\StripeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
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

// Native Mobile App Routes
Route::post('mobile/register', [RegisteredUserController::class, 'store']);
Route::post('mobile/login', [MobileAuthController::class, 'requestToken']);

// Facebook Routes
Route::post('ad-preview', [FacebookAdsController::class, 'adPreview']);
Route::post('list-ad-sets', [FacebookAdsController::class, 'listAdSets']);
Route::post('list-ads', [FacebookAdsController::class, 'listAds']);

// Miscellaneous Routes
Route::get('petfinder-token', [PetfinderController::class, 'requestToken']);
Route::post('recaptcha-token', [RecaptchaController::class, 'checkToken']);

// Auth Protected Routes
Route::middleware(['auth:sanctum'])->group(function () {
    // User
    Route::post('mobile/logout', [MobileAuthController::class, 'destroyToken']);
    Route::get('user', [UserController::class, 'getUser']);

    // Paypal
    Route::post('paypal-create-order', [PaypalController::class, 'createOrder']);
    Route::post('paypal-capture-payment', [PaypalController::class, 'capturePayment']);

    // Stripe
    Route::post('stripe-create-intent', [StripeController::class, 'createPaymentIntent']);
    Route::post('stripe-request-intent', [StripeController::class, 'requestPaymentIntent']);

    // Facebook
    Route::post('create-ad', [FacebookAdsController::class, 'createAd']);

    // Miscellaneous
    Route::resource('ad', AdController::class)->except(['create', 'edit']);
});
