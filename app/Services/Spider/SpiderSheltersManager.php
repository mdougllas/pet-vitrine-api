<?php

namespace App\Services\Spider;

use App\Models\Organization;
use App\Services\Spider\HttpRequest;
use App\Services\Spider\SpiderDataManager;
use App\Traits\Spider\UseSetOutput;

class SpiderSheltersManager
{
    use UseSetOutput;

    /**
     * @property \App\Services\Spider\HttpRequest $spider
     */
    private $spider;

    /**
     * @property integer $loop
     */
    private $loop = 1;

    /**
     * Blueprint for SpiderShelterManager.
     *
     * @param object $output
     * @return void
     */
    public function __construct(HttpRequest $spider)
    {
        $this->spider = $spider;
    }

    /**
     * Start the jobs to scrape and store data.
     *
     * @return bool;
     * @return Illuminate\Database\Eloquent\Collection;
     */
    public function parseShelters($page): bool
    {
        $response = $this->spider->getOrganizations($page);
        $shelters = collect($response->get('organizations'));

        $shelters->each(function ($shelter) {
            $shelter = collect($shelter);

            $this->output->info("This is shelter loop # $this->loop");

            $this->loop += 1;

            if ($shelter->get('address')['country'] !== 'US') {
                $this->output->info('Outside the US. Skipping saving the shelter.');
            }

            if ($this->shelterExists($shelter->get('id'))) {
                $id = $shelter->get('id');
                $this->output->warn("Shelter $id already on DB. Skipping saving the shelter.");

                return true;
            }

            if ($this->checkDuplicatedByName($shelter) !== false) {
                $this->output->warn("This is a duplicate. Skipping saving the shelter.");

                return true;
            }

            $this->saveShelter($shelter);
        });

        return false;
    }

    /**
     * Retrieve the id for the latest parsed pet.
     *
     * @return Illuminate\Database\Eloquent\Collection
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function shelterExists($id)
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
        $location = $shelter->get('address');
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
     * @return Illuminate\Database\Eloquent\Collection
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function saveShelter($shelter)
    {
        $this->output->info("SAVE SHELTER CALLED");

        $shelterData = SpiderDataManager::getShelterData($shelter);
        $shelterData->save();
    }
}
