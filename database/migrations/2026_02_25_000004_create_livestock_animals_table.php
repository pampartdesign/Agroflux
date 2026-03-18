<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livestock_animals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('tag', 60);                    // EAR-001 etc.
            $table->string('species', 60);                // Cattle, Sheep …
            $table->string('breed', 100)->nullable();
            $table->string('gender', 10)->nullable();     // male|female
            $table->date('dob')->nullable();
            $table->string('status', 30)->default('active'); // active|pregnant|sick|sold
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_animals');
    }
};
