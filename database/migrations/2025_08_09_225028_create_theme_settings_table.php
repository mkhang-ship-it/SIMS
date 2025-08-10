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
        Schema::create('theme_settings', function (Blueprint $table) {
            $table->id();

            // Theme Mode
            $table->enum('theme_mode', ['light', 'dark'])->default('light');

            // Direction
            $table->enum('direction', ['ltr', 'rtl'])->default('ltr');

            // Navigation
            $table->enum('navigation_style', ['vertical', 'horizontal'])->default('vertical');
            $table->enum('navigation_menu_style', ['menu-click', 'menu-hover', 'icon-click', 'icon-hover'])->nullable();

            // Sidemenu Layout
            $table->enum('sidemenu_layout', ['default', 'closed', 'icontext', 'overlay', 'detached', 'doublemenu'])->default('default');

            // Page Style
            $table->enum('page_style', ['regular', 'classic', 'modern'])->default('regular');

            // Layout Width
            $table->enum('layout_width', ['fullwidth', 'boxed'])->default('fullwidth');

            // Menu Position
            $table->enum('menu_position', ['fixed', 'scrollable'])->default('fixed');

            // Header Position
            $table->enum('header_position', ['fixed', 'scrollable'])->default('fixed');

            // Menu Colors
            $table->enum('menu_color', ['light', 'dark', 'color', 'gradient', 'transparent'])->default('dark');

            // Header Colors
            $table->enum('header_color', ['light', 'dark', 'color', 'gradient', 'transparent'])->default('light');

            // Theme Primary Color (RGB values)
            $table->string('primary_color')->default('58, 88, 146');

            // Theme Background Color (RGB values)
            $table->string('background_color')->nullable();
            $table->string('background_light_color')->nullable();

            // Background Image
            $table->enum('background_image', ['bgimg1', 'bgimg2', 'bgimg3', 'bgimg4', 'bgimg5'])->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theme_settings');
    }
};
