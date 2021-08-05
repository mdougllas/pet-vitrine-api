<?php

use FacebookAds\Api;
use Illuminate\Http\Request;
use FacebookAds\Object\AdAccount;
use FacebookAds\Logger\CurlLogger;
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

Route::get('/facebook-test', function () {
    $app_id = config('services.facebook.appId');
    $app_secret = config('services.facebook.appSecret');
    $access_token = config('services.facebook.accessToken');
    $account_id = config('services.facebook.accountId');

    $api = Api::init($app_id, $app_secret, $access_token);
    $api->setLogger(new CurlLogger());

    $account = new AdAccount($account_id);
    $fields = array(
        'impressions',
        'ad_name'
    );
    $params = array(
        'date_preset' => 'maximum',
        // 'time_range' => array('since' => '2018-12-01', 'until' => '2021-08-01'),
        'level' => 'ad',
    );
    $cursor = $account->getInsights($fields, $params)->getResponse()->getContent();

    return $cursor;


    echo json_encode((new AdAccount($account_id))->getInsights(
        $fields,
        $params
    )->getResponse()->getContent(), JSON_PRETTY_PRINT);
});
