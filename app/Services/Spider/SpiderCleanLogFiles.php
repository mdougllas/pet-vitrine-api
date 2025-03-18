<?php

namespace App\Services\Spider;

use App\Traits\Spider\UseSetOutput;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
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
        $path = $this->getSpiderLogFilePath();
        $fileName = Str::after($path, 'spider/');
        $dateString = Str::before($fileName, '.log');
        $date = Str::replace('-', '/', Str::replace('_', ' ', $dateString));
        $epoch = Carbon::parse($date, 'UTC')->timestamp;


        dd($epoch);
    }

    private function getSpiderLogFilePath(): string
    {
        $fileName = now()->format('m-d-Y_H:i:s');

        return storage_path("logs/spider/$fileName.log");
    }
}
