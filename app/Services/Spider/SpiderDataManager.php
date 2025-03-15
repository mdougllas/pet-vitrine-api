<?php

namespace App\Services\Spider;

use App\Models\Pet;
use Illuminate\Support\Str;
use App\Models\Organization;
use App\Traits\GeoSearch\LatLongGeoSearch;

class SpiderDataManager
{
    use LatLongGeoSearch;

    /**
     * Get the sehlter data to be persisted.
     * @return Organization|null;
     */
    public function getShelterData($shelter): Organization|null
    {
        $shelterModel = new Organization;
        $location = collect($shelter->get('address'));
        $coordinates = $this->getLatLongFromZipCode($location->get('postcode'));

        if (is_null($coordinates)) {
            return null;
        }

        $shelterModel->uuid = Str::uuid();
        $shelterModel->address_1 = $location->get('address1');
        $shelterModel->address_2 = $location->get('address2');
        $shelterModel->city = $location->get('city');
        $shelterModel->country = $location->get('country');
        $shelterModel->latitude = $coordinates->lat;
        $shelterModel->longitude = $coordinates->lng;
        $shelterModel->name = $shelter->get('name');
        $shelterModel->postal_code = $location->get('postcode');
        $shelterModel->petfinder_id = $shelter->get('id');
        $shelterModel->state = $location->get('state');

        return $shelterModel;
    }

    /**
     * Get the pet data to be persisted..
     *
     * @return void;
     */
    public function getPetData($pet)
    {
        dd($pet);
        $petModel = new Pet;
        $petData = $pet->animal;

        $petModel->uuid = Str::uuid();
        $petModel->age = $petData->age;
        $petModel->breed = $petData->primary_breed->name;
        $petModel->description = $petData->description ?? 'No description available.';
        $petModel->name = $petData->name;
        $petModel->photo_urls = $petData->photo_urls;
        $petModel->sex = $petData->sex;
        $petModel->species = $petData->species->name;
        $petModel->status = $petData->adoption_status;
        $petModel->petfinder_shelter_id = $pet->organization->display_id;
        $petModel->petfinder_id = $petData->id;
        $petModel->url = $petData->social_sharing->email_url;

        return $petModel;
    }
}
