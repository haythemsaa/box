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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique(); // EUR, GBP, CHF, USD
            $table->string('name'); // Euro, British Pound, Swiss Franc
            $table->string('symbol'); // €, £, CHF, $
            $table->decimal('exchange_rate_to_eur', 10, 6)->default(1.000000); // Rate to EUR (base currency)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
