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
        Schema::create('chat_user', function (Blueprint $table) {
            $table->foreignUuid('chat_id')->references('id')->on('chats')->onDelete('cascade');
            $table->foreignUuid('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->primary(['chat_id', 'user_id']);

            $table->index(['chat_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_user');
    }
};
