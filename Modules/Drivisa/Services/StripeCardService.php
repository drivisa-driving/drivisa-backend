<?php

namespace Modules\Drivisa\Services;

use Stripe\Customer;
use Stripe\Stripe;

class StripeCardService
{
    public function addCard($customer_id, $data)
    {
        Stripe::setApiKey(env("STRIPE_SECRET"));

        return Customer::createSource($customer_id, [
            [
                'source' => [
                    'object' => 'card',
                    'number' => $data['card_number'],
                    'exp_month' => $data['exp_month'],
                    'exp_year' => $data['exp_year'],
                    'cvc' => $data['cvc'],
                    'name' => $data['card_holder']
                ],
                'metadata' => [
                    'default' => $data['default'] ?? 0
                ]
            ]
        ]);
    }

    public function createCustomerIdFromStripe($trainee)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        return Customer::create([
            'email' => $trainee->user->email,
            'name' => $trainee->first_name,
        ]);
    }

    public function getAllCards($customer_id)
    {
        Stripe::setApiKey(env("STRIPE_SECRET"));
        return Customer::allSources($customer_id, ['object' => 'card']);
    }

    public function updateCard($customer_id, $card_id, $data)
    {
        Stripe::setApiKey(env("STRIPE_SECRET"));
        return Customer::updateSource($customer_id, $card_id, [
            [
                'exp_month' => $data['exp_month'],
                'exp_year' => $data['exp_year'],
                'name' => $data['card_holder'],
                'metadata' => [
                    'default' => $data['default'] ?? 0
                ]
            ]
        ]);
    }

    public function deleteCard($customer_id, $card_id)
    {
        Stripe::setApiKey(env("STRIPE_SECRET"));
        return Customer::deleteSource($customer_id, $card_id);
    }
}