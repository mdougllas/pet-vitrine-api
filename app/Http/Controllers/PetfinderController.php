<?php

namespace App\Http\Controllers;

use App\Helpers\HandleHttpException;
use Illuminate\Support\Facades\Http;

class PetfinderController extends Controller
{
    const PETFINDER_TOKEN_URL = 'https://api.petfinder.com/v2/oauth2/token';

    /**
     * Requests access token for Petfinder API.
     *
     * @return \Illuminate\Http\Response
     */
    public function requestToken()
    {
        $data = [
            'grant_type' => 'client_credentials',
            'client_id' => config('services.petfinder.api_key'),
            'client_secret' => config('services.petfinder.api_secret')
        ];

        $response = Http::post(self::PETFINDER_TOKEN_URL, $data);
        $response->onError(fn ($err) => HandleHttpException::throw($err));

        return response()->json($response->object());
    }
}
