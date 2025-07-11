<?php
// Migration for event_plan_objectives table - Event Plan Objectives Management
// Manages objectives and measurement indicators for event plans
// Fields: objective, expected_result, responsible_party

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
        Schema::create('event_plan_objectives', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Foreign key relationship
            $table->foreignUuid('event_plan_id')->constrained('event_plans')->onDelete('cascade');

            // Objective fields
            $table->text('objective'); // Mục tiêu
            $table->text('expected_result'); // Kết quả cần đạt
            $table->string('responsible_party', 255); // Tổ chức/cá nhân chịu trách nhiệm

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_plan_objectives');
    }
};
