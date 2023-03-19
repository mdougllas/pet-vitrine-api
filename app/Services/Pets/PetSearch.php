<?php

namespace App\Services\Pets;

use App\Models\Organization;
use App\Models\Pet;
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
            : $this->withoutLocationFilter($request['limit']);
    }

    /**
     * Search without location filter
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function withoutLocationFilter(): Collection
    {
        return Pet::whereJsonLength('photo_urls', '>', 0)
            ->latest()
            ->get();
    }

    /**
     * Search with ZIP or city location
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
     * @param string $zipNumber
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function zipLocation($zipNumber): Collection
    {
        $latitude = 26.13765;
        $longitude = -80.12302;
        $distance = 10;

        return Pet::whereHas(
            'organization',
            fn ($query) => $query->whereRaw($this->queryHaversineFormula($latitude, $longitude, $distance))
        )->get();

        // $pets = Pet::whereHas('organization', function ($query) use ($latitude, $longitude, $distance) {
        //     return $query->whereRaw(
        //         "acos(
        //             sin(radians($latitude))
        //             * sin(radians(latitude))
        //             + cos(radians($latitude))
        //             * cos(radians(latitude))
        //             * cos( radians($longitude)
        //             - radians(longitude))
        //         ) * 6371 <= $distance"
        //     );
        // })->get();

        // return $pets;

        // $latitude = 26.13765;
        // $longitude = -80.12302;
        // $distance = 10;

        // $haversine = "(
        //     6371 * acos(
        //         cos(radians(" . $latitude . "))
        //         * cos(radians(latitude))
        //         * cos(radians(longitude) - radians(" . $longitude . "))
        //         + sin(radians(" . $latitude . ")) * sin(radians(latitude))
        //     )
        // )";

        // $data = Organization::with('pets')
        //     ->select("*")
        //     ->selectRaw("$haversine AS distance")
        //     ->having("distance", "<=", $distance)
        //     ->limit(12)
        //     ->get();

        // return $data->map(fn ($item) => $item->pets)->flatten();
    }

    /**
     * Filter pets using City as location
     *
     * @param string $city
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function cityLocation($city): Collection
    {
        return Pet::whereHas('organization', fn ($query) => $query->whereCity($city))
            ->latest()
            ->get();
    }
}
