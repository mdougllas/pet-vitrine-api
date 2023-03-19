<?php

namespace App\Services\Pets;

use Illuminate\Support\Facades\DB;
use App\Models\Pet;
use App\Models\Organization;

class PetSearch
{
    /**
     * Search Pets in the database
     *
     * @param \App\Http\Requests\PetRequest $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search($request)
    {
        return $request->has('location')
            ? $this->withLocationFilter($request['location'])
            : $this->withoutLocationFilter($request['limit']);
    }

    /**
     * Search without location filter
     *
     * @param int $limit
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    private function withoutLocationFilter($limit)
    {
        return Pet::whereJsonLength('photo_urls', '>', 0)
            ->latest()
            ->paginate($limit);
    }

    /**
     * Search with ZIP or city location
     *
     * @param int $limit
     * @param string $location
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    private function withLocationFilter($location)
    {
        return is_numeric($location)
            ? $this->zipLocation($location)
            : $this->cityLocation($location);
    }

    /**
     * Filter pets using a Zip Number as location
     *
     * @param string $zipNumber
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    private function zipLocation($zipNumber)
    {
        $latitude = 26.13765;
        $longitude = -80.12302;
        $distance = 10;

        $pets = Pet::whereHas('organization', function ($query) use ($latitude, $longitude, $distance) {
            return $query->whereRaw(
                "acos(
                    sin(radians($latitude))
                    * sin(radians(latitude))
                    + cos(radians($latitude))
                    * cos(radians(latitude))
                    * cos( radians($longitude)
                    - radians(longitude))
                ) * 6371 <= $distance"
            );
        })->get();

        return $pets;

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
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    private function cityLocation($city)
    {
        return Pet::whereHas('organization', fn ($query) => $query->whereCity($city))
            ->latest()
            ->paginate(12);
    }

    public function scopedistance($query, $latitude, $longitude, $distance)
    {
        $haversine = "(
            6371 * acos(
                cos(radians(" . $latitude . "))
                * cos(radians(`latitude`))
                * cos(radians(`longitude`) - radians(" . $longitude . "))
                + sin(radians(" . $latitude . ")) * sin(radians(`latitude`))
            )
        )";

        return Organization::select("*")
            ->selectRaw("$haversine AS distance")
            ->having("distance", "<=", $distance)
            ->orderby("distance", "desc");
    }
}
