<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Services\Pet\PetSearch;
use App\Http\Requests\PetRequest;
use App\Http\Resources\PetResource;

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
        $result = $pets->search(collect($request->validated()));

        return new PetResource($result);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Pet $pet
     * @return \App\Http\Resources\PetResource
     */
    public function show(Pet $pet): PetResource
    {
        return new PetResource($pet);
    }
}
