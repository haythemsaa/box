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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // Numéro de facture unique
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('restrict');
            $table->foreignId('contract_id')->nullable()->constrained()->onDelete('set null');
            $table->string('type')->default('invoice'); // invoice, credit_note, deposit, proforma
            $table->string('status')->default('draft'); // draft, sent, paid, partial, overdue, cancelled
            $table->date('issue_date'); // Date d'émission
            $table->date('due_date'); // Date d'échéance
            $table->date('paid_at')->nullable(); // Date de paiement complet
            $table->string('period_start')->nullable(); // Début période facturée (YYYY-MM)
            $table->string('period_end')->nullable(); // Fin période facturée (YYYY-MM)
            $table->json('line_items'); // Lignes de facturation détaillées
            $table->decimal('subtotal_ht', 10, 2); // Sous-total HT
            $table->decimal('vat_amount', 10, 2)->default(0); // Montant TVA
            $table->decimal('vat_rate', 5, 2)->default(20.00); // Taux TVA en %
            $table->decimal('discount_amount', 10, 2)->default(0); // Réduction
            $table->string('discount_type')->nullable(); // percentage, fixed
            $table->decimal('total_ttc', 10, 2); // Total TTC
            $table->decimal('amount_paid', 10, 2)->default(0); // Montant payé
            $table->decimal('amount_due', 10, 2); // Montant restant dû
            $table->string('currency', 3)->default('EUR');
            $table->string('payment_method')->nullable(); // card, sepa, bank_transfer, cash, check
            $table->text('payment_instructions')->nullable(); // Instructions de paiement
            $table->string('billing_address');
            $table->string('billing_city');
            $table->string('billing_postal_code');
            $table->string('billing_country', 2);
            $table->json('legal_mentions')->nullable(); // Mentions légales selon le pays
            $table->string('pdf_path')->nullable(); // Chemin vers le PDF généré
            $table->boolean('sent_to_customer')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->integer('reminder_count')->default(0); // Nombre de relances
            $table->timestamp('last_reminder_at')->nullable();
            $table->text('notes')->nullable(); // Notes internes
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['customer_id', 'status']);
            $table->index('invoice_number');
            $table->index('issue_date');
            $table->index('due_date');
            $table->index(['status', 'due_date']); // Pour les impayés
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
