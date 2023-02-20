<?php

namespace App\Services\Spider;

use App\Models\Organization;
use App\Services\Spider\HttpRequest;
use App\Services\Spider\SpiderDataManager;
use Illuminate\Support\Collection;

class SpiderSheltersManager
{
    /**
     * @property \App\Services\Spider\HttpRequest $spider
     */
    private $spider;

    /**
     * @property integer $loop
     */
    private $loop = 1;

    /**
     * @property object $output
     */
    private $output;

    /**
     * Blueprint for SpiderPetsManager.
     *
     * @param object $output
     * @return void
     */
    public function __construct($output)
    {
        $this->spider = new HttpRequest;
        $this->loop = 1;
        $this->output = $output;
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
        $shelters = collect($response->organizations);

        $shelters->each(function ($shelter) {
            $this->output->info("This is shelter loop # $this->loop");

            $this->loop += 1;

            if ($this->shelterExists($shelter->display_id)) {
                $this->output->warn("Shelter $shelter->display_id already on DB. Skipping saving the shelter.");

                return true;
            }

            if ($this->checkDuplicatedShelter($shelter)) {
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
     * @return \Illuminate\Support\Collection | false
     */
    private function checkDuplicatedShelter($shelter): Collection | false
    {
        $location = $shelter->location->address;
        $nameMatches = Organization::where('name', $shelter->name)->get();

        if (!$nameMatches->isEmpty()) {
            $checkDuplicate = $nameMatches->map(function ($match) use ($location) {
                $address1Matches = $match->address_1 == $location->address1;
                $address2Matches = $match->address_2 == $location->address2;
                $cityMatches = $match->city == $location->city;
                $stateMatches = $match->state == $location->state;
                $postalCodeMatches = $match->postal_code == $location->postal_code;

                return $address1Matches &&
                    $address2Matches &&
                    $cityMatches &&
                    $stateMatches &&
                    $postalCodeMatches;
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
    private function saveShelter($shelter)
    {
        $this->output->info("SAVE SHELTER CALLED");

        $shelterData = SpiderDataManager::getShelterData($shelter);
        $shelterData->save();
    }
}
