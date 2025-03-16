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
        $petModel = new Pet;

        $petModel->uuid = Str::uuid();
        $petModel->age = $pet->get('age');
        $petModel->breed = $pet->get('breeds')['primary'];
        $petModel->description = $pet->get('description') ?? 'No description available.';
        $petModel->name = $pet->get('name');
        $petModel->sex = $pet->get('gender');
        $petModel->species = $pet->get('species');
        $petModel->status = $pet->get('status');
        $petModel->petfinder_shelter_id = $pet->get('organization_id');
        $petModel->petfinder_id = $pet->get('id');
        $petModel->url = Str::before($pet->get('url'), '/?');
        $petModel->photo_urls = $pet->get('primary_photo_cropped')
            ? [$pet->get('primary_photo_cropped')['medium']]
            : [];

        return $petModel;
    }
}
