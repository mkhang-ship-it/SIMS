<?php
// Added modal_background_image field to lucky_draws table for customizing the spinning wheel modal background

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
        Schema::table('lucky_draws', function (Blueprint $table) {
            $table->string('modal_background_image')->nullable()->after('background_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lucky_draws', function (Blueprint $table) {
            $table->dropColumn('modal_background_image');
        });
    }
};
