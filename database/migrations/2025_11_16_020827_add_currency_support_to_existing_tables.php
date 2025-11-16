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
        // Add currency to tenants
        Schema::table('tenants', function (Blueprint $table) {
            $table->foreignId('currency_id')->nullable()->after('plan')->constrained('currencies')->onDelete('set null');
        });

        // Add currency to sites
        Schema::table('sites', function (Blueprint $table) {
            $table->foreignId('currency_id')->nullable()->after('email')->constrained('currencies')->onDelete('set null');
        });

        // Add currency and VAT fields to invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('currency_id')->nullable()->after('total_amount')->constrained('currencies')->onDelete('set null');
            $table->decimal('subtotal_amount', 10, 2)->after('total_amount')->default(0);
            $table->decimal('vat_rate', 5, 2)->after('subtotal_amount')->default(0);
            $table->decimal('vat_amount', 10, 2)->after('vat_rate')->default(0);
            $table->string('country_code', 2)->nullable()->after('vat_amount');
        });

        // Add currency to payments
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('currency_id')->nullable()->after('amount')->constrained('currencies')->onDelete('set null');
        });

        // Add currency to contracts
        Schema::table('contracts', function (Blueprint $table) {
            $table->foreignId('currency_id')->nullable()->after('deposit_amount')->constrained('currencies')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');
        });

        Schema::table('sites', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn(['currency_id', 'subtotal_amount', 'vat_rate', 'vat_amount', 'country_code']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');
        });
    }
};
