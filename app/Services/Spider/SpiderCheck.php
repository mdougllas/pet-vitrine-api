<?php

namespace App\Services\Spider;

use App\Models\Pet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

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
     * @property object $output
     */
    private $dataManager;

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

    public function startPetStatusCheck()
    {
        $response = $this->spider->getPet(3483368);
        $pet = collect($response->result->animals)
            ->first()
            ->animal;

        dd($pet->adoption_status);
    }

    public function startUrlsCheck()
    {
        dd('url check started');
    }
}
