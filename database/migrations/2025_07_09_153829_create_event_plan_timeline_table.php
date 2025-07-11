<?php
// Created: 2025-01-23
// Changes: Created event_plan_timeline table for timeline management with drag/drop ordering capability

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('event_plan_timeline', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('event_plan_id');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('content');
            $table->integer('order_index')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('event_plan_id')->references('id')->on('event_plans')->onDelete('cascade');

            // Index for better performance
            $table->index(['event_plan_id', 'order_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_plan_timeline');
    }
};
