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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom de l'entreprise
            $table->string('subdomain')->unique(); // Sous-domaine dédié
            $table->string('custom_domain')->nullable()->unique(); // Domaine personnalisé
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('country', 2)->default('FR'); // Code pays ISO
            $table->string('language', 5)->default('fr'); // Langue par défaut
            $table->string('currency', 3)->default('EUR'); // Devise
            $table->string('plan')->default('starter'); // starter, professional, enterprise
            $table->integer('max_sites')->default(1); // Nombre de sites autorisés
            $table->integer('max_boxes')->nullable(); // Limite de box selon le plan
            $table->json('settings')->nullable(); // Configuration personnalisée (branding, etc.)
            $table->boolean('is_active')->default(true);
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
