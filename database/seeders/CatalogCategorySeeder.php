<?php

namespace Database\Seeders;

use App\Models\CatalogCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogCategorySeeder extends Seeder
{
    public function run(): void
    {
        $roots = [
            'Livestock Products',
            'Vegetables & Fruits',
        ];

        foreach ($roots as $name) {
            CatalogCategory::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'parent_id' => null]
            );
        }
    }
}
