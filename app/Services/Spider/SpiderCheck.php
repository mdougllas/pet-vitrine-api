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
     * @property HttpRequest $spider
     */
    private HttpRequest $spider;

    /**
     * Undocumented variable
     *
     * @var integer
     */
    private int $adoptedPetCount = 0;

    /**
     * Undocumented variable
     *
     * @var integer
     */
    private int $shelterExists = 0;

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
        Pet::latest()
            ->chunk(200, fn (Collection $pets) => $this->checkPetsInChunks($pets));

        Organization::latest()
            ->chunk(200, fn (Collection $organizations) => $this->checkOrganizationsInChunks($organizations));
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
            $name = $pet->name;
            $status = $pet->status;

            if ($status === 'adopted') {
                $this->adoptedPetCount++;
            }

            if ($status === 'adoptable') {
                $this->adoptedPetCount = 0;
            }

            if ($this->adoptedPetCount >= 100) {
                $this->spiderCheckOutput->info("All adoptable pets processed. Aborting spider.");

                return false;
            }

            if ($this->spider->requestCount >= 1000) {
                $this->spiderCheckOutput->info("Reached request limit. Aborting spider.");

                return false;
            }

            $this->spiderCheckOutput->info("Checking pet name $name.");

            if ($pet->created_at->diffInDays(now()) > 60) {

                $this->spiderCheckOutput->info("Changing pet name $name status to adopted.");
                $pet->status = 'adopted';
                $pet->save();

                return;
            }

            $response = $this->spider->getPet($pet->petfinder_id);
            $fetchedPet = collect($response->get('animal'));

            if (collect($pet->photo_urls)->isEmpty()) {
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
        $name = $pet->name;

        $this->spiderCheckOutput->info("Checking for photo to pet $name.");

        $photo = collect($fetchedPet->get('primary_photo_cropped'));

        if ($photo) {
            $pet->photo_urls = [$photo->value('medium')];
            $pet->save();
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
            $name = $organization->name;

            if ($this->shelterExists >= 100) {
                $this->spiderCheckOutput->info("All fake shelters processed. Aborting spider.");

                return false;
            }

            if ($this->spider->requestCount >= 1000) {
                $this->spiderCheckOutput->info("Reached request limit. Aborting spider.");

                return false;
            }

            $exists = $this->spider->getOrganization($id);

            if (! $exists) {
                $this->shelterExists = 0;
                $this->spiderCheckOutput->info("Organization $name does not exist. Deleting.");
                $organization->delete;
            }
        });
    }
}
