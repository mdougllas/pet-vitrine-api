<?php

namespace App\Services\FacebookAds;

use App\Services\FacebookAds\FacebookAds;
use App\Services\FacebookAds\FacebookAdsAdCreative;
use FacebookAds\Object\Ad;
use Illuminate\Support\Facades\Http;

class FacebookAdsAd
{
    private $account;
    private FacebookAdsAdCreative $creative;

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
        // $fields = [];
        // $params = ['ad_format' => 'MOBILE_FEED_STANDARD'];
        $adCreative = $this->creative->createAdCreative($url, $link, $name);

        // $previews = $adCreative->getPreviews($fields, $params)
        //     ->getResponse()
        //     ->getContent();

        $endpoint = "https://graph.facebook.com/v23.0/$adCreative/previews?access_token=EAAC73fSImUcBPHy2kwYaOfrRYZBy11F92QjoZAAL1lJzPecZCbmWvQqxXieZA6jENRosHsIEJElMIxbF8R2rKH1TBMRqfTAVDWywPEoGQHGTQORhQNGRBdc3GH9WkhHgZCUuRviEJSLiPBQc8Dopu27kHD7H07wQeKaUdovhnlBHxyLttBSFBOlJCHT7LwyOv5AZDZD&ad_format=DESKTOP_FEED_STANDARD";

        $previews = Http::get($endpoint);

        return $previews->body();
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
            'status' => 'PAUSED'
        ];

        return $this->account->createAd($fields, $params);
    }
}
