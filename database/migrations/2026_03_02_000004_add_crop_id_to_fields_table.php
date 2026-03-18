<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add column only if it doesn't already exist (idempotent — safe to re-run).
        if (!Schema::hasColumn('fields', 'crop_id')) {
            \Illuminate\Support\Facades\DB::statement(
                "ALTER TABLE `fields` ADD COLUMN `crop_id` BIGINT UNSIGNED NULL AFTER `crop_type_id`"
            );
        }

        // Try to add the FK — silently skip if it already exists or crops table
        // is in an unexpected state. App-level validation (exists:crops,id) handles integrity.
        try {
            Schema::table('fields', function (Blueprint $table) {
                $table->foreign('crop_id')
                      ->references('id')->on('crops')
                      ->nullOnDelete();
            });
        } catch (\Throwable $e) {
            // FK could not be added or already exists — column is present, app handles integrity.
        }
    }

    public function down(): void
    {
        try {
            Schema::table('fields', function (Blueprint $table) {
                $table->dropForeign(['crop_id']);
            });
        } catch (\Throwable $e) {
            // FK may not have been created
        }

        Schema::table('fields', function (Blueprint $table) {
            $table->dropColumn('crop_id');
        });
    }
};
