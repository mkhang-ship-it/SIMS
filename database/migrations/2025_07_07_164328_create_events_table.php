<?php
// Migration for events table - Club event management system
// Fields: event_code (unique), name, description, image, qr_hash, relationships to slots, class_rooms, semesters
// Uses single slot_id for event timing (simplified from dual start/end slots)
// Updated: Changed status enum from 4 states to 2 states (active, inactive)
// Updated: Changed requires_approval from boolean to enum (yes, no)

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
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('event_code')->unique(); // VD: EVT-2025-123456
            $table->string('name'); // Tên sự kiện
            $table->text('description')->nullable(); // Mô tả chi tiết
            $table->string('image')->nullable(); // Đường dẫn ảnh
            $table->string('qr_hash')->nullable(); // Mã Hash QR code cho checkin
            $table->date('event_date'); // Ngày diễn ra sự kiện

            // Relationships
            $table->foreignUuid('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignUuid('semester_id')->constrained('semesters')->onDelete('cascade');
            $table->foreignUuid('slot_id')->constrained('slots')->onDelete('cascade'); // Thời gian diễn ra
            $table->foreignUuid('class_room_id')->constrained('class_rooms')->onDelete('cascade'); // Địa điểm

            $table->enum('status', ['pending', 'active', 'cancelled', 'completed'])->default('pending');
            $table->enum('requires_approval', ['yes', 'no'])->default('yes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
