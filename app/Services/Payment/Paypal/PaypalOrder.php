<?php

namespace App\Services\Payment\Paypal;

use App\Exceptions\PaymentException;
use App\Helpers\HandleHttpException;
use App\Services\Payment\PaymentInterface;
use App\Services\Payment\Paypal\Paypal;
use Illuminate\Support\Facades\Http;

class PaypalOrder extends Paypal implements PaymentInterface
{
    /**
     * Instantiates PayPal Order.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->url = "$this->rootUrl/v2/checkout/orders";
        $this->returnUrl = config('services.paypal.return_url');
        $this->token = $this->getToken();
    }

    /**
     * Creates PayPal Order.
     *
     * @param  integer $amount
     * @return object Illuminate\Support\Facades\Http
     */
    public function createOrder($amount)
    {
        $data = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => $amount
                    ]
                ]
            ],
            'application_context' => [
                'brand_name' => 'Pet Vitrine',
                'shipping_preference' => 'NO_SHIPPING',
                'return_url' => $this->returnUrl
            ]
        ];

        $order = Http::withToken($this->token)->post($this->url, $data);
        $order->onError(fn ($err) => HandleHttpException::throw($err));

        return $order->object();
    }

    /**
     * Captures payment for an authorized PayPal order.
     *
     * @param  string $url
     * @return object Illuminate\Support\Facades\Http
     */
    public function capturePayment($url)
    {
        $data = ['' => ''];

        $capture = Http::withToken($this->token)->post($url, $data);
        $capture->onError(fn ($err) => HandleHttpException::throw($err));

        return $capture->object();
    }

    /**
     * Validates Paypal Payment Intent Id.
     *
     * @param  string $id
     * @return void
     */
    public function validatePayment($id, $amount)
    {
        $order = Http::withToken($this->token)->get("$this->rootUrl/v2/checkout/orders/$id");
        $order->onError(fn ($err) => HandleHttpException::throw($err));
        $orderObject = $order->object();

        $this->validateOrderCompleted($orderObject);
        $this->validateAmountPaid($orderObject, $amount);
        $this->validatePaymentNotRefunded($orderObject);
    }

    /**
     * Validates if order was completed.
     *
     * @param \Illuminate\Http\Client\Response $orderObject
     *
     * @throws App\Exceptions\PaymentException
     * @return void;
     */
    private function validateOrderCompleted($orderObject)
    {
        if ($orderObject->status !== 'COMPLETED') {
            throw new PaymentException('This payment was not finalized', 409);
        }
    }

    /**
     * Get amount received by order.
     *
     * @param \Illuminate\Http\Client\Response $orderObject
     *
     * @throws App\Exceptions\PaymentException
     * @return void;
     */
    private function getAmountReceived($orderObject)
    {
        $purchaseUnit = collect($orderObject->purchase_units)->first();

        return $purchaseUnit->amount->value;
    }

    /**
     * Validates PayPal Payment amount is the same as on the request
     *
     * @param \Illuminate\Http\Client\Response $orderObject
     * @param integer $amount
     *
     * @throws App\Exceptions\PaymentException
     * @return void;
     */
    private function validateAmountPaid($orderObject, $amount)
    {
        $amountReceived = $this->getAmountReceived($orderObject);
        $percentual = 4;
        $fixed = 1.55;
        $variableFees = ($amount / 100) * $percentual;
        $amountPlusFees = $amount + $fixed + $variableFees;

        if (round($amountPlusFees, 2) != $amountReceived) {
            throw new PaymentException('The amount requested is different from the paid amount.', 409);
        }
    }

    private function validatePaymentNotRefunded($orderObject)
    {
        $purchaseUnit = collect($orderObject->purchase_units)->first();
        $payments = collect($purchaseUnit->payments);
        $captureObject = $payments['captures'][0];

        if ($captureObject->status === 'REFUNDED') {
            throw new PaymentException('This order was refunded.', 409);
        }
    }
}
