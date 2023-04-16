<?php

namespace App\Services\Spider;

use App\Models\Pet;
use Illuminate\Support\Facades\Http;

class SpiderCheckStatus
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

    public function __construct($output)
    {
        $this->spider = new HttpRequest;
        $this->output = $output;
    }

    public function startPetStatusCheck()
    {
        $test = $this->spider->getPet(3483368);

        dd($test);
    }
}
