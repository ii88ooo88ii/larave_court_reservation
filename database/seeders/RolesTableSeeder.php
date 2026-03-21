<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'id' => 1,
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrator with full access',
            ],
            [
                'id' => 2,
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Manager with limited access',
            ],
            [
                'id' => 3,
                'name' => 'Regular User',
                'slug' => 'user',
                'description' => 'Regular user with basic access',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['id' => $role['id']], $role);
        }
    }
}