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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->unique(); // Numéro unique du contrat
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('restrict');
            $table->foreignId('box_id')->constrained()->onDelete('restrict');
            $table->foreignId('site_id')->constrained()->onDelete('restrict');
            $table->string('status')->default('pending'); // pending, active, suspended, terminated, cancelled
            $table->date('start_date'); // Date de début de location
            $table->date('end_date')->nullable(); // Date de fin (si durée déterminée)
            $table->string('duration_type')->default('indefinite'); // fixed, indefinite
            $table->integer('initial_duration_months')->nullable(); // Durée initiale en mois
            $table->boolean('auto_renewal')->default(true); // Reconduction automatique
            $table->string('billing_frequency')->default('monthly'); // monthly, quarterly, annually
            $table->decimal('monthly_price', 10, 2); // Prix mensuel TTC
            $table->decimal('deposit_amount', 10, 2)->default(0); // Caution/dépôt de garantie
            $table->decimal('setup_fee', 10, 2)->default(0); // Frais de dossier
            $table->boolean('insurance_included')->default(false);
            $table->decimal('insurance_amount', 10, 2)->nullable(); // Montant assurance mensuelle
            $table->decimal('declared_value', 10, 2)->nullable(); // Valeur déclarée des biens
            $table->json('stored_items')->nullable(); // Inventaire des objets stockés
            $table->string('payment_method')->default('card'); // card, sepa, bank_transfer, cash, check
            $table->string('access_code')->nullable(); // Code d'accès généré
            $table->json('access_schedule')->nullable(); // Horaires d'accès autorisés
            $table->text('special_conditions')->nullable(); // Conditions particulières
            $table->date('signed_at')->nullable(); // Date de signature
            $table->string('signature_method')->nullable(); // electronic, paper, tablet
            $table->json('signed_documents')->nullable(); // Documents signés
            $table->date('terminated_at')->nullable(); // Date de résiliation
            $table->string('termination_reason')->nullable(); // Motif de résiliation
            $table->integer('notice_period_days')->default(30); // Préavis en jours
            $table->text('notes')->nullable(); // Notes internes
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['customer_id', 'status']);
            $table->index(['box_id', 'status']);
            $table->index('contract_number');
            $table->index('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
