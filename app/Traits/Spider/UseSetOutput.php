<?php

namespace App\Traits\Spider;

use App\Console\Commands\StartSpiderCommand;
use App\Console\Commands\StartSpiderCheckCommand;

trait UseSetOutput
{
    /**
     * Undocumented variable
     *
     * @var StartSpiderCommand
     */
    protected StartSpiderCommand $spiderOutput;

    /**
     * Undocumented variable
     *
     * @var StartSpiderCheckCommand
     */
    protected StartSpiderCheckCommand $spiderCheckOutput;

    /**
     * Undocumented function
     *
     * @param StartSpiderCommand $output
     * @return void
     */
    public function setSpiderOutput(StartSpiderCommand $spiderOutput): void
    {
        $this->spiderOutput = $spiderOutput;
    }

    /**
     * Undocumented function
     *
     * @param StartSpiderCommand $output
     * @return void
     */
    public function setSpiderCheckOutput(StartSpiderCheckCommand $spiderCheckOutput): void
    {
        $this->spiderCheckOutput = $spiderCheckOutput;
    }
}
