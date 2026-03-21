<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courts', function (Blueprint $table) {
            // Add tenant_id if not exists
            if (!Schema::hasColumn('courts', 'tenant_id')) {
                $table->foreignId('tenant_id')->after('id')->constrained()->onDelete('cascade');
            }
            
            // Add court_type_id if not exists
            if (!Schema::hasColumn('courts', 'court_type_id')) {
                $table->foreignId('court_type_id')->nullable()->after('tenant_id')->constrained()->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courts', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
            
            $table->dropForeign(['court_type_id']);
            $table->dropColumn('court_type_id');
        });
    }
};