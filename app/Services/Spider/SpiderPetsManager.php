<?php

namespace App\Services\Spider;

use App\Models\Organization;
use App\Models\Pet;
use App\Models\SpiderJob;
use App\Traits\Spider\UseSetOutput;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SpiderPetsManager
{
    use UseSetOutput;

    /**
     * Undocumented variable
     *
     * @var SpiderDataManager
     */
    private SpiderDataManager $manager;

    /**
     * @property \App\Services\Spider\HttpRequest $spider
     */
    private HttpRequest $spider;

    /**
     * Undocumented variable
     *
     * @var integer
     */
    private int $duplicateCount;

    /**
     * Undocumented variable
     *
     * @var boolean
     */
    private bool $abortSpider;

    /**
     * Undocumented function
     *
     * @param HttpRequest $spider
     * @param SpiderDataManager $manager
     */
    public function __construct(HttpRequest $spider, SpiderDataManager $manager)
    {
        $this->spider = $spider;
        $this->manager = $manager;
        $this->duplicateCount = 0;
        $this->abortSpider = false;
    }

    /**
     * Start the jobs to scrape and store data.
     *
     * @return void;
     */
    public function parsePets(): void
    {
        if ($this->spider->requestCount >= 1000) {
            $this->spiderOutput->warn("Reached requests limit for PetFinder. Aborting spider.");
            $this->abortSpider = true;

            return;
        }

        $pets = $this->spider->getPets();
        $petCount = $this->getNumberOfPets();

        $pagination = collect($pets->get('pagination'));
        $totalPets = $pagination->get('total_count');
        $totalPages = $pagination->get('total_pages');

        if ($petCount === $totalPets) {
            $this->spiderOutput->info("No new pets registered.");

            return;
        }

        $page = 0;

        while ($totalPages > 0) {
            if ($this->abortSpider) {
                break;
            }

            $totalPages--;
            $page++;

            $this->collectPets($page);
        }

        $this->setNumberOfPets($totalPets);
    }

    private function collectPets(int $page)
    {
        $this->spiderOutput->info("Parsing page # $page.");

        $response = $this->spider->getPets($page);
        $pets = collect($response->get('animals'));

        $pets->each(fn ($pet) => $this->analizePet(collect($pet)));
    }

    private function analizePet(Collection $pet): bool
    {
        $name = $pet->get('name');
        $id = $pet->get('id');
        $species = $pet->get('species');

        $this->spiderOutput->info("Analizing pet $name");

        if ($this->petExists($id)) {
            $this->spiderOutput->warn("Pet $id already on DB. Skipping saving the pet.");

            return $this->spiderStopThreshold();
        }

        if ($this->checkDuplicateByName($pet) !== false) {
            $this->spiderOutput->warn("Pet $name is a dulicate. Skipping saving the pet.");

            return true;
        }

        if ($this->checkNameCharacters($name) > 80) {
            $this->spiderOutput->warn("The name of pet $name is too long. Skipping saving the pet.");

            return true;
        }

        if (!$this->filterSpecies($species)) {
            $this->spiderOutput->warn("Pet $species is not a cat or a dog. Skipping saving the pet.");

            return true;
        }

        $this->savePet($pet);
        $this->duplicateCount = 0;

        return true;
    }

    private function spiderStopThreshold()
    {
        $this->duplicateCount++;

        if ($this->duplicateCount >= 100) {
            $this->spiderOutput->warn("All new pets already registered. Aborting spider.");
            $this->abortSpider = true;

            return false;
        }

        $this->checkRequestCount();

        return true;
    }

    /**
     * Retrieve the id for the latest parsed pet.
     *
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
     */
    private function checkDuplicateByName($pet)
    {
        $nameMatches = Pet::where('name', $pet['name'])->get();

        if (!$nameMatches->isEmpty()) {
            $duplicate = $this->doubleCheckDuplicate($pet, $nameMatches);

            return $duplicate;
        }

        return false;
    }

    /**
     * Retrieve the id for the latest parsed pet.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function doubleCheckDuplicate($newPet, $nameMatches)
    {
        $checkDuplicate = $nameMatches->map(function ($pet) use ($newPet) {
            $sexMatches = $pet->sex == $newPet['gender'];
            $speciesMatches = $pet->species == $newPet['species'];
            $breedMatches = $pet->breed == $newPet['breeds']['primary'];

            return $sexMatches && $speciesMatches && $breedMatches;
        });

        return $checkDuplicate->search(true);
    }

    private function checkNameCharacters($name)
    {
        return Str::length($name);
    }


    /**
     * Retrieve the id for the latest parsed pet.
     *
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
     */
    private function attachToOrganization($pet, $organizationId)
    {
        $organization = Organization::where('petfinder_id', $organizationId)
            ->first();

        if (! $organization) {
            return false;
        }

        $pet->organization()->associate($organization);

        return true;
    }

    /**
     * Retrieve the id for the latest parsed pet.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function savePet($pet)
    {
        $petData = $this->manager->getPetData($pet);
        $petId = $petData->petfinder_id;
        $shelterId = $petData->petfinder_shelter_id;

        if (!$this->attachToOrganization($petData, $shelterId)) {
            $this->spiderOutput->warn("Pet not associated with a valid organization. Skip saving the pet.");

            return;
        }

        $this->spiderOutput->info("Pet $petId was associated to shelter $shelterId.");

        $petData->save();
        $this->spiderOutput->info("Pet $petId saved on database.");
    }

    /**
     * Store the id for the latest parsed pet.
     *
     * @return void
     */
    private function setNumberOfPets($qty = 1): void
    {
        $spiderJob = SpiderJob::first();
        $spiderJob->number_of_pets = $qty;

        $spiderJob->save();
    }

    /**
     * Retrieve the id for the latest parsed pet.
     *
     * @return int
     */
    private function getNumberOfPets(): int
    {
        return SpiderJob::first()->number_of_pets;
    }
}
