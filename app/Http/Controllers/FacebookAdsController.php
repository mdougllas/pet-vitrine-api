<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FacebookAds\FacebookAdsAdSet;
use App\Services\FacebookAds\FacebookAdsCampaign;

class FacebookAdsController extends Controller
{
    public function createAd(Request $request, FacebookAdsCampaign $campaign, FacebookAdsAdSet $adSet)
    {
        $lastCampaign = $campaign->getLastCampaign();

        $created = $adSet->createAdSet($request->petName, $lastCampaign->id, $request->zipCode, $request->budget);

        return $created->id;

        return response()->json([
            'data' => [
                'name' => $created->name,
                'id' => $created->id,
            ]
        ]);
    }
}
