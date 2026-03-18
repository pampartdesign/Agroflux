<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenant_users', function (Blueprint $table) {
            if (!Schema::hasColumn('tenant_users', 'permissions')) {
                // JSON array of module keys the member can access.
                // null = inherit from plan (default). Set explicitly to restrict/expand.
                $table->json('permissions')->nullable()->after('role');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenant_users', function (Blueprint $table) {
            if (Schema::hasColumn('tenant_users', 'permissions')) {
                $table->dropColumn('permissions');
            }
        });
    }
};
