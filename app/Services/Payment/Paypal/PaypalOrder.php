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
        // $curl = curl_init();

        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => 'https://api.sandbox.paypal.com/v2/checkout/orders/8U478874457976903/capture',
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => '',
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => 'POST',
        //     CURLOPT_HTTPHEADER => array(
        //         'Content-Type: application/json',
        //         'Authorization: Bearer A21AALP-zTFmrCJTMf8Bdw_5Tj5xUh8QwVyFAyA7cKH9GsrHEDE62me_rG5piYvTOxdud1rnthA3AxG_lPdEEvCRYIhBuyM3g'
        //     ),
        // ));

        // $response = curl_exec($curl);

        // curl_close($curl);
        // return $response;

        $url = session('paypal_url');
        $data = ['' => ''];

        $capture = Http::withToken($this->token)->post($url, $data);
        $capture->onError(fn ($err) => HandleHttpException::throw($err));

        return $capture->object();
    }
}
