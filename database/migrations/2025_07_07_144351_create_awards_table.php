<?php
// Migration for awards table - Club award and certificate management system
// Fields: award_code (unique), title, type, semester, award_date, status, attachment, notes

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
        Schema::create('awards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('award_code')->unique(); // VD: BK-2025-001
            $table->string('title'); // Tên giải thưởng
            $table->enum('type', ['individual', 'group']); // Cá nhân / Tập thể
            $table->foreignUuid('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignUuid('semester_id')->constrained('semesters')->onDelete('cascade');
            $table->date('award_date'); // Ngày trao
            $table->enum('status', ['pending', 'awarded', 'revoked'])->default('pending'); // Đang xét duyệt / Đã trao / Đã thu hồi
            $table->string('attachment')->nullable(); // Tệp đính kèm
            $table->text('notes')->nullable(); // Ghi chú thêm
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('awards');
    }
};
