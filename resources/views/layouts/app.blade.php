<?php

use App\Models\Setting;
use App\Models\ThemeSetting;

$generalSettings = Setting::getMany(null, ['site_name', 'image_square_light']);
$themeSettings = ThemeSetting::getActiveTheme();

// Get HTML attributes from theme settings
$htmlAttributes = [];
if (is_object($themeSettings) && method_exists($themeSettings, 'getHtmlAttributes')) {
    $htmlAttributes = $themeSettings->getHtmlAttributes();
} else {
    // Default attributes if no theme settings
    $htmlAttributes = [
        'data-nav-layout' => 'vertical',
        'data-theme-mode' => 'light',
        'data-header-styles' => 'light',
        'data-menu-styles' => 'dark',
        'data-toggled' => 'close',
        'dir' => 'ltr',
    ];
}

// Build HTML attributes string
$htmlAttributesString = '';
foreach ($htmlAttributes as $key => $value) {
    $htmlAttributesString .= ' ' . $key . '="' . $value . '"';
}

// Get CSS custom properties from theme settings
$cssProperties = [];
if (is_object($themeSettings) && method_exists($themeSettings, 'getCssProperties')) {
    $cssProperties = $themeSettings->getCssProperties();
}

// Build CSS style string
$cssStyleString = '';
foreach ($cssProperties as $property => $value) {
    $cssStyleString .= $property . ': ' . $value . '; ';
}
?>
<!DOCTYPE html>
<html lang="en"<?php echo $htmlAttributesString; ?><?php echo $cssStyleString ? ' style="' . trim($cssStyleString) . '"' : ''; ?>>

<head>

    <!-- Meta Data -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=no'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> {{ $generalSettings['site_name'] ?? '-' }} </title>
    <meta name="Description" content="{{ $generalSettings['site_name'] ?? '-' }}">
    <meta name="Author" content="YNEX">
    <meta name="keywords" content="{{ $generalSettings['site_name'] ?? '-' }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon"
        href="{{ !empty($generalSettings['image_square_light']) ? Storage::url($generalSettings['image_square_light']) : asset('favicon.ico') }}"
        type="image/x-icon">

    @include('layouts.css')

</head>

<body>

    <!-- Flasher Render -->

    <div class="page">
        <!-- app-header -->
        @include('layouts.topbar')
        <!-- /app-header -->
        <!-- Start::app-sidebar -->
        @include('layouts.sidebar')
        <!-- End::app-sidebar -->

        <!-- Start::app-content -->
        <div class="main-content app-content">
            @yield('content')
        </div>
        <!-- End::app-content -->

        <!-- Footer Start -->
        @include('layouts.footer')
        <!-- Footer End -->

    </div>


    <div class="scrollToTop">
        <span class="arrow"><i class="ri-arrow-up-s-fill fs-20"></i></span>
    </div>
    <div id="responsive-overlay"></div>

    <!-- Chatbot Widget -->
    @include('partials.chatbot')

    @include('layouts.js')
    @yield('scripts')

</body>

</html>
