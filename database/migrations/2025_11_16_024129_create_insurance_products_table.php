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
        Schema::create('insurance_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');

            $table->string('name'); // ex: "Assurance Standard", "Assurance Premium"
            $table->text('description')->nullable();

            // Coverage details
            $table->decimal('max_coverage_amount', 12, 2); // Maximum coverage (e.g., 5000€, 10000€)
            $table->text('coverage_details')->nullable(); // What is covered
            $table->text('exclusions')->nullable(); // What is NOT covered

            // Pricing
            $table->decimal('monthly_price', 8, 2); // Monthly premium
            $table->decimal('yearly_price', 8, 2)->nullable(); // Yearly premium (discounted)
            $table->decimal('commission_rate', 5, 2)->default(25.00); // Commission % for the site (20-40%)

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_mandatory')->default(false); // Can be made mandatory

            $table->timestamps();

            $table->index(['tenant_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_products');
    }
};
