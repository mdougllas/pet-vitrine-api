<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FacebookAds\FacebookAdsAdSet;
use App\Services\FacebookAds\FacebookAdsCampaign;

class FacebookAdsController extends Controller
{
    public function createAd(Request $request, FacebookAdsCampaign $campaign, FacebookAdsAdSet $adSet)
    {
        $test = $campaign->createCampaign('Another Campaign');

        return $test->name;
        $validData = $request->validate([
            'petName' => 'required|string',
            'zipCode' => 'required|digits:5',
            'budget' => 'required|numeric|min:5'
        ]);

        $cents = $this->convertToCents($validData['budget']);

        $lastCampaign = $campaign->getLastCampaign();

        $created = $adSet->createAdSet($validData['petName'], $lastCampaign->id, $validData['zipCode'], $cents);

        return response()->json([
            'data' => [
                'name' => $created->name,
                'id' => $created->id,
            ]
        ]);
    }

    private function convertToCents($amount)
    {
        return $amount * 100;
    }
}
