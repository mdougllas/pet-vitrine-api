<?php

namespace App\Http\Controllers;

use App\Services\Payment\Stripe\StripeOrder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    public function createPaymentIntent(StripeOrder $stripe, Request $request)
    {
        $validData = $request->validate([
            'donation' => 'required|numeric|min:5',
            'total' => 'required|numeric|min:5'
        ]);

        $payload = [
            'amount' => $validData['total'] * 100,
            'currency' => 'usd',
            'payment_method_types' => ['card']
        ];

        $paymentIntent = $stripe->createPaymentIntent($payload);

        return response()->json($paymentIntent->object(), 200);
    }

    public function requestPaymentIntent(Request $request, StripeOrder $stripe)
    {
        $validData = $request->validate([
            'payment_intent' => 'required|string'
        ]);

        $paymentIntentId = $validData['payment_intent'];
        $paymentIntent = $stripe->getPaymentIntent($paymentIntentId);

        return response()->json($paymentIntent, 200);
    }

    public function sendEmailReceipt(Request $request, Mail $mail, StripeOrder $stripe)
    {
        $validData = $request->validate([
            'payment_intent' => 'required|string'
        ]);

        $paymentIntentId = $validData['payment_intent'];
        $response = $stripe->sendEmailReceipt($paymentIntentId, $request);

        return response()->json($response, 200);
    }
}
