<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('farm_id')->index();
            $table->string('name', 150);
            $table->decimal('area_ha', 8, 2)->nullable();
            $table->string('crop_type', 120)->nullable();
            // active = growing, fallow = resting, harvested = done, prep = preparing
            $table->string('status', 30)->default('active');
            $table->date('planted_at')->nullable();
            $table->date('harvest_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};
