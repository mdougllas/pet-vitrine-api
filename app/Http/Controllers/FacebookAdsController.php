<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Services\FacebookAds\FacebookAdsAd;
use App\Services\FacebookAds\FacebookAdsAdSet;
use App\Services\FacebookAds\FacebookAdsCampaign;
use App\Services\FacebookAds\FacebookAdsAdCreative;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FacebookAdsController extends Controller
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
        Request $request,
        FacebookAdsCampaign $campaign,
        FacebookAdsAdSet $adSet,
        FacebookAdsAdCreative $creative,
        FacebookAdsAd $ad
    ) {
        $validData = $request->validate([
            'petId' => 'required|numeric',
            'petName' => 'required|string',
            'zipCode' => 'required|digits:5',
            'budget' => 'required|numeric|min:5',
            'url' => 'required',
            'link' => 'required'
        ]);

        $userId = $request->user()->id;
        $petId = $validData['petId'];
        $petName = $validData['petName'];
        $zipCode = $validData['zipCode'];
        $budget = (int) $validData['budget'];
        $url = $validData['url'];
        $link = $validData['link'];

        $lastCampaignId = $campaign->getLastCampaign()->id;
        $adSet = $adSet->createAdSet($petName, $lastCampaignId, $zipCode, $budget);
        $adCreative = $creative->createAdCreative($url, $link, $petName);
        $ad = $ad->createAd($petName, $adSet->id, $adCreative->id);

        $this->store(
            $petId,
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

    private function store($id, $campaign, $adSet, $adCreative, $adId, $budget, $ad, $userId)
    {
        $ad->ad_id = $adId;
        $ad->ad_set_id = $adSet->id;
        $ad->budget = $budget;
        $ad->campaign_id = $campaign;
        $ad->creative_id = $adCreative->id;
        $ad->end_time = new Carbon($adSet->end_time);
        $ad->pet_id = $id;
        $ad->start_time = new Carbon($adSet->start_time);
        $ad->uuid = Str::uuid();
        $ad->user_id = $userId;
        $ad->save();
    }
}
