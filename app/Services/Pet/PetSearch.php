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
     * The payload from the request
     *
     * @var Illuminate\Support\Collection $payload
     */
    private $payload;

    /**
     * Search Pets in the database
     *
     * @param \App\Http\Requests\PetRequest $request
     * @return \Illuminate\Pagination\LengthAwarePaginator;
     */
    public function search($request): LengthAwarePaginator
    {
        $this->payload = collect($request);

        return $this->payload->has('location')
            ? $this->withLocationFilter($this->payload->get('location'))
            : $this->withoutLocationFilter();
    }

    /**
     * Search without location filter
     *
     * @param int $limit
     * @return \Illuminate\Pagination\LengthAwarePaginator;
     */
    private function withoutLocationFilter(): LengthAwarePaginator
    {
        return $this->payload->has('organization')
            ? $this->withOrganization($this->payload->get('organization'))
            : Pet::whereJsonLength('photo_urls', '>', 0)
            ->where('status', 'adoptable')
            ->with('organization')
            ->orderBy('id', 'desc')
            ->paginate($this->payload->get('limit'));
    }

    /**
     * Search with ZIP or city as location
     *
     * @param string $location
     * @return \Illuminate\Pagination\LengthAwarePaginator;
     */
    private function withLocationFilter($location): LengthAwarePaginator
    {
        return is_numeric($this->payload->get('location'))
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
            )
            ->where('status', 'adoptable')
            ->with('organization')
            ->paginate($this->payload->get('limit'));
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
            ->whereRelation('organization', 'city', $this->extractCityFromString($city))
            ->whereRelation('organization', 'state', $this->extractStateFromString($city))
            ->where('status', 'adoptable')
            ->with('organization')
            ->paginate(12);
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
            ->whereRelation('organization', 'id', $organization)
            ->where('status', 'adoptable')
            ->with('organization')
            ->paginate(12);
    }
}
