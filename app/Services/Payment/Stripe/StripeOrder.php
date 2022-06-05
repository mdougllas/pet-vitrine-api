<?php

namespace App\Services\Payment\Stripe;

use Illuminate\Support\Str;
use App\Exceptions\HttpException;
use App\Exceptions\PaymentException;
use App\Helpers\HandleHttpException;
use Illuminate\Support\Facades\Http;
use App\Services\Payment\Stripe\Stripe;
use App\Services\Payment\PaymentInterface;

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
    public function validatePayment($id, $amount)
    {
        $paymentIntent = Http::asForm()
            ->withBasicAuth($this->secretKey, '')
            ->post("$this->baseUrl/v1/payment_intents/$id");
        $paymentIntent->onError(fn ($err) => HandleHttpException::throw($err));

        $intentObject = $paymentIntent->object();
        $this->validatePaymentIsLive($intentObject);
        $this->validateStatusSucceeded($intentObject);
        $this->validateAmountPaid($intentObject, $amount);
        $this->validatePaymentNotCanceled($intentObject);
        $this->validatePaymentNotRefunded($intentObject);
    }

    private function validatePaymentIsLive($intentObject)
    {
        return $intentObject->livemode
            ?? throw new PaymentException('This payment is not a live payment.', 401);
    }

    private function validateStatusSucceeded($intentObject)
    {
        return $intentObject->status === 'succeeded'
            ?? throw new PaymentException('This payment was not finalized.', 401);
    }

    private function validateAmountPaid($intentObject, $amount)
    {
        $amountReceived = $intentObject->amount_received / 100;

        return $amountReceived === $amount
            ?? throw new PaymentException('The amount requested is different from the paid amount', 401);
    }

    private function validatePaymentNotCanceled($intentObject)
    {
        return !$intentObject->canceled_at
            ?? throw new PaymentException('This payment was canceled.', 401);
    }

    private function validatePaymentNotRefunded($intentObject)
    {
        return !isset($intentObject->amount_refunded)
            ?? throw new PaymentException('This payment was refunded.', 401);
    }
}
