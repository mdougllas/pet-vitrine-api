<?php

namespace App\Services\FacebookAds;

use FacebookAds\Api;
use FacebookAds\Object\AdAccount;

class FacebookAds
{
    /**
     * Instantiates SDK API and Ad Account.
     *
     * @return void
     */
    protected function __construct()
    {
        $this->accessToken = config('services.facebook.access_token');
        $this->adAccountId = config('services.facebook.ad_account_id');
        $this->appSecret = config('services.facebook.app_secret');
        $this->appId = config('services.facebook.app_id');
        $this->pageId = config('services.facebook.page_id');

        Api::init($this->appId, $this->appSecret, $this->accessToken);

        $this->api = Api::instance();
        $this->account = new AdAccount($this->adAccountId);
    }
}
