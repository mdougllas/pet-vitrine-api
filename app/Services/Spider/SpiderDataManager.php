<?php

namespace App\Services\Spider;

use App\Models\Pet;
use Illuminate\Support\Str;

class SpiderDataManager
{
    /**
     * Blueprint for SpiderJobsManager.
     *
     * @param \App\Services\Spider\SpiderPetsManager $pets
     * @return void
     */
    public function __construct()
    {
    }


    /**
     * Check if the pet has photos available.
     *
     * @return void;
     */
    public function getPetData($pet)
    {
        $petModel = new Pet;
        $petData = $pet->animal;

        $petModel->uuid = Str::uuid();
        $petModel->ad_id = null;
        $petModel->age = $petData->age;
        $petModel->breed = $petData->primary_breed->name;
        $petModel->description = $petData->description ?? 'No description available.';
        $petModel->name = $petData->name;
        $petModel->photo_urls = $petData->photo_urls;
        $petModel->sex = $petData->sex;
        $petModel->species = $petData->species->name;
        $petModel->status = $petData->adoption_status;
        $petModel->organization_id = $pet->organization->display_id;
        $petModel->petfinder_id = $petData->id;

        return $petModel;
    }
}
