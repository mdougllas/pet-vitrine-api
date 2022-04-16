<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Payment\Paypal\PaypalOrder;

class PaypalController extends Controller
{
    /**
     * Creates PayPal order.
     *
     * @param  App\Services\Payment\Paypal\PaypalOrder $paypal
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function createOrder(PaypalOrder $paypal, Request $request)
    {
        $validData = $request->validate([
            'donation' => 'required|numeric|min:5',
            'total' => 'required|numeric|min:5',
            'pet_id' => 'required|numeric'
        ]);

        $petId = $validData['pet_id'];
        $donation = $validData['donation'];
        $total = $validData['total'];
        $response = $paypal->createOrder($total);
        $link = $response->links[3];
        $url = $link->href;

        session([
            'paypal_url' => $url,
            'pet_id' => $petId,
            'donation' => $donation,
            'total' => $total,
        ]);

        return response()->json([
            'token' => $response
        ]);
    }

    /**
     * Captures payment for an authorized PayPal order.
     *
     * @param  App\Services\Payment\Paypal\PaypalOrder $paypal
     * @return Illuminate\Http\Response
     */
    public function capturePayment(PaypalOrder $paypal)
    {
        $url = session('paypal_url');
        $petId = session('pet_id');
        $donation = session('donation');
        $total = session('total');

        $payment = $paypal->capturePayment($url);

        return response()->json([
            'data' => [
                'payment' => $payment,
                'petId' => $petId,
                'donation' => $donation,
                'total' => $total
            ]
        ]);
    }
}
