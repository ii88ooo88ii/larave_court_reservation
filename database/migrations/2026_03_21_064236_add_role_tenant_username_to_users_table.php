<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First check if username column exists, if not add it
        if (!Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username')->unique()->after('name');
            });
        }
        
        // Check if role_id column exists
        if (!Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id')->nullable()->after('email');
            });
        }
        
        // Check if tenant_id column exists
        if (!Schema::hasColumn('users', 'tenant_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('tenant_id')->nullable()->after('role_id');
            });
        }
        
        // Add foreign keys (only if tables exist)
        if (Schema::hasTable('roles') && !$this->hasForeignKey('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('role_id')
                      ->references('id')
                      ->on('roles')
                      ->onDelete('set null');
            });
        }
        
        if (Schema::hasTable('tenants') && !$this->hasForeignKey('users', 'tenant_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('tenant_id')
                      ->references('id')
                      ->on('tenants')
                      ->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['tenant_id']);
            $table->dropColumn(['username', 'role_id', 'tenant_id']);
        });
    }
    
    private function hasForeignKey($table, $column)
    {
        try {
            $database = Schema::getConnection()->getDatabaseName();
            $result = Schema::getConnection()->select("
                SELECT COUNT(*) as count 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = ? 
                AND TABLE_NAME = ? 
                AND COLUMN_NAME = ?
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ", [$database, $table, $column]);
            
            return $result[0]->count > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
};