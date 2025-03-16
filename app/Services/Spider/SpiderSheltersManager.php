<?php

namespace App\Services\Spider;

use App\Models\Organization;
use App\Models\SpiderJob;
use App\Services\Spider\HttpRequest;
use App\Traits\Spider\UseSetOutput;
use Illuminate\Support\Collection;

class SpiderSheltersManager
{
    use UseSetOutput;

    private $manager;

    /**
     * @property \App\Services\Spider\HttpRequest $spider
     */
    private $spider;

    /**
     * Blueprint for SpiderShelterManager.
     *
     * @param HttpRequest $spider
     * @param SpiderDataManager $manager
     * @return void
     */
    public function __construct(HttpRequest $spider, SpiderDataManager $manager)
    {
        $this->spider = $spider;
        $this->manager = $manager;
    }

    /**
     * Start the jobs to scrape and store data.
     *
     * @return void;
     */
    public function parseShelters(): void
    {
        $organizations = $this->spider->getOrganizations();
        $organizationCount = $this->getNumberOfShelters();

        $pagination = collect($organizations->get('pagination'));
        $totalShelters = $pagination->get('total_count');
        $totalPages = $pagination->get('total_pages');

        if ($organizationCount === $totalShelters) {
            $this->output->info("No new organizations registered.");

            return;
        }

        $page = 0;

        while ($totalPages > 0) {
            $totalPages--;
            $page++;

            $this->collectShelters($page);

            sleep(1);
        }

        $this->setNumberOfShelters($totalShelters);
    }

    /**
     * Undocumented function
     *
     * @param integer $page
     * @return void
     */
    private function collectShelters(int $page): void
    {
        $this->output->info("Parsing page # $page.");

        $response = $this->spider->getOrganizations($page);
        $shelters = collect($response->get('organizations'));

        $shelters->each(fn ($shelter) => $this->analizeShelter(collect($shelter)));
    }

    private function analizeShelter(Collection $shelter): bool
    {
        $name = $shelter->get('name');
        $id = $shelter->get('id');

        $this->output->info("Analizing shelter $name");

        if ($shelter->get('address')['country'] !== 'US') {
            $this->output->info('Outside the US. Skipping saving the shelter.');
        }

        if ($this->shelterExists($id)) {
            $this->output->warn("Shelter $id already on DB. Skipping saving the shelter.");

            return true;
        }

        if ($this->checkDuplicatedByName($shelter) !== false) {
            $this->output->warn("This is a duplicate. Skipping saving the shelter.");

            return true;
        }

        $this->saveShelter($shelter);

        return true;
    }

    /**
     * Retrieve the id for the latest parsed pet.
     *
     * @param string $id
     * @return bool
     */
    private function shelterExists(string $id): bool
    {
        return Organization::where('petfinder_id', $id)->exists();
    }

    /**
     * Retrieve the id for the latest parsed pet.
     *
     * @param
     * @return int | false
     */
    private function checkDuplicatedByName($shelter): int | false
    {
        $location = collect($shelter->get('address'));
        $nameMatches = Organization::where('name', $shelter->get('name'))->get();

        if (!$nameMatches->isEmpty()) {
            $duplicate = $this->doubleCheckDuplicate($location, $nameMatches);

            return $duplicate;
        }

        return false;
    }

    private function doubleCheckDuplicate($location, $nameMatches)
    {
        $checkDuplicate = $nameMatches->map(function ($match) use ($location) {
            $address1Matches = $match->address_1 == $location->get('address1');
            $address2Matches = $match->address_2 == $location->get('address2');
            $cityMatches = $match->city == $location->get('city');
            $stateMatches = $match->state == $location->get('state');
            $postalCodeMatches = $match->postal_code == $location->get('postal_code');

            return $address1Matches &&
                $address2Matches &&
                $cityMatches &&
                $stateMatches &&
                $postalCodeMatches;
        });

        return $checkDuplicate->search(true);
    }

    /**
     * Retrieve the id for the latest parsed pet.
     *
     * @return bool
     */
    private function saveShelter($shelter): bool
    {
        $name = $shelter->get('name');
        $id = $shelter->get('id');

        $this->output->info("Saving shelter $name");

        $shelterData = $this->manager->getShelterData($shelter);

        if (! $shelterData) {
            $this->output->info("The zipcode for shelter ($name) with id $id is missing or wrong. Skipping saving the shelter.");

            return true;
        }

        return $shelterData->save();
    }

    /**
     * Store the id for the latest parsed pet.
     *
     * @return void
     */
    private function setNumberOfShelters($qty): void
    {
        $spiderJob = SpiderJob::first();
        $spiderJob->number_of_shelters = $qty;

        $spiderJob->save();
    }

    /**
     * Retrieve the id for the latest parsed pet.
     *
     * @return int
     */
    private function getNumberOfShelters(): int
    {
        return SpiderJob::first()->number_of_shelters;
    }
}
