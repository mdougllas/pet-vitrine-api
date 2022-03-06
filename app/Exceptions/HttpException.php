<?php

namespace App\Exceptions;

use Exception;

class HttpException extends Exception
{
    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report()
    {
        //
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->json([
            'status' => $this->getCode(),
            'message' => $this->getMessage()
        ], $this->getCode());
    }
}
