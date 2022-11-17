<?php

namespace App\Services\Spider;

use App\Models\SpiderJob;
use App\Models\Organization;
use App\Services\Spider\HttpRequest;
use App\Services\Spider\SpiderPetsManager;

class SpiderJobsManager
{
    /**
     * Blueprint for SpiderJobsManager.
     *
     * @param \App\Services\Spider\HttpRequest $spider
     * @return void
     */
    public function __construct(HttpRequest $spider, SpiderPetsManager $pets, SpiderSheltersManager $shelters)
    {
        $this->spider = $spider;
        $this->shelters = $shelters;
        $this->pets = $pets;
        $this->cicle = 1;
    }

    /**
     * Start the jobs to scrape and store data.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function startJobs()
    {
        echo "Spider jobs initiated." . PHP_EOL;

        if ($this->getJobRunning()) {
            echo "Spider is already running." . PHP_EOL;
            exit;
        }

        $this->setJobRunning(true);

        $organizations = $this->spider->getOrganizations();
        $pets = $this->spider->getPets();

        $this->parseSheltersInfo($organizations);
        $this->parsePetsInfo($pets->result);
        $this->setJobRunning(false);

        echo "Spider jobs finished." . PHP_EOL;
    }

    /**
     * Parse meta-data to collect number of pages.
     *
     * @param  json  $result
     * @return mixed void | null
     */
    public function parseSheltersInfo($result)
    {
        $totalShelters = $result->pagination->total_count;
        $totalPages = $result->pagination->total_pages;
        $sheltersOnDatabase = Organization::count();

        if ($sheltersOnDatabase != $totalShelters) {
            echo "No new shelters where created." . PHP_EOL;

            return;
        }

        $pages = collect()->range(1, $totalPages);

        $pages->each(function ($page) {
            $this->shelters->parseShelters($page);
        });
    }

    /**
     * Parse meta-data to collect number of pages.
     *
     * @param  json  $result
     * @return mixed void | null
     */
    private function parsePetsInfo($result)
    {
        $fromPage = $this->getLatestParsedPage();
        $toPage = $result->pagination->total_pages;

        dd($toPage);

        $pages = collect()
            ->range($fromPage, $toPage);

        $pages->each(function ($page) use ($toPage) {
            echo "Parsing Page $page" . PHP_EOL;
            echo "Cicle $this->cicle \n" . PHP_EOL;
            $this->pets->parsePets($page);
            $this->cicle += 1;

            if ($page >= $toPage || $this->cicle >= 100) {
                $this->setLatestParsedPage($page);

                return false;
            }

            $randomNumber = collect([5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15])->random();

            echo "Pause for $randomNumber seconds." . PHP_EOL;

            sleep($randomNumber);
        });

        // $this->setLatestParsedPage($result->pagination->total_pages - 5);
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
     * @return Illuminate\Database\Eloquent\Collection
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function getLatestParsedPage()
    {
        return SpiderJob::first()->last_page_processed;
    }
}
