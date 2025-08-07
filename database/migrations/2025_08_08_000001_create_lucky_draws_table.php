<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lucky_draws', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('club_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('background_image')->nullable();
            $table->string('slug')->unique(); // For unique link
            $table->json('participants')->nullable(); // Store participants as JSON
            $table->json('winners')->nullable(); // Store winners as JSON
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by');
            $table->timestamps();

            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->index(['club_id', 'is_active']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lucky_draws');
    }
};
