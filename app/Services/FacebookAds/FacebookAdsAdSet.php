<?php

namespace App\Services\FacebookAds;

use Illuminate\Support\Carbon;
use App\Services\FacebookAds\FacebookAds;

class FacebookAdsAdSet
{
    /**
     * Lists all Ad Sets.
     *
     * @return Illuminate\Database\Eloquent\Collection;
     */
    public function listAdSets()
    {
        $account = FacebookAdsAccount::adAccountInstance();
        $adSets = [];

        $fields = [
            'name', 'id'
        ];

        $cursor = $account->getAdSets($fields);
        $cursor->setUseImplicitFetch(true);

        foreach ($cursor as $adSet) {
            $adSets[] = [
                'id' => $adSet->id,
                'name' => $adSet->name
            ];
        }

        return collect($adSets)->all();
    }


    /**
     * Creates Ad Set.
     *
     * @param  string $name
     * @param  integer $campaignId
     * @param  integer $zipCode
     * @param  integer $budget
     * @return FacebookAds\Object\AdSet
     */
    public function createAdSet($name, $campaignId, $zipCode, $budget)
    {
        $account = FacebookAdsAccount::adAccountInstance();
        $fields = ['name'];

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
            'lifetime_budget' => $this->convertToCents($budget),
            'start_time' => Carbon::today()->timestamp,
            'end_time' => $this->setAdDuration($budget),
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

    /**
     * Set the duration for the ad.
     *
     * @param  integer $budget
     * @return integer Epoch
     */
    private function setAdDuration($budget)
    {
        $oneWeek = Carbon::today()->addWeek()->timestamp;
        $twoWeeks = Carbon::today()->addWeeks(2)->timestamp;
        $threeWeeks = Carbon::today()->addWeeks(3)->timestamp;
        $oneMonth = Carbon::today()->addMonth()->timestamp;

        $donation = collect([['value' => $budget]]);

        $duration = collect([
            ['duration' => $oneWeek, 'budget' => $donation->whereBetween('value', [5, 10])->all()],
            ['duration' => $twoWeeks, 'budget' => $donation->whereBetween('value', [11, 20])->all()],
            ['duration' => $threeWeeks, 'budget' => $donation->whereBetween('value', [21, 30])->all()],
            ['duration' => $oneMonth, 'budget' => $donation->whereBetween('value', [31, 40])->all()],
        ]);

        return $duration->filter(fn ($value) => $value['budget'])->flatten()->first();
    }

    /**
     * Convert dollars into cents.
     *
     * @param  integer $amount
     * @return integer
     */
    private function convertToCents($amount)
    {
        return $amount * 100;
    }
}
