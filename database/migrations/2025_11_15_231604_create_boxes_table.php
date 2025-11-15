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
        Schema::create('boxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('floor_id')->constrained()->onDelete('cascade');
            $table->string('number')->unique(); // Numéro unique de la box
            $table->string('type')->default('standard'); // standard, climatized, outdoor
            $table->decimal('length', 5, 2); // Longueur en cm
            $table->decimal('width', 5, 2); // Largeur en cm
            $table->decimal('height', 5, 2); // Hauteur en cm
            $table->decimal('volume', 8, 3); // Volume en m³ (calculé)
            $table->decimal('area', 8, 2); // Surface en m² (calculée)
            $table->decimal('monthly_price', 10, 2); // Prix mensuel
            $table->string('status')->default('available'); // available, occupied, maintenance, out_of_service
            $table->boolean('has_vehicle_access')->default(false);
            $table->boolean('is_ground_floor')->default(false);
            $table->boolean('has_power_outlet')->default(false);
            $table->json('features')->nullable(); // Caractéristiques additionnelles
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['floor_id', 'status']);
            $table->index('number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boxes');
    }
};
