<?php

namespace App\Services\Spider;

use App\Helpers\HandleHttpException;
use App\Services\PetFinder\PetFinderConfig;
use Illuminate\Support\Collection;
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
    private $rootUrl;

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
        $this->rootUrl = $this->petFinder->petFinderApiRootUrl;
    }

    /**
     * Get latests pets posted.
     *
     * @param integer $page
     * @return Collection
     */
    public function getPets($page = 1): Collection
    {
        dd('getPets');
        $perPage = $this->perPage;
        $token = config('spider.token',);
        $url = "$this->rootUrl/?token=$token&page=$page&limit[]=$perPage&status=adoptable&sort[]=available_longest&distance[]=Anywhere&include_transportable=true";

        return $this->dispatch($url);
    }

    /**
     * Get all organizations.
     *
     * @param \App\Services\Spider\HttpRequest $spider
     * @return Collection
     */
    public function getOrganizations($page = 1): Collection
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
     * @return Collection
     */
    public function getPet($id): Collection
    {
        dd('getPet');
        $this->url = "https://www.petfinder.com/search/?pet_id[]=$id";

        return $this->dispatch();
    }

    /**
     * Get organization by id.
     *
     * @param sting $id
     * @return Collection
     */
    public function getOrganization($id): Collection
    {
        $this->url = "$this->rootUrl/v2/organizations/$id";

        return $this->dispatch();
    }

    /**
     * Get pets by organization.
     *
     * @param \App\Services\Spider\HttpRequest $spider
     * @return Collection
     */
    public function getPetsByOrganization($id, $page = 1): Collection
    {

        $this->url = "$this->rootUrl/animals";

        $this->queryParameters = [
            'page' => $page,
            'limit' => $this->perPage,
            'organization' => $id,
            'sort' => 'name'
        ];

        return $this->dispatch();
    }

    /**
     * Dispatch a CURL request to the server.
     *
     * @return Collection
     */
    private function dispatch(): Collection
    {
        $response = Http::withToken($this->petFinder->accessToken)
            ->withQueryParameters($this->queryParameters)
            ->get($this->url);

        // $response->onError(fn ($err) => HandleHttpException::throw($err));

        return collect($response->json());
    }
}
