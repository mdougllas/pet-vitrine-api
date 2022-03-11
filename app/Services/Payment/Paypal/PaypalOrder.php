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
        $this->returnUrl = config('services.paypal.return_url');
        $this->token = $this->getToken();
    }

    /**
     * Creates PayPal Order.
     *
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

    /**
     * FINISH THIS DOCBLOCK.
     *
     * @param  array  $fields
     * @param  array  $params
     * @return FacebookAds\Object\Campaign
     */
    public function capturePayment()
    {
        $url = session('paypal_url');
        $data = ['' => ''];

        $capture = Http::withToken($this->token)->post($url, $data);
        $capture->onError(fn ($err) => HandleHttpException::throw($err));

        return $capture->object();
    }
}
