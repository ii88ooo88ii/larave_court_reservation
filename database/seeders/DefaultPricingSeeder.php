<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pricing;
use App\Models\Court;

class DefaultPricingSeeder extends Seeder
{
    public function run(): void
    {
        $courts = Court::all();
        
        foreach ($courts as $court) {
            // Check if pricing already exists
            $exists = Pricing::where('court_id', $court->id)->exists();
            
            if (!$exists) {
                Pricing::create([
                    'tenant_id' => $court->tenant_id,
                    'court_id' => $court->id,
                    'name' => 'Standard Rate',
                    'type' => 'standard',
                    'base_price' => 50.00,
                    'peak_price' => 75.00,
                    'off_peak_price' => 40.00,
                    'currency' => 'USD',
                    'minimum_hours' => 1,
                    'is_active' => true,
                    'description' => 'Default pricing for ' . $court->name,
                ]);
                
                $this->command->info("Default pricing created for court: {$court->name}");
            }
        }
    }
}