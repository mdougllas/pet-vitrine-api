<?php

namespace App\Helpers;

use App\Exceptions\HttpException;

/**
 * Handles HTTP requests errors.
 *
 * @param  array  $error
 * @throws App\Exceptions\HttpException
 * @return void
 */
class HandleHttpException
{
    public static function throw($error)
    {
        $code = $error->getStatusCode();
        $message = $error->getReasonPhrase();

        throw new HttpException($message, $code);
    }
}
