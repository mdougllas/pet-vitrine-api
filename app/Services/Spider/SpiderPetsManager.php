<?php

namespace App\Services\Spider;

use App\Models\Pet;
use Illuminate\Support\Str;
use App\Services\Spider\HttpRequest;
use Illuminate\Support\Facades\Redis;

class SpiderPetsManager
{
    /**
     * Blueprint for SpiderPetsManager.
     *
     * @param \App\Services\Spider\HttpRequest $spider
     * @return void
     */
    public function __construct(HttpRequest $spider, Pet $pet)
    {
        $this->spider = $spider;
        $this->pet = $pet;
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
        $pets = collect($response->result->animals);

        $pets->each(function ($pet) {
            $petData = $pet->animal;

            if ($this->getLatestParsedId() >= $petData->id) {
                return false;
            }

            $this->checkPhotos($petData->photo_urls);
            $this->savePet($pet);

            sleep(2);
        });

        $latestPet = collect($response->result->animals)->first()->animal;

        if ($latestPet->id > $this->getLatestParsedId()) {
            $this->setLatestParsedId($latestPet->id);
        }

        return false;
    }

    /**
     * List all pets available.
     *
     * @param  Illuminate\Database\Eloquent\Collection
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function listPets()
    {
        $result = Redis::lrange('pets', 0, Redis::llen('pets'));
        $pets = collect($result);
        $data = $pets->map(fn ($pet) => json_decode($pet));

        return $data;
    }

    /**
     * Store the id for the latest parsed pet.
     *
     * @return Illuminate\Database\Eloquent\Collection
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function setLatestParsedId($id)
    {
        Redis::set('latest_parsed_pet', $id);
    }

    /**
     * Retrieve the id for the latest parsed pet.
     *
     * @return Illuminate\Database\Eloquent\Collection
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function getLatestParsedId()
    {
        return (int) Redis::get('latest_parsed_pet');
    }

    /**
     * Check if the pet has photos available.
     *
     * @return void||true;
     */
    private function checkPhotos($urls)
    {
        if (count($urls) < 1) {
            return true;
        }
    }

    /**
     * Retrieve the id for the latest parsed pet.
     *
     * @return Illuminate\Database\Eloquent\Collection
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function savePet($pet)
    {
        $this->getPetData($pet);
        $this->pet->save();
        Redis::lpush('pets', json_encode($pet));
    }

    /**
     * Check if the pet has photos available.
     *
     * @return void;
     */
    private function getPetData($pet)
    {
        $petData = $pet->animal;

        $this->pet->uuid = Str::uuid();
        $this->pet->ad_id = null;
        $this->pet->age = $petData->age;
        $this->pet->breed = $petData->primary_breed->name;
        $this->pet->description = $petData->description;
        $this->pet->name = $petData->name;
        $this->pet->photo_urls = $petData->photo_urls;
        $this->pet->sex = $petData->sex;
        $this->pet->species = $petData->species->name;
        $this->pet->status = $petData->adoption_status;
        $this->pet->organization_id = $pet->organization->display_id;
        $this->pet->petfinder_id = $petData->id;
    }
}
