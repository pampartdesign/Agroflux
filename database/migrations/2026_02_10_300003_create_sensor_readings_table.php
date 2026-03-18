<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('sensor_id')->constrained('sensors')->cascadeOnDelete();

            $table->decimal('value', 12, 4)->nullable();
            $table->json('payload')->nullable();
            $table->boolean('is_manual')->default(false);
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['tenant_id','sensor_id','recorded_at']);
            $table->index(['tenant_id','recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensor_readings');
    }
};
