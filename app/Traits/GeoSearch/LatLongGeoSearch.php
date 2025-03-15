<?php

namespace App\Traits\GeoSearch;

use App\Helpers\HandleHttpException;
use Illuminate\Support\Facades\Http;

trait LatLongGeoSearch
{
    /**
     * Get latitude and longitude
     * from zip code
     *
     * @var string $zipCode
     *
     */


    /**
     * Get latitude and longitude
     * from zip code
     *
     * @param int $zipCode
     * @return object|null
     */
    protected function getLatLongFromZipCode($zipCode): object|null
    {
        $apiKey = config('services.geocode.api_key');
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=$zipCode&key=$apiKey";

        $response = Http::get($url);
        $response->onError(fn ($err) => HandleHttpException::throw($err));

        $results = collect($response->object())->get('results');
        $unwrapped = collect($results)->first();
        $geometry = collect($unwrapped)->get('geometry');
        $coordinates = collect($geometry)->get('location');

        return $coordinates;
    }

    /**
     * Get query for the harversine
     * formula with given data.
     *
     * @param float $latitude
     * @param float $longitude
     * @param int $distance
     * @return string
     */
    protected function queryHaversineFormula($latitude, $longitude, $distance): string
    {
        return $this->haversine = "acos(
            sin(radians($latitude))
            * sin(radians(latitude))
            + cos(radians($latitude))
            * cos(radians(latitude))
            * cos( radians($longitude)
            - radians(longitude))
        ) * 6371 <= $distance";
    }
}
