<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_boundaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('field_id')->nullable()->constrained('fields')->nullOnDelete();
            $table->unsignedBigInteger('crop_id')->nullable(); // soft ref — no FK, crops table may not exist
            $table->string('name');
            $table->longText('geojson');                       // GeoJSON Polygon feature
            $table->decimal('area_ha', 10, 4)->nullable();     // Calculated by Turf.js
            $table->decimal('centroid_lat', 10, 7)->nullable();
            $table->decimal('centroid_lng', 10, 7)->nullable();
            $table->decimal('perimeter_m', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'field_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_boundaries');
    }
};
