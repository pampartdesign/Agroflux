<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Idempotent — safe to re-run if column already exists.
        if (!Schema::hasColumn('crop_types', 'field_id')) {
            \Illuminate\Support\Facades\DB::statement(
                "ALTER TABLE `crop_types` ADD COLUMN `field_id` BIGINT UNSIGNED NULL AFTER `tenant_id`"
            );
        }

        try {
            Schema::table('crop_types', function (Blueprint $table) {
                $table->foreign('field_id')
                      ->references('id')->on('fields')
                      ->nullOnDelete();
            });
        } catch (\Throwable $e) {
            // FK already exists or fields table state uncertain — column is present.
        }
    }

    public function down(): void
    {
        try {
            Schema::table('crop_types', function (Blueprint $table) {
                $table->dropForeign(['field_id']);
            });
        } catch (\Throwable $e) {}

        if (Schema::hasColumn('crop_types', 'field_id')) {
            Schema::table('crop_types', function (Blueprint $table) {
                $table->dropColumn('field_id');
            });
        }
    }
};
