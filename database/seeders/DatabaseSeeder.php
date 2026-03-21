<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Remove any User::factory() calls
        $this->call([
            AdminUserSeeder::class,
        ]);
    }
}