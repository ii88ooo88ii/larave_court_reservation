<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdditionalFee;
use App\Models\Pricing;

class DefaultAdditionalFeesSeeder extends Seeder
{
    public function run(): void
    {
        $pricings = Pricing::all();
        
        foreach ($pricings as $pricing) {
            // Check if fees already exist
            $exists = AdditionalFee::where('pricing_id', $pricing->id)->exists();
            
            if (!$exists) {
                // Add court maintenance fee
                AdditionalFee::create([
                    'tenant_id' => $pricing->tenant_id,
                    'pricing_id' => $pricing->id,
                    'name' => 'Court Maintenance Fee',
                    'type' => 'fixed',
                    'amount' => 10.00,
                    'is_mandatory' => true,
                    'is_active' => true,
                    'description' => 'Standard court maintenance fee',
                ]);
                
                // Add service fee (percentage)
                AdditionalFee::create([
                    'tenant_id' => $pricing->tenant_id,
                    'pricing_id' => $pricing->id,
                    'name' => 'Service Fee',
                    'type' => 'percentage',
                    'amount' => 5.00,
                    'is_mandatory' => true,
                    'is_active' => true,
                    'description' => 'Service fee for reservation',
                ]);
                
                $this->command->info("Default fees created for pricing ID: {$pricing->id}");
            }
        }
    }
}