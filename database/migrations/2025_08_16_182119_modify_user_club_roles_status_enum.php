<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update any existing 'suspended' statuses to 'inactive'
        DB::table('user_club_roles')
            ->where('status', 'suspended')
            ->update(['status' => 'inactive']);

        // Use raw SQL to modify the enum column (MySQL compatible)
        DB::statement("ALTER TABLE `user_club_roles` MODIFY COLUMN `status` ENUM('active', 'inactive', 'pending') DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, update any existing 'pending' statuses to 'inactive'
        DB::table('user_club_roles')
            ->where('status', 'pending')
            ->update(['status' => 'inactive']);

        // Use raw SQL to modify the enum column back (MySQL compatible)
        DB::statement("ALTER TABLE `user_club_roles` MODIFY COLUMN `status` ENUM('active', 'inactive', 'suspended') DEFAULT 'active'");
    }
};
