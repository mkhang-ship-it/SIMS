<?php
// Updated: Added club_id foreign key to addresses table to support club addresses
// Made user_id nullable since addresses can now belong to either users or clubs

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
        Schema::table('addresses', function (Blueprint $table) {
            $table->foreignUuid('club_id')->nullable()->after('user_id')->constrained('clubs')->onDelete('cascade');
            // Make user_id nullable since addresses can now belong to either users or clubs
            $table->foreignUuid('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropForeign(['club_id']);
            $table->dropColumn('club_id');
            // Restore user_id as required
            $table->foreignUuid('user_id')->nullable(false)->change();
        });
    }
};
