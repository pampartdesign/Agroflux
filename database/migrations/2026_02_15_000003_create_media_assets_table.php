<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('media_assets', function (Blueprint $table) {
            $table->id();

            // NULL tenant_id => global (super-admin curated) asset
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();

            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('disk', 50)->default('public');
            $table->string('path');          // storage path relative to disk
            $table->string('filename');
            $table->string('mime', 100);
            $table->unsignedBigInteger('size')->default(0);

            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();

            $table->string('alt_text')->nullable();

            $table->timestamps();

            $table->index(['tenant_id', 'created_at']);
            $table->index(['mime']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_assets');
    }
};
