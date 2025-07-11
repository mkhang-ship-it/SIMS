<?php
// Created: Action Plans Migration - Creates action_plans table linked to event_plans
// Manages action plan activities with responsibilities, timelines, and risk management

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
        Schema::create('event_action_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_plan_id')->constrained('event_plans')->onDelete('cascade');
            $table->string('activity_item'); // Hạng mục hoạt động
            $table->text('expected_result'); // Kết quả cần đạt
            $table->datetime('start_time'); // Thời gian bắt đầu
            $table->datetime('end_time'); // Thời gian kết thúc
            $table->foreignUuid('primary_responsible_id')->constrained('users')->onDelete('cascade'); // Chịu trách nhiệm chính
            $table->json('support_members')->nullable(); // Thành viên phối hợp (array of user IDs)
            $table->text('potential_risks')->nullable(); // Rủi ro phát sinh
            $table->text('risk_prevention_actions')->nullable(); // Hành động phòng ngừa rủi ro
            $table->timestamps();

            // Index for performance
            $table->index(['event_plan_id', 'start_time']);
            $table->index('primary_responsible_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_action_plans');
    }
};
