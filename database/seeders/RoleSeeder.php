<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Super Admin',
            'Admin',
            'Farmer/Seller',
            'Buyer',
            'Trucker/Delivery',
            'Auditor',
        ];

        foreach ($roles as $role) {
            Role::findOrCreate($role);
        }
    }
}
