<?php
// Added: Migration to add image and files fields to notifications table
// Added image_path for single image and files_path for multiple files

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
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('content'); // Single image path
            $table->json('files_path')->nullable()->after('image_path'); // Multiple files paths
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'files_path']);
        });
    }
};
