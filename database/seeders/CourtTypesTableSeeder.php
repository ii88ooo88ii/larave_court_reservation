<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourtType;
use Illuminate\Support\Str;

class CourtTypesTableSeeder extends Seeder
{
    public function run(): void
    {
        $courtTypes = [
            [
                'name' => 'Basketball Court',
                'slug' => 'basketball',
                'icon' => 'fa-basketball-ball',
                'color' => '#e74a3b',
                'description' => 'Full-size basketball court with professional-grade flooring',
            ],
            [
                'name' => 'Tennis Court',
                'slug' => 'tennis',
                'icon' => 'fa-tennis-ball',
                'color' => '#1cc88a',
                'description' => 'Standard tennis court with clay surface',
            ],
            [
                'name' => 'Pickleball Court',
                'slug' => 'pickleball',
                'icon' => 'fa-table-tennis',
                'color' => '#36b9cc',
                'description' => 'Pickleball court with professional net system',
            ],
            [
                'name' => 'Badminton Court',
                'slug' => 'badminton',
                'icon' => 'fa-table-tennis',
                'color' => '#f6c23e',
                'description' => 'Indoor badminton court with proper lighting',
            ],
            [
                'name' => 'Volleyball Court',
                'slug' => 'volleyball',
                'icon' => 'fa-volleyball-ball',
                'color' => '#4e73df',
                'description' => 'Beach or indoor volleyball court',
            ],
        ];

        foreach ($courtTypes as $type) {
            CourtType::updateOrCreate(
                ['slug' => $type['slug']],
                array_merge($type, [
                    'tenant_id' => 1, // Main tenant
                    'is_active' => true,
                ])
            );
        }
    }
}