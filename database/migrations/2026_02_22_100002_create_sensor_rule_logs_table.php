<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sensor_rule_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sensor_rule_id')
                  ->constrained('sensor_rules')
                  ->cascadeOnDelete();

            $table->foreignId('tenant_id')
                  ->constrained('tenants')
                  ->cascadeOnDelete();

            // executed | skipped_weather | skipped_sensor | waiting_retry | retried
            $table->string('status', 30);

            // Human-readable summary of what happened
            $table->text('summary');

            // Snapshot of the data used during evaluation (for debugging)
            $table->json('context')->nullable();
            // context keys: trigger_type, trigger_fired, weather_prob, sensor_value,
            //               condition_blocked, retry_scheduled_at, alert_created

            $table->timestamp('evaluated_at');

            $table->timestamps();

            $table->index(['sensor_rule_id', 'evaluated_at']);
            $table->index(['tenant_id', 'evaluated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensor_rule_logs');
    }
};
