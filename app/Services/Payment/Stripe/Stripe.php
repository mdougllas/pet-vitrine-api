<?php

namespace App\Services\Payment\Stripe;

class Stripe
{
    /**
     * Instantiates Stripe API.
     *
     * @return void
     */
    protected function __construct()
    {
        $this->publishableKey = config('services.stripe.publishable_key');
        $this->secretKey = config('services.stripe.secret_key');
        $this->baseUrl = config('services.stripe.base_url');
    }
}
