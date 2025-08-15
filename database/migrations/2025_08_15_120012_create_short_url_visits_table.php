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
        Schema::create('short_url_visits', function (Blueprint $table) {
            $table->id(); // Khóa chính tự tăng
            $table->foreignUuid('short_url_id') // Liên kết đến URL rút gọn
                ->constrained('short_urls')
                ->cascadeOnDelete();
            $table->ipAddress('ip'); // Địa chỉ IP người dùng
            $table->string('user_agent'); // Trình duyệt hoặc thiết bị
            $table->string('referrer')->nullable(); // URL giới thiệu nếu có
            $table->timestamp('visited_at')->useCurrent(); // Thời điểm truy cập
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('short_url_visits');
    }
};
