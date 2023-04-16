<?php

namespace App\Services\Pet;

use App\Models\Pet;
use App\Traits\StringManipulation;
use App\Traits\GeoSearch\LatLongGeoSearch;
use Illuminate\Pagination\LengthAwarePaginator;

class PetSearch
{
    use LatLongGeoSearch, StringManipulation;

    /**
     * Search Pets in the database
     *
     * @param \App\Http\Requests\PetRequest $request
     * @return \Illuminate\Pagination\LengthAwarePaginator;
     */
    public function search($request): LengthAwarePaginator
    {
        return $request->has('location')
            ? $this->withLocationFilter($request['location'])
            : $this->withoutLocationFilter($request);
    }

    /**
     * Search without location filter
     *
     * @param int $limit
     * @return \Illuminate\Pagination\LengthAwarePaginator;
     */
    private function withoutLocationFilter($request): LengthAwarePaginator
    {
        return $request->has('organization')
            ? $this->withOrganization($request['organization'])
            : Pet::whereJsonLength('photo_urls', '>', 0)
            ->latest()->paginate(12);
    }

    /**
     * Search with ZIP or city as location
     *
     * @param string $location
     * @return \Illuminate\Pagination\LengthAwarePaginator;
     */
    private function withLocationFilter($location): LengthAwarePaginator
    {
        return is_numeric($location)
            ? $this->zipLocation($location)
            : $this->cityLocation($location);
    }

    /**
     * Filter pets using a Zip Number as location
     *
     * @param string $zipCode
     * @return \Illuminate\Pagination\LengthAwarePaginator;
     */
    private function zipLocation($zipCode): LengthAwarePaginator
    {
        $distance = 10;
        $coordinates = $this->getLatLongFromZipCode($zipCode);

        return Pet::whereJsonLength('photo_urls', '>', 0)
            ->whereHas(
                'organization',
                fn ($query) => $query->whereRaw($this->queryHaversineFormula(
                    $coordinates->lat,
                    $coordinates->lng,
                    $distance
                ))
            )->paginate(12);
    }

    /**
     * Filter pets using City as location
     *
     * @param string $city
     * @return \Illuminate\Pagination\LengthAwarePaginator;
     */
    private function cityLocation($city): LengthAwarePaginator
    {
        return Pet::whereJsonLength('photo_urls', '>', 0)
            ->whereHas('organization', fn ($query) => $query
                ->whereCity($this->extractCityFromString($city)))
            ->latest()->paginate(12);
    }

    /**
     * Filter pets by organization_petfinder_id
     *
     * @param string $organization
     * @return \Illuminate\Pagination\LengthAwarePaginator;
     */
    private function withOrganization($organization): LengthAwarePaginator
    {
        return Pet::whereJsonLength('photo_urls', '>', 0)
            ->whereHas('organization', fn ($query) => $query
                ->wherePetfinderId($organization))
            ->latest()->paginate(12);
    }
}
