<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('delivery_offers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('delivery_request_id')->constrained('delivery_requests')->cascadeOnDelete();
            $table->foreignId('trucker_user_id')->constrained('users')->cascadeOnDelete();

            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('EUR');

            $table->text('message')->nullable();

            $table->string('status')->default('sent'); // sent|accepted|rejected

            $table->timestamps();

            $table->index(['delivery_request_id', 'status']);
            $table->index(['trucker_user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_offers');
    }
};
