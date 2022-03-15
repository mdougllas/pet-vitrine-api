<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class StripeController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        $validData = $request->validate([
            'amount' => 'required|numeric|min:5'
        ]);

        $payload = [
            'amount' => $validData['amount'] * 100,
            'currency' => 'usd',
            'payment_method_types' => ['card']
        ];

        $paymentIntent = Http::asForm()
            ->withBasicAuth(config('services.stripe.secret_key'), '')
            ->post('https://api.stripe.com/v1/payment_intents', $payload);

        return response()->json($paymentIntent->object(), 200);
    }

    public function requestPaymentIntent(Request $request)
    {
    }
}
