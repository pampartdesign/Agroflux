<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();

            $table->string('type')->default('instock'); // instock, preorder
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('available_qty', 10, 2)->nullable(); // instock only
            $table->timestamp('expected_harvest_at')->nullable(); // preorder
            $table->decimal('upfront_percent', 5, 2)->default(25.00); // preorder only, <=99.99
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['tenant_id', 'is_active']);
            $table->index(['tenant_id', 'type']);
            $table->index(['product_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
