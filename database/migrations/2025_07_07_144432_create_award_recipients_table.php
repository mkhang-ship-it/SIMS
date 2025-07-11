<?php
// Migration for award_recipients table - Pivot table for many-to-many relationship between awards and users
// Allows multiple recipients per award

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
        Schema::create('award_recipients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('award_id')->constrained('awards')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('attachment')->nullable();
            $table->timestamps();

            // Ensure unique combination of award and user
            $table->unique(['award_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('award_recipients');
    }
};
