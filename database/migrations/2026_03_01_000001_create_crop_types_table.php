<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crop_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('name', 150);

            // ── Minimum Requirements ──────────────────────────────────────
            $table->decimal('min_soil_moisture_pct', 5, 2)->nullable(); // % e.g. 40.00
            $table->decimal('min_daily_water_lt', 8, 2)->nullable();    // liters/100m²/day
            $table->decimal('min_temperature_c', 5, 2)->nullable();     // °C
            $table->decimal('max_temperature_c', 5, 2)->nullable();     // °C
            $table->decimal('min_soil_ph', 4, 2)->nullable();           // e.g. 5.5
            $table->decimal('max_soil_ph', 4, 2)->nullable();           // e.g. 7.5
            $table->decimal('min_sunlight_h', 4, 2)->nullable();        // hours/day
            $table->unsignedSmallInteger('growing_days')->nullable();   // days planting → harvest
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_types');
    }
};
