<?php

namespace App\Services\Spider;

use App\Services\PetFinder\PetFinderConfig;
use App\Traits\Spider\UseSetOutput;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class HttpRequest
{
    use UseSetOutput;

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

    /**
     * Undocumented variable
     *
     * @var integer
     */
    private int $requestCount = 0;

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
        $this->url = "$this->rootUrl/animals";

        $this->queryParameters = [
            'page' => $page,
            'limit' => $this->perPage,
            'sort' => 'recent',
        ];

        return $this->dispatch();
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
            'sort' => 'name',
        ];

        return $this->dispatch();
    }

    /**
     * Dispatch a request to the server.
     *
     * @return Collection
     */
    private function dispatch(): Collection
    {
        $this->requestCount++;

        $this->output->info("This is request # $this->requestCount for URL $this->url");

        $response = Http::withToken($this->petFinder->accessToken)
            ->withQueryParameters($this->queryParameters)
            ->get($this->url);

        return collect($response->json());
    }
}
