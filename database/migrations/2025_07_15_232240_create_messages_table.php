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
        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->text('content')->nullable();
            $table->enum('type', ['text', 'image', 'file'])->default('text'); // text | image | file
            $table->string('file_path')->nullable(); // nếu là ảnh/file thì lưu đường dẫn
            $table->timestamps();

            $table->foreignUuid('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignUuid('chat_id')->references('id')->on('chats')->onDelete('cascade');

            $table->index(['chat_id', 'sender_id']);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
