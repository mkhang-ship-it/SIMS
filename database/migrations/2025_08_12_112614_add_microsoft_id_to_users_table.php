<?php
// Migration to add microsoft_id column to users table for Microsoft OAuth authentication
// Adds nullable string column to store Microsoft user ID for OAuth linking
// Created to support Microsoft OAuth login functionality

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
            $table->string('microsoft_id')->nullable()->after('github_id');
            $table->index('microsoft_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['microsoft_id']);
            $table->dropColumn('microsoft_id');
        });
    }
};
