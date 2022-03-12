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
            'amount' => 'required|numeric|min:5',
            'pet_id' => 'required|numeric'
        ]);

        $petId = $validData['pet_id'];
        $donation = $validData['amount'];
        $response = $paypal->createOrder($donation);
        $link = $response->links[3];
        $url = $link->href;

        session([
            'paypal_url' => $url,
            'pet_id' => $petId,
            'donation' => $donation
        ]);

        return response()->json([
            'token' => $response
        ]);
    }

    public function capturePayment(PaypalOrder $paypal)
    {
        $test = $paypal->capturePayment();
        $petId = session('pet_id');
        $donation = session('donation');

        return response()->json([
            'data' => [
                'payment' => $test,
                'petId' => $petId,
                'donation' => $donation
            ]
        ]);
    }
}
