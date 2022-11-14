<?php

namespace App\Console\Commands;

use App\Services\Spider\SpiderJobsManager;
use Illuminate\Console\Command;

class StartJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spider:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Start spider's job.";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(SpiderJobsManager $spider)
    {
        return $spider->startJobs();
    }
}
