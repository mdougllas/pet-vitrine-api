<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PetfinderController;
use App\Http\Controllers\RecaptchaController;
use App\Http\Controllers\FacebookAdController;
use App\Http\Controllers\MobileAuthController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PostCategoryController;
use App\Http\Controllers\PostSubCategoryController;
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
Route::post('ad-preview', [FacebookAdController::class, 'adPreview']);
// Route::post('list-ad-sets', [FacebookAdController::class, 'listAdSets']);
// Route::post('list-ads', [FacebookAdController::class, 'listAds']);
Route::post('check-city', [FacebookAdController::class, 'checkCityValid']);

// Pet Routes
Route::get('pets', [PetController::class, 'search']);
Route::get('featured-pets', [PetController::class, 'featured']);
Route::resource('pet', PetController::class)->only('show');

// Organization Routes
Route::get('organizations', [OrganizationController::class, 'search']);
Route::resource('organizations', OrganizationController::class)->only('show');

// Miscellaneous Routes
Route::get('petfinder-token', [PetfinderController::class, 'requestToken']);
Route::post('recaptcha-token', [RecaptchaController::class, 'checkToken']);
Route::post('send-contact-message', [ContactController::class, 'sendContactMessage']);

// Post Routes
Route::resource('post', PostController::class)->except(['create', 'edit', 'update', 'delete']);
Route::get('post-slugs', [PostController::class, 'slugs']);

// Category Routes
Route::get('post-category-related/{postCategory}', [PostCategoryController::class, 'relatedPosts']);
Route::get('search/post', [PostController::class, 'search']);

// Subcategory Routes
Route::resource('post-sub-category', PostSubCategoryController::class)->only('index');

// Auth Protected Routes
Route::middleware(['auth:sanctum'])->group(function () {
    // User
    Route::post('mobile/logout', [MobileAuthController::class, 'destroyToken']);
    Route::get('user', [UserController::class, 'getUser']);
    Route::resource('user', UserController::class)->only('update');
    Route::post('add-to-favorites', [UserController::class, 'addToFavorites'])->middleware('verified');
    Route::get('remove-from-favorites/{id}', [UserController::class, 'removeFromFavorites'])->middleware('verified');

    // Paypal
    Route::post('paypal-create-order', [PaypalController::class, 'createOrder']);
    Route::post('paypal-capture-payment', [PaypalController::class, 'capturePayment']);

    // Stripe
    Route::post('stripe-create-intent', [StripeController::class, 'createPaymentIntent']);
    Route::post('stripe-request-intent', [StripeController::class, 'requestPaymentIntent']);
    Route::post('send-email-stripe-receipt', [StripeController::class, 'sendEmailReceipt']);

    // Facebook
    Route::post('create-ad', [FacebookAdController::class, 'createAd']);
    Route::get('ad-results/{id}', [FacebookAdController::class, 'adResults']);

    // Post
    Route::resource('post', PostController::class)->except(['create', 'edit', 'show', 'index']);

    // Miscellaneous
    Route::resource('ad', AdController::class)->except(['create', 'edit']);
});
