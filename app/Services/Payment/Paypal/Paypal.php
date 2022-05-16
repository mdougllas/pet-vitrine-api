<?php

namespace App\Services\Payment\Paypal;

use Illuminate\Support\Carbon;
use App\Helpers\HandleHttpException;
use Illuminate\Support\Facades\Http;

class Paypal
{
    /**
     * Instantiates PayPal API.
     *
     * @return void
     */
    protected function __construct()
    {
        $this->account = config('services.paypal.account');
        $this->rootUrl = config('services.paypal.url');
        $this->clientId = config('services.paypal.client_id');
        $this->secret = config('services.paypal.secret');
    }

    /**
     * Checks session for valid PayPal access token.
     *
     * @return string
     */
    public function getToken()
    {
        $token = session('paypal_access_token');
        $expiration = session('paypal_access_token_expiration');
        $now = Carbon::now()->timestamp;

        return $now < $expiration ? $token : $this->requestToken();
    }

    /**
     * Requests a new PayPal access token.
     *
     * @return string
     */
    private function requestToken()
    {
        $data = ['grant_type' => 'client_credentials'];

        $headers = [
            'Accept' => 'application/json',
            'Accept-Language' => 'en_US',
        ];

        $response = Http::withHeaders($headers)
            ->asForm()
            ->withBasicAuth($this->clientId, $this->secret)
            ->post("$this->rootUrl/v1/oauth2/token", $data);

        $response->onError(fn ($err) => HandleHttpException::throw($err));


        $token = $response->object()->access_token;
        $seconds = $response->object()->expires_in;
        $expiration = $this->setTokenExpiration($seconds);

        session([
            'paypal_access_token' => $token,
            'paypal_access_token_expiration' => $expiration
        ]);

        return $token;
    }

    /**
     * Sets PayPal access token expiration.
     *
     * @param  int  $seconds
     * @return FacebookAds\Object\Campaign
     */
    private function setTokenExpiration($seconds)
    {
        $now = Carbon::now()->timestamp;
        $expiration = $now + $seconds;

        return $expiration;
    }
}
