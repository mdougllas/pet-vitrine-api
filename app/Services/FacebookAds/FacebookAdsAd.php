<?php

namespace App\Services\FacebookAds;

use App\Services\FacebookAds\FacebookAdsAdCreative;
use App\Services\FacebookAds\FacebookAds;
use FacebookAds\Object\Ad;

class FacebookAdsAd
{
    /**
     * Instantiates / set up the class.
     *
     * @param  object App\Services\FacebookAdsAdCreative
     * @return void
     */
    public function __construct(FacebookAdsAccount $account, FacebookAdsAdCreative $creative)
    {
        $this->account = $account::adAccountInstance();
        $this->creative = $creative;
    }

    /**
     * Lists all Ads.
     *
     * @return Illuminate\Database\Eloquent\Collection;
     */
    public function listAds()
    {
        $ads = [];

        $fields = ['name'];

        $cursor = $this->account->getAds($fields);
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
     * Gets the ad previews.
     *
     * @param  string $url
     * @param  string $link
     * @param  string $name
     *
     * @return array
     */
    public function getAdPreview($url, $link, $name)
    {
        $fields = [];
        $params = ['ad_format' => 'DESKTOP_FEED_STANDARD'];
        $adCreative = $this->creative->createAdCreative($url, $link, $name);

        $previews = $adCreative->getPreviews($fields, $params)
            ->getResponse()
            ->getContent();

        return $previews;
    }

    /**
     * Creates Ad.
     *
     * @param  string $name
     * @param  integer $adSetId
     * @param  integer $creativeId
     * @return FacebookAds\Object\Ad
     */
    public function createAd($name, $adSetId, $creativeId)
    {
        $fields = ['name'];
        $params = [
            'name' => "Ad for $name",
            'adset_id' => $adSetId,
            'creative' => ['creative_id' => $creativeId],
            'status' => 'ACTIVE'
        ];

        return $this->account->createAd($fields, $params);
    }
}
