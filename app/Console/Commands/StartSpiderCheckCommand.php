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
    protected $signature = 'spider:check {status?}';

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
        $status = $this->argument('status');

        $spider->setOutput($this);

        return $status
            ? $spider->startPetCheck('status-check')
            : $spider->startPetCheck('check-urls');
    }
}
