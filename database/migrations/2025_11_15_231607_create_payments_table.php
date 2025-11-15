<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_reference')->unique(); // Référence unique du paiement
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('restrict');
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('contract_id')->nullable()->constrained()->onDelete('set null');
            $table->string('type')->default('payment'); // payment, refund, deposit_return
            $table->string('status')->default('pending'); // pending, processing, completed, failed, refunded, cancelled
            $table->string('method'); // card, sepa, bank_transfer, cash, check, paypal, apple_pay, google_pay
            $table->decimal('amount', 10, 2); // Montant du paiement
            $table->string('currency', 3)->default('EUR');
            $table->decimal('fee_amount', 10, 2)->default(0); // Frais de transaction
            $table->decimal('net_amount', 10, 2); // Montant net (amount - fee)
            $table->string('payment_gateway')->nullable(); // stripe, adyen, paypal
            $table->string('gateway_transaction_id')->nullable(); // ID transaction du gateway
            $table->string('gateway_payment_intent_id')->nullable(); // Payment Intent ID (Stripe)
            $table->json('gateway_response')->nullable(); // Réponse complète du gateway
            $table->string('card_brand')->nullable(); // visa, mastercard, amex
            $table->string('card_last4')->nullable(); // 4 derniers chiffres
            $table->string('sepa_mandate_id')->nullable(); // ID mandat SEPA
            $table->string('bank_account_iban')->nullable(); // IBAN (partiel, masqué)
            $table->string('check_number')->nullable(); // Numéro de chèque
            $table->date('check_date')->nullable(); // Date du chèque
            $table->timestamp('processed_at')->nullable(); // Date de traitement effectif
            $table->timestamp('failed_at')->nullable(); // Date d'échec
            $table->string('failure_reason')->nullable(); // Raison de l'échec
            $table->string('failure_code')->nullable(); // Code erreur
            $table->timestamp('refunded_at')->nullable(); // Date de remboursement
            $table->decimal('refunded_amount', 10, 2)->nullable(); // Montant remboursé
            $table->string('refund_reason')->nullable(); // Raison du remboursement
            $table->text('description')->nullable(); // Description du paiement
            $table->string('receipt_url')->nullable(); // URL du reçu
            $table->string('receipt_number')->nullable(); // Numéro de reçu
            $table->json('metadata')->nullable(); // Métadonnées additionnelles
            $table->text('notes')->nullable(); // Notes internes
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['customer_id', 'status']);
            $table->index(['invoice_id', 'status']);
            $table->index('payment_reference');
            $table->index('gateway_transaction_id');
            $table->index(['status', 'processed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
