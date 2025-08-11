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
        Schema::create('discipline_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('club_id');
            $table->uuid('discipline_id');
            $table->uuid('semester_id');
            $table->integer('points_applied');
            $table->text('reason');
            $table->date('applied_date');
            $table->enum('status', ['active', 'revoked'])->default('active');
            $table->uuid('applied_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('cascade');
            $table->foreign('discipline_id')->references('id')->on('disciplines')->onDelete('cascade');
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('cascade');
            $table->foreign('applied_by')->references('id')->on('users')->onDelete('cascade');

            $table->index(['user_id', 'semester_id', 'club_id']);
            $table->index(['club_id', 'semester_id']);
            $table->index('applied_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discipline_records');
    }
};
