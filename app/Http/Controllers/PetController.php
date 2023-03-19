<?php

namespace App\Http\Controllers;

use App\Http\Requests\PetRequest;
use App\Http\Resources\PetResource;
use App\Services\Pets\PetSearch;

class PetController extends Controller
{
    public function search(PetRequest $request, PetSearch $pets)
    {
        $result = $pets->search(collect($request->validated()));

        return PetResource::collection($result)->paginate(12);
    }
}
