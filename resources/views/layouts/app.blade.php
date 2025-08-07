<?php

use App\Models\Setting;

$generalSettings = Setting::getMany(null, ['site_name', 'image_square_light']);
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light"
    data-menu-styles="dark" data-toggled="close">

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
