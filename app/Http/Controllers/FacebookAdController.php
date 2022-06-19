<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exceptions\DuplicateEntryException;
use App\Services\FacebookAds\FacebookAdsAd;
use App\Services\FacebookAds\FacebookAdsAdSet;
use App\Services\FacebookAds\FacebookAdsCampaign;
use App\Services\FacebookAds\FacebookAdsAdCreative;
use App\Services\Payment\Paypal\PaypalOrder;
use App\Services\Payment\Stripe\StripeOrder;

class FacebookAdController extends Controller
{
    /**
     * Creates a preview for the Facebook Ad.
     *
     * @param  Illuminate\Http\Request $request
     * @param  App\Services\FacebookAds\FacebookAdsAd $ad

     * @return object Illuminate\Http\response
     */
    public function adPreview(Request $request, FacebookAdsAd $ad)
    {
        $validData = $request->validate([
            'name' => 'required|string',
            'url' => 'required',
            'link' => 'required'
        ]);

        $name = $validData['name'];
        $url = $validData['url'];
        $link = $validData['link'];

        $preview = $ad->getAdPreview($url, $link, $name);

        return $preview;
    }

    /**
     * Creates Facebook Ad
     *
     * @param  Illuminate\Http\Request $request
     * @param  App\Services\FacebookAds\FacebookAdsCampaign $campaign
     * @param  App\Services\FacebookAds\FacebookAdsAdSet $adSet
     * @param  App\Services\FacebookAds\FacebookAdsAdCreative $creative
     * @param  App\Services\FacebookAds\FacebookAdsAd $ad

     * @return object Illuminate\Http\response
     */
    public function createAd(
        Ad $storeAd,
        FacebookAdsCampaign $campaign,
        FacebookAdsAdSet $adSet,
        FacebookAdsAdCreative $creative,
        FacebookAdsAd $ad,
        Request $request,
        PaypalOrder $paypal,
        StripeOrder $stripe
    ) {
        $validData = $request->validate([
            'petId' => 'required|numeric',
            'paymentId' => 'required|string',
            'paymentProvider' => 'required|string',
            'petName' => 'required|string',
            'zipCode' => 'required|digits:5',
            'budget' => 'required|numeric|min:5',
            'url' => 'required',
            'link' => 'required'
        ]);

        $userId = $request->user()->id;
        $paymentId = $request['paymentId'];
        $paymentProvider = $request['paymentProvider'];
        $petId = $validData['petId'];
        $petName = $validData['petName'];
        $zipCode = $validData['zipCode'];
        $budget = (int) $validData['budget'];
        $url = $validData['url'];
        $link = $validData['link'];

        $this->verifyAdExists($paymentId);
        $this->validatePayment($paypal, $stripe, $paymentId, $paymentProvider, $budget);
        $lastCampaignId = $campaign->getLastCampaign()->id;
        $adSet = $adSet->createAdSet($petName, $lastCampaignId, $zipCode, $budget);
        $adCreative = $creative->createAdCreative($url, $link, $petName);
        $ad = $ad->createAd($petName, $adSet->id, $adCreative->id);
        dd('I am ahere');

        $this->store(
            $petId,
            $paymentId,
            $lastCampaignId,
            $adSet,
            $adCreative,
            $ad->id,
            $budget,
            $storeAd,
            $userId
        );

        return response()->json([
            'data' => [
                'ad' => [
                    'name' => $ad->name,
                    'id' => $ad->id,
                ],
                'targeting' => [$adSet->targeting]
            ]
        ]);
    }

    /**
     * Shows results for given ad set.
     *
     * @param  $id

     * @return object Illuminate\Http\response
     */
    public function adResults($id, FacebookAdsAdSet $adSet)
    {
        $results = $adSet->getAdSet($id);

        return response()->json([
            'data' => $results
        ]);
    }

    /**
     * Lists all Facebook Ad Sets.
     *
     * @param  App\Services\FacebookAds\FacebookAdsAdSet $adSet

     * @return object Illuminate\Http\response
     */
    public function listAdSets(FacebookAdsAdSet $adSet)
    {
        $adSets = $adSet->listAdSets();

        return response()->json([
            'adSets' => $adSets
        ]);
    }

    /**
     * Lists all Facebook Ads.
     *
     * @param  App\Services\FacebookAds\FacebookAdsAdSet $ad

     * @return object Illuminate\Http\response
     */
    public function listAds(FacebookAdsAd $ad)
    {
        $ads = $ad->listAds();

        return response()->json([
            'ads' => $ads
        ]);
    }

    /**
     * Helper function to verify payment id is valid.
     *
     * @param  String $id

     * @return App\Models\Ad;
     */
    private function validatePayment(PaypalOrder $paypal, StripeOrder $stripe, $id, $provider, $amount)
    {
        return $provider === 'paypal'
            ? $paypal->validatePayment($id, $amount)
            : $stripe->validatePayment($id, $amount);
    }

    /**
     * Helper function to verify ad exists.
     *
     * @param  String $id

     * @throws App\Exceptions\DuplicateEntryException
     * @return void;
     */
    private function verifyAdExists($id)
    {
        $exists = Ad::where('payment_id', $id)->get();

        if ($exists->count() !== 0) {
            throw new DuplicateEntryException('This ad already exists.', 409);
        }
    }

    /**
     * Helper function to store the ad.
     *
     * @param  App\Services\FacebookAds\FacebookAdsAd $ad
     * @param  Integer $adCreative
     * @param  Integer $adId
     * @param  Integer $adSet
     * @param  Integer $budget
     * @param  Integer $campaign
     * @param  Integer $id
     * @param  String $paymentId
     * @param  Integer $userId

     * @return void
     */
    private function store($id, $paymentId, $campaign, $adSet, $adCreative, $adId, $budget, $ad, $userId)
    {
        $ad->ad_id = $adId;
        $ad->ad_set_id = $adSet->id;
        $ad->budget = $budget;
        $ad->campaign_id = $campaign;
        $ad->creative_id = $adCreative->id;
        $ad->end_time = new Carbon($adSet->end_time);
        $ad->payment_id = $paymentId;
        $ad->pet_id = $id;
        $ad->start_time = new Carbon($adSet->start_time);
        $ad->user_id = $userId;
        $ad->uuid = Str::uuid();

        $ad->save();
    }
}
