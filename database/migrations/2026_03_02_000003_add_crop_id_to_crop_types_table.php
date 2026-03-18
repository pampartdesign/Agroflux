<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make `name` nullable (existing records keep their value; new records
        // derive the display name from the linked global crop).
        // We use DB::statement to avoid the doctrine/dbal dependency.
        DB::statement("ALTER TABLE crop_types MODIFY COLUMN name VARCHAR(150) NULL");

        Schema::table('crop_types', function (Blueprint $table) {
            // crop_id → references the global crops table (nullable for backward compat)
            $table->unsignedBigInteger('crop_id')->nullable()->after('tenant_id');

            $table->foreign('crop_id')
                  ->references('id')->on('crops')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('crop_types', function (Blueprint $table) {
            $table->dropForeign(['crop_id']);
            $table->dropColumn('crop_id');
        });

        // Restore name to NOT NULL (fill nulls first to avoid constraint error)
        DB::statement("UPDATE crop_types SET name = 'Unknown' WHERE name IS NULL");
        DB::statement("ALTER TABLE crop_types MODIFY COLUMN name VARCHAR(150) NOT NULL");
    }
};
