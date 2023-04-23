<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait StringManipulation
{
    /**
     * Extract only the city from the input
     *
     * @param string $city
     * @return string
     */
    protected function extractCityFromString($city): string
    {
        return Str::of($city)->before(',');
    }

    protected function extractStateFromString($city): string
    {
        return Str::of($city)->after(', ');
    }
}
