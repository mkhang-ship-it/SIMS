<?php

use App\Models\Setting;
use App\Models\ThemeSetting;

$generalSettings = Setting::getMany(null, ['site_name', 'image_square_light']);
$themeSettings = ThemeSetting::getActiveTheme();

$htmlAttributes = [];
if (is_object($themeSettings) && method_exists($themeSettings, 'getHtmlAttributes')) {
    $htmlAttributes = $themeSettings->getHtmlAttributes();
} else {
    $htmlAttributes = [
        'data-nav-layout' => 'vertical',
        'data-theme-mode' => 'light',
        'data-header-styles' => 'light',
        'data-menu-styles' => 'dark',
        'data-toggled' => 'close',
        'dir' => 'ltr',
    ];
}

$htmlAttributesString = '';
foreach ($htmlAttributes as $key => $value) {
    $htmlAttributesString .= ' ' . $key . '="' . $value . '"';
}

$cssProperties = [];
if (is_object($themeSettings) && method_exists($themeSettings, 'getCssProperties')) {
    $cssProperties = $themeSettings->getCssProperties();
}

$cssStyleString = '';
foreach ($cssProperties as $property => $value) {
    $cssStyleString .= $property . ': ' . $value . '; ';
}
?>
<!DOCTYPE html>
<html lang="en"<?php echo $htmlAttributesString; ?><?php echo $cssStyleString ? ' style="' . trim($cssStyleString) . '"' : ''; ?>>

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> {{ $generalSettings['site_name'] ?? '-' }} </title>
    <meta name="Description" content="{{ $generalSettings['site_name'] ?? '-' }}">
    <meta name="keywords" content="{{ $generalSettings['site_name'] ?? '-' }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon"
        href="{{ !empty($generalSettings['image_square_light']) ? Storage::url($generalSettings['image_square_light']) : asset('favicon.ico') }}"
        type="image/x-icon">

    @include('layouts.css')

    <script>
        function isIOS() {
            return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        }
        // Khi modal đã mở
        document.addEventListener('shown.bs.modal', function() {
            if (isIOS()) {
                // Gỡ overflow: hidden mà Bootstrap tự thêm
                document.body.style.overflow = 'auto';

                // Nếu modal không hiển thị đúng do stacking
                const modal = document.querySelector('.modal.show');
                if (modal) {
                    modal.style.position = 'absolute';
                    modal.style.zIndex = 1050;
                }

                // Optional: fix scroll chạm
                document.addEventListener('touchstart', function() {}, true);
            }
        });
    </script>

</head>

<body>

    <div class="page">
        @include('layouts.topbar')
        @include('layouts.sidebar')

        <div class="main-content app-content">
            @yield('content')
        </div>

        @include('layouts.footer')

    </div>

    <div class="scrollToTop">
        <span class="arrow"><i class="ri-arrow-up-s-fill fs-20"></i></span>
    </div>
    <div id="responsive-overlay"></div>

    @include('layouts.js')
    @yield('scripts')

</body>

</html>
