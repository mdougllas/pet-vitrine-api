<?php

namespace App\Traits\Spider;

use App\Console\Commands\StartSpiderCommand;

trait UseSetOutput
{
    /**
     * Undocumented variable
     *
     * @var StartSpiderCommand
     */
    protected StartSpiderCommand $output;

    /**
     * Undocumented function
     *
     * @param StartSpiderCommand $output
     * @return void
     */
    public function setOutput($output): void
    {
        $this->output = $output;
    }
}
