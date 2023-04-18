<?php

namespace App\Services\Spider;

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
     * Undocumented function
     *
     * @return void
     */
    public function startPetStatusCheck()
    {
        $this->output->info('Starting loop from newest registered pet and checking status.');
        $this->loopThroughPets();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    private function loopThroughPets()
    {
        $range = Collection::range(Pet::max('id'), 1, -1);

        $range->takeWhile(function ($id) {
            $this->pauseJob();

            $pet = Pet::find($id);

            $this->output->info("Pet ID $id.");

            return $this->checkPetStatus($pet, $id);
        });

        $this->startPetStatusCheck();
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
        // $response = $this->spider->getPet(5478);

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
            ? $this->updatePetStatus($pet, $status)
            : $id != 1;
    }

    /**
     * Undocumented function
     *
     * @param [type] $pet
     * @param [type] $status
     * @return void
     */
    private function updatePetStatus($pet, $status = 'adopted')
    {
        $this->output->info("Saving pet $pet->id new status: $status");
        $pet->status = $status;

        return $pet->save();
    }

    /**
     *
     */
    public function startUrlsCheck()
    {
        dd('url check started');
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
