<?php

namespace App\Http\Controllers;

use App\Http\Requests\PetRequest;
use App\Http\Resources\PetResource;
use App\Services\Pets\PetSearch;

class PetController extends Controller
{
    /**
     * Search for pets in the DB.
     *
     * @param PetRequest $request
     * @param PetSearch $pets
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(PetRequest $request, PetSearch $pets): \Illuminate\Pagination\LengthAwarePaginator
    {
        $result = $pets->search(collect($request->validated()));

        return PetResource::collection($result)->paginate(12);
    }
}
