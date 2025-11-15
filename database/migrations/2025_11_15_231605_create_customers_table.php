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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('type')->default('individual'); // individual, company
            $table->string('civility')->nullable(); // mr, mrs, miss
            $table->string('first_name');
            $table->string('last_name');
            $table->string('company_name')->nullable();
            $table->string('siret')->nullable(); // Pour les professionnels français
            $table->string('vat_number')->nullable(); // Numéro de TVA intracommunautaire
            $table->string('email')->unique();
            $table->string('phone_mobile')->nullable();
            $table->string('phone_landline')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('address');
            $table->string('address_complement')->nullable();
            $table->string('postal_code');
            $table->string('city');
            $table->string('country', 2)->default('FR');
            $table->string('billing_address')->nullable(); // Si différente
            $table->string('billing_postal_code')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_country', 2)->nullable();
            $table->string('language', 5)->default('fr');
            $table->json('communication_preferences')->nullable(); // Email, SMS, Push
            $table->string('acquisition_source')->nullable(); // web, phone, visit, referral
            $table->json('tags')->nullable(); // Tags personnalisables
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_vip')->default(false);
            $table->text('notes')->nullable(); // Notes internes
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'email']);
            $table->index(['tenant_id', 'last_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
