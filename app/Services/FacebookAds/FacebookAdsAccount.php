<?php

namespace App\Services\FacebookAds;

use FacebookAds\Object\AdAccount;

class FacebookAdsAccount extends FacebookAds
{
    /**
     *
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function AdAccount()
    {
        return new AdAccount($this->adAccountId);
    }

    public static function AdAccountInstance()
    {
        return app(FacebookAdsAccount::class)->AdAccount();
    }
}
