<?php

namespace App\Services\Organization;

use App\Models\Organization;
use App\Traits\StringManipulation;
use Illuminate\Support\Collection;
use App\Traits\GeoSearch\LatLongGeoSearch;

class OrganizationSearch
{
    use LatLongGeoSearch, StringManipulation;

    /**
     * Search Organizations in the database
     *
     * @param \App\Http\Requests\OrganizationRequest $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function search($request): Collection
    {
        $location = $request->get('location');

        return is_numeric($location)
            ? $this->zipLocation($location)
            : $this->cityLocation($location);
    }

    /**
     * Filter organizations using a Zip Number as location
     *
     * @param string $zipCode
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function zipLocation($zipCode): Collection
    {
        $distance = 10;
        $coordinates = $this->getLatLongFromZipCode($zipCode);

        return Organization::whereRaw($this->queryHaversineFormula(
            $coordinates->lat,
            $coordinates->lng,
            $distance
        ))->get();
    }

    /**
     * Filter organizations using City as location
     *
     * @param string $city
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function cityLocation($city): Collection
    {
        return Organization::whereCity($this->extractCityFromString($city))
            ->whereState($this->extractStateFromString($city))
            ->latest()->get();
    }
}
