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
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('document_type_id')->constrained('document_types')->cascadeOnDelete();
            $table->foreignUuid('uploaded_by')->constrained('users');
            $table->foreignUuid('club_id')->constrained('clubs')->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path');
            $table->enum('visibility', ['public', 'private', 'restricted'])->default('private');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reason_rejected')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};