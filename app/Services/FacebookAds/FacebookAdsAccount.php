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

    protected function adAccount()
    {
        return new AdAccount($this->adAccountId);
    }

    public static function adAccountInstance()
    {
        return app(FacebookAdsAccount::class)->adAccount();
    }

    public function getPageId()
    {
        return $this->pageId;
    }
}
