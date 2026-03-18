<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('catalog_categories')->nullOnDelete();

            $table->string('sku')->nullable();
            $table->string('default_name');
            $table->text('default_description')->nullable();
            $table->string('unit')->nullable(); // kg, piece, litre
            $table->timestamps();

            $table->index(['tenant_id', 'category_id']);
            $table->index(['tenant_id', 'default_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
