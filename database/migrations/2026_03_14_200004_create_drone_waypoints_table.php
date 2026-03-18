<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drone_waypoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mission_id')->constrained('drone_missions')->cascadeOnDelete();
            $table->unsignedSmallInteger('sequence');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('altitude_m', 6, 1)->default(50.0);
            $table->enum('action', ['waypoint', 'photo', 'spray', 'hover'])->default('waypoint');
            $table->timestamps();

            $table->index(['mission_id', 'sequence']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drone_waypoints');
    }
};
