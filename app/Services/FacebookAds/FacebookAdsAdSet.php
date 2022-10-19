<?php

namespace App\Services\FacebookAds;

use Illuminate\Support\Carbon;
use App\Services\FacebookAds\FacebookAds;
use FacebookAds\Object\AdSet;

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

    public function getAdSet($id)
    {
        FacebookAdsAccount::adAccountInstance();

        $fields = [
            'spend', 'clicks', 'cpc', 'reach', 'impressions'
        ];

        $params = [
            'date_preset' => 'maximum' //Use time_range in the future: https://developers.facebook.com/docs/marketing-api/insights/parameters/v13.0
        ];

        $set = new AdSet($id);
        $rawData = $set->getInsights($fields, $params)->getResponse()->getContent();
        $data = collect($rawData['data'])->first();

        return $data;
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
    public function createAdSet($name, $campaignId, $city, $budget)
    {
        $account = FacebookAdsAccount::adAccountInstance();
        $fields = ['name', 'targeting', 'start_time', 'end_time'];
        $cityData = FacebookAdsTargetingSearch::search('adgeolocation', 'city', $city);

        $city = collect([
            'key' => $cityData->key,
            'radius' => 10,
            'distance_unit' => 'mile'
        ]);

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
                    'cities' => [
                        $city
                    ]
                ],
                'interests' => [
                    ['id' => 6003430816269, 'name' => 'Pets at Home'],
                    ['id' => 6003132295608, 'name' => 'Pets Lovers'],
                    ['id' => 6003225292461, 'name' => 'Pet adoption'],
                    ['id' => 6003741164891, 'name' => 'Animal shelter'],
                ],
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
        $fourDays = Carbon::today()->addDays(4)->timestamp;
        $oneWeek = Carbon::today()->addWeek()->timestamp;
        $twoWeeks = Carbon::today()->addWeeks(2)->timestamp;
        $threeWeeks = Carbon::today()->addWeeks(3)->timestamp;
        $oneMonth = Carbon::today()->addMonth()->timestamp;

        $donation = collect([['value' => $budget]]);

        $duration = collect([
            ['duration' => $fourDays, 'budget' => $donation->where('value', '<=', 10)->all()],
            ['duration' => $oneWeek, 'budget' => $donation->whereBetween('value', [11, 20])->all()],
            ['duration' => $twoWeeks, 'budget' => $donation->whereBetween('value', [21, 30])->all()],
            ['duration' => $threeWeeks, 'budget' => $donation->whereBetween('value', [31, 40])->all()],
            ['duration' => $oneMonth, 'budget' => $donation->where('value', '>=', 41)->all()],
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
