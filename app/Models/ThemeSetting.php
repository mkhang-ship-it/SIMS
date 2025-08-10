<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThemeSetting extends Model
{
    protected $fillable = [
        'theme_mode',
        'direction',
        'navigation_style',
        'navigation_menu_style',
        'sidemenu_layout',
        'page_style',
        'layout_width',
        'menu_position',
        'header_position',
        'menu_color',
        'header_color',
        'primary_color',
        'background_color',
        'background_light_color',
        'background_image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the active theme settings
     */
    public static function getActiveTheme()
    {
        return self::where('is_active', true)->first() ?? self::getDefaultTheme();
    }

    /**
     * Get default theme settings
     */
    public static function getDefaultTheme()
    {
        return (object) [
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
        ];
    }

    /**
     * Create or update theme settings
     */
    public static function updateTheme(array $data)
    {
        // Deactivate all existing themes
        self::query()->update(['is_active' => false]);

        // Create new active theme
        return self::create(array_merge($data, ['is_active' => true]));
    }

    /**
     * Get theme attributes for HTML data attributes
     */
    public function getHtmlAttributes()
    {
        $attributes = [];

        // Theme mode
        $attributes['data-theme-mode'] = $this->theme_mode;

        // Direction
        $attributes['dir'] = $this->direction;

        // Navigation layout
        $attributes['data-nav-layout'] = $this->navigation_style;

        // Navigation style
        if ($this->navigation_menu_style) {
            $attributes['data-nav-style'] = $this->navigation_menu_style;
        }

        // Vertical style (sidemenu layout)
        if ($this->navigation_style === 'vertical' && $this->sidemenu_layout !== 'default') {
            $attributes['data-vertical-style'] = $this->sidemenu_layout === 'doublemenu' ? 'doublemenu' : $this->sidemenu_layout;
        }

        // Page style
        if ($this->page_style !== 'regular') {
            $attributes['data-page-style'] = $this->page_style;
        }

        // Layout width
        if ($this->layout_width !== 'fullwidth') {
            $attributes['data-width'] = $this->layout_width === 'fullwidth' ? 'fullwidth' : 'boxed';
        }

        // Menu position
        if ($this->menu_position !== 'fixed') {
            $attributes['data-menu-position'] = $this->menu_position;
        }

        // Header position
        if ($this->header_position !== 'fixed') {
            $attributes['data-header-position'] = $this->header_position;
        }

        // Menu styles
        $attributes['data-menu-styles'] = $this->menu_color;

        // Header styles
        $attributes['data-header-styles'] = $this->header_color;

        // Background image
        if ($this->background_image) {
            $attributes['data-bg-img'] = $this->background_image;
        }

        return $attributes;
    }

    /**
     * Get CSS custom properties
     */
    public function getCssProperties()
    {
        $properties = [];

        // Primary color
        if ($this->primary_color) {
            $properties['--primary-rgb'] = $this->primary_color;
        }

        // Background colors for dark themes
        if ($this->background_color && $this->background_light_color) {
            $properties['--body-bg-rgb'] = $this->background_color;
            $properties['--light-rgb'] = $this->background_light_color;
            $properties['--form-control-bg'] = "rgb({$this->background_light_color})";
            $properties['--input-border'] = 'rgba(255,255,255,0.1)';
        }

        return $properties;
    }
}
