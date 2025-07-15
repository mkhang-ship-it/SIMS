<?php
// Updated: Migration for award_recipients table - Replace user_id with user_club_role_id (foreign key to user_club_roles)
// Now links awards to user_club_roles for recipient management

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
            $table->foreignUuid('user_club_role_id')->constrained('user_club_roles')->onDelete('cascade');
            $table->string('attachment')->nullable();
            $table->timestamps();

            // Ensure unique combination of award and user_club_role
            $table->unique(['award_id', 'user_club_role_id']);
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
