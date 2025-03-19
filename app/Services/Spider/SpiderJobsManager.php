<?php

namespace App\Services\Spider;

use App\Models\SpiderJob;
use App\Traits\Spider\UseSetOutput;

class SpiderJobsManager
{
    use UseSetOutput;

    /**
     * @property \App\Services\Spider\HttpRequest $spider
     */
    private $spider;

    /**
     * @property \App\Services\Spider\SpiderSheltersManager $shelters
     */
    private $shelters;

    /**
     * @property \App\Services\Spider\SpiderPetsManager $shelters
     */
    private $pets;

    private $logs;

    /**
     * Blueprint for SpiderJobsManager.
     *
     * @param \App\Services\Spider\HttpRequest $spider
     * @return void
     */
    public function __construct(
        HttpRequest $spider,
        SpiderSheltersManager $shelters,
        SpiderPetsManager $pets,
        SpiderCleanLogFiles $logs
    )
    {
        $this->spider = $spider;
        $this->shelters = $shelters;
        $this->pets = $pets;
        $this->logs = $logs;
    }

    /**
     * Start the jobs to scrape and store data.
     *
     * @return int
     */
    public function startJobs()
    {
        // if ($this->getJobRunning()) {
        //     $this->spiderOutput->warn("Spider is already running.");

        //     return 0;
        // }

        $this->spiderOutput->info("Spider jobs initiated.");

        $this->setJobRunning(1);
        $this->spider->setSpiderOutput($this->spiderOutput);
        $this->shelters->setSpiderOutput($this->spiderOutput);
        $this->pets->setSpiderOutput($this->spiderOutput);

        $this->pets->parsePets();
        $this->shelters->parseShelters();
        $this->logs->cleanLogFiles();

        $this->spiderOutput->info("Spider jobs finished.");
        $this->setJobRunning(0);

        return 0;
    }

    /**
     * Store the id for the latest parsed pet.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function getJobRunning()
    {
        return SpiderJob::first()->job_running;
    }

    /**
     * Store the id for the latest parsed pet.
     *
     * @return void
     */
    private function setJobRunning($state)
    {
        $spiderJob = SpiderJob::first();
        $spiderJob->job_running = $state;

        $spiderJob->save();
    }
}
