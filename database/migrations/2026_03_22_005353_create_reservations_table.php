<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('court_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reservation_code')->unique();
            $table->date('reservation_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_hours');
            $table->decimal('base_price', 10, 2);
            $table->decimal('additional_fees_total', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default('pending'); // pending, confirmed, ongoing, completed, cancelled, extended
            $table->text('notes')->nullable();
            $table->json('additional_fees_breakdown')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->boolean('is_extended')->default(false);
            $table->integer('extension_count')->default(0);
            $table->timestamps();
            
            // Indexes for faster availability checks
            $table->index(['court_id', 'reservation_date', 'start_time', 'end_time']);
            $table->index(['tenant_id', 'status']);
            $table->index('reservation_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};