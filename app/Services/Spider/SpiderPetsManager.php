<?php

namespace App\Services\Spider;

use App\Models\Organization;
use App\Models\Pet;

class SpiderPetsManager
{
    /**
     * @property integer $cicle
     */
    private $loop = 1;

    /**
     * @property \App\Services\Spider\HttpRequest $spider
     */
    private $spider;

    /**
     * @property object $output
     */
    private $output;

    /**
     * @property object $output
     */
    private $dataManager;

    public function __construct($output)
    {
        $this->spider = new HttpRequest;
        $this->output = $output;
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

        if (!$response || !$response->result) {
            $this->output->warn("No pets received from this request. Skipping parsing pets.");

            return true;
        }

        $pets = collect($response->result->animals);

        $pets->each(function ($pet) {
            $petData = $pet->animal;

            $this->output->info("This is pets loop # $this->loop");

            $this->loop += 1;

            if ($this->petExists($petData->id)) {
                $this->output->warn("Pet $petData->id already on DB. Skipping saving the pet.");

                return true;
            }

            $duplicate = $this->checkDuplicateByName($pet);

            if ($duplicate !== false) {
                $this->output->warn("Pet $petData->id is a dulicate. Skipping saving the pet.");

                return true;
            }

            if (!$this->filterSpecies($petData->species->name)) {
                $this->output->warn("Pet $petData->id is not a cat or a dog. Skipping saving the pet.");

                return true;
            }

            $this->savePet($pet);
        });

        return false;
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
    private function checkDuplicateByName($pet)
    {
        $petData = $pet->animal;
        $organizationData = $pet->organization;
        $nameMatches = Pet::where('name', $petData->name)->get();

        if (!$nameMatches->isEmpty()) {
            $duplicate = $this->doubleCheckDuplicate($petData, $organizationData, $nameMatches);

            return $duplicate;
        }

        return false;
    }

    /**
     * Retrieve the id for the latest parsed pet.
     *
     * @return Illuminate\Database\Eloquent\Collection
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function doubleCheckDuplicate($petData, $organizationData, $nameMatches)
    {
        $checkDuplicate = $nameMatches->map(function ($pet) use ($petData, $organizationData) {
            $sexMatches = $pet->sex == $petData->sex;
            $speciesMatches = $pet->species == $petData->species->name;
            $organizationMatches = $pet->organization_id == $organizationData->display_id;

            return $sexMatches && $speciesMatches && $organizationMatches;
        });

        return $checkDuplicate->search(true);
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
    private function attachToOrganization($pet, $organization_id)
    {
        $petId = $pet->petfinder_id;
        $organization = Organization::where('petfinder_id', $organization_id);

        $pet->organization()->associate($organization);

        $this->output->info("Pet $petId was attached to shelter $organization_id");
    }

    /**
     * Retrieve the id for the latest parsed pet.
     *
     * @return Illuminate\Database\Eloquent\Collection
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function savePet($pet)
    {
        $petData = SpiderDataManager::getPetData($pet);

        $this->attachToOrganization($petData, $petData->petfinder_shelter_id);
        $petData->save();

        $this->output->info("Pet saved on the DB.");
    }
}
