<?php
// Migration for Event Plan Equipment Table
// Created: Equipment management for event plans with name, quantity, and type (club/school)
// Table structure: id (UUID), event_plan_id, name, quantity, type, created_at, updated_at

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
        Schema::create('event_plan_equipment', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('event_plan_id');
            $table->string('name');
            $table->integer('quantity')->default(1);
            $table->enum('type', ['club', 'school'])->default('club');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('event_plan_id')->references('id')->on('event_plans')->onDelete('cascade');

            // Index for better performance
            $table->index('event_plan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_plan_equipment');
    }
};
