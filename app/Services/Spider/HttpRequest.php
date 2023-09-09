<?php

namespace App\Services\Spider;

class HttpRequest
{
    /**
     * @property integer $perPage
     */
    private $perPage = 300;

    /**
     * @property array $headers
     */
    private $headers = [
        'Authority: www.petfinder.com',
        'Accept: application/json, text/plain, */*',
        'Accept-Language: en-US,en;q=0.9,pt-BR;q=0.8,pt;q=0.7,es;q=0.6',
        'Referer: https://www.petfinder.com',
        'Sec-Ch-Ua: \"Google Chrome\";v=\"107\", \"Chromium\";v=\"107\", \"Not=A?Brand\";v=\"24\"',
        'Sec-Ch-Ua-Mobile: ?0',
        'Sec-Ch-Ua-Platform: \"macOS\"',
        'Sec-Fetch-Dest: empty',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Site: same-origin',
        'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36',
        'X-Requested-With: XMLHttpRequest',
    ];

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
        $url = "https://www.petfinder.com/v2/search/organizations?&page=$page&limit=300&sort=name";

        return $this->dispatch($url);
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
     * @param string $url
     * @return object|null
     */
    private function dispatch($url): object|null
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            $result = 'Error:' . curl_error($ch);
        }

        curl_close($ch);

        return json_decode($result);
    }
}
