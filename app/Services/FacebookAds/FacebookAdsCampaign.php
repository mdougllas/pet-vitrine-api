<?php

namespace App\Services\FacebookAds;

use FacebookAds\Object\Campaign;
use App\Services\FacebookAds\FacebookAds;

class FacebookAdsCampaign
{
    /**
     * Creates a new Facebook Ads campaign.
     *
     * @param  array  $fields
     * @param  array  $params
     * @return FacebookAds\Object\Campaign
     */
    public function createCampaign($name)
    {
        $account = FacebookAdsAccount::adAccountInstance();

        $fields = ['name'];
        $params = [
            'name' => $name,
            'buying_type' => 'AUCTION',
            'objective' => 'LINK_CLICKS',
            'status' => 'PAUSED',
            'special_ad_categories' => ['NONE']
        ];

        return $account->createCampaign($fields, $params);
    }

    /**
     * Gets a specific campaign.
     *
     * @param  string  $id
     * @return FacebookAds\Object\Campaign
     */
    public function getCampaign($id)
    {
        return new Campaign($id);
    }

    /**
     * Gets the last created campaign.
     *
     * @param  array  $fields
     * @return FacebookAds\Object\Campaign
     */
    public function getLastCampaign()
    {
        var_dump('get Last Campaign');
        $account = FacebookAdsAccount::adAccountInstance();

        $fields = [
            'name', 'id'
        ];

        $cursor = $account->getCampaigns($fields);
        $cursor->end();

        $data = $cursor[0];

        return $data;
    }
}
