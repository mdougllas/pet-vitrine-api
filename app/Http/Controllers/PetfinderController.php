<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cookie;

class PetfinderController extends Controller
{
    const PETFINDER_ROOT = 'https://api.petfinder.com/v2/oauth2/token';

    /**
     * Display a listing of the resource.
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

        $response = Http::post(self::PETFINDER_ROOT, $data);

        if (!$response->ok()) {
            $responseObject = $response->object();

            return response()->json($responseObject, $responseObject->status);
        }

        $cookie = cookie(
            'petfinder_token',
            $response['access_token'],
            60,
            '/petfinderToken/',
            null,
            true,
            true,
            false,
            'strict'
        );

        return response()->json($response->object())->cookie($cookie);
    }
}
