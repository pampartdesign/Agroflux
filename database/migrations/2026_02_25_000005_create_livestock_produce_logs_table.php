<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livestock_produce_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('animal_id')->nullable()->index();
            $table->string('type', 60);                   // Milk, Eggs, Meat …
            $table->decimal('quantity', 10, 2);
            $table->string('unit', 30)->default('Litres');
            $table->date('logged_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('animal_id')->references('id')->on('livestock_animals')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_produce_logs');
    }
};
