<?php
// Fixed: Corrected foreign key constraints and column definitions in notifications and notification_reads tables.
// Ensured proper use of foreignUuid and removed duplicate column definitions.

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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('content');
            $table->enum('target_type', ['all_clubs', 'specific_clubs'])->default('all_clubs');
            $table->json('target_club_ids')->nullable(); // Array of club IDs for specific clubs
            $table->foreignUuid('sender_id')->nullable()->constrained('users')->nullOnDelete(); // Admin who sent the notification
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Create notification_reads table to track which clubs have read the notification
        Schema::create('notification_reads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('notification_id')->constrained('notifications')->onDelete('cascade');
            $table->foreignUuid('club_id')->constrained('clubs')->onDelete('cascade');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->unique(['notification_id', 'club_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_reads');
        Schema::dropIfExists('notifications');
    }
};
