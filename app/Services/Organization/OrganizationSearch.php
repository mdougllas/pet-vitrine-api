<?php

namespace App\Services\Organization;

use App\Models\Organization;
use Illuminate\Support\Collection;
use App\Traits\GeoSearch\LatLongGeoSearch;
use App\Traits\StringManipulation;

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
        return $request->has('zip')
            ? $this->searchByZipCode($request['location'])
            : $this->searchByCity($request);
    }

    /**
     * Search by ZipCode
     *
     * @param string $zipCode
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function searchByZipCode($zipCode): Collection
    {
        $distance = 10;
        $coordinates = $this->getLatLongFromZipCode($zipCode);

        return Organization::whereRaw(
            $this->queryHaversineFormula(
                $coordinates->lat,
                $coordinates->lng,
                $distance
            )
        )->get();
    }

    /**
     * Search by City
     *
     * @param string $city
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function searchByCity($city): Collection
    {
        return Organization::whereCity($this->extractCityFromString($city))
            ->latest()
            ->get();
    }
}
