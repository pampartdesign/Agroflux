<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketplace_delivery_requests', function (Blueprint $table) {
            $table->string('frequency')->default('weekly')->after('qty'); // daily|weekly|biweekly|monthly
            $table->date('start_date')->nullable()->after('frequency');
        });
    }

    public function down(): void
    {
        Schema::table('marketplace_delivery_requests', function (Blueprint $table) {
            $table->dropColumn(['frequency', 'start_date']);
        });
    }
};
