<?php

namespace App\Services\Payment\Stripe;

use App\Helpers\HandleHttpException;
use App\Services\Payment\PaymentInterface;
use Illuminate\Support\Facades\Http;
use App\Services\Payment\Stripe\Stripe;

class StripeOrder extends Stripe implements PaymentInterface
{
    /**
     * Instantiates Stripe API
     *
     * @return void;
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Creates Stripe Payment Intent
     *
     * @param array $payload
     *
     * @return void;
     */
    public function createPaymentIntent($payload)
    {
        return Http::asForm()
            ->withBasicAuth($this->secretKey, '')
            ->post("$this->baseUrl/v1/payment_intents", $payload);
    }

    /**
     * Validates Stripe Payment Intent Id
     *
     * @param String $id
     *
     * @return void;
     */
    public function validatePaymentId($id)
    {
        $paymentIntent = Http::asForm()
            ->withBasicAuth($this->secretKey, '')
            ->post("$this->baseUrl/v1/payment_intents/$id");

        $paymentIntent->onError(fn ($err) => HandleHttpException::throw($err));
    }
}
