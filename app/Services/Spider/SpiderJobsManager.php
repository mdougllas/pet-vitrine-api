<?php

namespace App\Services\Spider;

use App\Models\SpiderJob;
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
    public function __construct(HttpRequest $spider, SpiderPetsManager $pets)
    {
        $this->spider = $spider;
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

        $response = $this->spider->getPets();
        $this->parseMetaInfo($response->result);

        $this->setJobRunning(false);

        echo ("Spider jobs finished.");
    }

    /**
     * Parse meta-data to collect number of pages.
     *
     * @param  json  $result
     * @return mixed void | null
     */
    private function parseMetaInfo($result)
    {
        $fromPage = $this->getLatestParsedPage();
        $toPage = $result->pagination->total_pages;

        $pages = collect()
            ->range($fromPage, $toPage);

        $pages->each(function ($page) {
            echo "Parsing Page $page" . PHP_EOL;
            echo "Cicle $this->cicle \n" . PHP_EOL;
            $this->pets->parsePets($page);
            $this->cicle += 1;

            if ($this->cicle >= 3) {
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
