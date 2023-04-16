<?php

namespace App\Console\Commands;

use App\Services\Spider\SpiderCheckStatus;
use Illuminate\Console\Command;

class StartPetStatusCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spider:check-status';

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
    public function handle()
    {
        $spider = new SpiderCheckStatus($this);

        return $spider->startPetStatusCheck();
    }
}
