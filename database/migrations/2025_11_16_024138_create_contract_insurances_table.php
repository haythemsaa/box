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
        Schema::create('contract_insurances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->onDelete('cascade');
            $table->foreignId('insurance_product_id')->constrained('insurance_products')->onDelete('restrict');

            // Pricing at time of subscription (historical record)
            $table->decimal('monthly_premium', 8, 2);
            $table->decimal('commission_amount', 8, 2); // Commission earned

            // Coverage
            $table->decimal('coverage_amount', 12, 2); // Actual coverage amount
            $table->date('start_date');
            $table->date('end_date')->nullable();

            // Status
            $table->enum('status', ['active', 'cancelled', 'expired'])->default('active');
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();

            $table->index(['contract_id', 'status']);
            $table->index(['insurance_product_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_insurances');
    }
};
