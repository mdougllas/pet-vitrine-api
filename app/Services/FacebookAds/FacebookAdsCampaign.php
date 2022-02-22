<?php

namespace App\Services\FacebookAds;

use FacebookAds\Object\Campaign;
use App\Services\FacebookAds\FacebookAds;

class FacebookAdsCampaign extends FacebookAds
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
     * Creates a new Facebook Ads campaign.
     *
     * @param  array  $fields
     * @param  array  $params
     * @return FacebookAds\Object\Campaign
     */
    public function createCampaign($name)
    {
        $fields = [];
        $params = [
            'name' => $name,
            'buying_type' => 'AUCTION',
            'objective' => 'LINK_CLICKS',
            'status' => 'PAUSED',
            'special_ad_categories' => ['NONE']
        ];

        return $this->account->createCampaign($fields, $params);
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
        $fields = [
            'name', 'id'
        ];

        $cursor = $this->account->getCampaigns($fields);
        $cursor->end();

        $data = $cursor[0];

        return $data;
    }
}
