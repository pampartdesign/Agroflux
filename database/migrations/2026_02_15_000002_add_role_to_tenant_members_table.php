<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('tenant_members')) {
            return;
        }

        Schema::table('tenant_members', function (Blueprint $table) {
            if (!Schema::hasColumn('tenant_members', 'role')) {
                $table->string('role', 30)->default('member')->after('user_id');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('tenant_members')) {
            return;
        }

        Schema::table('tenant_members', function (Blueprint $table) {
            if (Schema::hasColumn('tenant_members', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
