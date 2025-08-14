@php
    $themeSettings = \App\Models\ThemeSetting::getActiveTheme();
    $isRtl =
        (is_object($themeSettings) && $themeSettings->direction === 'rtl') || (!is_object($themeSettings) && false);
    $bootstrapCss = $isRtl ? 'bootstrap.rtl.min.css' : 'bootstrap.min.css';
@endphp
<link id="style" href="{{ asset('assets/libs/bootstrap/css/' . $bootstrapCss) }}" rel="stylesheet">

<link href="{{ asset('assets/css/styles.min.css') }}" rel="stylesheet">

<link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">

<link href="{{ asset('assets/libs/node-waves/waves.min.css') }}" rel="stylesheet">

<link href="{{ asset('assets/libs/simplebar/simplebar.min.css') }}" rel="stylesheet">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/nano.min.css" />

<link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">

<link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.css">

<link rel="stylesheet" href="{{ asset('vendor/flasher/flasher.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/glightbox/css/glightbox.min.css') }}">

@if (file_exists(public_path('assets/css/theme-backgrounds.css')))
    <link href="{{ asset('assets/css/theme-backgrounds.css') }}" rel="stylesheet">
@endif

<style>
    .fl-wrapper {
        z-index: 1050 !important;
    }
</style>
