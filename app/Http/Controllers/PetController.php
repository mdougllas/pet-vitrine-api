<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Services\Pet\PetSearch;
use App\Http\Requests\PetRequest;
use App\Http\Resources\PetResource;
use Illuminate\Support\Facades\Cache;

class PetController extends Controller
{
    /**
     * Search for pets in the DB.
     *
     * @param PetRequest $request
     * @param PetSearch $pets
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(PetRequest $request, PetSearch $pets): PetResource
    {
        $validData = collect($request->validated());
        $cacheKey ='search_' . md5(json_encode($validData));
        $result = Cache::rememberForever($cacheKey, fn () => $pets->search($validData));

        return new PetResource($result);
    }

    /**
     * Get 3 random pets for featured pets section.
     *
     * @param PetRequest $request
     * @param PetSearch $pets
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function featured(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return Cache::remember('featured-pets', 1, function () {
            return PetResource::collection(Pet::with('organization')
            ->where('status', 'adoptable')
            ->whereJsonLength('photo_urls', '>', 0)
            ->inRandomOrder()
            ->take(3)
            ->get());
        });
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Pet $pet
     * @return \App\Http\Resources\PetResource
     */
    public function show(Pet $pet): PetResource
    {
        return new PetResource($pet->load('organization'));
    }
}
