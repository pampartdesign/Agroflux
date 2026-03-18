<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livestock_routine_checks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('animal_id')->nullable()->index();
            $table->string('type', 60);                    // Morning Feeding, Vaccination …
            $table->string('status', 20)->default('normal'); // normal|alert|critical
            $table->date('checked_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('animal_id')->references('id')->on('livestock_animals')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_routine_checks');
    }
};
