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
        Schema::create('chats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->boolean('is_group')->default(false);
            $table->string('name')->nullable(); // chỉ dùng cho group
            $table->foreignUuid('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
