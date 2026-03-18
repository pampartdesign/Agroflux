<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->nullable()->after('description');
            $table->string('billing_cycle', 20)->nullable()->after('price'); // monthly, yearly, custom
            $table->json('modules')->nullable()->after('billing_cycle');     // ["core","farm","livestock",...]
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['price', 'billing_cycle', 'modules']);
        });
    }
};
