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
        Schema::create('vat_rates', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 2); // FR, BE, NL, DE, etc.
            $table->string('country_name'); // France, Belgium, Netherlands
            $table->decimal('standard_rate', 5, 2); // 20.00, 21.00, 19.00
            $table->decimal('reduced_rate', 5, 2)->nullable(); // For specific services
            $table->decimal('super_reduced_rate', 5, 2)->nullable(); // Extra low rate
            $table->boolean('is_eu_member')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('country_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vat_rates');
    }
};
