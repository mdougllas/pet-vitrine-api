<?php

namespace App\Services\FacebookAds;

use App\Services\FacebookAds\FacebookAds;
use Illuminate\Support\Carbon;

class FacebookAdsAdSet extends FacebookAds
{
    /**
     *
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();
    }

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
        $fields = [
            'name'
        ];

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
            'bid_strategy' => 'LOWEST_COST_WITH_BID_CAP',
            'bid_amount' => '1',
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

        return $this->account->createAdSet($fields, $params);
    }

    private function setAdLifetime()
    {
        $today = Carbon::today()->timestamp;
        $oneWeek = Carbon::today()->addDay()->timestamp;

        return [$today, $oneWeek];
    }
}
