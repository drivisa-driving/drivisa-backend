<?php

namespace Modules\Drivisa\Services;

use Stripe\Stripe;
use Stripe\Transfer;

class PaymentTransferService
{
    public function createTransfer($amount, $instructor_account)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        return \Stripe\Transfer::create([
            'amount' => (int)($amount * 100),
            'currency' => 'CAD',
            'destination' => $instructor_account->account_id,
            'description' => "Payment transfer to instructor after lesson completion"
        ]);
    }
}