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
        Schema::create('sepa_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();

            $table->string('transaction_reference')->unique(); // Unique reference for this transaction
            $table->string('mandate_reference'); // SEPA mandate reference
            $table->string('creditor_identifier'); // Your SEPA creditor ID

            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('EUR');

            $table->string('debtor_name'); // Customer name
            $table->string('debtor_iban');
            $table->string('debtor_bic')->nullable();

            $table->date('collection_date'); // Date when the debit will be executed
            $table->date('requested_date'); // Date when the request was made

            $table->enum('status', [
                'pending',      // Created, waiting to be sent to bank
                'submitted',    // Submitted to bank
                'confirmed',    // Confirmed by bank
                'completed',    // Successfully debited
                'failed',       // Failed (insufficient funds, etc.)
                'rejected',     // Rejected by bank
                'cancelled',    // Cancelled by us
            ])->default('pending');

            $table->string('end_to_end_id')->nullable(); // End-to-end identification
            $table->text('description')->nullable();

            $table->text('bank_response')->nullable(); // Response from bank
            $table->string('failure_reason')->nullable(); // Reason if failed/rejected

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index('collection_date');
            $table->index(['customer_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sepa_transactions');
    }
};
