<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use Illuminate\Support\Str;

class TenantsTableSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = [
            [
                'name' => 'Main Corporation',
                'slug' => 'main-corp-' . Str::random(5),
                'email' => 'main@example.com',
                'phone' => '+1234567890',
                'address' => '123 Main Street',
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'USA',
                'postal_code' => '10001',
                'is_active' => true,
            ],
            [
                'name' => 'Tech Solutions Inc',
                'slug' => 'tech-solutions-' . Str::random(5),
                'email' => 'info@techsolutions.com',
                'phone' => '+1987654321',
                'address' => '456 Tech Park',
                'city' => 'San Francisco',
                'state' => 'CA',
                'country' => 'USA',
                'postal_code' => '94105',
                'is_active' => true,
            ],
        ];

        foreach ($tenants as $tenant) {
            Tenant::create($tenant);
        }
    }
}