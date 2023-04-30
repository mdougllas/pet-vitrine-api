<?php

namespace App\Services\Spider;

use App\Models\Pet;
use Illuminate\Support\Str;
use App\Models\Organization;

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

            if ($this->checkDuplicateByName($pet) !== false) {
                $this->output->warn("Pet $petData->id is a dulicate. Skipping saving the pet.");

                return true;
            }

            if ($this->checkNameCharacters($petData->name) > 250) {
                $this->output->warn("The name of pet $petData->id is too long. Skipping saving the pet.");

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

    private function checkNameCharacters($name)
    {
        // dd($name);
        return Str::length($name);
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
    private function attachToOrganization($pet, $organizationId)
    {
        $organization = Organization::where('petfinder_id', $organizationId)
            ->get();

        if ($organization->isEmpty()) {
            return false;
        }

        $pet->organization()->associate($organization[0]);

        return true;
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
        $petId = $petData->petfinder_id;
        $shelterId = $petData->petfinder_shelter_id;

        if (!$this->attachToOrganization($petData, $shelterId)) {
            $this->output->warn("Pet not associated with a valid organization. Skip saving the pet.");

            return;
        }



        $this->output->info("Pet $petId was associated to shelter $shelterId.");

        $petData->save();
        $this->output->info("Pet $petId saved on database.");
    }
}
