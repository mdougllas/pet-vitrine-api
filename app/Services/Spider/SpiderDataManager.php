<?php

namespace App\Services\Spider;

use Illuminate\Support\Facades\Redis;
use App\Services\Spider\SpiderPetsManager;

class SpiderDataManager
{
    /**
     * Blueprint for SpiderJobsManager.
     *
     * @param \App\Services\Spider\SpiderPetsManager $pets
     * @return void
     */
    public function __construct(SpiderPetsManager $pets)
    {
        $this->pets = $pets;
    }

    /**
     * Start the jobs to scrape and store data.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function startJobs()
    {
    }
}
