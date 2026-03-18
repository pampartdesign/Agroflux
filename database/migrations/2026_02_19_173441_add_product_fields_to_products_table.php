<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Sub-category (child of category_id)
            $table->foreignId('subcategory_id')
                  ->nullable()
                  ->after('category_id')
                  ->constrained('catalog_categories')
                  ->nullOnDelete();

            // Stock status: in_stock | pre_order
            $table->string('stock_status', 20)->default('in_stock')->after('unit');

            // Inventory quantity (matches unit of measure)
            $table->decimal('inventory', 12, 3)->nullable()->after('stock_status');

            // Unit price for buyers
            $table->decimal('unit_price', 12, 2)->nullable()->after('inventory');

            // Product image (stored in storage/app/public)
            $table->string('image_path')->nullable()->after('unit_price');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['subcategory_id']);
            $table->dropColumn(['subcategory_id', 'stock_status', 'inventory', 'unit_price', 'image_path']);
        });
    }
};
