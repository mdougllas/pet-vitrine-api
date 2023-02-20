<?php

namespace App\Services\Spider;

use App\Models\Organization;
use App\Services\Spider\HttpRequest;
use App\Services\Spider\SpiderDataManager;

class SpiderSheltersManager
{
    /**
     * @property \App\Services\Spider\ $spider
     */
    private $spider = null;

    /**
     * @property App\Services\Spider\SpiderDataManager $dataManager
     */
    private $dataManager = null;

    /**
     * @property integer $loop
     */
    private $loop = 0;

    /**
     * Blueprint for SpiderPetsManager.
     *
     * @param \App\Services\Spider\HttpRequest $spider
     * @return void
     */
    public function __construct(HttpRequest $spider, SpiderDataManager $dataManager)
    {
        $this->spider = $spider;
        $this->dataManager = $dataManager;
        $this->loop = 1;
    }

    /**
     * Start the jobs to scrape and store data.
     *
     * @return Illuminate\Database\Eloquent\Collection;
     * @return Illuminate\Database\Eloquent\Collection;
     */
    public function parseShelters($page)
    {
        $response = $this->spider->getOrganizations($page);
        $shelters = collect($response->organizations);

        $shelters->each(function ($shelter) {
            echo "This is shelter loop # $this->loop" . PHP_EOL;
            $this->loop += 1;

            if ($this->shelterExists($shelter->display_id)) {
                echo "Shelter $shelter->display_id already on DB. Skipping saving the shelter. \n" . PHP_EOL;
                return true;
            }

            if ($this->checkDuplicatedShelter($shelter)) {
                echo "This is a dulicate. Skipping saving the shelter. \n" . PHP_EOL;
                return true;
            }

            $this->saveShelter($shelter);
        });

        return false;
    }

    /**
     * List all pets available.
     *
     * @param  Illuminate\Database\Eloquent\Collection
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function listShelters()
    {
        //todo
        return;
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
     * @return Illuminate\Database\Eloquent\Collection
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function checkDuplicatedShelter($shelter)
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
        echo "SAVE SHELTER CALLED \n" . PHP_EOL;
        $shelterData = $this->dataManager->getShelterData($shelter);

        $shelterData->save();
    }
}
