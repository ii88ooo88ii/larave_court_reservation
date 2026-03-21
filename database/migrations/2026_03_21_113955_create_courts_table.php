<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type')->default('standard'); // standard, vip, premium
            $table->string('surface')->nullable(); // clay, grass, hard, etc.
            $table->boolean('is_indoor')->default(false);
            $table->boolean('has_floodlights')->default(false);
            $table->decimal('hourly_rate', 10, 2)->default(0);
            $table->integer('capacity')->default(4); // number of players
            $table->text('description')->nullable();
            $table->text('facilities')->nullable(); // JSON or comma-separated
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Add indexes
            $table->index(['tenant_id', 'is_active']);
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courts');
    }
};