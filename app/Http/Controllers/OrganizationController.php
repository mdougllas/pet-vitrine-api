<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Http\Requests\OrganizationRequest;
use App\Http\Resources\OrganizationResource;
use App\Services\Organization\OrganizationSearch;

class OrganizationController extends Controller
{
    /**
     * Search for organizations in the DB.
     *
     * @param OrganizationRequest $request
     * @param OrganizationSearch $organizations
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(OrganizationRequest $request, OrganizationSearch $pets): OrganizationResource
    {
        $result = $pets->search(collect($request->validated()));

        return new OrganizationResource($result);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Organization $organization
     * @return \App\Http\Resources\OrganizationResource
     */
    public function show(Organization $organization): OrganizationResource
    {
        return new OrganizationResource($organization);
    }
}
