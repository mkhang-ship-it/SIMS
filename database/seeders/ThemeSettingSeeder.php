<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ThemeSetting;

class ThemeSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default theme settings
        ThemeSetting::create([
            'theme_mode' => 'light',
            'direction' => 'ltr',
            'navigation_style' => 'vertical',
            'navigation_menu_style' => null,
            'sidemenu_layout' => 'default',
            'page_style' => 'regular',
            'layout_width' => 'fullwidth',
            'menu_position' => 'fixed',
            'header_position' => 'fixed',
            'menu_color' => 'dark',
            'header_color' => 'light',
            'primary_color' => '58, 88, 146',
            'background_color' => null,
            'background_light_color' => null,
            'background_image' => null,
            'is_active' => true,
        ]);
    }
}
