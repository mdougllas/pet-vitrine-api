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
        var_dump('adaccount');
        return new AdAccount($this->adAccountId);
    }

    public static function adAccountInstance()
    {
        var_dump('adaccount instance');

        return app(FacebookAdsAccount::class)->adAccount();
    }

    public function getPageId()
    {
        var_dump('adaccount get page id');

        return $this->pageId;
    }
}
