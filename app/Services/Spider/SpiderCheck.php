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
     * Start checking pet for status and URLs
     *
     * @return int
     */
    public function startSpiderCheck(): int
    {
        $this->spider->setSpiderCheckOutput($this->spiderCheckOutput);
        $this->spiderCheckOutput->info('Pets check started.');
        $this->spiderCheck();
        $this->spiderCheckOutput->info('Pets check finished.');

        return 0;
    }

    /**
     * Dispatch pet and organization's jobs.
     *
     * @return void
     */
    private function spiderCheck(): void
    {
        Pet::chunk(200, fn (Collection $pets) => $this->checkPetsInChunks($pets));
        Organization::chunk(200, fn (Collection $organizations) => $this->checkOrganizationsInChunks($organizations));
    }

    /**
     * Undocumented function
     *
     * @param Collection $pets
     * @return void
     */
    private function checkPetsInChunks(Collection $pets): void
    {
        $pets->each(function (Pet $pet) {
            if ($pet->created_at->diffInDays(now()) > 60) {
                $pet->status = 'adopted';

                return;
            }

            $response = $this->spider->getPet($pet->petfinder_id);
            $fetchedPet = collect($response->get('animal'));

            if ($pet->photos_url->isEmpty()) {
                $this->checkForPetPhoto($pet, $fetchedPet);
            }
        });
    }

    /**
     * Undocumented function
     *
     * @param Pet $pet
     * @param Collection $fetchedPet
     * @return void
     */
    private function checkForPetPhoto(Pet $pet, Collection $fetchedPet): void
    {
        $photo = collect($fetchedPet->get('primary_photo_cropped'));

        if ($photo) {
            $pet->photo_urls = [$photo->value('medium')];
        }
    }

    /**
     * Undocumented function
     *
     * @param Collection $organizations
     * @return void
     */
    private function checkOrganizationsInChunks(Collection $organizations): void
    {
        $organizations->each(function (Organization $organization) {
            $id = $organization->pet_finder_id;

            $exists = Organization::find($id);

            if (! $exists) {
                $organization->delete;
            }
        });
    }
}
