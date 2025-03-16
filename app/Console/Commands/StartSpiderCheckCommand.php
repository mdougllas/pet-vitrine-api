<?php

namespace App\Console\Commands;

use App\Services\Spider\SpiderCheck;
use Illuminate\Console\Command;

class StartSpiderCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spider:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Start status check job.";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(SpiderCheck $spider)
    {
        $spider->setSpiderCheckOutput($this);

        return $spider->startSpiderCheck('check-status');
    }
}
