<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Dashboard permissions
            [
                'name' => 'View Dashboard',
                'slug' => 'dashboard.view',
                'module' => 'dashboard',
                'action' => 'view',
                'description' => 'Can view dashboard',
            ],
            
            // User permissions
            [
                'name' => 'View Users',
                'slug' => 'users.view',
                'module' => 'users',
                'action' => 'view',
                'description' => 'Can view users list',
            ],
            [
                'name' => 'Create Users',
                'slug' => 'users.create',
                'module' => 'users',
                'action' => 'create',
                'description' => 'Can create new users',
            ],
            [
                'name' => 'Edit Users',
                'slug' => 'users.edit',
                'module' => 'users',
                'action' => 'edit',
                'description' => 'Can edit users',
            ],
            [
                'name' => 'Delete Users',
                'slug' => 'users.delete',
                'module' => 'users',
                'action' => 'delete',
                'description' => 'Can delete users',
            ],
            
            // Role permissions
            [
                'name' => 'View Roles',
                'slug' => 'roles.view',
                'module' => 'roles',
                'action' => 'view',
                'description' => 'Can view roles list',
            ],
            [
                'name' => 'Create Roles',
                'slug' => 'roles.create',
                'module' => 'roles',
                'action' => 'create',
                'description' => 'Can create new roles',
            ],
            [
                'name' => 'Edit Roles',
                'slug' => 'roles.edit',
                'module' => 'roles',
                'action' => 'edit',
                'description' => 'Can edit roles',
            ],
            [
                'name' => 'Delete Roles',
                'slug' => 'roles.delete',
                'module' => 'roles',
                'action' => 'delete',
                'description' => 'Can delete roles',
            ],
            [
                'name' => 'Manage Role Permissions',
                'slug' => 'roles.manage-permissions',
                'module' => 'roles',
                'action' => 'manage_permissions',
                'description' => 'Can manage role permissions',
            ],
            
            // Tenant permissions
            [
                'name' => 'View Tenants',
                'slug' => 'tenants.view',
                'module' => 'tenants',
                'action' => 'view',
                'description' => 'Can view tenants list',
            ],
            [
                'name' => 'Create Tenants',
                'slug' => 'tenants.create',
                'module' => 'tenants',
                'action' => 'create',
                'description' => 'Can create new tenants',
            ],
            [
                'name' => 'Edit Tenants',
                'slug' => 'tenants.edit',
                'module' => 'tenants',
                'action' => 'edit',
                'description' => 'Can edit tenants',
            ],
            [
                'name' => 'Delete Tenants',
                'slug' => 'tenants.delete',
                'module' => 'tenants',
                'action' => 'delete',
                'description' => 'Can delete tenants',
            ],
            // Court permissions
            [
                'name' => 'View Courts',
                'slug' => 'courts.view',
                'module' => 'courts',
                'action' => 'view',
                'description' => 'Can view courts list',
            ],
            [
                'name' => 'Create Courts',
                'slug' => 'courts.create',
                'module' => 'courts',
                'action' => 'create',
                'description' => 'Can create new courts',
            ],
            [
                'name' => 'Edit Courts',
                'slug' => 'courts.edit',
                'module' => 'courts',
                'action' => 'edit',
                'description' => 'Can edit courts',
            ],
            [
                'name' => 'Delete Courts',
                'slug' => 'courts.delete',
                'module' => 'courts',
                'action' => 'delete',
                'description' => 'Can delete courts',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}