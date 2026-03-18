<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $existing = collect(DB::select('SHOW COLUMNS FROM orders'))->pluck('Field')->flip();

        Schema::table('orders', function (Blueprint $table) use ($existing) {
            if (!$existing->has('customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('tenant_id');
                try {
                    $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
                } catch (\Throwable) {}
            }
            // If column already exists we leave it as-is (no FK attempt — column type may differ)

            if (!$existing->has('customer_surname')) {
                $table->string('customer_surname')->nullable()->after('customer_name');
            }
            if (!$existing->has('customer_phone')) {
                $table->string('customer_phone', 30)->nullable()->after('customer_email');
            }
            if (!$existing->has('document_type')) {
                $table->enum('document_type', ['receipt', 'invoice'])->default('receipt')->after('customer_phone');
            }
            if (!$existing->has('company_name')) {
                $table->string('company_name')->nullable()->after('document_type');
            }
            if (!$existing->has('vat_country')) {
                $table->char('vat_country', 2)->nullable()->after('company_name');
            }
            if (!$existing->has('vat_number')) {
                $table->string('vat_number', 30)->nullable()->after('vat_country');
            }
            if (!$existing->has('delivery_address')) {
                $table->string('delivery_address')->nullable()->after('vat_number');
            }
            if (!$existing->has('delivery_city')) {
                $table->string('delivery_city')->nullable()->after('delivery_address');
            }
            if (!$existing->has('delivery_zip')) {
                $table->string('delivery_zip', 20)->nullable()->after('delivery_city');
            }
            if (!$existing->has('delivery_country')) {
                $table->char('delivery_country', 2)->nullable()->after('delivery_zip');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            try { $table->dropForeign(['customer_id']); } catch (\Throwable) {}
            $cols = array_filter([
                'customer_id', 'customer_surname', 'customer_phone',
                'document_type', 'company_name', 'vat_country', 'vat_number',
                'delivery_address', 'delivery_city', 'delivery_zip', 'delivery_country',
            ], fn ($c) => Schema::hasColumn('orders', $c));
            if ($cols) $table->dropColumn(array_values($cols));
        });
    }
};
