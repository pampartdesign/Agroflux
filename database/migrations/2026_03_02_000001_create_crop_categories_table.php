<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crop_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique();
            $table->timestamps();
        });

        // Seed categories
        $now = now();
        DB::table('crop_categories')->insert([
            ['name' => 'Cereal',     'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Oilseed',    'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Industrial', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Protein',    'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Root',       'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Vegetable',  'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Fruit',      'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Fodder',     'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Other',      'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_categories');
    }
};
