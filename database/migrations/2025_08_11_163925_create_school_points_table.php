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
        Schema::create('school_points', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('club_id');
            $table->uuid('semester_id');
            $table->decimal('club_points', 8, 2)->default(0); // Điểm club gốc
            $table->decimal('school_points', 8, 2)->default(0); // Điểm trường đã quy đổi
            $table->boolean('is_leader')->default(false); // Cache của role.is_leader
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('cascade');
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('cascade');

            // Unique constraint: một user chỉ có một record điểm cho mỗi club mỗi semester
            $table->unique(['user_id', 'club_id', 'semester_id']);

            // Indexes for performance
            $table->index(['club_id', 'semester_id']);
            $table->index('school_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_points');
    }
};
