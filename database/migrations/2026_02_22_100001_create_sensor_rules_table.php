<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sensor_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();

            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            // ── Trigger ──────────────────────────────────────────────────────
            // 'time'             → fire at a fixed clock time every day
            // 'sensor_threshold' → fire when a sensor reading crosses a value
            $table->string('trigger_type', 30)->default('time');         // time|sensor_threshold
            $table->string('trigger_time', 5)->nullable();               // "14:00"
            $table->foreignId('trigger_sensor_id')->nullable()
                  ->constrained('sensors')->nullOnDelete();
            $table->string('trigger_operator', 5)->nullable();           // lt|gt|lte|gte|eq
            $table->decimal('trigger_threshold', 10, 2)->nullable();

            // ── Weather Condition ─────────────────────────────────────────────
            // If enabled: skip (hold) the action when rain probability >= threshold
            $table->boolean('weather_condition_enabled')->default(false);
            $table->unsignedTinyInteger('weather_rain_skip_pct')->nullable(); // 0–100

            // ── Sensor Condition ──────────────────────────────────────────────
            // If enabled: skip (hold) the action when a sensor reading matches the condition
            $table->boolean('sensor_condition_enabled')->default(false);
            $table->foreignId('condition_sensor_id')->nullable()
                  ->constrained('sensors')->nullOnDelete();
            $table->string('condition_operator', 5)->nullable();         // lt|gt|lte|gte|eq
            $table->decimal('condition_threshold', 10, 2)->nullable();

            // ── Action ────────────────────────────────────────────────────────
            $table->string('action_type', 20)->default('both');          // log|notify|both
            $table->text('action_notes')->nullable();

            // ── Retry ─────────────────────────────────────────────────────────
            // 'wait_window'    → wait X minutes, then retry if no rain arrived
            // 'next_scheduled' → skip today, fire again at next trigger time
            // 'both'           → try wait window first, then fall back to next scheduled
            $table->string('retry_type', 20)->default('next_scheduled'); // wait_window|next_scheduled|both
            $table->unsignedSmallInteger('retry_wait_minutes')->nullable();

            // ── Execution tracking ────────────────────────────────────────────
            $table->timestamp('last_triggered_at')->nullable();
            // executed|skipped_weather|skipped_sensor|waiting_retry|retried
            $table->string('last_status', 30)->nullable();
            $table->timestamp('next_retry_at')->nullable();

            $table->timestamps();

            $table->index(['tenant_id', 'is_active']);
            $table->index('trigger_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensor_rules');
    }
};
