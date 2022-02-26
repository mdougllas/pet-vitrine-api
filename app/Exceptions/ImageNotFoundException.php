<?php

namespace App\Exceptions;

use Exception;

class ImageNotFoundException extends Exception
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->json([
            'status' => 404,
            'message' => "We couldn't find an image with the URL $request->url"
        ]);
    }
}
