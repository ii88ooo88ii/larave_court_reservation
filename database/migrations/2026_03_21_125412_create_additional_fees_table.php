<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('additional_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('pricing_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Court Fee, Guest Fee, Equipment Fee, etc.
            $table->string('type'); // fixed, percentage
            $table->decimal('amount', 10, 2);
            $table->boolean('is_mandatory')->default(true);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['tenant_id', 'pricing_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('additional_fees');
    }
};