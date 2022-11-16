<?php

namespace App\Services\Spider;

use App\Models\Pet;
use App\Services\Spider\HttpRequest;
use App\Services\Spider\SpiderDataManager;

class SpiderSheltersManager
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
        $this->loop = 1;
    }

    /**
     * Start the jobs to scrape and store data.
     *
     * @return Illuminate\Database\Eloquent\Collection;
     * @return Illuminate\Database\Eloquent\Collection;
     */
    public function parseShelters($page)
    {
        $response = $this->spider->getOrganizations($page);
        $shelters = collect($response->organizations);

        $shelters->each(function ($shelter) {
            echo "This is shelter loop # $this->loop" . PHP_EOL;
            $this->loop += 1;

            // if ($this->shelterExists($shelter->display_id)) {
            //     echo "Shelter $shelter->display_id already on DB. Skipping saving the shelter. \n" . PHP_EOL;
            //     return true;
            // }

            // if ($this->checkDuplicatedPet($pet)) {
            //     echo "This is a dulicate. Skipping saving the pet. \n" . PHP_EOL;
            //     return true;
            // }

            // if (!$this->filterSpecies($petData->species->name)) {
            //     echo "Not a cat or a dog. Skipping saving the pet. \n" . PHP_EOL;
            //     return true;
            // }

            $this->saveShelter($shelter);
        });

        return false;
    }

    /**
     * List all pets available.
     *
     * @param  Illuminate\Database\Eloquent\Collection
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function listShelters()
    {
        //todo
        return;
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
    private function saveShelter($shelter)
    {
        echo "SAVE SHELTER CALLED \n" . PHP_EOL;
        $shelterData = $this->dataManager->getShelterData($shelter);

        $shelterData->save();
        dd($shelterData);
    }
}
