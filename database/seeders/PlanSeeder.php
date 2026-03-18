<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::query()->updateOrCreate(
            ['key' => 'core'],
            ['name' => 'AgroFlux Core', 'description' => 'Core features with manual data entry', 'is_active' => true]
        );

        Plan::query()->updateOrCreate(
            ['key' => 'plus'],
            ['name' => 'AgroFlux Plus', 'description' => 'Core + IoT/RFID features', 'is_active' => true]
        );

        Plan::query()->updateOrCreate(
            ['key' => 'logitrace'],
            ['name' => 'LogiTrace', 'description' => 'Trucker logistics module', 'is_active' => true]
        );
    }
}
