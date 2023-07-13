<?php

namespace App\Services\Spider;

use App\Models\Organization;
use App\Models\Pet;
use Illuminate\Support\Collection;

class SpiderCheck
{
    /**
     * @property \App\Services\Spider\HttpRequest $spider
     */
    private $spider;

    /**
     * @property object $output
     */
    private $output;

    /**
     * Blueprint for SpiderCheck.
     *
     * @param \App\Services\Spider\HttpRequest $spider
     * @return void
     */
    public function __construct($output)
    {
        $this->spider = new HttpRequest;
        $this->output = $output;
    }

    /**
     * Starte checking pet for status and URLs
     *
     * @return int||null
     */
    public function startPetCheck($action): ?int
    {
        $this->output->info('Starting loop from newest registered pet and checking.');
        $this->loopThroughShelters();

        return $this->loopThroughPets($action);
    }

    /**
     * Loop through all Pets
     *
     * @param string $action
     * @return int||null
     */
    private function loopThroughPets(string $action): ?int
    {
        $range = Collection::range(Pet::max('id'), 1, -1);

        $range->takeWhile(function ($id) use ($action) {
            $this->pauseJob();

            $pet = Pet::find($id);

            if (!$pet) {
                $this->output->warn("No available pets yet. Skipping.");

                return true;
            }

            $this->output->info("Pet ID $id.");

            return $action === 'check-urls'
                ? $this->checkPetUrls()
                : $this->checkPetStatus($pet, $id);
        });

        return $action === 'check-urls'
            ? 1
            : $this->startPetCheck('status-check');
    }

    /**
     * Undocumented function
     *
     * @param [type] $pet
     * @param [type] $id
     * @return void
     */
    private function checkPetStatus($pet, $id)
    {
        if ($pet->status === 'adopted') {
            $this->output->warn("Pet $id is adopted. Skipping.");

            return true;
        }

        $response = $this->spider->getPet($pet->petfinder_id);

        $exists = collect($response->result->animals)->first();

        if (!$exists) {
            $this->output->warn("Pet $id no longer exists. Saving as adopted.");

            return $this->updatePetStatus($pet);
        }

        $status = collect($response->result->animals)
            ->first()
            ->animal
            ->adoption_status;

        $this->output->info("Status $status.");

        return $status !== 'adoptable'
            ? $this->updatePetStatus($pet)
            : $id != 1;
    }

    private function loopThroughShelters()
    {
        $range = Collection::range(Organization::max('id'), 1, -1);

        $range->takeWhile(function ($id) {
            $this->pauseJob();

            $shelter = Organization::find($id);

            if (!$shelter) {
                $this->output->warn("Shelter not found. Skipping.");

                return true;
            }

            $this->output->info("Checking shelter ID $id.");

            return $this->checkShelterExists($shelter);
        });
    }

    private function checkShelterExists($shelter)
    {
        $response = $this->spider->getOrganization(urlencode($shelter->name));
        $organizations = collect($response->organizations);

        $organization = $organizations->filter(function ($item) use ($shelter) {
            return $item->display_id === $shelter->petfinder_id;
        });

        return $organization->value('display_id')
            ? true
            : $this->deleteShelter($shelter);
    }

    private function deleteShelter($shelter)
    {
        $this->output->info("Shelter $shelter->id not found. Deleting from database.");

        $test = $shelter->delete();

        $this->output->info("Delete action returned $test");

        return $test;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    private function checkPetUrls()
    {
    }

    /**
     * Updates the pet status and save.
     *
     * @param /App/Models/Pet $pet
     * @param string $status
     * @return bool
     */
    private function updatePetStatus(Pet $pet, string $status = 'adopted'): bool
    {
        $this->output->info("Saving pet $pet->id new status: $status");
        $pet->status = $status;

        return $pet->save();
    }


    /**
     * Pause the job for a random
     * number of seconds
     * from 1 to 2.
     *
     * @return void
     */
    private function pauseJob()
    {
        // $randomNumber = collect([5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15])->random();
        $randomNumber = collect([1, 2])->random();

        $this->output->info("Pause for $randomNumber seconds.");

        sleep($randomNumber);
        // sleep(1);
    }
}
