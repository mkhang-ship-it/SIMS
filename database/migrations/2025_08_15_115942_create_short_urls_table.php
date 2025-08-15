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
        Schema::create('short_urls', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Khóa chính dạng UUID
            $table->foreignUuid('club_id')->nullable()->constrained('clubs')->nullOnDelete();
            $table->enum('type', ['central', 'branch'])->default('central'); // Liên kết đến club
            $table->string('original_url'); // URL gốc cần rút gọn
            $table->string('short_code')->unique(); // Mã rút gọn (vd: abc123)
            $table->string('title')->nullable(); // Tiêu đề tùy chọn cho URL
            $table->timestamp('expires_at')->nullable(); // Thời gian hết hạn URL (nếu có)
            $table->timestamps(); // created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('short_urls');
    }
};
