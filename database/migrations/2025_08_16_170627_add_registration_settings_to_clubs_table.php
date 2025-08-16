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
        Schema::table('clubs', function (Blueprint $table) {
            $table->boolean('registration_enabled')->default(false);
            $table->string('registration_token', 12)->nullable()->unique();
            $table->timestamp('registration_opened_at')->nullable();
            $table->timestamp('registration_closed_at')->nullable();
            $table->uuid('default_role_id')->nullable();
            $table->foreign('default_role_id')->references('id')->on('roles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->dropColumn([
                'registration_enabled',
                'registration_token',
                'registration_opened_at',
                'registration_closed_at',
                'default_role_id'
            ]);
        });
    }
};
