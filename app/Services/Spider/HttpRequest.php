<?php

namespace App\Services\Spider;

use App\Services\PetFinder\PetFinderConfig;
use Illuminate\Support\Facades\Http;

class HttpRequest
{
    /**
     * Undocumented variable
     *
     * @var PetFinderConfig
     */
    private PetFinderConfig $petFinder;

    /**
     * Undocumented variable
     *
     * @var string
     */
    private string $accessToken;

    /**
     * @property integer $perPage
     */
    private $perPage = 100;

    /**
     * Undocumented variable
     *
     * @var string
     */
    private $rootUrl = 'https://api.petfinder.com/v2';

    /**
     * Undocumented variable
     *
     * @var string
     */
    private $url;

    /**
     * Undocumented variable
     *
     * @var array
     */
    private $queryParameters;

    public function __construct(PetFinderConfig $petFinder)
    {
        $this->petFinder = $petFinder;
    }

    /**
     * Get latests pets posted.
     *
     * @param integer $page
     * @return object|null
     */
    public function getPets($page = 1): object|null
    {
        $perPage = $this->perPage;
        $token = config('spider.token',);
        $url = "https://www.petfinder.com/search/?token=$token&page=$page&limit[]=$perPage&status=adoptable&sort[]=available_longest&distance[]=Anywhere&include_transportable=true";

        return $this->dispatch($url);
    }

    /**
     * Get all organizations.
     *
     * @param \App\Services\Spider\HttpRequest $spider
     * @return object|null
     */
    public function getOrganizations($page = 1): object|null
    {
        $this->url = "$this->rootUrl/organizations";

        $this->queryParameters = [
            'page' => $page,
            'limit' => $this->perPage,
            'sort' => 'name'
        ];

        return $this->dispatch();
    }

    /**
     * Get pet by id.
     *
     * @param sting $id
     * @return object|null
     */
    public function getPet($id): object|null
    {
        $url = "https://www.petfinder.com/search/?pet_id[]=$id";

        return $this->dispatch($url);
    }

    /**
     * Get organization by name.
     *
     * @param sting $name
     * @return object|null
     */
    public function getOrganization($name): object|null
    {
        $url = "https://www.petfinder.com/v2/search/organizations?name_substring=$name";

        return $this->dispatch($url);
    }

    /**
     * Get pets by organization.
     *
     * @param \App\Services\Spider\HttpRequest $spider
     * @return object|null
     */
    public function getPetsByOrganization($id, $page = 1): object|null
    {
        $perPage = $this->perPage;
        $token = config('spider.token',);
        $url = "https://www.petfinder.com/search/?token=$token&page=$page&limit[]=$perPage&status=adoptable&shelter_id[]=$id&sort[]=available_longest&distance[]=Anywhere&include_transportable=true";

        return $this->dispatch($url);
    }

    /**
     * Dispatch a CURL request to the server.
     *
     * @return object|null
     */
    private function dispatch(): object|null
    {
        $response = Http::withToken($this->petFinder->accessToken)
            ->withQueryParameters($this->queryParameters)
            ->get($this->url);

            dd($response->json());

        return $response->body();
    }
}
