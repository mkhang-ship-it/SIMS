<?php
// Created: Migration for user_club_roles pivot table to support multi-club membership for users

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
        Schema::create('user_club_roles', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Foreign keys
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignUuid('role_id')->constrained('roles')->onDelete('cascade');

            // Additional fields
            $table->datetime('joined_at')->useCurrent();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');

            // Composite unique constraint - user can only have one role per club
            $table->unique(['user_id', 'club_id']);

            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['club_id', 'status']);
            $table->index(['role_id', 'status']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_club_roles');
    }
};
