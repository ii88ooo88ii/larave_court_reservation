<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('court_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type')->default('standard'); // standard, peak, off_peak, holiday
            $table->decimal('base_price', 10, 2);
            $table->decimal('peak_price', 10, 2)->nullable();
            $table->decimal('off_peak_price', 10, 2)->nullable();
            $table->string('currency')->default('USD');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->json('days_of_week')->nullable();
            $table->integer('minimum_hours')->default(1);
            $table->integer('maximum_hours')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index(['tenant_id', 'court_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricings');
    }
};