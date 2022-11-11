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
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function parseMetaInfo($result)
    {
        $pages = collect()
            ->range(1, $result->pagination->total_pages)
            ->sortDesc();
        $pages->each(fn ($page) => $this->pets->parsePets($page));
    }

    private function sortResult($result)
    {
        return collect($result->organizations)->sortBy('id');
    }
}
