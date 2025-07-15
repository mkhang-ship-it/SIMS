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
        // Fixed: Remove redundant unique() on 'name' (now using composite unique on ['name', 'club_id'])
        Schema::create('roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->enum('type', ['central', 'branch'])->default('central');
            $table->foreignUuid('club_id')->nullable()->constrained('clubs');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->boolean('is_default')->default(false);
            $table->enum('is_leader', ['leader', 'member'])->default('member');
            $table->boolean('is_admin')->default(false);
            $table->unique(['name', 'club_id']);
            $table->timestamps();
        });

        // Đã sắp xếp lại các cột trong bảng users cho hợp lý hơn (id, thông tin cá nhân, liên hệ, học tập, liên kết, trạng thái)
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Thông tin cá nhân
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('birthday')->nullable();
            $table->string('identity_card_code')->nullable();

            // Liên hệ
            $table->string('phone')->nullable();

            // Học tập
            $table->string('student_code')->nullable()->unique();
            $table->foreignUuid('major_id')->nullable()->constrained('majors');
            $table->foreignUuid('course_id')->nullable()->constrained('courses');
            $table->foreignUuid('role_id')->nullable()->constrained('roles');
            $table->enum('type', ['central', 'branch'])->default('central');

            // --- Thêm trường default_club_id ---
            $table->foreignUuid('default_club_id')->nullable()->constrained('clubs');

            // Trạng thái & token
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignUuid('user_id')->nullable()->index()->constrained('users');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
    }
};
