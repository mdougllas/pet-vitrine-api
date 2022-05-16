<?php

namespace App\Helpers;

use App\Exceptions\HttpException;

class HandleHttpException
{
    /**
     * Handles HTTP requests errors.
     *
     * @param  App\Exceptions\HttpException  $error
     * @throws App\Exceptions\HttpException
     * @return void
     */
    public static function throw($error, $customException = null)
    {
        $code = $customException ? $customException->getCode() : $error->getStatusCode();
        $message = $customException ? $customException->getMessage() : $error->getReasonPhrase();

        throw new HttpException($message, $code, $customException);
    }
}
