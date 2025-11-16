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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('iban')->nullable()->after('country');
            $table->string('bic')->nullable()->after('iban');
            $table->string('bank_name')->nullable()->after('bic');
            $table->string('account_holder_name')->nullable()->after('bank_name');
            $table->boolean('sepa_mandate_signed')->default(false)->after('account_holder_name');
            $table->string('sepa_mandate_reference')->nullable()->unique()->after('sepa_mandate_signed');
            $table->date('sepa_mandate_signed_date')->nullable()->after('sepa_mandate_reference');
            $table->string('sepa_mandate_type')->nullable()->after('sepa_mandate_signed_date'); // RCUR (recurrent) or OOFF (one-off)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'iban',
                'bic',
                'bank_name',
                'account_holder_name',
                'sepa_mandate_signed',
                'sepa_mandate_reference',
                'sepa_mandate_signed_date',
                'sepa_mandate_type',
            ]);
        });
    }
};
