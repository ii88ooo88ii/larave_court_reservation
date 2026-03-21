<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pricings', function (Blueprint $table) {
            // Rename price to base_price if it exists
            if (Schema::hasColumn('pricings', 'price')) {
                $table->renameColumn('price', 'base_price');
            }
            
            // Add new columns if they don't exist
            if (!Schema::hasColumn('pricings', 'peak_price')) {
                $table->decimal('peak_price', 10, 2)->nullable()->after('base_price');
            }
            
            if (!Schema::hasColumn('pricings', 'off_peak_price')) {
                $table->decimal('off_peak_price', 10, 2)->nullable()->after('peak_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pricings', function (Blueprint $table) {
            $table->renameColumn('base_price', 'price');
            $table->dropColumn(['peak_price', 'off_peak_price']);
        });
    }
};