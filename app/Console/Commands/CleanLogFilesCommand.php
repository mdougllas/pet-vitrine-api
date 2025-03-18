<?php

namespace App\Console\Commands;

use App\Services\Spider\SpiderCleanLogFiles;
use Illuminate\Console\Command;

class CleanLogFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spider:clean-log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(SpiderCleanLogFiles $spider)
    {
        $spider->setSpiderCleanLogOuput($this);

        return $spider->cleanLogFiles();
    }
}
