<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lucky_draws', function (Blueprint $table) {
            // Make club_id nullable
            $table->uuid('club_id')->nullable()->change();

            // Add type enum field with default 'branch'
            $table->enum('type', ['central', 'branch'])->default('branch')->after('club_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lucky_draws', function (Blueprint $table) {
            // Revert club_id to not nullable
            $table->uuid('club_id')->nullable(false)->change();

            // Remove type field
            $table->dropColumn('type');
        });
    }
};
