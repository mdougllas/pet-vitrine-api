<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FacebookAds\FacebookAdsAd;
use App\Services\FacebookAds\FacebookAdsAdSet;
use App\Services\FacebookAds\FacebookAdsPreview;
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
    public function adPreview(Request $request, FacebookAdsPreview $ad)
    {
        $validData = $request->validate([
            'name' => 'required|string',
            'url' => 'required',
            'link' => 'required'
        ]);

        $name = $validData['name'];
        $url = $validData['url'];
        $link = $validData['link'];

        $preview = $ad->getPreview($url, $link, $name);

        return $preview;
    }

    /**
     * Todo - finish this adblock.
     *
     * @param  object Illuminate\Http\Request
     * @param  object App\Services\FacebookAds\FacebookAdsPreview

     * @return object Illuminate\Http\response
     */
    public function createAd(Request $request, FacebookAdsCampaign $campaign, FacebookAdsAdSet $adSet)
    {
        $validData = $request->validate([
            'petName' => 'required|string',
            'zipCode' => 'required|digits:5',
            'budget' => 'required|numeric|min:5',
            'url' => 'required',
            'link' => 'required'
        ]);

        $budget = (int) $validData['budget'];
        $petName = $validData['petName'];
        $zipCode = $validData['zipCode'];
        $lastCampaignId = $campaign->getLastCampaign()->id;

        $created = $adSet->createAdSet($petName, $lastCampaignId, $zipCode, $budget);

        return response()->json([
            'data' => [
                'name' => $created->name,
                'id' => $created->id,
            ]
        ]);
    }

    /**
     * Lists all Ad Sets.
     *
     * @param  object Illuminate\Http\Request
     * @param  object App\Services\FacebookAds\FacebookAdsAdSet

     * @return object Illuminate\Http\response
     */
    public function listAdSets(Request $request, FacebookAdsAdSet $adSet)
    {
        $adSets = $adSet->listAdSets();

        return response()->json([
            'adSets' => $adSets
        ]);
    }

    /**
     * Lists all Ad Sets.
     *
     * @param  object Illuminate\Http\Request
     * @param  object App\Services\FacebookAds\FacebookAdsAdSet

     * @return object Illuminate\Http\response
     */
    public function listAds(Request $request, FacebookAdsAd $ad)
    {
        $ads = $ad->listAds();

        return response()->json([
            'ads' => $ads
        ]);
    }
}
