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
     * @param Object $object
     * @return Object
     */
    public static function convert($collection, $object)
    {
        foreach ($collection as $key => $value) {
            $parameter = Str::contains($key, '_')
                ? Str::camel($key)
                : $key;

            $object->{$parameter} = $value;
        }

        return $object;
    }
}
