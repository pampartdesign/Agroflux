<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drone_missions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('field_boundary_id')->nullable()->constrained('field_boundaries')->nullOnDelete();
            $table->foreignId('drone_id')->nullable()->constrained('drones')->nullOnDelete();
            $table->string('name');
            $table->enum('mission_type', ['spray', 'imaging', 'survey'])->default('imaging');
            $table->enum('status', ['draft', 'planned', 'in_progress', 'completed', 'aborted'])->default('draft');
            // Flight parameters
            $table->decimal('altitude_m', 6, 1)->default(50.0);
            $table->decimal('speed_ms', 5, 1)->default(8.0);
            $table->decimal('spacing_m', 6, 1)->default(10.0);
            $table->smallInteger('angle_deg')->default(0);
            $table->unsignedTinyInteger('overlap_pct')->default(75);
            $table->decimal('buffer_m', 5, 1)->default(5.0);
            // Generated route stored as GeoJSON LineString / FeatureCollection
            $table->longText('waypoints_geojson')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('planned_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drone_missions');
    }
};
