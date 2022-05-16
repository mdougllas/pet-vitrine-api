<?php

namespace App\Services\Payment\Paypal;

use App\Helpers\HandleHttpException;
use App\Services\Payment\PaymentInterface;
use Illuminate\Support\Facades\Http;
use App\Services\Payment\paypal\Paypal;

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
     * Checks if payment ID is valid.
     *
     * @param  string $id
     * @return void
     */
    public function validatePaymentId($id)
    {
        $order = Http::withToken($this->token)->get("$this->rootUrl/v2/checkout/orders/$id");
        $order->onError(fn ($err) => HandleHttpException::throw($err));
    }
}
