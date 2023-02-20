<?php

namespace App\Services\Spider;

use App\Models\Pet;
use Illuminate\Support\Str;
use App\Models\Organization;

class SpiderDataManager
{
    /**
     * Blueprint for SpiderDataManager.
     *
     * @param \App\Services\Spider\SpiderPetsManager $pets
     * @return void
     */
    public function __construct()
    {
    }


    /**
     * Get the sehlter data to be persisted.
     *
     * @return void;
     */
    public function getShelterData($shelter)
    {
        $shelterModel = new Organization;
        $location = $shelter->location->address;
        $geo = $shelter->location->geo;

        $shelterModel->uuid = Str::uuid();
        $shelterModel->address_1 = $location->address1;
        $shelterModel->address_2 = $location->address2;
        $shelterModel->city = $location->city;
        $shelterModel->country = $location->country;
        $shelterModel->latitude = $geo->latitude;
        $shelterModel->longitude = $geo->longitude;
        $shelterModel->name = $shelter->name;
        $shelterModel->postal_code = $location->postal_code;
        $shelterModel->petfinder_id = $shelter->display_id;
        $shelterModel->state = $location->state;

        return $shelterModel;
    }

    /**
     * Get the pet data to be persisted..
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
