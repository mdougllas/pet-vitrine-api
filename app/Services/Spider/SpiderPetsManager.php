<?php

namespace App\Services\Spider;

use App\Models\Pet;
use Illuminate\Support\Str;
use App\Services\Spider\HttpRequest;
use Illuminate\Support\Facades\Redis;
use App\Services\Spider\SpiderDataManager;

class SpiderPetsManager
{
    /**
     * Blueprint for SpiderPetsManager.
     *
     * @param \App\Services\Spider\HttpRequest $spider
     * @return void
     */
    public function __construct(HttpRequest $spider, SpiderDataManager $dataManager)
    {
        $this->spider = $spider;
        $this->dataManager = $dataManager;
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

        $petData = $this->dataManager->getPetData($pet);
        $petData->save();
        Redis::lpush('pets', json_encode($pet));
    }
}
