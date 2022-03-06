<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\Payment\Paypal\PaypalOrder;

class PaypalController extends Controller
{
    public function createOrder(PaypalOrder $paypal, Request $request)
    {
        $response = $paypal->createOrder();

        return response()->json([
            'token' => $response
        ]);
    }
}
