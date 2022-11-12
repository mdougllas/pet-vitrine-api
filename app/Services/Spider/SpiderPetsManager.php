<?php

namespace App\Services\Spider;

use App\Models\Pet;
use Illuminate\Support\Str;
use App\Services\Spider\HttpRequest;
use Illuminate\Support\Facades\Redis;

class SpiderPetsManager
{
    /**
     * Blueprint for SpiderPetsManager.
     *
     * @param \App\Services\Spider\HttpRequest $spider
     * @return void
     */
    public function __construct(HttpRequest $spider)
    {
        $this->spider = $spider;
        $this->pet = null;
    }

    /**
     * Start the jobs to scrape and store data.
     *
     * @return Illuminate\Database\Eloquent\Collection;
     * @return Illuminate\Database\Eloquent\Collection;
     */
    public function parsePets($page)
    {
        $response = $this->spider->getPets($page);
        $pets = collect($response->result->animals);

        $pets->each(function ($pet) {
            $petData = $pet->animal;

            if ($this->petExists($petData->id)) {
                var_dump("Pet $petData->id already on DB. Skipping saving the pet.");
                return true;
            }

            if ($this->checkDuplicatedPet($pet)) {
                var_dump("This is a dulicate. Skipping saving the pet.");
                return true;
            }

            if (!$this->filterSpecies($petData->species->name)) {
                var_dump('Not a cat or a dog. Skipping saving the pet.');
                return true;
            }

            $this->savePet($pet);

            sleep(2);
        });

        return false;
    }

    /**
     * List all pets available.
     *
     * @param  Illuminate\Database\Eloquent\Collection
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function listPets()
    {
        $result = Redis::lrange('pets', 0, Redis::llen('pets'));
        $pets = collect($result);
        $data = $pets->map(fn ($pet) => json_decode($pet));

        return $data;
    }

    /**
     * Retrieve the id for the latest parsed pet.
     *
     * @return Illuminate\Database\Eloquent\Collection
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function petExists($id)
    {
        return Pet::where('petfinder_id', $id)->exists();
    }

    /**
     * Retrieve the id for the latest parsed pet.
     *
     * @return Illuminate\Database\Eloquent\Collection
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function checkDuplicatedPet($pet)
    {
        $petData = $pet->animal;
        $organizationData = $pet->organization;

        $nameMatches = Pet::where('name', $petData->name)->get();

        if (!$nameMatches->isEmpty()) {
            $checkDuplicate = $nameMatches->map(function ($pet) use ($petData, $organizationData) {
                $sexMatches = $pet->sex == $petData->sex;
                $speciesMatches = $pet->species == $petData->species->name;
                $organizationMatches = $pet->organization_id == $organizationData->display_id;

                return $sexMatches && $speciesMatches && $organizationMatches;
            });

            return $checkDuplicate;
        }

        return false;
    }


    /**
     * Retrieve the id for the latest parsed pet.
     *
     * @return Illuminate\Database\Eloquent\Collection
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function filterSpecies($species)
    {
        return $species === 'Cat' || $species === 'Dog';
    }

    /**
     * Retrieve the id for the latest parsed pet.
     *
     * @return Illuminate\Database\Eloquent\Collection
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function savePet($pet)
    {
        var_dump('SAVE PET CALLED');

        $this->getPetData($pet);
        $this->pet->save();
        Redis::lpush('pets', json_encode($pet));
    }

    /**
     * Check if the pet has photos available.
     *
     * @return void;
     */
    private function getPetData($pet)
    {
        $this->pet = new Pet;
        $petData = $pet->animal;

        $this->pet->uuid = Str::uuid();
        $this->pet->ad_id = null;
        $this->pet->age = $petData->age;
        $this->pet->breed = $petData->primary_breed->name;
        $this->pet->description = $petData->description ?? 'No description available.';
        $this->pet->name = $petData->name;
        $this->pet->photo_urls = $petData->photo_urls;
        $this->pet->sex = $petData->sex;
        $this->pet->species = $petData->species->name;
        $this->pet->status = $petData->adoption_status;
        $this->pet->organization_id = $pet->organization->display_id;
        $this->pet->petfinder_id = $petData->id;
    }
}
