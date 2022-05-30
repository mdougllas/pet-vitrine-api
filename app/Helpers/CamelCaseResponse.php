<?php

namespace app\Helpers;

use Illuminate\Support\Str;

class CamelCaseResponse
{
    /**
     * Converts response parameters
     * from snake_case to CamelCase.
     *
     * @param Illuminate\Support\Collection $collection
     * @return Illuminate\Support\Collection
     */
    public static function convert($collection)
    {
        return $collection->mapWithKeys(fn ($item, $key) => [Str::camel($key) => $item]);
    }
}
