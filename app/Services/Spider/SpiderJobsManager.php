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
        var_dump('Start Jobs');
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
        Var_dump('Parse Meta Info');

        $fromPage = $this->getLatestParsedPage();
        $toPage = $result->pagination->total_pages;
        var_dump("Current page", $fromPage);
        var_dump("Total Pages on request", $toPage);

        $pages = collect()
            ->range($fromPage, $toPage);

        var_dump("Number of pages to parse", $pages->count());

        $pages->each(function ($page) {
            if ($this->cicle === 2) return false;

            var_dump("Parse meta info cicle", $this->cicle);
            var_dump('For Page', $page);
            $this->pets->parsePets($page);
            $this->cicle += 1;
        });

        $this->setLatestParsedPage($result->pagination->total_pages - 5);
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
        var_dump('Set Latest Parsed Page');
        var_dump('Page', $page);
        var_dump('Last Parsed Page', $this->getLatestParsedPage());
        Redis::set('latest_parsed_pet', $page);
    }

    /**
     * Retrieve the id for the latest parsed pet.
     *
     * @return Illuminate\Database\Eloquent\Collection
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function getLatestParsedPage()
    {
        var_dump('Get Latest Parsed Page');
        var_dump((int) Redis::get('latest_parsed_pet'));
        return (int) Redis::get('latest_parsed_pet');
    }
}
