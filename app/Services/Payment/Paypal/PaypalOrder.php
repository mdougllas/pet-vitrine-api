<?php

namespace App\Services\Payment\Paypal;

use App\Helpers\HandleHttpException;
use Illuminate\Support\Facades\Http;
use App\Services\Payment\Paypal\Paypal;

class PaypalOrder extends Paypal
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
    }

    /**
     * Creates PayPal Order.
     *
     * @return object Illuminate\Support\Facades\Http
     */
    public function createOrder()
    {
        $token = $this->getToken();

        $data = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => '5.00'
                    ]
                ]
            ]
        ];

        $order = Http::withToken($token)->post($this->url, $data);
        $order->onError(fn ($err) => HandleHttpException::throw($err));

        return $order->object();
    }

    /**
     * FINISH THIS DOCBLOCK.
     *
     * @param  array  $fields
     * @param  array  $params
     * @return FacebookAds\Object\Campaign
     */
    public function updateOrder()
    {
    }

    /**
     * FINISH THIS DOCBLOCK.
     *
     * @param  array  $fields
     * @param  array  $params
     * @return FacebookAds\Object\Campaign
     */
    public function orderDetails()
    {
    }
}
