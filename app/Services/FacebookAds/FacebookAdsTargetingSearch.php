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
    public static function search($type, $targeting, $query)
    {
        FacebookAdsAccount::adAccountInstance();

        $search = collect(TargetingSearch::search($type, $targeting, $query));

        $city = $search->firstWhere('type', 'city');
        $subcity = $search->firstWhere('type', 'subcity');

        return $city ?? $subcity;
    }
}
