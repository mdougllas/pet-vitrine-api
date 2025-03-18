<?php

namespace App\Services\Spider;

use App\Traits\Spider\UseSetOutput;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SpiderCleanLogFiles
{
    use UseSetOutput;

    public $output;

    public function __construct()
    {

    }

    public function setSpiderCleanLogOuput($output)
    {
        $this->output = $output;
    }

    public function cleanLogFiles()
    {
        $this->output->info("Started clening log files.");
        $files = collect(File::files(storage_path('logs/spider/')));

        $files->each(fn ($file) => $this->checkDiffFromFileName($file));
    }

    private function checkDiffFromFileName($file)
    {
        $fileName = $file->getFileName();
        $fileNameDateString = Str::before($fileName, '.log');
        $fileNameDate = Str::replace('-', '/', Str::replace('_', ' ', $fileNameDateString));
        $fileEpoch = Carbon::parse($fileNameDate, 'UTC');
        $now = Carbon::now('UTC');
        $diff = $now->diffInDays($fileEpoch);

        if ($diff >= 20) {
            $this->output->info("Found an old file.");
            $this->deleteLogFile($file);
        }
    }

    private function deleteLogFile($file)
    {
        $this->output->info("Deleting old file.");

        File::delete($file);
    }
}
