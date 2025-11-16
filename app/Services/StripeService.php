<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Customer as StripeCustomer;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createPaymentIntent(float $amount, string $currency = 'eur', array $metadata = []): PaymentIntent
    {
        return PaymentIntent::create([
            'amount' => $amount * 100, // Stripe uses cents
            'currency' => strtolower($currency),
            'metadata' => $metadata,
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);
    }

    public function createCustomer(string $email, string $name, array $metadata = []): StripeCustomer
    {
        return StripeCustomer::create([
            'email' => $email,
            'name' => $name,
            'metadata' => $metadata,
        ]);
    }

    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntent
    {
        return PaymentIntent::retrieve($paymentIntentId);
    }
}
