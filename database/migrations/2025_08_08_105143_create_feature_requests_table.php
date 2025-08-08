<?php

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
        Schema::create('feature_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Ai gửi
            $table->uuid('club_id');    // CLB gửi yêu cầu
            $table->uuid('created_by'); // user tạo

            // Nội dung
            $table->string('title', 255);
            $table->text('description')->nullable();

            // Phân loại + mức độ ưu tiên
            $table->enum('type', ['feature', 'bug'])->default('feature');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');

            // Trạng thái
            $table->enum('status', [
                'new_request',   // Yêu cầu mới
                'in_progress',   // Đang triển khai
                'acknowledged',  // Đã tiếp nhận
                'rejected',      // Từ chối
                'completed'      // Đã hoàn thành
            ])->default('new_request');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Meta
            $table->timestamps();

            // FK
            $table->foreign('club_id')
                ->references('id')->on('clubs')
                ->cascadeOnUpdate()->restrictOnDelete();

            $table->foreign('created_by')
                ->references('id')->on('users')
                ->cascadeOnUpdate()->restrictOnDelete();

            // Index
            $table->index(['club_id', 'status']);
            $table->index(['created_by', 'status']);
            $table->index(['type', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_requests');
    }
};
