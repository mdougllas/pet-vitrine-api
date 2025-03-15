<?php


namespace App\Services\PetFinder;

use App\Helpers\HandleHttpException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PetFinderConfig
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    public string $petFinderApiRootUrl = 'https://api.petfinder.com/v2';

    /**
     * Undocumented variable
     *
     * @var string
     */
    public string $petFinderApiTokenUrlPath = '/oauth2/token';

    /**
     * Undocumented variable
     *
     * @var integer
     */
    private int $tokenExpiration;

    /**
     * Undocumented variable
     *
     * @var string|null
     */
    public string|null $accessToken;

    /**
     * Instantiates the class.
     */
    public function __construct()
    {
        $this->tokenExpiration = 0;
        $this->accessToken = $this->getAccessToken();
    }

    /**
     * Retrieves the access token from cache or fetches a new one.
     *
     * @return string
     */
    private function getAccessToken(): string
    {
        return Cache::remember('petfinder_access_token', $this->tokenExpiration, fn () => $this->fetchNewToken());
    }

    /**
     * Requests a new access token from Petfinder API.
     *
     * @return string
     */
    private function fetchNewToken(): string
    {
        $data = [
            'grant_type' => 'client_credentials',
            'client_id' => config('services.petfinder.api_key'),
            'client_secret' => config('services.petfinder.api_secret')
        ];

        $url = $this->petFinderApiRootUrl . $this->petFinderApiTokenUrlPath;
        $response = Http::post($url, $data);

        $tokenObject = $response->json();
        $this->tokenExpiration = $tokenObject['expires_in'];

        return $tokenObject['access_token'];
    }
}
