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
        Schema::create('floors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->onDelete('cascade');
            $table->integer('level'); // 0 = RDC, -1 = Sous-sol, 1+ = Étages
            $table->string('name')->nullable();
            $table->boolean('has_elevator')->default(false);
            $table->boolean('has_freight_elevator')->default(false);
            $table->decimal('corridor_width', 5, 2)->nullable(); // Largeur couloirs en cm
            $table->timestamps();
            $table->softDeletes();

            $table->index('building_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('floors');
    }
};
