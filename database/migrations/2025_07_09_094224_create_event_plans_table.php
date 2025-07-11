<?php
// Migration for event_plans table - Event Plan Management System
// Auto-generated when creating events - manages comprehensive event planning information
// Includes: general info, organization content, event format, organization components

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
        Schema::create('event_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relationships
            $table->foreignUuid('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignUuid('club_id')->constrained('clubs')->onDelete('cascade');

            // 1. Thông tin chung
            $table->foreignUuid('prepared_by')->nullable()->constrained('users')->onDelete('set null'); // Người lập
            $table->date('implementation_time')->nullable(); // Thời gian triển khai
            $table->text('approved_by')->nullable(); // Được phê duyệt bởi

            // 2. Nội dung tổ chức
            // event_time và event_location sẽ lấy từ bảng events qua relationship
            $table->text('participants')->nullable(); // Đối tượng tham gia

            // 3. Hình thức tổ chức
            $table->text('description')->nullable(); // Mô tả sự kiện (hình thức tổ chức)

            // 4. Thành phần tổ chức
            $table->text('responsible_organizer')->nullable(); // Chịu trách nhiệm tổ chức
            $table->text('advisor_coordinator')->nullable(); // Cố vấn và phối hợp tổ chức

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_plans');
    }
};
