<?php
// Migration for prizes table - Lucky draw prize management system
// Fields: name, image, quantity, lucky_draw_id

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prizes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // Tên giải thưởng
            $table->string('image')->nullable(); // Hình ảnh giải thưởng
            $table->integer('quantity')->default(1); // Số lượng giải
            $table->foreignUuid('lucky_draw_id')->constrained('lucky_draws')->onDelete('cascade');
            $table->integer('order')->default(0); // Thứ tự hiển thị
            $table->boolean('special_gift')->default(false);
            $table->timestamps();

            $table->index(['lucky_draw_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prizes');
    }
};
