<?php

namespace App\Services\FacebookAds;

use FacebookAds\Object\TargetingSearch;

class FacebookAdsTargetingSearch
{
    /**
     * Searches for geolocation and returns location codes.
     *
     * @param  string  $type
     * @param  string  $targeting
     * @param  string  $query
     *
     * @return Object
     */
    public function search($type, $targeting, $query)
    {
        FacebookAdsAccount::adAccountInstance();

        $search = collect(TargetingSearch::search($type, $targeting, $query));

        return $search->firstWhere('name', $query);
    }
}
