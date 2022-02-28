<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FacebookAds\FacebookAdsAd;
use App\Services\FacebookAds\FacebookAdsAdCreative;
use App\Services\FacebookAds\FacebookAdsAdSet;
use App\Services\FacebookAds\FacebookAdsCampaign;

class FacebookAdsController extends Controller
{
    /**
     * Creates a preview for the ad.
     *
     * @param  object Illuminate\Http\Request
     * @param  object App\Services\FacebookAds\FacebookAdsPreview

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
     * Todo - finish this adblock.
     *
     * @param  object Illuminate\Http\Request
     * @param  object App\Services\FacebookAds\FacebookAdsPreview

     * @return object Illuminate\Http\response
     */
    public function createAd(
        Request $request,
        FacebookAdsCampaign $campaign,
        FacebookAdsAdSet $adSet,
        FacebookAdsAdCreative $creative,
        FacebookAdsAd $ad
    ) {
        $validData = $request->validate([
            'petName' => 'required|string',
            'zipCode' => 'required|digits:5',
            'budget' => 'required|numeric|min:5',
            'url' => 'required',
            'link' => 'required'
        ]);

        $petName = $validData['petName'];
        $zipCode = $validData['zipCode'];
        $budget = (int) $validData['budget'];
        $url = $validData['url'];
        $link = $validData['link'];

        $lastCampaignId = $campaign->getLastCampaign()->id;
        $adSet = $adSet->createAdSet($petName, $lastCampaignId, $zipCode, $budget);
        $adCreative = $creative->createAdCreative($url, $link, $petName);
        $ad = $ad->createAd($petName, $adSet->id, $adCreative->id);

        return response()->json([
            'data' => [
                'Ad' => [
                    'name' => $ad->name,
                    'id' => $ad->id,
                ],
                'Targeting' => [$adSet->targeting]
            ]
        ]);
    }

    /**
     * Lists all Ad Sets.
     *
     * @param  object App\Services\FacebookAds\FacebookAdsAdSet

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
     * Lists all Ads.
     *
     * @param  object App\Services\FacebookAds\FacebookAdsAdSet

     * @return object Illuminate\Http\response
     */
    public function listAds(FacebookAdsAd $ad)
    {
        $ads = $ad->listAds();

        return response()->json([
            'ads' => $ads
        ]);
    }
}
