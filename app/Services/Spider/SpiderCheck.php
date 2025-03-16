<?php

namespace App\Services\Spider;

use App\Models\Organization;
use App\Models\Pet;
use App\Traits\Spider\UseSetOutput;
use Illuminate\Support\Collection;

class SpiderCheck
{
    use UseSetOutput;

    /**
     * @property \App\Services\Spider\HttpRequest $spider
     */
    private $spider;

    /**
     * Blueprint for SpiderCheck.
     *
     * @param HttpRequest $spider
     */
    public function __construct(HttpRequest $spider)
    {
        $this->spider = $spider;
    }

    /**
     * Starte checking pet for status and URLs
     *
     * @return int|null
     */
    public function startPetCheck($action): ?int
    {
        $this->spider->setSpiderCheckOutput($this->spiderCheckOutput);

        $this->spiderCheckOutput->info('Starting loop from newest registered pet and checking.');
        $this->loopThroughShelters();

        return $this->loopThroughPets($action);
    }

    /**
     * Loop through all Pets
     *
     * @param string $action
     * @return int|null
     */
    private function loopThroughPets(string $action): ?int
    {
        $range = Collection::range(Pet::max('id'), 1, -1);

        $range->takeWhile(function ($id) use ($action) {
            $pet = Pet::find($id);

            if (!$pet) {
                $this->spiderCheckOutput->warn("No available pets yet. Skipping.");

                return true;
            }

            $this->spiderCheckOutput->info("Pet ID $id.");

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
     * @param Pet $pet
     * @param int $id
     * @return bool
     */
    private function checkPetStatus(Pet $pet, int $id): bool
    {
        if ($pet->status === 'adopted') {
            $this->spiderCheckOutput->warn("Pet $id is adopted. Skipping.");

            return true;
        }

        $response = $this->spider->getPet($pet->petfinder_id);

        $exists = collect($response->get('animals'))->first();

        if (!$exists) {
            $this->spiderCheckOutput->warn("Pet $id no longer exists. Saving as adopted.");

            return $this->updatePetStatus($pet);
        }

        $status = $response->get('animals')
            ->first()['animal']['adoption_status'];

        $this->spiderCheckOutput->info("Status $status.");

        return $status !== 'adoptable'
            ? $this->updatePetStatus($pet)
            : $id != 1;
    }

    /**
     * Undocumented function
     *
     * @return bool
     */
    private function loopThroughShelters(): bool
    {
        $range = Collection::range(Organization::max('id'), 1, -1);

        $range->takeWhile(function ($id) {
            $shelter = Organization::find($id);

            if (!$shelter) {
                $this->spiderCheckOutput->warn("Shelter not found. Skipping.");

                return true;
            }

            $this->spiderCheckOutput->info("Checking shelter ID $id.");

            return $this->checkShelterExists($shelter);
        });

        return true;
    }

    /**
     * Undocumented function
     *
     * @param [type] $shelter
     * @return boolean
     */
    private function checkShelterExists($shelter): bool
    {
        dd($shelter);
        $response = $this->spider->getOrganization(urlencode($shelter->name));
        $organizations = collect($response->organizations);

        $organization = $organizations->filter(function ($item) use ($shelter) {
            return $item->display_id === $shelter->petfinder_id;
        });

        return $organization->get('display_id')
            ? true
            : $this->deleteShelter($shelter);
    }

    private function deleteShelter($shelter)
    {
        $this->spiderCheckOutput->info("Shelter $shelter->id not found. Deleting from database.");

        $test = $shelter->delete();

        $this->spiderCheckOutput->info("Delete action returned $test");

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
        $this->spiderCheckOutput->info("Saving pet $pet->id new status: $status");
        $pet->status = $status;

        return $pet->save();
    }
}
