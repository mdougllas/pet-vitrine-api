<?php

namespace App\Services\Pets;

use App\Models\Pet;
use Illuminate\Support\Str;
use App\Models\Organization;
use App\Traits\GeoSearch\LatLongGeoSearch;
use Illuminate\Database\Eloquent\Collection;

class PetSearch
{
    use LatLongGeoSearch;
    /**
     * Search Pets in the database
     *
     * @param \App\Http\Requests\PetRequest $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function search($request): Collection
    {
        return $request->has('location')
            ? $this->withLocationFilter($request['location'])
            : $this->withoutLocationFilter($request);
    }

    /**
     * Search without location filter
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function withoutLocationFilter($request): Collection
    {
        return $request['organization']
            ? $this->withOrganization($request['organization'])
            : Pet::whereJsonLength('photo_urls', '>', 0)
            ->latest()->get();
    }

    /**
     * Search with ZIP or city as location
     *
     * @param int $limit
     * @param string $location
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function withLocationFilter($location): Collection
    {
        return is_numeric($location)
            ? $this->zipLocation($location)
            : $this->cityLocation($location);
    }

    /**
     * Filter pets using a Zip Number as location
     *
     * @param string $zipCode
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function zipLocation($zipCode): Collection
    {
        $distance = 10;
        $coordinates = $this->getLatLongFromZipCode($zipCode);

        return Pet::whereHas(
            'organization',
            fn ($query) => $query->whereRaw($this->queryHaversineFormula(
                $coordinates->lat,
                $coordinates->lng,
                $distance
            ))
        )->get();
    }

    /**
     * Filter pets using City as location
     *
     * @param string $city
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function cityLocation($city): Collection
    {
        return Pet::whereHas('organization', fn ($query) => $query
            ->whereCity($this->extractCity($city)))
            ->latest()
            ->get();
    }

    /**
     * Filter pets by organization_petfinder_id
     *
     * @param string $organization
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function withOrganization($organization): Collection
    {
        return Pet::whereHas('organization', fn ($query) => $query
            ->wherePetfinderId($organization))
            ->latest()->get();
    }

    /**
     * Extract only the city from the input
     *
     * @param string $city
     * @return string
     */
    private function extractCity($city): string
    {
        return Str::of($city)->before(',');
    }
}
