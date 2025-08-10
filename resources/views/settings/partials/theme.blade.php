@php
    use Illuminate\Support\Facades\Storage;
@endphp

<form action="{{ route('backend.settings.theme.update') }}" method="POST" id="theme-settings-form"
    enctype="multipart/form-data">
    @csrf

    <!-- Theme Styles Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="card-title mb-0">Theme Styles</h6>
        </div>
        <div class="card-body">
            <div class="">
                <p class="switcher-style-head">Theme Color Mode:</p>
                <div class="row switcher-style gx-0">
                    <div class="col-4">
                        <div class="form-check switch-select">
                            <label class="form-check-label" for="switcher-light-theme">
                                Light
                            </label>
                            <input class="form-check-input" type="radio" name="theme_mode" id="switcher-light-theme"
                                value="light"
                                {{ ($themeSettings->theme_mode ?? 'light') === 'light' ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-check switch-select">
                            <label class="form-check-label" for="switcher-dark-theme">
                                Dark
                            </label>
                            <input class="form-check-input" type="radio" name="theme_mode" id="switcher-dark-theme"
                                value="dark"
                                {{ ($themeSettings->theme_mode ?? 'light') === 'dark' ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
            </div>
            <div class="">
                <p class="switcher-style-head">Directions:</p>
                <div class="row switcher-style gx-0">
                    <div class="col-4">
                        <div class="form-check switch-select">
                            <label class="form-check-label" for="switcher-ltr">
                                LTR
                            </label>
                            <input class="form-check-input" type="radio" name="direction" id="switcher-ltr"
                                value="ltr" {{ ($themeSettings->direction ?? 'ltr') === 'ltr' ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-check switch-select">
                            <label class="form-check-label" for="switcher-rtl">
                                RTL
                            </label>
                            <input class="form-check-input" type="radio" name="direction" id="switcher-rtl"
                                value="rtl" {{ ($themeSettings->direction ?? 'ltr') === 'rtl' ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
            </div>
            <div class="">
                <p class="switcher-style-head">Navigation Styles:</p>
                <div class="row switcher-style gx-0">
                    <div class="col-4">
                        <div class="form-check switch-select">
                            <label class="form-check-label" for="switcher-vertical">
                                Vertical
                            </label>
                            <input class="form-check-input" type="radio" name="navigation_style"
                                id="switcher-vertical" value="vertical"
                                {{ ($themeSettings->navigation_style ?? 'vertical') === 'vertical' ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-check switch-select">
                            <label class="form-check-label" for="switcher-horizontal">
                                Horizontal
                            </label>
                            <input class="form-check-input" type="radio" name="navigation_style"
                                id="switcher-horizontal" value="horizontal"
                                {{ ($themeSettings->navigation_style ?? 'vertical') === 'horizontal' ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
            </div>
            <div class="navigation-menu-styles">
                <p class="switcher-style-head">Vertical & Horizontal Menu Styles:</p>
                <div class="row switcher-style gx-0 pb-2 gy-2">
                    <div class="col-4">
                        <div class="form-check switch-select">
                            <label class="form-check-label" for="switcher-menu-click">
                                Menu Click
                            </label>
                            <input class="form-check-input" type="radio" name="navigation_menu_style"
                                id="switcher-menu-click" value="menu-click"
                                {{ ($themeSettings->navigation_menu_style ?? '') === 'menu-click' ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-check switch-select">
                            <label class="form-check-label" for="switcher-menu-hover">
                                Menu Hover
                            </label>
                            <input class="form-check-input" type="radio" name="navigation_menu_style"
                                id="switcher-menu-hover" value="menu-hover"
                                {{ ($themeSettings->navigation_menu_style ?? '') === 'menu-hover' ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-check switch-select">
                            <label class="form-check-label" for="switcher-icon-click">
                                Icon Click
                            </label>
                            <input class="form-check-input" type="radio" name="navigation_menu_style"
                                id="switcher-icon-click" value="icon-click"
                                {{ ($themeSettings->navigation_menu_style ?? '') === 'icon-click' ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-check switch-select">
                            <label class="form-check-label" for="switcher-icon-hover">
                                Icon Hover
                            </label>
                            <input class="form-check-input" type="radio" name="navigation_menu_style"
                                id="switcher-icon-hover" value="icon-hover"
                                {{ ($themeSettings->navigation_menu_style ?? '') === 'icon-hover' ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sidemenu-layout-styles">
                <p class="switcher-style-head">Sidemenu Layout Styles:</p>
                <div class="row switcher-style gx-0 pb-2 gy-2">
                    <div class="col-sm-6">
                        <div class="form-check switch-select">
                            <label class="form-check-label" for="switcher-default-menu">
                                Default Menu
                            </label>
                            <input class="form-check-input" type="radio" name="sidemenu_layout"
                                id="switcher-default-menu" value="default"
                                {{ ($themeSettings->sidemenu_layout ?? 'default') === 'default' ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-check switch-select">
                            <label class="form-check-label" for="switcher-closed-menu">
                                Closed Menu
                            </label>
                            <input class="form-check-input" type="radio" name="sidemenu_layout"
                                id="switcher-closed-menu" value="closed"
                                {{ ($themeSettings->sidemenu_layout ?? 'default') === 'closed' ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-check switch-select">
                            <label class="form-check-label" for="switcher-icontext-menu">
                                Icon Text
                            </label>
                            <input class="form-check-input" type="radio" name="sidemenu_layout"
                                id="switcher-icontext-menu" value="icontext"
                                {{ ($themeSettings->sidemenu_layout ?? 'default') === 'icontext' ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-check switch-select">
                            <label class="form-check-label" for="switcher-icon-overlay">
                                Icon Overlay
                            </label>
                            <input class="form-check-input" type="radio" name="sidemenu_layout"
                                id="switcher-icon-overlay" value="overlay"
                                {{ ($themeSettings->sidemenu_layout ?? 'default') === 'overlay' ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-check switch-select">
                            <label class="form-check-label" for="switcher-detached">
                                Detached
                            </label>
                            <input class="form-check-input" type="radio" name="sidemenu_layout"
                                id="switcher-detached" value="detached"
                                {{ ($themeSettings->sidemenu_layout ?? 'default') === 'detached' ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-check switch-select">
                            <label class="form-check-label" for="switcher-double-menu">
                                Double Menu
                            </label>
                            <input class="form-check-input" type="radio" name="sidemenu_layout"
                                id="switcher-double-menu" value="doublemenu"
                                {{ ($themeSettings->sidemenu_layout ?? 'default') === 'doublemenu' ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
            </div>
            <div class="">
                <p class="switcher-style-head">Page Styles:</p>
                <div class="row switcher-style gx-0">
                    <div class="col-4">
                        <div class="form-check switch-select">
                            <label class="form-check-label" for="switcher-regular">
                                Regular
                            </label>
                            <input class="form-check-input" type="radio" name="page_style" id="switcher-regular"
                                value="regular"
                                {{ ($themeSettings->page_style ?? 'regular') === 'regular' ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-check switch-select">
                            <label class="form-check-label" for="switcher-classic">
                                Classic
                            </label>
                            <input class="form-check-input" type="radio" name="page_style" id="switcher-classic"
                                value="classic"
                                {{ ($themeSettings->page_style ?? 'regular') === 'classic' ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-check switch-select">
                            <label class="form-check-label" for="switcher-modern">
                                Modern
                            </label>
                            <input class="form-check-input" type="radio" name="page_style" id="switcher-modern"
                                value="modern"
                                {{ ($themeSettings->page_style ?? 'regular') === 'modern' ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
            </div>
            <div class="">
                <p class="switcher-style-head">Layout Width Styles:</p>
                <div class="row switcher-style gx-0">
                    <div class="col-4">
                        <div class="form-check switch-select">
                            <label class="form-check-label" for="switcher-full-width">
                                Full Width
                            </label>
                            <input class="form-check-input" type="radio" name="layout_width"
                                id="switcher-full-width" value="fullwidth"
                                {{ ($themeSettings->layout_width ?? 'fullwidth') === 'fullwidth' ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-check switch-select">
                            <label class="form-check-label" for="switcher-boxed">
                                Boxed
                            </label>
                            <input class="form-check-input" type="radio" name="layout_width" id="switcher-boxed"
                                value="boxed"
                                {{ ($themeSettings->layout_width ?? 'fullwidth') === 'boxed' ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
            </div>
            <div class="">
                <p class="switcher-style-head">Menu Positions:</p>
                <div class="row switcher-style gx-0">
                    <div class="col-4">
                        <div class="form-check switch-select">
                            <label class="form-check-label" for="switcher-menu-fixed">
                                Fixed
                            </label>
                            <input class="form-check-input" type="radio" name="menu_position"
                                id="switcher-menu-fixed" value="fixed"
                                {{ ($themeSettings->menu_position ?? 'fixed') === 'fixed' ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-check switch-select">
                            <label class="form-check-label" for="switcher-menu-scroll">
                                Scrollable
                            </label>
                            <input class="form-check-input" type="radio" name="menu_position"
                                id="switcher-menu-scroll" value="scrollable"
                                {{ ($themeSettings->menu_position ?? 'fixed') === 'scrollable' ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
            </div>
            <div class="">
                <p class="switcher-style-head">Header Positions:</p>
                <div class="row switcher-style gx-0">
                    <div class="col-4">
                        <div class="form-check switch-select">
                            <label class="form-check-label" for="switcher-header-fixed">
                                Fixed
                            </label>
                            <input class="form-check-input" type="radio" name="header_position"
                                id="switcher-header-fixed" value="fixed"
                                {{ ($themeSettings->header_position ?? 'fixed') === 'fixed' ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-check switch-select">
                            <label class="form-check-label" for="switcher-header-scroll">
                                Scrollable
                            </label>
                            <input class="form-check-input" type="radio" name="header_position"
                                id="switcher-header-scroll" value="scrollable"
                                {{ ($themeSettings->header_position ?? 'fixed') === 'scrollable' ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Theme Colors Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="card-title mb-0">Theme Colors</h6>
        </div>
        <div class="card-body">
            <div class="theme-colors">
                <p class="switcher-style-head">Menu Colors:</p>
                <div class="d-flex switcher-style pb-2">
                    <div class="form-check switch-select me-3">
                        <input class="form-check-input color-input color-white" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="Light Menu" type="radio" name="menu_color"
                            id="switcher-menu-light" value="light"
                            {{ ($themeSettings->menu_color ?? 'dark') === 'light' ? 'checked' : '' }}>
                    </div>
                    <div class="form-check switch-select me-3">
                        <input class="form-check-input color-input color-dark" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="Dark Menu" type="radio" name="menu_color"
                            id="switcher-menu-dark" value="dark"
                            {{ ($themeSettings->menu_color ?? 'dark') === 'dark' ? 'checked' : '' }}>
                    </div>
                    <div class="form-check switch-select me-3">
                        <input class="form-check-input color-input color-primary" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="Color Menu" type="radio" name="menu_color"
                            id="switcher-menu-primary" value="color"
                            {{ ($themeSettings->menu_color ?? 'dark') === 'color' ? 'checked' : '' }}>
                    </div>
                    <div class="form-check switch-select me-3">
                        <input class="form-check-input color-input color-gradient" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="Gradient Menu" type="radio" name="menu_color"
                            id="switcher-menu-gradient" value="gradient"
                            {{ ($themeSettings->menu_color ?? 'dark') === 'gradient' ? 'checked' : '' }}>
                    </div>
                    <div class="form-check switch-select me-3">
                        <input class="form-check-input color-input color-transparent" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="Transparent Menu" type="radio" name="menu_color"
                            id="switcher-menu-transparent" value="transparent"
                            {{ ($themeSettings->menu_color ?? 'dark') === 'transparent' ? 'checked' : '' }}>
                    </div>
                </div>
                <div class="px-4 pb-3 text-muted fs-11">Note: If you want to change color Menu dynamically
                    change from below Theme Primary color picker</div>
            </div>
            <div class="theme-colors">
                <p class="switcher-style-head">Header Colors:</p>
                <div class="d-flex switcher-style pb-2">
                    <div class="form-check switch-select me-3">
                        <input class="form-check-input color-input color-white" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="Light Header" type="radio" name="header_color"
                            id="switcher-header-light" value="light"
                            {{ ($themeSettings->header_color ?? 'light') === 'light' ? 'checked' : '' }}>
                    </div>
                    <div class="form-check switch-select me-3">
                        <input class="form-check-input color-input color-dark" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="Dark Header" type="radio" name="header_color"
                            id="switcher-header-dark" value="dark"
                            {{ ($themeSettings->header_color ?? 'light') === 'dark' ? 'checked' : '' }}>
                    </div>
                    <div class="form-check switch-select me-3">
                        <input class="form-check-input color-input color-primary" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="Color Header" type="radio" name="header_color"
                            id="switcher-header-primary" value="color"
                            {{ ($themeSettings->header_color ?? 'light') === 'color' ? 'checked' : '' }}>
                    </div>
                    <div class="form-check switch-select me-3">
                        <input class="form-check-input color-input color-gradient" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="Gradient Header" type="radio" name="header_color"
                            id="switcher-header-gradient" value="gradient"
                            {{ ($themeSettings->header_color ?? 'light') === 'gradient' ? 'checked' : '' }}>
                    </div>
                    <div class="form-check switch-select me-3">
                        <input class="form-check-input color-input color-transparent" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="Transparent Header" type="radio" name="header_color"
                            id="switcher-header-transparent" value="transparent"
                            {{ ($themeSettings->header_color ?? 'light') === 'transparent' ? 'checked' : '' }}>
                    </div>
                </div>
                <div class="px-4 pb-3 text-muted fs-11">Note:If you want to change color Header dynamically
                    change from below Theme Primary color picker</div>
            </div>
            <div class="theme-colors">
                <p class="switcher-style-head">Theme Primary:</p>
                <div class="d-flex flex-wrap align-items-center switcher-style">
                    <div class="form-check switch-select me-3">
                        <input class="form-check-input color-input color-primary-1" type="radio"
                            name="primary_color" id="switcher-primary" value="58, 88, 146"
                            {{ ($themeSettings->primary_color ?? '58, 88, 146') === '58, 88, 146' ? 'checked' : '' }}>
                    </div>
                    <div class="form-check switch-select me-3">
                        <input class="form-check-input color-input color-primary-2" type="radio"
                            name="primary_color" id="switcher-primary1" value="92, 144, 163"
                            {{ ($themeSettings->primary_color ?? '58, 88, 146') === '92, 144, 163' ? 'checked' : '' }}>
                    </div>
                    <div class="form-check switch-select me-3">
                        <input class="form-check-input color-input color-primary-3" type="radio"
                            name="primary_color" id="switcher-primary2" value="161, 90, 223"
                            {{ ($themeSettings->primary_color ?? '58, 88, 146') === '161, 90, 223' ? 'checked' : '' }}>
                    </div>
                    <div class="form-check switch-select me-3">
                        <input class="form-check-input color-input color-primary-4" type="radio"
                            name="primary_color" id="switcher-primary3" value="78, 172, 76"
                            {{ ($themeSettings->primary_color ?? '58, 88, 146') === '78, 172, 76' ? 'checked' : '' }}>
                    </div>
                    <div class="form-check switch-select me-3">
                        <input class="form-check-input color-input color-primary-5" type="radio"
                            name="primary_color" id="switcher-primary4" value="223, 90, 90"
                            {{ ($themeSettings->primary_color ?? '58, 88, 146') === '223, 90, 90' ? 'checked' : '' }}>
                    </div>
                    <div class="form-check switch-select ps-0 mt-1 color-primary-light">
                        <div class="theme-container-primary"></div>
                        <div class="pickr-container-primary"></div>
                    </div>
                </div>
            </div>
            <div class="theme-colors">
                <p class="switcher-style-head">Theme Background:</p>
                <div class="d-flex flex-wrap align-items-center switcher-style">
                    <div class="form-check switch-select me-3">
                        <input class="form-check-input color-input color-bg-1" type="radio" name="background_color"
                            id="switcher-background" value="20, 30, 96"
                            {{ ($themeSettings->background_color ?? '') === '20, 30, 96' ? 'checked' : '' }}>
                    </div>
                    <div class="form-check switch-select me-3">
                        <input class="form-check-input color-input color-bg-2" type="radio" name="background_color"
                            id="switcher-background1" value="8, 78, 115"
                            {{ ($themeSettings->background_color ?? '') === '8, 78, 115' ? 'checked' : '' }}>
                    </div>
                    <div class="form-check switch-select me-3">
                        <input class="form-check-input color-input color-bg-3" type="radio" name="background_color"
                            id="switcher-background2" value="90, 37, 135"
                            {{ ($themeSettings->background_color ?? '') === '90, 37, 135' ? 'checked' : '' }}>
                    </div>
                    <div class="form-check switch-select me-3">
                        <input class="form-check-input color-input color-bg-4" type="radio" name="background_color"
                            id="switcher-background3" value="24, 101, 51"
                            {{ ($themeSettings->background_color ?? '') === '24, 101, 51' ? 'checked' : '' }}>
                    </div>
                    <div class="form-check switch-select me-3">
                        <input class="form-check-input color-input color-bg-5" type="radio" name="background_color"
                            id="switcher-background4" value="120, 66, 20"
                            {{ ($themeSettings->background_color ?? '') === '120, 66, 20' ? 'checked' : '' }}>
                    </div>
                    <div class="form-check switch-select ps-0 mt-1 tooltip-static-demo color-bg-transparent">
                        <div class="theme-container-background"></div>
                        <div class="pickr-container-background"></div>
                    </div>
                </div>
            </div>
            <div class="menu-image mb-3">
                <p class="switcher-style-head">Menu With Background Image:</p>
                <div class="d-flex flex-wrap align-items-center switcher-style">
                    @for ($i = 1; $i <= 5; $i++)
                        @php
                            $bgValue = "bgimg{$i}";
                            $customImagePath = "theme/backgrounds/bgimg{$i}.jpg";
                            $customImagePathPng = "theme/backgrounds/bgimg{$i}.png";
                            $customImagePathGif = "theme/backgrounds/bgimg{$i}.gif";

                            $hasCustomImage = false;
                            $customImageUrl = '';

                            // Check for custom uploaded images
                            if (Storage::disk('public')->exists($customImagePath)) {
                                $hasCustomImage = true;
                                $customImageUrl = Storage::url($customImagePath);
                            } elseif (Storage::disk('public')->exists($customImagePathPng)) {
                                $hasCustomImage = true;
                                $customImageUrl = Storage::url($customImagePathPng);
                            } elseif (Storage::disk('public')->exists($customImagePathGif)) {
                                $hasCustomImage = true;
                                $customImageUrl = Storage::url($customImagePathGif);
                            }

                            // Default image URLs
                            $defaultImageUrl = asset("assets/images/menu-bg-images/bg-img{$i}.jpg");

                            // Use custom image if available, otherwise use default
                            $imageUrl = $hasCustomImage ? $customImageUrl : $defaultImageUrl;
                        @endphp

                        <div class="form-check switch-select m-2 position-relative">
                            <!-- Hidden Radio Input -->
                            <input class="form-check-input bgimage-input bg-img{{ $i }}" type="radio"
                                name="background_image" id="switcher-bg-img{{ $i === 1 ? '' : $i - 1 }}"
                                value="{{ $bgValue }}" style="display: none;"
                                {{ ($themeSettings->background_image ?? '') === $bgValue ? 'checked' : '' }}>

                            <!-- Background Image Preview (Only Image) -->
                            <div class="bg-image-preview {{ ($themeSettings->background_image ?? '') === $bgValue ? 'selected' : '' }} position-relative"
                                style="width: 60px; height: 80px; border: 2px solid {{ ($themeSettings->background_image ?? '') === $bgValue ? 'var(--primary-color, #3a5892)' : '#dee2e6' }}; border-radius: 8px; overflow: hidden; cursor: pointer; transition: border-color 0.3s ease;"
                                onclick="document.getElementById('switcher-bg-img{{ $i === 1 ? '' : $i - 1 }}').click();">
                                <img src="{{ $imageUrl }}" alt="Background {{ $i }}"
                                    style="width: 100%; height: 100%; object-fit: cover;"
                                    onerror="this.src='{{ $defaultImageUrl }}'">

                                <!-- Custom Image Indicator -->
                                @if ($hasCustomImage)
                                    <div class="position-absolute top-0 end-0 bg-success text-white rounded-circle"
                                        style="width: 16px; height: 16px; margin: 2px; display: flex; align-items: center; justify-content: center; font-size: 10px;">
                                        <i class="ri-check-line"></i>
                                    </div>
                                @endif

                                <!-- Selection Indicator -->
                                @if (($themeSettings->background_image ?? '') === $bgValue)
                                    <div class="position-absolute bottom-0 start-0 end-0 bg-primary text-white text-center"
                                        style="font-size: 8px; padding: 1px 2px;">
                                        Đã chọn
                                    </div>
                                @endif
                            </div>

                            <!-- Image Label -->
                            <small class="d-block text-center mt-1" style="font-size: 10px;">
                                {{ $hasCustomImage ? 'Custom' : 'Mẫu' }} {{ $i }}
                            </small>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- Background Image Upload Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="card-title mb-0">Tải lên hình nền tùy chỉnh</h6>
        </div>
        <div class="card-body">
            <div class="row">
                @for ($i = 1; $i <= 5; $i++)
                    <div class="col-md-6 col-lg-4 mb-3">
                        <label class="form-label">Hình nền {{ $i }}:</label>
                        <div class="input-group">
                            <input type="file" class="form-control" name="bg_image_{{ $i }}"
                                accept="image/*" id="bg_image_{{ $i }}">
                            <button type="button" class="btn btn-outline-secondary"
                                onclick="clearBackgroundImage({{ $i }})">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                        <small class="form-text text-muted">
                            Định dạng: JPG, PNG, GIF. Kích thước: 360x1000px
                        </small>
                        <!-- Preview -->
                        <div id="bg_preview_{{ $i }}" class="mt-2" style="display: none;">
                            <img src="" alt="Preview" class="img-thumbnail"
                                style="max-width: 100px; max-height: 100px;">
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-start align-items-center gap-2">
        <button type="button" id="reset-all" class="btn btn-danger">
            <i class="ri-refresh-line me-1"></i>Đặt Lại
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="ri-save-line me-1"></i>Lưu Cài Đặt
        </button>
    </div>
</form>

<!-- Integrated Theme Settings JavaScript -->
<script>
    "use strict";

    // Global variables for theme management
    let mainContent;
    let themeSettings = @json($themeSettings);
    let primaryRGB, myVarVal;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize theme system
        initializeThemeSystem();

        // Background image upload functionality
        setupBackgroundImageUploads();

        // Initialize color pickers
        initializeColorPickers();

        // Setup theme switcher event listeners
        setupThemeSwitchers();

        // Initialize complete theme system
        initializeComplete();

        // Setup manual save functionality
        setupManualSave();
    });

    // Initialize the complete theme system
    function initializeThemeSystem() {
        let html = document.querySelector('html');
        mainContent = document.querySelector('.main-content');

        // Apply theme settings from database on page load
        setTimeout(() => {
            applyThemeFromDatabase();
        }, 100);
    }

    // Apply theme settings from database
    function applyThemeFromDatabase() {
        const html = document.querySelector('html');

        // Apply all theme attributes from database
        if (themeSettings.theme_mode) {
            html.setAttribute('data-theme-mode', themeSettings.theme_mode);
        }

        if (themeSettings.direction) {
            html.setAttribute('dir', themeSettings.direction);
        }

        if (themeSettings.navigation_style) {
            html.setAttribute('data-nav-layout', themeSettings.navigation_style);
        }

        if (themeSettings.navigation_menu_style) {
            html.setAttribute('data-nav-style', themeSettings.navigation_menu_style);
        }

        if (themeSettings.sidemenu_layout && themeSettings.sidemenu_layout !== 'default') {
            html.setAttribute('data-vertical-style', themeSettings.sidemenu_layout === 'doublemenu' ? 'doublemenu' :
                themeSettings.sidemenu_layout);
        }

        if (themeSettings.page_style && themeSettings.page_style !== 'regular') {
            html.setAttribute('data-page-style', themeSettings.page_style);
        }

        if (themeSettings.layout_width && themeSettings.layout_width !== 'fullwidth') {
            html.setAttribute('data-width', themeSettings.layout_width);
        }

        if (themeSettings.menu_position && themeSettings.menu_position !== 'fixed') {
            html.setAttribute('data-menu-position', themeSettings.menu_position);
        }

        if (themeSettings.header_position && themeSettings.header_position !== 'fixed') {
            html.setAttribute('data-header-position', themeSettings.header_position);
        }

        if (themeSettings.menu_color) {
            html.setAttribute('data-menu-styles', themeSettings.menu_color);
        }

        if (themeSettings.header_color) {
            html.setAttribute('data-header-styles', themeSettings.header_color);
        }

        if (themeSettings.background_image) {
            html.setAttribute('data-bg-img', themeSettings.background_image);
        }

        // Apply CSS custom properties
        if (themeSettings.primary_color) {
            html.style.setProperty('--primary-rgb', themeSettings.primary_color);
        }

        if (themeSettings.background_color && themeSettings.background_light_color) {
            html.style.setProperty('--body-bg-rgb', themeSettings.background_color);
            html.style.setProperty('--light-rgb', themeSettings.background_light_color);
            html.style.setProperty('--form-control-bg', `rgb(${themeSettings.background_light_color})`);
            html.style.setProperty('--input-border', 'rgba(255,255,255,0.1)');
        }

        // Update colors for charts and components
        updateColors();
    }

    // Setup theme switcher event listeners
    function setupThemeSwitchers() {
        // Theme mode switchers
        const lightBtn = document.querySelector('#switcher-light-theme');
        const darkBtn = document.querySelector('#switcher-dark-theme');

        if (lightBtn) {
            lightBtn.addEventListener('click', () => {
                applyLightTheme();
            });
        }

        if (darkBtn) {
            darkBtn.addEventListener('click', () => {
                applyDarkTheme();
            });
        }

        // Direction switchers
        const ltrBtn = document.querySelector('#switcher-ltr');
        const rtlBtn = document.querySelector('#switcher-rtl');

        if (ltrBtn) {
            ltrBtn.addEventListener('click', () => {
                applyLtrDirection();
            });
        }

        if (rtlBtn) {
            rtlBtn.addEventListener('click', () => {
                applyRtlDirection();
            });
        }

        // Navigation style switchers
        const verticalBtn = document.querySelector('#switcher-vertical');
        const horizontalBtn = document.querySelector('#switcher-horizontal');

        if (verticalBtn) {
            verticalBtn.addEventListener('click', () => {
                applyVerticalNavigation();
            });
        }

        if (horizontalBtn) {
            horizontalBtn.addEventListener('click', () => {
                applyHorizontalNavigation();
            });
        }

        // Navigation menu style switchers
        setupNavigationMenuSwitchers();

        // Sidemenu layout switchers
        setupSidemenuLayoutSwitchers();

        // Page style switchers
        setupPageStyleSwitchers();

        // Layout width switchers
        setupLayoutWidthSwitchers();

        // Position switchers
        setupPositionSwitchers();

        // Color switchers
        setupColorSwitchers();

        // Primary color switchers
        setupPrimaryColorSwitchers();

        // Background color switchers
        setupBackgroundColorSwitchers();

        // Background image switchers
        setupBackgroundImageSwitchers();

        // Reset functionality
        const resetBtn = document.getElementById('reset-all');
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                if (confirm('Bạn có chắc chắn muốn đặt lại tất cả cài đặt về mặc định?')) {
                    resetAllSettings();
                }
            });
        }
    }

    // Navigation menu style switchers
    function setupNavigationMenuSwitchers() {
        const menuClickBtn = document.querySelector('#switcher-menu-click');
        const menuHoverBtn = document.querySelector('#switcher-menu-hover');
        const iconClickBtn = document.querySelector('#switcher-icon-click');
        const iconHoverBtn = document.querySelector('#switcher-icon-hover');

        if (menuClickBtn) {
            menuClickBtn.addEventListener('click', () => {
                applyNavigationMenuStyle('menu-click');
            });
        }

        if (menuHoverBtn) {
            menuHoverBtn.addEventListener('click', () => {
                applyNavigationMenuStyle('menu-hover');
            });
        }

        if (iconClickBtn) {
            iconClickBtn.addEventListener('click', () => {
                applyNavigationMenuStyle('icon-click');
            });
        }

        if (iconHoverBtn) {
            iconHoverBtn.addEventListener('click', () => {
                applyNavigationMenuStyle('icon-hover');
            });
        }
    }

    // Sidemenu layout switchers
    function setupSidemenuLayoutSwitchers() {
        const layouts = ['default', 'closed', 'icontext', 'overlay', 'detached', 'doublemenu'];

        layouts.forEach(layout => {
            const btn = document.querySelector(
                `#switcher-${layout === 'doublemenu' ? 'double-menu' : (layout === 'icontext' ? 'icontext-menu' : (layout === 'overlay' ? 'icon-overlay' : (layout === 'detached' ? 'detached' : (layout === 'closed' ? 'closed-menu' : 'default-menu'))))}`
            );

            if (btn) {
                btn.addEventListener('click', () => {
                    applySidemenuLayout(layout);
                });
            }
        });
    }

    // Page style switchers
    function setupPageStyleSwitchers() {
        const styles = ['regular', 'classic', 'modern'];

        styles.forEach(style => {
            const btn = document.querySelector(`#switcher-${style}`);

            if (btn) {
                btn.addEventListener('click', () => {
                    applyPageStyle(style);
                });
            }
        });
    }

    // Layout width switchers
    function setupLayoutWidthSwitchers() {
        const fullWidthBtn = document.querySelector('#switcher-full-width');
        const boxedBtn = document.querySelector('#switcher-boxed');

        if (fullWidthBtn) {
            fullWidthBtn.addEventListener('click', () => {
                applyLayoutWidth('fullwidth');
            });
        }

        if (boxedBtn) {
            boxedBtn.addEventListener('click', () => {
                applyLayoutWidth('boxed');
            });
        }
    }

    // Position switchers
    function setupPositionSwitchers() {
        // Menu position
        const menuFixedBtn = document.querySelector('#switcher-menu-fixed');
        const menuScrollBtn = document.querySelector('#switcher-menu-scroll');

        if (menuFixedBtn) {
            menuFixedBtn.addEventListener('click', () => {
                applyMenuPosition('fixed');
            });
        }

        if (menuScrollBtn) {
            menuScrollBtn.addEventListener('click', () => {
                applyMenuPosition('scrollable');
            });
        }

        // Header position
        const headerFixedBtn = document.querySelector('#switcher-header-fixed');
        const headerScrollBtn = document.querySelector('#switcher-header-scroll');

        if (headerFixedBtn) {
            headerFixedBtn.addEventListener('click', () => {
                applyHeaderPosition('fixed');
            });
        }

        if (headerScrollBtn) {
            headerScrollBtn.addEventListener('click', () => {
                applyHeaderPosition('scrollable');
            });
        }
    }

    // Color switchers
    function setupColorSwitchers() {
        // Menu colors
        const menuColors = ['light', 'dark', 'color', 'gradient', 'transparent'];

        menuColors.forEach(color => {
            const btn = document.querySelector(`#switcher-menu-${color === 'color' ? 'primary' : color}`);

            if (btn) {
                btn.addEventListener('click', () => {
                    applyMenuColor(color);
                });
            }
        });

        // Header colors
        const headerColors = ['light', 'dark', 'color', 'gradient', 'transparent'];

        headerColors.forEach(color => {
            const btn = document.querySelector(`#switcher-header-${color === 'color' ? 'primary' : color}`);

            if (btn) {
                btn.addEventListener('click', () => {
                    applyHeaderColor(color);
                });
            }
        });
    }

    // Primary color switchers
    function setupPrimaryColorSwitchers() {
        const primaryColors = {
            'switcher-primary': '58, 88, 146',
            'switcher-primary1': '92, 144, 163',
            'switcher-primary2': '161, 90, 223',
            'switcher-primary3': '78, 172, 76',
            'switcher-primary4': '223, 90, 90'
        };

        Object.entries(primaryColors).forEach(([id, rgb]) => {
            const btn = document.querySelector(`#${id}`);

            if (btn) {
                btn.addEventListener('click', () => {
                    applyPrimaryColor(rgb);
                });
            }
        });
    }

    // Background color switchers
    function setupBackgroundColorSwitchers() {
        const backgroundColors = {
            'switcher-background': {
                bg: '20, 30, 96',
                light: '25, 38, 101'
            },
            'switcher-background1': {
                bg: '8, 78, 115',
                light: '13, 86, 120'
            },
            'switcher-background2': {
                bg: '90, 37, 135',
                light: '95, 45, 140'
            },
            'switcher-background3': {
                bg: '24, 101, 51',
                light: '29, 109, 56'
            },
            'switcher-background4': {
                bg: '120, 66, 20',
                light: '125, 74, 25'
            }
        };

        Object.entries(backgroundColors).forEach(([id, colors]) => {
            const btn = document.querySelector(`#${id}`);

            if (btn) {
                btn.addEventListener('click', () => {
                    applyBackgroundColor(colors.bg, colors.light);
                });
            }
        });
    }

    // Background image switchers
    function setupBackgroundImageSwitchers() {
        const images = ['bgimg1', 'bgimg2', 'bgimg3', 'bgimg4', 'bgimg5'];

        images.forEach((img, index) => {
            const btn = document.querySelector(`#switcher-bg-img${index === 0 ? '' : index}`);

            if (btn) {
                btn.addEventListener('click', () => {
                    applyBackgroundImage(img);
                    updateBackgroundImagePreviews(img);
                });
            }
        });
    }

    // Update background image preview borders
    function updateBackgroundImagePreviews(selectedImg) {
        // Remove selected class and indicators from all previews
        document.querySelectorAll('.bg-image-preview').forEach(preview => {
            preview.classList.remove('selected');
            preview.style.borderColor = '#dee2e6';

            // Remove existing selection indicator
            const existingIndicator = preview.querySelector('.bg-primary');
            if (existingIndicator) {
                existingIndicator.remove();
            }
        });

        // Add selected class to current preview
        const images = ['bgimg1', 'bgimg2', 'bgimg3', 'bgimg4', 'bgimg5'];
        const selectedIndex = images.indexOf(selectedImg);
        if (selectedIndex !== -1) {
            const selectedPreview = document.querySelectorAll('.bg-image-preview')[selectedIndex];
            if (selectedPreview) {
                selectedPreview.classList.add('selected');
                selectedPreview.style.borderColor = 'var(--primary-color, #3a5892)';

                // Add selection indicator
                const selectionIndicator = document.createElement('div');
                selectionIndicator.className =
                    'position-absolute bottom-0 start-0 end-0 bg-primary text-white text-center';
                selectionIndicator.style.cssText = 'font-size: 8px; padding: 1px 2px;';
                selectionIndicator.textContent = 'Đã chọn';
                selectedPreview.appendChild(selectionIndicator);
            }
        }
    }

    // Update background image preview after upload
    function updateBackgroundImagePreviewAfterUpload(imageNumber, imageDataUrl) {
        const previews = document.querySelectorAll('.bg-image-preview');
        if (previews[imageNumber - 1]) {
            const imgElement = previews[imageNumber - 1].querySelector('img');
            const labelElement = previews[imageNumber - 1].parentElement.querySelector('small');

            if (imgElement) {
                imgElement.src = imageDataUrl;
            }

            if (labelElement) {
                labelElement.textContent = `Custom ${imageNumber}`;
            }

            // Add custom indicator if not exists
            if (!previews[imageNumber - 1].querySelector('.bg-success')) {
                const indicator = document.createElement('div');
                indicator.className = 'position-absolute top-0 end-0 bg-success text-white rounded-circle';
                indicator.style.cssText =
                    'width: 16px; height: 16px; margin: 2px; display: flex; align-items: center; justify-content: center; font-size: 10px;';
                indicator.innerHTML = '<i class="ri-check-line"></i>';
                previews[imageNumber - 1].appendChild(indicator);
            }
        }
    }

    // Theme application functions
    function applyLightTheme() {
        const html = document.querySelector('html');
        html.setAttribute('data-theme-mode', 'light');
        html.setAttribute('data-header-styles', 'light');

        if (themeSettings.navigation_style === 'horizontal') {
            html.setAttribute('data-menu-styles', 'light');
        }

        // Clear dark theme properties
        html.style.removeProperty('--body-bg-rgb');
        html.style.removeProperty('--light-rgb');
        html.style.removeProperty('--form-control-bg');
        html.style.removeProperty('--input-border');
        html.style.removeProperty('--primary-rgb');
        html.removeAttribute('style');

        // Update form checkboxes
        document.querySelector('#switcher-light-theme').checked = true;
        document.querySelector('#switcher-menu-light').checked = true;
        document.querySelector('#switcher-header-light').checked = true;

        // Clear background color selections
        document.querySelector("#switcher-background4").checked = false;
        document.querySelector("#switcher-background3").checked = false;
        document.querySelector("#switcher-background2").checked = false;
        document.querySelector("#switcher-background1").checked = false;
        document.querySelector("#switcher-background").checked = false;

        updateColors();
    }

    function applyDarkTheme() {
        const html = document.querySelector('html');
        html.setAttribute('data-theme-mode', 'dark');
        html.setAttribute('data-header-styles', 'dark');
        html.setAttribute('data-menu-styles', 'dark');

        // Clear light theme properties
        html.style.removeProperty('--body-bg-rgb');
        html.style.removeProperty('--light-rgb');
        html.style.removeProperty('--form-control-bg');
        html.style.removeProperty('--input-border');
        html.style.removeProperty("--primary-rgb");

        // Update form checkboxes
        document.querySelector('#switcher-menu-dark').checked = true;
        document.querySelector('#switcher-header-dark').checked = true;

        // Clear background color selections
        document.querySelector("#switcher-background4").checked = false;
        document.querySelector("#switcher-background3").checked = false;
        document.querySelector("#switcher-background2").checked = false;
        document.querySelector("#switcher-background1").checked = false;
        document.querySelector("#switcher-background").checked = false;

        updateColors();
    }

    function applyLtrDirection() {
        const html = document.querySelector('html');
        html.setAttribute('dir', 'ltr');

        // Update Bootstrap CSS for LTR
        const styleLink = document.querySelector('#style');
        if (styleLink && !styleLink.href.includes('bootstrap.min.css')) {
            styleLink.setAttribute('href', '{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}');
        }
    }

    function applyRtlDirection() {
        const html = document.querySelector('html');
        html.setAttribute('dir', 'rtl');

        // Update Bootstrap CSS for RTL
        const styleLink = document.querySelector('#style');
        if (styleLink) {
            styleLink.setAttribute('href', '{{ asset('assets/libs/bootstrap/css/bootstrap.rtl.min.css') }}');
        }
    }

    function applyVerticalNavigation() {
        const html = document.querySelector('html');
        html.setAttribute('data-nav-layout', 'vertical');
        html.setAttribute('data-vertical-style', 'overlay');
        html.removeAttribute('data-nav-style');
        html.removeAttribute('data-toggled');

        if (mainContent) {
            mainContent.removeEventListener('click', clearNavDropdown);
        }

        // Reset menu margins
        const mainMenu = document.querySelector('.main-menu');
        if (mainMenu) {
            mainMenu.style.marginLeft = '0px';
            mainMenu.style.marginRight = '0px';
        }

        // Set navigation active state
        setNavActive();

        // Update menu color for vertical layout
        html.setAttribute("data-menu-styles", "dark");

        // Update form checkboxes
        document.querySelector('#switcher-menu-click').checked = false;
        document.querySelector('#switcher-menu-hover').checked = false;
        document.querySelector('#switcher-icon-click').checked = false;
        document.querySelector('#switcher-icon-hover').checked = false;

        document.querySelectorAll(".slide").forEach((element) => {
            if (element.classList.contains("open") && !element.classList.contains("active")) {
                element.querySelector("ul").style.display = "none";
            }
        });
    }

    function applyHorizontalNavigation() {
        const html = document.querySelector('html');
        html.setAttribute('data-nav-layout', 'horizontal');
        html.removeAttribute('data-vertical-style');

        if (!html.getAttribute('data-nav-style')) {
            html.setAttribute('data-nav-style', 'menu-click');
        }

        if (themeSettings.menu_color !== 'dark') {
            html.setAttribute('data-menu-styles', 'light');
        }

        if (mainContent) {
            mainContent.addEventListener('click', clearNavDropdown);
        }

        // Reset menu margins
        const mainMenu = document.querySelector('.main-menu');
        if (mainMenu) {
            mainMenu.style.marginLeft = '0px';
            mainMenu.style.marginRight = '0px';
        }

        // Update form checkboxes
        document.querySelector('#switcher-horizontal').checked = true;
        document.querySelector('#switcher-menu-click').checked = true;

        // Check horizontal menu
        checkHoriMenu();
    }

    function applyNavigationMenuStyle(style) {
        const html = document.querySelector('html');
        html.removeAttribute('data-vertical-style');

        switch (style) {
            case 'menu-click':
                menuClickFn();
                break;
            case 'menu-hover':
                menuhoverFn();
                break;
            case 'icon-click':
                iconClickFn();
                break;
            case 'icon-hover':
                iconHoverFn();
                break;
            default:
                html.setAttribute('data-nav-style', style);
                break;
        }

        const mainMenu = document.querySelector('.main-menu');
        if (mainMenu && html.getAttribute('data-nav-layout') === 'horizontal') {
            mainMenu.style.marginLeft = '0px';
            mainMenu.style.marginRight = '0px';
            ResizeMenu();

            if (style === 'icon-click' || style === 'icon-hover') {
                const slideLeft = document.querySelector("#slide-left");
                if (slideLeft) {
                    slideLeft.classList.add("d-none");
                }
            }
        }
    }

    function applySidemenuLayout(layout) {
        const html = document.querySelector('html');

        switch (layout) {
            case 'default':
                html.setAttribute('data-vertical-style', 'default');
                html.setAttribute('data-nav-layout', 'vertical');
                toggleSidemenu();
                break;
            case 'closed':
                closedSidemenuFn();
                break;
            case 'icontext':
                iconTextFn();
                break;
            case 'overlay':
                iconOverayFn();
                break;
            case 'detached':
                detachedFn();
                break;
            case 'doublemenu':
                doubletFn();
                break;
            default:
                html.setAttribute('data-vertical-style', 'default');
                html.setAttribute('data-nav-layout', 'vertical');
                toggleSidemenu();
                break;
        }
    }

    function applyPageStyle(style) {
        const html = document.querySelector('html');
        html.setAttribute('data-page-style', style);
    }

    function applyLayoutWidth(width) {
        const html = document.querySelector('html');
        html.setAttribute('data-width', width);
    }

    function applyMenuPosition(position) {
        const html = document.querySelector('html');
        html.setAttribute('data-menu-position', position);
    }

    function applyHeaderPosition(position) {
        const html = document.querySelector('html');
        html.setAttribute('data-header-position', position);
    }

    function applyMenuColor(color) {
        const html = document.querySelector('html');
        html.setAttribute('data-menu-styles', color);
    }

    function applyHeaderColor(color) {
        const html = document.querySelector('html');
        html.setAttribute('data-header-styles', color);
    }

    function applyPrimaryColor(rgb) {
        const html = document.querySelector('html');
        html.style.setProperty('--primary-rgb', rgb);
        updateColors();
    }

    function applyBackgroundColor(bgRGB, lightRGB) {
        const html = document.querySelector('html');
        html.setAttribute('data-theme-mode', 'dark');
        html.setAttribute('data-menu-styles', 'dark');
        html.setAttribute('data-header-styles', 'dark');

        html.style.setProperty('--body-bg-rgb', bgRGB);
        html.style.setProperty('--light-rgb', lightRGB);
        html.style.setProperty('--form-control-bg', `rgb(${lightRGB})`);
        html.style.setProperty('--input-border', 'rgba(255,255,255,0.1)');

        // Update form checkboxes
        const darkThemeCheckbox = document.querySelector('#switcher-dark-theme');
        const menuDarkCheckbox = document.querySelector('#switcher-menu-dark');
        const headerDarkCheckbox = document.querySelector('#switcher-header-dark');

        if (darkThemeCheckbox) darkThemeCheckbox.checked = true;
        if (menuDarkCheckbox) menuDarkCheckbox.checked = true;
        if (headerDarkCheckbox) headerDarkCheckbox.checked = true;
    }

    function applyBackgroundImage(image) {
        const html = document.querySelector('html');
        html.setAttribute('data-bg-img', image);
    }

    // Reset all settings to default
    function resetAllSettings() {
        const html = document.querySelector('html');

        // Show main menu if horizontal
        if (themeSettings.navigation_style === "horizontal") {
            const mainMenu = document.querySelector(".main-menu");
            if (mainMenu) {
                mainMenu.style.display = "block";
            }
        }

        // Clear all attributes
        html.removeAttribute('data-nav-style');
        html.removeAttribute('data-menu-position');
        html.removeAttribute('data-header-position');
        html.removeAttribute('data-width');
        html.removeAttribute('data-page-style');
        html.removeAttribute('data-bg-img');
        html.removeAttribute('data-vertical-style');
        html.removeAttribute('data-toggled');

        // Clear CSS properties
        html.style.removeProperty('--primary-rgb');
        html.style.removeProperty('--body-bg-rgb');
        html.style.removeProperty('--light-rgb');
        html.style.removeProperty('--form-control-bg');
        html.style.removeProperty('--input-border');
        html.removeAttribute('style');

        // Reset to light theme
        applyLightTheme();

        // Reset to LTR
        applyLtrDirection();

        // Reset to vertical
        applyVerticalNavigation();
        if (mainContent) {
            mainContent.removeEventListener('click', clearNavDropdown);
        }

        // Reset form values
        resetFormToDefaults();

        // Set navigation active state
        setNavActive();

        // Reset horizontal menu scroll
        const mainMenu = document.querySelector(".main-menu");
        if (mainMenu) {
            mainMenu.style.marginLeft = "0px";
            mainMenu.style.marginRight = "0px";
        }

        // Update colors
        updateColors();

        // Update database
    }

    function resetFormToDefaults() {
        // Theme mode
        document.getElementById('switcher-light-theme').checked = true;
        document.getElementById('switcher-dark-theme').checked = false;

        // Direction
        document.getElementById('switcher-ltr').checked = true;
        document.getElementById('switcher-rtl').checked = false;

        // Navigation layout
        document.getElementById('switcher-vertical').checked = true;
        document.getElementById('switcher-horizontal').checked = false;

        // Page styles
        document.getElementById('switcher-regular').checked = true;
        document.getElementById('switcher-classic').checked = false;
        document.getElementById('switcher-modern').checked = false;

        // Layout width
        document.getElementById('switcher-full-width').checked = true;
        document.getElementById('switcher-boxed').checked = false;

        // Menu position
        document.getElementById('switcher-menu-fixed').checked = true;
        document.getElementById('switcher-menu-scroll').checked = false;

        // Header position
        document.getElementById('switcher-header-fixed').checked = true;
        document.getElementById('switcher-header-scroll').checked = false;

        // Sidemenu layout
        document.getElementById('switcher-default-menu').checked = true;
        document.getElementById('switcher-closed-menu').checked = false;
        document.getElementById('switcher-icontext-menu').checked = false;
        document.getElementById('switcher-icon-overlay').checked = false;
        document.getElementById('switcher-detached').checked = false;
        document.getElementById('switcher-double-menu').checked = false;

        // Menu colors
        document.getElementById('switcher-menu-dark').checked = true;
        document.getElementById('switcher-menu-light').checked = false;
        document.getElementById('switcher-menu-primary').checked = false;
        document.getElementById('switcher-menu-gradient').checked = false;
        document.getElementById('switcher-menu-transparent').checked = false;

        // Header colors
        document.getElementById('switcher-header-light').checked = true;
        document.getElementById('switcher-header-dark').checked = false;
        document.getElementById('switcher-header-primary').checked = false;
        document.getElementById('switcher-header-gradient').checked = false;
        document.getElementById('switcher-header-transparent').checked = false;

        // Primary colors
        document.getElementById('switcher-primary').checked = false;
        document.getElementById('switcher-primary1').checked = false;
        document.getElementById('switcher-primary2').checked = false;
        document.getElementById('switcher-primary3').checked = false;
        document.getElementById('switcher-primary4').checked = false;

        // Background colors
        document.getElementById('switcher-background').checked = false;
        document.getElementById('switcher-background1').checked = false;
        document.getElementById('switcher-background2').checked = false;
        document.getElementById('switcher-background3').checked = false;
        document.getElementById('switcher-background4').checked = false;

        // Clear navigation menu style
        document.querySelectorAll('input[name="navigation_menu_style"]').forEach(input => {
            input.checked = false;
        });

        // Clear background image selection
        document.querySelectorAll('input[name="background_image"]').forEach(input => {
            input.checked = false;
        });

        // Clear background color selections
        document.querySelectorAll('input[name="background_color"]').forEach(input => {
            input.checked = false;
        });
    }



    // Setup manual save functionality
    function setupManualSave() {
        // Just let the form submit normally, no JavaScript intervention needed
        console.log('Manual save setup completed - form will submit normally');
    }



    // Clear navigation dropdown (for horizontal layout)
    function clearNavDropdown() {
        // Close any open navigation dropdowns
        document.querySelectorAll('.nav-item.dropdown.show').forEach(dropdown => {
            dropdown.classList.remove('show');
        });

        document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
            menu.classList.remove('show');
        });

        console.log('Navigation dropdown cleared');
    }

    // Chart colors and update function
    function updateColors() {
        'use strict';

        const html = document.querySelector('html');
        primaryRGB = getComputedStyle(html).getPropertyValue('--primary-rgb').trim();
        myVarVal = themeSettings.primary_color || primaryRGB;

        // Update chart colors if charts exist
        if (typeof window.updateChartColors === 'function') {
            window.updateChartColors();
        }

        console.log('Colors updated with primary RGB:', myVarVal);
    }

    // Background image upload functionality
    function setupBackgroundImageUploads() {
        for (let i = 1; i <= 5; i++) {
            const fileInput = document.getElementById(`bg_image_${i}`);
            const preview = document.getElementById(`bg_preview_${i}`);

            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        // Validate image dimensions
                        const img = new Image();
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            img.src = e.target.result;
                            img.onload = function() {
                                if (img.width !== 360 || img.height !== 1000) {
                                    alert(
                                        `Kích thước ảnh phải là 360x1000px. Ảnh hiện tại: ${img.width}x${img.height}px`
                                    );
                                    fileInput.value = '';
                                    if (preview) preview.style.display = 'none';
                                    return;
                                }

                                // Show preview
                                if (preview) {
                                    const imgElement = preview.querySelector('img');
                                    if (imgElement) {
                                        imgElement.src = e.target.result;
                                        preview.style.display = 'block';
                                    }
                                }

                                // Update background image preview if it exists
                                updateBackgroundImagePreviewAfterUpload(i, e.target.result);
                            };
                        };
                        reader.readAsDataURL(file);
                    } else {
                        if (preview) preview.style.display = 'none';
                    }
                });
            }
        }
    }

    // Clear background image
    function clearBackgroundImage(index) {
        const fileInput = document.getElementById(`bg_image_${index}`);
        const preview = document.getElementById(`bg_preview_${index}`);

        if (fileInput) fileInput.value = '';
        if (preview) preview.style.display = 'none';

        // Reset background image preview to default
        const previews = document.querySelectorAll('.bg-image-preview');
        if (previews[index - 1]) {
            const imgElement = previews[index - 1].querySelector('img');
            const labelElement = previews[index - 1].parentElement.querySelector('small');
            const indicator = previews[index - 1].querySelector('.bg-success');

            if (imgElement) {
                // Reset to default image
                const defaultImageUrl = '{{ asset('assets/images/menu-bg-images/bg-img') }}' + index + '.jpg';
                imgElement.src = defaultImageUrl;
            }

            if (labelElement) {
                labelElement.textContent = `Mẫu ${index}`;
            }

            // Remove custom indicator
            if (indicator) {
                indicator.remove();
            }
        }
    }

    // Initialize color pickers
    function initializeColorPickers() {
        // Primary color picker
        const primaryColorPicker = document.querySelector('.pickr-container-primary');
        if (primaryColorPicker && typeof Pickr !== 'undefined') {
            const pickr = Pickr.create({
                el: primaryColorPicker,
                theme: 'nano',
                default: themeSettings.primary_color ? `rgb(${themeSettings.primary_color})` : '#3a5892',

                swatches: [
                    'rgba(58, 88, 146, 1)',
                    'rgba(92, 144, 163, 1)',
                    'rgba(161, 90, 223, 1)',
                    'rgba(78, 172, 76, 1)',
                    'rgba(223, 90, 90, 1)',
                    'rgba(255, 159, 67, 1)',
                    'rgba(255, 205, 86, 1)',
                    'rgba(75, 192, 192, 1)'
                ],

                components: {
                    preview: true,
                    opacity: false,
                    hue: true,
                    interaction: {
                        hex: true,
                        rgba: true,
                        input: true,
                        clear: false,
                        save: false
                    }
                }
            });

            pickr.on('changestop', function(source, instance) {
                const color = instance.getColor().toRGBA();
                const primaryRGB = `${Math.floor(color[0])}, ${Math.floor(color[1])}, ${Math.floor(color[2])}`;

                // Apply immediately
                applyPrimaryColor(primaryRGB);
            });
        }

        // Background color picker
        const bgColorPicker = document.querySelector('.pickr-container-background');
        if (bgColorPicker && typeof Pickr !== 'undefined') {
            const pickr = Pickr.create({
                el: bgColorPicker,
                theme: 'nano',
                default: themeSettings.background_color ? `rgb(${themeSettings.background_color})` : '#141e60',

                swatches: [
                    'rgba(20, 30, 96, 1)',
                    'rgba(8, 78, 115, 1)',
                    'rgba(90, 37, 135, 1)',
                    'rgba(24, 101, 51, 1)',
                    'rgba(120, 66, 20, 1)',
                    'rgba(45, 52, 54, 1)',
                    'rgba(87, 96, 111, 1)',
                    'rgba(116, 125, 136, 1)'
                ],

                components: {
                    preview: true,
                    opacity: false,
                    hue: true,
                    interaction: {
                        hex: true,
                        rgba: true,
                        input: true,
                        clear: false,
                        save: false
                    }
                }
            });

            pickr.on('changestop', function(source, instance) {
                const color = instance.getColor().toRGBA();
                const bodyBgRGB = `${Math.floor(color[0])}, ${Math.floor(color[1])}, ${Math.floor(color[2])}`;
                const bodylightRGB =
                    `${Math.floor(color[0] + 14)}, ${Math.floor(color[1] + 14)}, ${Math.floor(color[2] + 14)}`;

                // Apply immediately
                applyBackgroundColor(bodyBgRGB, bodylightRGB);
            });
        }
    }

    // Additional helper functions from custom-switcher.min.js
    function toggleSidemenu() {
        // Toggle sidemenu functionality
        console.log('Toggle sidemenu');
    }

    function setNavActive() {
        // Set navigation active state
        document.querySelectorAll(".slide").forEach((element) => {
            if (element.classList.contains("open") && !element.classList.contains("active")) {
                element.querySelector("ul").style.display = "none";
            }
        });
    }

    function ResizeMenu() {
        // Resize menu functionality for horizontal layout
        console.log('Resize menu');
    }

    function checkHoriMenu() {
        // Check horizontal menu functionality
        console.log('Check horizontal menu');
    }

    // Sidemenu layout helper functions
    function closedSidemenuFn() {
        const html = document.querySelector('html');
        html.setAttribute('data-vertical-style', 'closed');
        html.setAttribute('data-nav-layout', 'vertical');
        toggleSidemenu();
    }

    function detachedFn() {
        const html = document.querySelector('html');
        html.setAttribute('data-vertical-style', 'detached');
        html.setAttribute('data-nav-layout', 'vertical');
        toggleSidemenu();
    }

    function iconTextFn() {
        const html = document.querySelector('html');
        html.setAttribute('data-vertical-style', 'icontext');
        html.setAttribute('data-nav-layout', 'vertical');
        toggleSidemenu();
    }

    function iconOverayFn() {
        const html = document.querySelector('html');
        html.setAttribute('data-vertical-style', 'overlay');
        html.setAttribute('data-nav-layout', 'vertical');
        toggleSidemenu();
    }

    function doubletFn() {
        const html = document.querySelector('html');
        html.setAttribute('data-vertical-style', 'doublemenu');
        html.setAttribute('data-nav-layout', 'vertical');
        toggleSidemenu();
    }

    function menuClickFn() {
        const html = document.querySelector('html');
        html.setAttribute('data-nav-style', 'menu-click');
        html.setAttribute('data-nav-layout', 'horizontal');
    }

    function menuhoverFn() {
        const html = document.querySelector('html');
        html.setAttribute('data-nav-style', 'menu-hover');
        html.setAttribute('data-nav-layout', 'horizontal');
    }

    function iconClickFn() {
        const html = document.querySelector('html');
        html.setAttribute('data-nav-style', 'icon-click');
        html.setAttribute('data-nav-layout', 'horizontal');
    }

    function iconHoverFn() {
        const html = document.querySelector('html');
        html.setAttribute('data-nav-style', 'icon-hover');
        html.setAttribute('data-nav-layout', 'horizontal');
    }

    // Local storage backup functionality
    function localStorageBackup() {
        if (themeSettings.background_color && themeSettings.background_light_color) {
            const darkThemeCheckbox = document.querySelector('#switcher-dark-theme');
            const menuDarkCheckbox = document.querySelector('#switcher-menu-dark');
            const headerDarkCheckbox = document.querySelector('#switcher-header-dark');

            if (darkThemeCheckbox) darkThemeCheckbox.checked = true;
            if (menuDarkCheckbox) menuDarkCheckbox.checked = true;
            if (headerDarkCheckbox) headerDarkCheckbox.checked = true;
        }

        // Check background color selections
        if (themeSettings.background_color && themeSettings.background_light_color) {
            const bgColorMap = {
                '20, 30, 96': '#switcher-background',
                '8, 78, 115': '#switcher-background1',
                '90, 37, 135': '#switcher-background2',
                '24, 101, 51': '#switcher-background3',
                '120, 66, 20': '#switcher-background4'
            };

            const bgElement = document.querySelector(bgColorMap[themeSettings.background_color]);
            if (bgElement) bgElement.checked = true;
        }

        // Check primary color selections
        if (themeSettings.primary_color) {
            const primaryColorMap = {
                '58, 88, 146': '#switcher-primary',
                '92, 144, 163': '#switcher-primary1',
                '161, 90, 223': '#switcher-primary2',
                '78, 172, 76': '#switcher-primary3',
                '223, 90, 90': '#switcher-primary4'
            };

            const primaryElement = document.querySelector(primaryColorMap[themeSettings.primary_color]);
            if (primaryElement) primaryElement.checked = true;
        }

        // Check background image selections
        if (themeSettings.background_image) {
            const bgImageMap = {
                'bgimg1': '#switcher-bg-img',
                'bgimg2': '#switcher-bg-img1',
                'bgimg3': '#switcher-bg-img2',
                'bgimg4': '#switcher-bg-img3',
                'bgimg5': '#switcher-bg-img4'
            };

            const bgImgElement = document.querySelector(bgImageMap[themeSettings.background_image]);
            if (bgImgElement) bgImgElement.checked = true;
        }
    }

    // Check options based on current theme settings
    function checkOptions() {
        // Theme mode
        if (themeSettings.theme_mode === 'dark') {
            document.querySelector('#switcher-dark-theme').checked = true;
        } else {
            document.querySelector('#switcher-light-theme').checked = true;
        }

        // Navigation layout
        if (themeSettings.navigation_style === 'horizontal') {
            document.querySelector('#switcher-horizontal').checked = true;
            if (themeSettings.navigation_menu_style) {
                document.querySelector(`#switcher-${themeSettings.navigation_menu_style.replace('-', '-')}`).checked =
                    true;
            } else {
                document.querySelector('#switcher-menu-click').checked = true;
            }
        } else {
            document.querySelector('#switcher-vertical').checked = true;
        }

        // Direction
        if (themeSettings.direction === 'rtl') {
            document.querySelector('#switcher-rtl').checked = true;
        } else {
            document.querySelector('#switcher-ltr').checked = true;
        }

        // Header styles
        if (themeSettings.header_color) {
            const headerElement = document.querySelector(
                `#switcher-header-${themeSettings.header_color === 'color' ? 'primary' : themeSettings.header_color}`
            );
            if (headerElement) headerElement.checked = true;
        }

        // Menu styles
        if (themeSettings.menu_color) {
            const menuElement = document.querySelector(
                `#switcher-menu-${themeSettings.menu_color === 'color' ? 'primary' : themeSettings.menu_color}`);
            if (menuElement) menuElement.checked = true;
        }

        // Layout width
        if (themeSettings.layout_width === 'boxed') {
            document.querySelector('#switcher-boxed').checked = true;
        } else {
            document.querySelector('#switcher-full-width').checked = true;
        }

        // Header position
        if (themeSettings.header_position === 'scrollable') {
            document.querySelector('#switcher-header-scroll').checked = true;
        } else {
            document.querySelector('#switcher-header-fixed').checked = true;
        }

        // Menu position
        if (themeSettings.menu_position === 'scrollable') {
            document.querySelector('#switcher-menu-scroll').checked = true;
        } else {
            document.querySelector('#switcher-menu-fixed').checked = true;
        }

        // Page styles
        if (themeSettings.page_style === 'classic') {
            document.querySelector('#switcher-classic').checked = true;
        } else if (themeSettings.page_style === 'modern') {
            document.querySelector('#switcher-modern').checked = true;
        } else {
            document.querySelector('#switcher-regular').checked = true;
        }

        // Sidemenu layout styles
        if (themeSettings.sidemenu_layout && themeSettings.sidemenu_layout !== 'default') {
            const sidemenuMap = {
                'closed': '#switcher-closed-menu',
                'icontext': '#switcher-icontext-menu',
                'overlay': '#switcher-icon-overlay',
                'detached': '#switcher-detached',
                'doublemenu': '#switcher-double-menu'
            };

            const sidemenuElement = document.querySelector(sidemenuMap[themeSettings.sidemenu_layout]);
            if (sidemenuElement) sidemenuElement.checked = true;
        } else {
            document.querySelector('#switcher-default-menu').checked = true;
        }

        // Navigation menu styles
        if (themeSettings.navigation_menu_style) {
            const navStyleElement = document.querySelector(`#switcher-${themeSettings.navigation_menu_style}`);
            if (navStyleElement) navStyleElement.checked = true;
        }
    }

    // Enhanced updateColors function with chart support
    function updateColors() {
        'use strict';

        const html = document.querySelector('html');
        primaryRGB = getComputedStyle(html).getPropertyValue('--primary-rgb').trim();
        myVarVal = themeSettings.primary_color || primaryRGB;

        // Update chart colors if charts exist
        const chartFunctions = [
            'salesOverview', 'saleValue', 'Earnings', 'visitors', 'cryptoCurrency',
            'subOverview', 'Candidates', 'nftBalane', 'nftStatistics', 'targetReport',
            'pageviews', 'Sessions', 'bounceRate', 'audienceReport', 'countrySessions',
            'userSession', 'projectAnalysis', 'crmtotalCustomers', 'revenueAnalytics',
            'crmProfitsearned', 'hrmperformanceReport', 'JobsSummary', 'stockstotalInvested',
            'totalInvestmentsStats', 'stocksMarketcap', 'earningsReport', 'coursePayouts',
            'waterTrack'
        ];

        chartFunctions.forEach(funcName => {
            if (typeof window[funcName] === 'function') {
                if (funcName === 'leads') {
                    window[funcName](myVarVal);
                } else {
                    window[funcName]();
                }
            }
        });

        // Check for specific chart elements
        const chartElements = [
            'salesOverview', 'sale-value', 'earnings', 'total-visitors', 'crypto',
            'subscriptionOverview', 'candidates-chart', 'nft-balance-chart', 'nft-statistics',
            'report', 'views', 'sessions', 'analytics-bouncerate', 'audienceReport',
            'country-sessions', 'session-users', 'projectAnalysis', 'crm-total-customers',
            'crm-revenue-analytics', 'crm-profits-earned', 'leads-source', 'performanceReport',
            'jobs-summary', 'total-invested', 'totalInvestmentsStats', 'stocks-marketcap',
            'courses-earnings', 'course-payouts', 'waterTrack'
        ];

        chartElements.forEach(elementId => {
            if (document.querySelector(`#${elementId}`) !== null) {
                const funcName = elementId.replace(/-([a-z])/g, (g) => g[1].toUpperCase());
                if (typeof window[funcName] === 'function') {
                    window[funcName]();
                }
            }
        });

        console.log('Colors updated with primary RGB:', myVarVal);
    }

    // Initialize everything
    function initializeComplete() {
        // Apply theme settings from database
        applyThemeFromDatabase();

        // Check and update form options
        checkOptions();

        // Backup local storage settings
        localStorageBackup();

        // Set navigation active state
        setNavActive();

        // Update colors
        updateColors();
    }

    // Global functions for compatibility with existing code
    window.updateColors = updateColors;
    window.clearBackgroundImage = clearBackgroundImage;
    window.applyThemeFromDatabase = applyThemeFromDatabase;
    window.checkOptions = checkOptions;
    window.localStorageBackup = localStorageBackup;
    window.setNavActive = setNavActive;

    // Export main functions for external use
    window.themeManager = {
        applyThemeFromDatabase,
        updateColors,
        resetAllSettings,
        checkOptions,
        localStorageBackup,
        setNavActive,
        initializeComplete
    };
</script>
