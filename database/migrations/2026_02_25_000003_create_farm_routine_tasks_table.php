<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farm_routine_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('field_id')->nullable()->index();
            $table->string('type', 60);           // Irrigation, Fertilisation, etc.
            $table->string('status', 30)->default('pending'); // pending|done|skipped
            $table->date('scheduled_at');
            $table->date('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('field_id')->references('id')->on('fields')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_routine_tasks');
    }
};
