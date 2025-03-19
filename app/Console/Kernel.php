<?php

namespace App\Console;

use App\Console\Commands\CleanLogFilesCommand;
use App\Console\Commands\StartSpiderCheckCommand;
use App\Console\Commands\StartSpiderCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
            ->dailyAt('23:00')
            ->sendOutputTo($this->getSpiderLogFilePath())
            ->withoutOverlapping();

        $schedule->command(StartSpiderCheckCommand::class)
            ->dailyAt('9:00')
            ->withoutOverlapping();

        $schedule->command(CleanLogFilesCommand::class)
            ->dailyAt('12:00')
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
