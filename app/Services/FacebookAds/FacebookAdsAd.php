<?php

namespace App\Services\FacebookAds;

use Illuminate\Support\Carbon;
use App\Services\FacebookAds\FacebookAds;

class FacebookAdsAd
{
    /**
     * Lists all Ads.
     *
     * @return Illuminate\Database\Eloquent\Collection;
     */
    public function listAds()
    {
        $account = FacebookAdsAccount::adAccountInstance();
        $ads = [];

        $fields = [
            'name', 'id'
        ];

        $cursor = $account->getAds($fields);
        $cursor->setUseImplicitFetch(true);

        foreach ($cursor as $ad) {
            $ads[] = [
                'id' => $ad->id,
                'name' => $ad->name
            ];
        }

        return collect($ads)->all();
    }

    /**
     * Creates Ad.
     *
     * @param  string $name
     * @param  integer $campaignId
     * @param  integer $zipCode
     * @param  integer $budget
     * @return FacebookAds\Object\AdSet
     */
    public function createAd($name, $campaignId, $zipCode, $budget)
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
        ];

        return $account->createAd($fields, $params);
    }
}
