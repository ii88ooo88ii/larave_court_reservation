<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        // Get all permissions
        $allPermissions = Permission::pluck('id')->toArray();
        
        // Get specific permission groups
        $userPermissions = Permission::whereIn('slug', [
            'dashboard.view',
            'users.view',
        ])->pluck('id')->toArray();
        
        $managerPermissions = Permission::whereIn('slug', [
            'dashboard.view',
            'users.view',
            'users.create',
            'users.edit',
            'tenants.view',
        ])->pluck('id')->toArray();
        
        $adminPermissions = $allPermissions;
        
        $roles = [
            [
                'id' => 1,
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrator with full access',
                'permissions' => $adminPermissions,
            ],
            [
                'id' => 2,
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Manager with limited access',
                'permissions' => $managerPermissions,
            ],
            [
                'id' => 3,
                'name' => 'Regular User',
                'slug' => 'user',
                'description' => 'Regular user with basic access',
                'permissions' => $userPermissions,
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::updateOrCreate(
                ['id' => $roleData['id']],
                [
                    'name' => $roleData['name'],
                    'slug' => $roleData['slug'],
                    'description' => $roleData['description'],
                ]
            );
            
            // Sync permissions
            $role->permissions()->sync($roleData['permissions']);
        }
    }
}