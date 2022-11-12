<?php

namespace App\Services\Spider;

class HttpRequest
{
    /**
     * Blueprint for HttpRequest.
     *
     * @param \App\Services\Spider\HttpRequest $spider
     * @return void
     */
    public function __construct()
    {
        $this->perPage = 5;
    }

    /**
     * Blueprint for SpiderPetsManager.
     *
     * @param \App\Services\Spider\HttpRequest $spider
     * @return void
     */
    public function getPets($page = 1)
    {
        $perPage = $this->perPage;
        $token = config('spider.token',);
        $url = "https://www.petfinder.com/search/?token=$token&page=$page&limit[]=$perPage&status=adoptable&sort[]=recenly_added&distance[]=Anywhere&include_transportable=true";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

        $headers = [
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

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            $result = 'Error:' . curl_error($ch);
        }

        curl_close($ch);

        return json_decode($result);
    }

    /**
     * Blueprint for SpiderPetsManager.
     *
     * @param \App\Services\Spider\HttpRequest $spider
     * @return void
     */
    public function getOrganizations($page = 1)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://www.petfinder.com/v2/search/organizations?&page=1&limit=10');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

        $headers = [
            'Authority: www.petfinder.com',
            'Accept: */*',
            'Accept-Language: en-US,en;q=0.9,pt-BR;q=0.8,pt;q=0.7,es;q=0.6',
            'Referer: https://www.petfinder.com/animal-shelters-and-rescues/search/?location=&shelter_name=',
            'Sec-Ch-Ua: \"Google Chrome\";v=\"107\", \"Chromium\";v=\"107\", \"Not=A?Brand\";v=\"24\"',
            'Sec-Ch-Ua-Mobile: ?0',
            'Sec-Ch-Ua-Platform: \"macOS\"',
            'Sec-Fetch-Dest: empty',
            'Sec-Fetch-Mode: cors',
            'Sec-Fetch-Site: same-origin',
            'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36',
            'X-Requested-With: XMLHttpRequest',
        ];


        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return json_decode($result);
    }
}
