<?php

namespace App\Services\Spider;

use App\Models\Organization;
use App\Models\SpiderJob;
use App\Traits\Spider\UseSetOutput;

class SpiderJobsManager
{
    use UseSetOutput;

    /**
     * @property \App\Services\Spider\HttpRequest $spider
     */
    private $spider = null;

    /**
     * @property \App\Services\Spider\SpiderSheltersManager $shelters
     */
    private $shelters = null;

    /**
     * @property \App\Services\Spider\SpiderPetsManager $shelters
     */
    private $pets = null;

    /**
     * @property integer $cicle
     */
    private $cicle = 1;

    /**
     * Blueprint for SpiderJobsManager.
     *
     * @param \App\Services\Spider\HttpRequest $spider
     * @return void
     */
    public function __construct(HttpRequest $spider, SpiderSheltersManager $shelters, SpiderPetsManager $pets)
    {
        $this->spider = $spider;
        $this->shelters = $shelters;
        $this->pets = $pets;
    }

    /**
     * Start the jobs to scrape and store data.
     *
     * @return int
     */
    public function startJobs()
    {
        $this->output->info("Spider jobs initiated.");

        if ($this->getJobRunning()) {
            $this->output->warn("Spider is already running.");
            return 0;
        }

        $organizations = $this->spider->getOrganizations();

        $this->parseOrganizations($organizations);

        $lastOrganizationId = $this->getLatestParsedPage();
        $organizationId = $lastOrganizationId + 1;
        $organization = Organization::find($lastOrganizationId + 1);

        if (empty($organization)) {
            $this->setLatestParsedPage($lastOrganizationId + 1);

            return 0;
        }

        $pets = $this->spider->getPetsByOrganization($organization->petfinder_id);

        if (!$pets || !$pets->result) {
            $this->output->warn("No pets received from this request. Skipping parsing pets.");

            return 0;
        }

        $this->parsePetsInfo($pets, $organization->petfinder_id);

        if ($organizationId >= $organizations->pagination->total_count) {
            $this->setLatestParsedPage(0);

            return 0;
        }

        $this->setLatestParsedPage($organizationId);
        $this->setJobRunning(false);

        $this->output->info("Spider jobs finished.");

        return 0;
    }

    /**
     * Parse meta-data to collect number of pages.
     *
     * @param  json  $result
     * @return mixed int
     */
    private function parseOrganizations($result)
    {
        $totalShelters = $result->pagination->total_count;
        $totalPages = $result->pagination->total_pages;
        $numberOfShelters = $this->getNumberOfShelters();

        if ($numberOfShelters == $totalShelters) {
            $this->output->warn("No new shelters where created.");

            return;
        }

        $pages = collect()->range(1, $totalPages);

        $pages->each(function ($page) {
            $this->shelters->parseShelters($page);

            $this->pauseJob();
        });

        $this->setNumberOfShelters($totalShelters);

        return;
    }

    /**
     * Parse meta-data to collect number of pages.
     *
     * @param  json  $result
     * @return mixed int
     */
    private function parsePetsInfo($pets, $organizationId)
    {
        $fromPage = 1;
        $toPage = $pets->result->pagination->total_pages;

        $pages = collect()
            ->range($fromPage, $toPage);

        $pages->each(function ($page) use ($toPage, $organizationId) {
            $this->output->info("Parsing Page $page");
            $this->output->info("Cicle $this->cicle");

            $this->pets->parsePets($page, $organizationId);
            $this->cicle += 1;

            if ($page >= $toPage) {
                return false;
            }

            $this->pauseJob();
        });

        return;
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

    /**
     * Store the id for the latest parsed pet.
     *
     * @return Illuminate\Database\Eloquent\Collection
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function setLatestParsedPage($page)
    {
        $spiderJob = SpiderJob::first();
        $spiderJob->last_page_processed = $page;

        $spiderJob->save();
    }

    /**
     * Retrieve the id for the latest parsed pet.
     *
     * @return int
     */
    private function getLatestParsedPage(): int
    {
        return SpiderJob::first()->last_page_processed;
    }

    /**
     * Store the id for the latest parsed pet.
     *
     * @return Illuminate\Database\Eloquent\Collection
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function setNumberOfShelters($qty)
    {
        $spiderJob = SpiderJob::first();
        $spiderJob->number_of_shelters = $qty;

        $spiderJob->save();
    }

    /**
     * Retrieve the id for the latest parsed pet.
     *
     * @return Illuminate\Database\Eloquent\Collection
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function getNumberOfShelters()
    {
        return SpiderJob::first()->number_of_shelters;
    }

    /**
     * Pause the job for a random
     * number of seconds
     * from 5 to 15.
     *
     * @return void
     */
    private function pauseJob()
    {
        // $randomNumber = collect([5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15])->random();
        $randomNumber = collect([2, 3])->random();

        $this->output->info("Pause for $randomNumber seconds.");

        sleep($randomNumber);
    }
}
