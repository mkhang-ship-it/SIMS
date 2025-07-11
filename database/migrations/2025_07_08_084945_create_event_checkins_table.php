<?php
// Migration for event_checkins table - QR Code Check-in system for events
// Handles member check-ins for events with geolocation validation and time constraints
// Fields: event_id, user_id, checkin_time, user_agent, plus location data for validation

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
        Schema::create('event_checkins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('checkin_time'); // Thời điểm thực hiện check-in
            $table->text('user_agent')->nullable(); // Thông tin trình duyệt hoặc thiết bị dùng để quét mã
            $table->decimal('latitude', 10, 8)->nullable(); // Vĩ độ của người check-in
            $table->decimal('longitude', 11, 8)->nullable(); // Kinh độ của người check-in
            $table->decimal('distance', 8, 2)->nullable(); // Khoảng cách tính được (km)
            $table->timestamps();

            // Unique constraint: mỗi user chỉ được check-in một lần cho mỗi event
            $table->unique(['event_id', 'user_id']);

            // Index để tìm kiếm nhanh
            $table->index(['event_id', 'checkin_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_checkins');
    }
};
