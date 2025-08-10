<?php
// Migration to add github_id column to users table for GitHub OAuth authentication
// Adds nullable string column to store GitHub user ID for OAuth linking
// Created to support GitHub OAuth login functionality

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
        Schema::table('users', function (Blueprint $table) {
            $table->string('github_id')->nullable()->after('default_club_id');
            $table->index('github_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['github_id']);
            $table->dropColumn('github_id');
        });
    }
};
