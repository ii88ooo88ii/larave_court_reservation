<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courts', function (Blueprint $table) {
            if (Schema::hasColumn('courts', 'hourly_rate')) {
                $table->dropColumn('hourly_rate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courts', function (Blueprint $table) {
            $table->decimal('hourly_rate', 10, 2)->nullable()->after('has_floodlights');
        });
    }
};