<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\Payment\Paypal\PaypalOrder;

class PaypalController extends Controller
{
    public function createOrder(PaypalOrder $paypal, Request $request)
    {
        $validData = $request->validate([
            'amount' => 'required|numeric|min:5'
        ]);

        $response = $paypal->createOrder($validData['amount']);
        $link = $response->links[3];
        $url = $link->href;

        session(['paypal_url' => $url]);

        return response()->json([
            'token' => $response
        ]);
    }

    public function capturePayment(PaypalOrder $paypal, Request $request)
    {
        $test = $paypal->capturePayment();

        return response()->json([
            'data' => $test
        ]);
    }
}
