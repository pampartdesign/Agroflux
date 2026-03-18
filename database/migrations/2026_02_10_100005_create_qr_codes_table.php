<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->string('qrable_type');
            $table->unsignedBigInteger('qrable_id');
            $table->string('public_token')->unique();
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();

            $table->index(['qrable_type', 'qrable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};
