<?php

namespace App\Services\Spider;

use App\Services\Spider\HttpRequest;
use Illuminate\Support\Facades\Redis;
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
        $response = $this->spider->getPets();
        $this->parseMetaInfo($response->result);
        $data = $this->pets->listPets();

        return $data;
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
            $this->pets->parsePets($page);
            $this->cicle += 1;

            if ($this->cicle === 11) {
                $this->setLatestParsedPage($page - 1);

                return false;
            }
        });

        // $this->setLatestParsedPage($result->pagination->total_pages - 5);
    }

    private function sortResult($result)
    {
        return collect($result->organizations)->sortBy('id');
    }

    /**
     * Store the id for the latest parsed pet.
     *
     * @return Illuminate\Database\Eloquent\Collection
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function setLatestParsedPage($page)
    {
        Redis::set('latest_parsed_page', $page);
    }

    /**
     * Retrieve the id for the latest parsed pet.
     *
     * @return Illuminate\Database\Eloquent\Collection
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function getLatestParsedPage()
    {
        return (int) Redis::get('latest_parsed_page');
    }
}
