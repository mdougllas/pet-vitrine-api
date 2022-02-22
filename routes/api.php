<?php

use App\Http\Controllers\FacebookAdsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PetfinderController;
use App\Http\Controllers\RecaptchaController;
use App\Http\Controllers\MobileAuthController;
use App\Services\FacebookAds\FacebookAdsAdSet;
use App\Services\FacebookAds\FacebookAdsCampaign;
use Illuminate\Database\Eloquent\Collection;
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
    Route::get('/user', [UserController::class, 'getUser']);

    //Testing route - will be removed
    Route::get('/auth-tests', function (Request $request) {
        dd(config('cors.allowed_origins'));
    });
});

Route::post('/tests', function (Request $request) {
    return response()->json(['ok']);
});

Route::post('pet-ads', [FacebookAdsController::class, 'createAd']);

// Route::get('pet-ads', function (FacebookAdsCampaign $campaign, FacebookAdsAdSet $adSet, Request $request) {

//     // $created = $campaign->createCampaign('PV-1');
//     // $lastCampaign = $campaign->getLastCampaign();
//     // $countAdSets = $adSet->countAdSets($lastCampaign);

//     // $lastCampaign->deleteSelf();

//     $api_root_url = config('services.facebook.graph_api_root_url');
//     $access_token = config('services.facebook.access_token');
//     $ad_account_id = config('services.facebook.ad_account_id');
//     $app_secret = config('services.facebook.app_secret');
//     $business_id = config('services.facebook.business_id');
//     $app_id = config('services.facebook.app_secret');
//     $page_id = config('services.facebook.pageId');

//     // Api::init($app_id, $app_secret, $access_token);

//     // $fields = [
//     //     'name', 'id', 'creted_time'
//     // ];

//     // $adAccount = new AdAccount($ad_account_id);

//     // $fields = array();
//     // $params = array(
//     //     'name' => 'A new world is beginning',
//     //     'buying_type' => 'AUCTION',
//     //     'objective' => 'LINK_CLICKS',
//     //     'status' => 'PAUSED',
//     //     'special_ad_categories' => ['NONE']
//     // );

//     // $cursor = $adAccount->getCampaigns(['id', 'name']);

//     // $test = new Collection;

//     // foreach ($cursor as $campaign) {
//     //     $test['name'] = $campaign->name;
//     // }

//     // return $test;

//     // // $campaign = $adAccount->createCampaign($fields, $params);

//     $fields = collect([
//         'name', 'id', 'created_time'
//     ])->toJson();

//     $response = Http::get("$api_root_url/$ad_account_id/campaigns?fields=$fields&access_token=$access_token");

//     return response()->json($response->object(), 200);
// });
