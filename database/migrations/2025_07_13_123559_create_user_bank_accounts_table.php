<?php
// Đã cập nhật: Tạo bảng user_bank_accounts để lưu thông tin ngân hàng của user với các trường: user_id, bank_name, bank_code, bank_bin, bank_short_name, bank_swift_code, bank_account_name, bank_account_number

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
        Schema::create('user_bank_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('bank_name')->nullable();
            $table->string('bank_code')->nullable();
            $table->string('bank_bin')->nullable();
            $table->string('bank_short_name')->nullable();
            $table->string('bank_swift_code')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->timestamps();

            // Đảm bảo mỗi user chỉ có 1 tài khoản ngân hàng
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_bank_accounts');
    }
};
