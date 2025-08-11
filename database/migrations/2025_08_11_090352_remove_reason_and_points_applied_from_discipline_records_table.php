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
        Schema::table('discipline_records', function (Blueprint $table) {
            $table->dropColumn(['reason', 'points_applied']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discipline_records', function (Blueprint $table) {
            $table->text('reason')->after('semester_id');
            $table->integer('points_applied')->after('reason');
        });
    }
};
