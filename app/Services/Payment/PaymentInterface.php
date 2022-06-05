<?php

namespace App\Services\Payment;

interface PaymentInterface
{
    public function validatePayment($id, $amount);
}
