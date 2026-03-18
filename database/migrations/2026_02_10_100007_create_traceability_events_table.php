<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('traceability_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('batch_id')->constrained('batches')->cascadeOnDelete();
            $table->string('event_type'); // planting, treatment, harvest, packaging, shipping...
            $table->timestamp('occurred_at');
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'batch_id', 'occurred_at']);
            $table->index(['batch_id', 'event_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('traceability_events');
    }
};
