<?php
// Created: 504 Gateway Timeout error page based on 403 template

use App\Models\Setting;

$generalSettings = Setting::getMany(null, ['site_name', 'image_square_light']);
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-vertical-style="overlay" data-theme-mode="light"
    data-header-styles="light" data-menu-styles="light" data-toggled="close">

<head>

    <!-- Meta Data -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> {{ $generalSettings['site_name'] ?? '-' }} </title>
    <meta name="Description" content="{{ $generalSettings['site_name'] ?? '-' }}">

    <!-- Favicon -->
    <link rel="icon"
        href="{{ !empty($generalSettings['image_square_light']) ? Storage::url($generalSettings['image_square_light']) : asset('favicon.ico') }}"
        type="image/x-icon">

    <!-- Main Theme Js -->
    <script src="{{ asset('assets/js/authentication-main.js') }}"></script>

    <!-- Bootstrap Css -->
    <link id="style" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Style Css -->
    <link href="{{ asset('assets/css/styles.min.css') }}" rel="stylesheet">

    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet">


</head>

<body>

    <div class="page error-bg" id="particles-js">
        <!-- Start::error-page -->
        <div class="error-page">
            <div class="container">
                <div class="text-center p-5 my-auto">
                    <div class="row align-items-center justify-content-center h-100">
                        <div class="col-xl-7">
                            <p class="error-text mb-sm-0 mb-2">504</p>
                            <p class="fs-18 fw-semibold mb-3">Gateway Timeout
                            </p>
                            <div class="row justify-content-center mb-5">
                                <div class="col-xl-6">
                                    <p class="mb-0 op-7">Máy chủ đã hết thời gian chờ khi xử lý yêu cầu của bạn. Vui
                                        lòng thử lại sau.</p>
                                </div>
                            </div>
                            <a href="{{ session('club_id') ? route('backend.club.dashboard') : route('backend.dashboard') }}"
                                class="btn btn-primary">
                                <i class="ri-arrow-left-line align-middle me-1 d-inline-block"></i>Quay lại Trang Chủ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Custom-Switcher JS -->
    <script src="{{ asset('assets/js/custom-switcher.min.js') }}"></script>

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Particles JS -->
    <script src="{{ asset('assets/libs/particles.js/particles.js') }}"></script>

    <!-- Error JS -->
    <script src="{{ asset('assets/js/error.js') }}"></script>

</body>

</html>
