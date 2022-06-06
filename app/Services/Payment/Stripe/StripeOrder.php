<?php

namespace App\Services\Payment\Stripe;

use App\Exceptions\PaymentException;
use App\Helpers\HandleHttpException;
use App\Mail\DonationReceipt;
use App\Services\Payment\PaymentInterface;
use App\Services\Payment\Stripe\Stripe;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

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
     * Fetches Stripe Payment Intent
     *
     * @param integer $id
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function getPaymentIntent($id)
    {
        $paymentIntent = Http::asForm()
            ->withBasicAuth($this->secretKey, '')
            ->post("$this->baseUrl/v1/payment_intents/$id");

        $paymentIntent->onError(fn ($err) => HandleHttpException::throw($err));

        return $paymentIntent->object();
    }

    /**
     * Validates Stripe Payment Intent Id
     *
     * @param string $id
     *
     * @return void;
     */
    public function validatePayment($id, $amount)
    {
        $intentObject = $this->getPaymentIntent($id);

        $this->validatePaymentIsLive($intentObject);
        $this->validateStatusSucceeded($intentObject);
        $this->validateAmountPaid($intentObject, $amount);
        $this->validatePaymentNotCanceled($intentObject);
        $this->validatePaymentNotRefunded($intentObject);
    }

    /**
     * Sends email with the receipt to the user
     *
     * @param string $id
     * @param Illuminate\Http\Request $request
     *
     * @return void;
     */
    public function sendEmailReceipt($id, $request)
    {
        $intentObject = $this->getPaymentIntent($id);

        $chargeData = collect($intentObject->charges->data)->first();
        $receiptUrl = $chargeData->receipt_url;

        $emailBody = Http::get($receiptUrl);
        $emailBody->onError(fn ($err) => HandleHttpException::throw($err));

        return Mail::to($request->user())->send(new DonationReceipt($emailBody));
    }

    /**
     * Validates Stripe Payment Is Live (production)
     *
     * @param \Illuminate\Http\Client\Response $paymentObject
     *
     * @throws App\Exceptions\PaymentException
     * @return void;
     */
    private function validatePaymentIsLive($intentObject)
    {
        $isProduction = config('app.env') === 'production';

        if (!$intentObject->livemode && $isProduction) {
            throw new PaymentException('This payment is not a live payment.', 409);
        }
    }

    /**
     * Validates Stripe Payment is finalized
     *
     * @param \Illuminate\Http\Client\Response $paymentObject
     *
     * @throws App\Exceptions\PaymentException
     * @return void;
     */
    private function validateStatusSucceeded($intentObject)
    {
        if ($intentObject->status !== 'succeeded') {
            throw new PaymentException('This payment was not finalized.', 409);
        }
    }

    /**
     * Validates Stripe Payment amount is the same as on the request
     *
     * @param \Illuminate\Http\Client\Response $paymentObject
     * @param integer $amount
     *
     * @throws App\Exceptions\PaymentException
     * @return void;
     */
    private function validateAmountPaid($intentObject, $amount)
    {
        $amountReceived = $intentObject->amount_received / 100;
        $percentual = 3.5;
        $fixed = 1.35;
        $variableFees = ($amount / 100) * $percentual;
        $amountPlusFees = $amount + $fixed + $variableFees;

        if (round($amountPlusFees, 2) != $amountReceived) {
            throw new PaymentException('The amount requested is different from the paid amount', 409);
        }
    }

    /**
     * Validates Stripe Payment amount is not canceled
     *
     * @param \Illuminate\Http\Client\Response $paymentObject
     *
     * @throws App\Exceptions\PaymentException
     * @return void;
     */
    private function validatePaymentNotCanceled($intentObject)
    {
        if ($intentObject->canceled_at) {
            throw new PaymentException('This payment was canceled.', 409);
        }
    }

    /**
     * Validates Stripe Payment amount is not refunded
     *
     * @param \Illuminate\Http\Client\Response $paymentObject
     *
     * @throws App\Exceptions\PaymentException
     * @return void;
     */
    private function validatePaymentNotRefunded($intentObject)
    {
        if (isset($intentObject->amount_refunded)) {
            throw new PaymentException('This payment was refunded.', 409);
        }
    }
}
