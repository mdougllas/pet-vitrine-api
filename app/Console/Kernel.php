<?php

namespace App\Console;

use App\Console\Commands\StartSpiderCheckCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\StartSpiderCommand;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(StartSpiderCommand::class)
            ->everyMinute()
            ->sendOutputTo($this->getSpiderLogFilePath())
            ->withoutOverlapping();

        $schedule->command(StartSpiderCheckCommand::class, ['status'])
            ->runInBackground()
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Generate a different file name for
     * each day and return the full path.
     *
     * @return string
     */
    private function getSpiderLogFilePath(): string
    {
        $fileName = now()->format('m-d-Y_H:i:s');

        return storage_path("logs/spider/$fileName.log");
    }
}
