<!-- Bootstrap Css - Dynamic based on theme direction -->
@php
    $themeSettings = \App\Models\ThemeSetting::getActiveTheme();
    $isRtl =
        (is_object($themeSettings) && $themeSettings->direction === 'rtl') || (!is_object($themeSettings) && false);
    $bootstrapCss = $isRtl ? 'bootstrap.rtl.min.css' : 'bootstrap.min.css';
@endphp
<link id="style" href="{{ asset('assets/libs/bootstrap/css/' . $bootstrapCss) }}" rel="stylesheet">

<!-- Style Css -->
<link href="{{ asset('assets/css/styles.min.css') }}" rel="stylesheet">

<!-- Icons Css -->
<link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">

<!-- Node Waves Css -->
<link href="{{ asset('assets/libs/node-waves/waves.min.css') }}" rel="stylesheet">

<!-- Simplebar Css -->
<link href="{{ asset('assets/libs/simplebar/simplebar.min.css') }}" rel="stylesheet">

<!-- Pickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/nano.min.css" />

<!-- Choices Css -->
<link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">

<link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.css">

<!-- Flasher CSS -->
<link rel="stylesheet" href="{{ asset('vendor/flasher/flasher.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/glightbox/css/glightbox.min.css') }}">

<!-- Theme Background Images CSS -->
@if (file_exists(public_path('assets/css/theme-backgrounds.css')))
    <link href="{{ asset('assets/css/theme-backgrounds.css') }}" rel="stylesheet">
@endif

<!-- Custom CSS for Flasher z-index fix -->
<style>
    /* Fixed: Updated comment to reflect z-index fix for flasher notifications */
    .fl-wrapper {
        z-index: 1050 !important;
        /* Ensure flasher appears above header (header z-index is 102) */
    }
</style>
