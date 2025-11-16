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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->foreignId('box_id')->constrained('boxes')->onDelete('cascade');

            // Reservation reference
            $table->string('reservation_number')->unique();

            // Customer information (not yet a Customer record)
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');

            // Reservation details
            $table->date('desired_start_date');
            $table->integer('estimated_duration_months')->nullable();
            $table->text('notes')->nullable();

            // Reservation status
            $table->enum('status', ['pending', 'confirmed', 'converted', 'cancelled', 'expired'])->default('pending');

            // Timestamps and tracking
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->foreignId('converted_to_contract_id')->nullable()->constrained('contracts')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['status', 'expires_at']);
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
