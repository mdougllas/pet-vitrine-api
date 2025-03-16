<?php

namespace App\Exceptions;

use Exception;

class HandlePetFinderRequestException extends Exception
{
    public static function resolve($error)
    {
        $code = $error->getStatusCode();
        $message = $error->getReasonPhrase();

        throw new Exception($message, $code);
    }
}
