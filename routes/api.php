<?php

use FacebookAds\Api;
use FacebookAds\Object\Ad;
use Illuminate\Http\Request;
use FacebookAds\Object\AdSet;
use FacebookAds\Object\AdImage;
use FacebookAds\Object\Campaign;
use FacebookAds\Object\AdAccount;

use FacebookAds\Object\AdPreview;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\AdCreative;
use Illuminate\Support\Facades\Route;
use FacebookAds\Object\Fields\AdFields;
use App\Http\Controllers\UserController;
use FacebookAds\Object\AdCreativeLinkData;
use FacebookAds\Object\Fields\AdImageFields;
use App\Http\Controllers\PetfinderController;
use App\Http\Controllers\MobileAuthController;
use FacebookAds\Object\Fields\AdCreativeFields;
use FacebookAds\Object\AdCreativeObjectStorySpec;
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

Route::get('petfinderToken', [PetfinderController::class, 'requestToken']);

Route::middleware('auth:sanctum')->post('mobile/logout', [MobileAuthController::class, 'destroyToken']);

Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'getUser']);


// Route::get('/facebook-test', function () {
//     $app_id = config('services.facebook.appId');
//     $app_secret = config('services.facebook.appSecret');
//     $access_token = config('services.facebook.accessToken');
//     $account_id = config('services.facebook.accountId');
//     $page_id = config('services.facebook.pageId');

//     $api = Api::init($app_id, $app_secret, $access_token);
//     $api->setLogger(new CurlLogger());

//     $account = new AdAccount($account_id);
//     $fields = array(
//         'impressions',
//         'ad_name'
//     );
//     $params = array(
//         'date_preset' => 'maximum',
//         // 'time_range' => array('since' => '2018-12-01', 'until' => '2021-08-01'),
//         'level' => 'ad',
//     );
//     $cursor = $account->getInsights($fields, $params)->getResponse()->getContent();

//     return $cursor;


//     echo json_encode((new AdAccount($account_id))->getInsights(
//         $fields,
//         $params
//     )->getResponse()->getContent(), JSON_PRETTY_PRINT);
// });

Route::get('/facebook-test', function () {
    $app_id = config('services.facebook.appId');
    $app_secret = config('services.facebook.appSecret');
    $access_token = config('services.facebook.accessToken');
    $ad_account_id = config('services.facebook.adAccountId');
    $page_id = config('services.facebook.pageId');

    $api = Api::init($app_id, $app_secret, $access_token);
    $api->setLogger(new CurlLogger());

    $fields = array();
    $params = array(
        'name' => 'My Campaign',
        'buying_type' => 'AUCTION',
        'objective' => 'LINK_CLICKS',
        'status' => 'PAUSED',
        'special_ad_categories' => ['NONE']
    );
    $campaign = (new AdAccount($ad_account_id))->createCampaign(
        $fields,
        $params
    );
    $campaign_id = $campaign->id;

    $fields = array();
    $params = array(
        'name' => 'My AdSet',
        'optimization_goal' => 'LINK_CLICKS',
        'billing_event' => 'IMPRESSIONS',
        'bid_amount' => '20',
        'daily_budget' => '1000',
        'campaign_id' => $campaign_id,
        'targeting' => array('geo_locations' => array('countries' => array('US'))),
        'status' => 'PAUSED',
    );
    $ad_set = (new AdAccount($ad_account_id))->createAdSet(
        $fields,
        $params
    );

    $ad_set_id = $ad_set->id;

    $fields = [];
    $params = [
        'filename' => 'https://dl5zpyw5k3jeb.cloudfront.net/photos/pets/52540612/1/scooter.jpg'
    ];

    $image = (new AdAccount($ad_account_id))->createAdImage(
        $fields,
        $params
    );

    $imageHash = $image->images['scooter.jpg']['hash'];

    $link_data = new AdCreativeLinkData();
    $link_data->setData([
        'image_hash' => $imageHash,
        'link' => 'https://www.petfinder.com/cat/jasmine-petco-51820860/fl/oakland-park/animal-aid-inc-fl179/',
        'message' => 'Adopt this beutiful black cat like a black cat!'
    ]);

    $object_story_spec = new AdCreativeObjectStorySpec();
    $object_story_spec->setData([
        'page_id' => $page_id,
        'link_data' => $link_data
    ]);

    $fields = [];
    $params = [
        'title' => 'My Test Creative',
        'body' => 'My Test Ad Creative Body',
        'object_story_spec' => $object_story_spec
    ];

    $creative = (new AdAccount($ad_account_id))->createAdCreative(
        $fields,
        $params
    );

    $creativeId = $creative->id;

    $fields = [];
    $params = [
        'ad_format' => 'DESKTOP_FEED_STANDARD',
    ];

    $previews = (new AdCreative($creativeId))->getPreviews(
        $fields,
        $params
    )->getResponse()->getContent();

    // $fields = [];
    // $params = [
    //     'name' => 'My Ad',
    //     'adset_id' => $ad_set_id,
    //     'creative' => ['creative_id' => $creativeId],
    //     'status' => 'PAUSED'
    // ];

    // $ad = (new AdAccount($ad_account_id))->createAd(
    //     $fields,
    //     $params
    // );

    // $ad_id = $ad->id;

    return response()->json([
        'Ad Preview' => $previews
    ]);



    // $fields = array();
    // $params = array(
    //     'name' => 'My Creative',
    //     'object_id' => $page_id,
    //     'title' => 'Test',
    //     'body' => 'Test',
    //     'image_url' => 'https://i.natgeofe.com/n/f0dccaca-174b-48a5-b944-9bcddf913645/01-cat-questions-nationalgeographic_1228126.jpg',
    // );

    // $creative = (new AdAccount($ad_account_id))->createAdCreative(
    //     $fields,
    //     $params
    // );
    // $creative_id = $creative->id;
    // $newCreativeId = 'creative_id: ' . $creative_id;

    // $fields = array();
    // $params = array(
    //     'name' => 'My Ad',
    //     'adset_id' => $ad_set_id,
    //     'creative' => array('creative_id' => $creative_id),
    //     'status' => 'PAUSED',
    // );
    // $ad = (new AdAccount($ad_account_id))->createAd(
    //     $fields,
    //     $params
    // );
    // $ad_id = $ad->id;
    // $newAdId = 'ad_id: ' . $ad_id;

    // $fields = array();
    // $params = array(
    //     'ad_format' => 'DESKTOP_FEED_STANDARD',
    // );
    // $result = (new Ad($ad_id))->getPreviews(
    //     $fields,
    //     $params
    // )->getResponse()->getContent();

    // return response()->json([
    //     'Campaign id' => $newCampaignId,
    //     'Ad Set id' => $newAdSetId,
    //     'Creative id' => $newCreativeId,
    //     'New Ad id' => $newAdId,
    //     'Result' => $result
    // ]);
});
