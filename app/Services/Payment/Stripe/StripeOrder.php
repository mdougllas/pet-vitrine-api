<?php

namespace App\Services\Payment\Stripe;

use App\Helpers\HandleHttpException;
use App\Services\Payment\PaymentInterface;
use Illuminate\Support\Facades\Http;
use App\Services\Payment\Stripe\Stripe;

class StripeOrder extends Stripe implements PaymentInterface
{
    /**
     * Instantiates PayPal Order.
     *
     * @return void;
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function createPaymentIntent($payload)
    {
        return Http::asForm()
            ->withBasicAuth($this->secretKey, '')
            ->post('https://api.stripe.com/v1/payment_intents', $payload);
    }

    public function validatePaymentId()
    {
    }
}
