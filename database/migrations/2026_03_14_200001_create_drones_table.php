<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->enum('status', ['active', 'maintenance', 'retired'])->default('active');
            // Default flight configuration
            $table->decimal('default_altitude_m', 6, 1)->default(50.0);
            $table->decimal('default_speed_ms', 5, 1)->default(8.0);
            $table->unsignedTinyInteger('default_overlap_pct')->default(75);
            $table->decimal('default_spacing_m', 6, 1)->default(10.0);
            $table->decimal('default_buffer_m', 5, 1)->default(5.0);
            $table->text('notes')->nullable();
            $table->timestamp('last_flight_at')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drones');
    }
};
