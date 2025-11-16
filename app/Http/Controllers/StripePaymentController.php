<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use App\Notifications\PaymentConfirmedNotification;

class StripePaymentController extends Controller
{
    public function __construct(
        private StripeService $stripeService
    ) {}

    /**
     * Show checkout page for an invoice.
     */
    public function checkout(Invoice $invoice)
    {
        // Verify the invoice belongs to the authenticated customer
        if ($invoice->customer->email !== auth()->user()->email) {
            abort(403, 'Unauthorized access to this invoice');
        }

        // Check if invoice is already paid
        if ($invoice->status === 'paid') {
            return redirect()->route('client.invoices')
                ->with('error', 'This invoice has already been paid');
        }

        return Inertia::render('ClientPortal/Checkout', [
            'invoice' => $invoice->load(['customer', 'currency', 'contract.box.site']),
            'stripeKey' => config('services.stripe.key'),
        ]);
    }

    /**
     * Create a payment intent for an invoice.
     */
    public function createPaymentIntent(Request $request, Invoice $invoice)
    {
        $request->validate([
            'payment_method_id' => 'nullable|string',
        ]);

        try {
            // Verify ownership
            if ($invoice->customer->email !== auth()->user()->email) {
                abort(403);
            }

            // Check if already paid
            if ($invoice->status === 'paid') {
                return response()->json(['error' => 'Invoice already paid'], 400);
            }

            // Create or retrieve Stripe customer
            $stripeCustomer = $this->getOrCreateStripeCustomer($invoice->customer);

            // Create payment intent
            $paymentIntent = $this->stripeService->createPaymentIntent(
                amount: $invoice->total_amount,
                currency: $invoice->currency->code ?? 'EUR',
                metadata: [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'customer_id' => $invoice->customer_id,
                    'contract_id' => $invoice->contract_id,
                ]
            );

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'paymentIntentId' => $paymentIntent->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Payment intent creation failed', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Payment initialization failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Handle successful payment confirmation.
     */
    public function confirmPayment(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            'invoice_id' => 'required|exists:invoices,id',
        ]);

        try {
            $invoice = Invoice::findOrFail($request->invoice_id);

            // Verify ownership
            if ($invoice->customer->email !== auth()->user()->email) {
                abort(403);
            }

            // Retrieve payment intent from Stripe
            $paymentIntent = $this->stripeService->retrievePaymentIntent($request->payment_intent_id);

            if ($paymentIntent->status === 'succeeded') {
                DB::transaction(function () use ($invoice, $paymentIntent) {
                    // Create payment record
                    $payment = Payment::create([
                        'invoice_id' => $invoice->id,
                        'contract_id' => $invoice->contract_id,
                        'customer_id' => $invoice->customer_id,
                        'amount' => $invoice->total_amount,
                        'currency_id' => $invoice->currency_id,
                        'payment_date' => now(),
                        'payment_method' => 'stripe_card',
                        'transaction_id' => $paymentIntent->id,
                        'status' => 'completed',
                        'notes' => 'Stripe payment - ' . ($paymentIntent->payment_method ?? 'card'),
                    ]);

                    // Update invoice status
                    $invoice->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);

                    // Send confirmation notification
                    $invoice->customer->notify(new PaymentConfirmedNotification($payment));
                });

                return redirect()->route('client.invoices')
                    ->with('success', 'Payment successful! Thank you for your payment.');
            }

            return redirect()->route('client.invoices')
                ->with('error', 'Payment was not successful. Please try again.');
        } catch (\Exception $e) {
            Log::error('Payment confirmation failed', [
                'payment_intent_id' => $request->payment_intent_id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('client.invoices')
                ->with('error', 'An error occurred while processing your payment.');
        }
    }

    /**
     * Handle Stripe webhooks.
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe webhook: Invalid payload', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook: Invalid signature', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($event->data->object);
                break;

            case 'charge.refunded':
                $this->handleChargeRefunded($event->data->object);
                break;

            case 'customer.subscription.created':
            case 'customer.subscription.updated':
            case 'customer.subscription.deleted':
                // Handle subscription events (for future recurring billing)
                Log::info('Stripe subscription event', [
                    'type' => $event->type,
                    'subscription_id' => $event->data->object->id ?? null,
                ]);
                break;

            default:
                Log::info('Stripe webhook: Unhandled event type', ['type' => $event->type]);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle successful payment intent.
     */
    private function handlePaymentIntentSucceeded($paymentIntent)
    {
        $invoiceId = $paymentIntent->metadata->invoice_id ?? null;

        if (!$invoiceId) {
            Log::warning('Payment intent succeeded but no invoice_id in metadata', [
                'payment_intent_id' => $paymentIntent->id,
            ]);
            return;
        }

        try {
            $invoice = Invoice::find($invoiceId);

            if (!$invoice) {
                Log::error('Invoice not found for payment intent', [
                    'invoice_id' => $invoiceId,
                    'payment_intent_id' => $paymentIntent->id,
                ]);
                return;
            }

            // Check if payment already recorded
            $existingPayment = Payment::where('transaction_id', $paymentIntent->id)->first();
            if ($existingPayment) {
                Log::info('Payment already recorded', ['payment_intent_id' => $paymentIntent->id]);
                return;
            }

            DB::transaction(function () use ($invoice, $paymentIntent) {
                // Create payment record
                $payment = Payment::create([
                    'invoice_id' => $invoice->id,
                    'contract_id' => $invoice->contract_id,
                    'customer_id' => $invoice->customer_id,
                    'amount' => $paymentIntent->amount / 100, // Stripe uses cents
                    'currency_id' => $invoice->currency_id,
                    'payment_date' => now(),
                    'payment_method' => 'stripe_card',
                    'transaction_id' => $paymentIntent->id,
                    'status' => 'completed',
                    'notes' => 'Stripe webhook payment confirmation',
                ]);

                // Update invoice
                $invoice->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);

                // Send notification
                $invoice->customer->notify(new PaymentConfirmedNotification($payment));
            });

            Log::info('Payment processed successfully via webhook', [
                'invoice_id' => $invoice->id,
                'payment_intent_id' => $paymentIntent->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing payment intent succeeded webhook', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle failed payment intent.
     */
    private function handlePaymentIntentFailed($paymentIntent)
    {
        $invoiceId = $paymentIntent->metadata->invoice_id ?? null;

        if (!$invoiceId) {
            return;
        }

        try {
            $invoice = Invoice::find($invoiceId);

            if (!$invoice) {
                return;
            }

            // Create failed payment record
            Payment::create([
                'invoice_id' => $invoice->id,
                'contract_id' => $invoice->contract_id,
                'customer_id' => $invoice->customer_id,
                'amount' => $paymentIntent->amount / 100,
                'currency_id' => $invoice->currency_id,
                'payment_date' => now(),
                'payment_method' => 'stripe_card',
                'transaction_id' => $paymentIntent->id,
                'status' => 'failed',
                'notes' => 'Payment failed: ' . ($paymentIntent->last_payment_error->message ?? 'Unknown error'),
            ]);

            Log::warning('Payment failed', [
                'invoice_id' => $invoice->id,
                'payment_intent_id' => $paymentIntent->id,
                'error' => $paymentIntent->last_payment_error->message ?? 'Unknown',
            ]);

            // TODO: Send payment failed notification to customer
        } catch (\Exception $e) {
            Log::error('Error processing payment intent failed webhook', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle charge refunded.
     */
    private function handleChargeRefunded($charge)
    {
        $paymentIntentId = $charge->payment_intent ?? null;

        if (!$paymentIntentId) {
            return;
        }

        try {
            $payment = Payment::where('transaction_id', $paymentIntentId)->first();

            if (!$payment) {
                Log::warning('Payment not found for refunded charge', [
                    'charge_id' => $charge->id,
                    'payment_intent_id' => $paymentIntentId,
                ]);
                return;
            }

            DB::transaction(function () use ($payment, $charge) {
                $payment->update([
                    'status' => 'refunded',
                    'notes' => ($payment->notes ?? '') . ' | Refunded on ' . now()->format('Y-m-d H:i:s'),
                ]);

                // Update invoice back to unpaid if fully refunded
                if ($charge->amount_refunded === $charge->amount) {
                    $payment->invoice->update([
                        'status' => 'unpaid',
                        'paid_at' => null,
                    ]);
                }
            });

            Log::info('Charge refunded processed', [
                'payment_id' => $payment->id,
                'charge_id' => $charge->id,
                'amount_refunded' => $charge->amount_refunded / 100,
            ]);

            // TODO: Send refund notification to customer
        } catch (\Exception $e) {
            Log::error('Error processing charge refunded webhook', [
                'charge_id' => $charge->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get or create Stripe customer for a BoxManager customer.
     */
    private function getOrCreateStripeCustomer($customer)
    {
        // Check if customer already has a Stripe ID stored
        if ($customer->stripe_customer_id) {
            return $customer->stripe_customer_id;
        }

        // Create new Stripe customer
        $stripeCustomer = $this->stripeService->createCustomer(
            email: $customer->email,
            name: $customer->first_name . ' ' . $customer->last_name,
            metadata: [
                'customer_id' => $customer->id,
                'phone' => $customer->phone_mobile ?? $customer->phone_landline,
            ]
        );

        // Store Stripe customer ID
        $customer->update(['stripe_customer_id' => $stripeCustomer->id]);

        return $stripeCustomer->id;
    }
}
