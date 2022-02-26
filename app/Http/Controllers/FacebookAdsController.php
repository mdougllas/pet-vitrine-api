<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FacebookAds\FacebookAdsAdSet;
use App\Services\FacebookAds\FacebookAdsCampaign;

class FacebookAdsController extends Controller
{
    public function createAd(Request $request, FacebookAdsCampaign $campaign, FacebookAdsAdSet $adSet)
    {
        $validData = $request->validate([
            'petName' => 'required|string',
            'zipCode' => 'required|digits:5',
            'budget' => 'required|numeric|min:5'
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
}
