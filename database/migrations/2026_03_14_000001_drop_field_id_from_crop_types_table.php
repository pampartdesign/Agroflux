<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('crop_types', 'field_id')) {
            return; // Already done
        }

        // Disable FK checks so we can drop the column whether or not
        // the foreign key constraint exists (it may have been created
        // conditionally / already dropped).
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        Schema::table('crop_types', function (Blueprint $table) {
            $table->dropColumn('field_id');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        if (!Schema::hasColumn('crop_types', 'field_id')) {
            Schema::table('crop_types', function (Blueprint $table) {
                $table->unsignedBigInteger('field_id')->nullable()->after('tenant_id');
            });
        }
    }
};
