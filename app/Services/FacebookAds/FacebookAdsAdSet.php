<?php

namespace App\Services\FacebookAds;

use Illuminate\Support\Carbon;
use App\Services\FacebookAds\FacebookAds;

class FacebookAdsAdSet
{
    /**
     * Creates Ad Set.
     *
     * @param  string $name
     * @param  integer $campaignId
     * @param  integer $zipCode
     * @return FacebookAds\Object\AdSet
     */
    public function createAdSet($name, $campaignId, $zipCode, $budget)
    {
        $account = FacebookAdsAccount::adAccountInstance();

        $fields = ['name'];

        $adLifeTime = $this->setAdLifetime();

        $zip = collect([
            'key' => "US:$zipCode",
            'radius' => 15,
            'distance_unit' => 'mile'
        ])->toJson();

        $params = [
            'name' => $name,
            'optimization_goal' => 'LINK_CLICKS',
            'billing_event' => 'IMPRESSIONS',
            'bid_strategy' => 'LOWEST_COST_WITHOUT_CAP',
            'lifetime_budget' => $budget,
            'start_time' => $adLifeTime[0],
            'end_time' => $adLifeTime[1],
            'campaign_id' => $campaignId,
            'targeting' => [
                'geo_locations' => [
                    'zips' => [
                        $zip
                    ]
                ]
            ],
            'status' => 'PAUSED',
        ];

        return $account->createAdSet($fields, $params);
    }

    private function setAdLifetime()
    {
        $today = Carbon::today()->timestamp;
        $oneWeek = Carbon::today()->addDay()->timestamp;

        return [$today, $oneWeek];
    }
}
