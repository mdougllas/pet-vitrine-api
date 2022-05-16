<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Payment\Stripe\StripeOrder;
use Illuminate\Support\Facades\Http;

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

    public function requestPaymentIntent(Request $request)
    {
        $validData = $request->validate([
            'payment_intent' => 'required|string'
        ]);

        $paymentIntentId = $validData['payment_intent'];

        $url = "https://api.stripe.com/v1/payment_intents/$paymentIntentId";

        $paymentIntent = Http::withBasicAuth(config('services.stripe.secret_key'), '')
            ->get($url);

        return response()->json($paymentIntent->object(), 200);
    }
}
