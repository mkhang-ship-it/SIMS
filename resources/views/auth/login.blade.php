<?php

use App\Models\Setting;
use App\Models\ThemeSetting;

$generalSettings = Setting::getMany(null, ['image_horizontal_light', 'image_horizontal_dark', 'image_square_light', 'image_square_dark', 'site_name']);
$oauthSettings = Setting::getMany(null, ['github_oauth_enabled', 'github_client_id', 'github_client_secret', 'microsoft_oauth_enabled', 'microsoft_client_id', 'microsoft_client_secret']);
$externalBgSettings = Setting::getExternalBackgroundSettings();

$themeSettings = ThemeSetting::getActiveTheme();
$htmlAttributes = [];
if (is_object($themeSettings) && method_exists($themeSettings, 'getHtmlAttributes')) {
    $htmlAttributes = $themeSettings->getHtmlAttributes();
} else {
    $htmlAttributes = [
        'lang' => 'en',
        'dir' => 'ltr',
        'data-nav-layout' => 'vertical',
        'data-vertical-style' => 'overlay',
        'data-theme-mode' => 'light',
        'data-header-styles' => 'light',
        'data-menu-styles' => 'light',
        'data-toggled' => 'close',
    ];
}

$htmlAttributesString = '';
foreach ($htmlAttributes as $key => $value) {
    $htmlAttributesString .= ' ' . $key . '="' . $value . '"';
}
?>
<!DOCTYPE html>
<html<?= $htmlAttributesString ?>>

    <head>

        <meta charset="UTF-8">
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Đăng Nhập - {{ $generalSettings['site_name'] ?? '-' }}</title>
        <meta name="Description" content="Đăng Nhập - {{ $generalSettings['site_name'] ?? '-' }}">
        <meta name="Author" content="YNEX">
        <meta name="keywords" content="Đăng Nhập, {{ $generalSettings['site_name'] ?? '-' }}">

        <link rel="icon" href="{{ Storage::url($generalSettings['image_square_light'] ?? '') }}"
            type="image/x-icon">

        <script src="{{ asset('assets/js/authentication-main.js') }}"></script>

        <link id="style" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

        <link href="{{ asset('assets/css/styles.min.css') }}" rel="stylesheet">

        <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet">

        <style>
            :root {
                @if (isset($themeSettings->primary_color) && $themeSettings->primary_color)
                    --primary-rgb: {{ $themeSettings->primary_color }};
                @else
                    --primary-rgb: 58, 88, 146;
                @endif
                --primary-color: rgb(var(--primary-rgb));
                --primary-border: rgb(var(--primary-rgb));
                --primary01: rgba(var(--primary-rgb), 0.1);
                --primary02: rgba(var(--primary-rgb), 0.2);
                --primary03: rgba(var(--primary-rgb), 0.3);
                --primary04: rgba(var(--primary-rgb), 0.4);
                --primary05: rgba(var(--primary-rgb), 0.5);
                --primary06: rgba(var(--primary-rgb), 0.6);
                --primary07: rgba(var(--primary-rgb), 0.7);
                --primary08: rgba(var(--primary-rgb), 0.8);
                --primary09: rgba(var(--primary-rgb), 0.9);
                --primary005: rgba(var(--primary-rgb), 0.05);
            }
        </style>

        @if (!empty($externalBgSettings['external_bg_image']))
            <style>
                /* Tầng nền cố định, hoạt động trên mọi thiết bị */
                body.has-background::before {
                    content: '';
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-image: url('{{ Storage::url($externalBgSettings['external_bg_image']) }}');
                    background-size: cover;
                    background-position: center;
                    background-repeat: no-repeat;
                    z-index: -2;
                }

                /* Lớp phủ (overlay) mờ phía trên ảnh nền */
                body.has-background::after {
                    content: '';
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: {{ $externalBgSettings['external_bg_overlay_color'] ?? '#000000' }};
                    opacity: {{ $externalBgSettings['external_bg_overlay_opacity'] ?? '0.5' }};
                    z-index: -1;
                }

                /* Lớp nội dung chính giữ ở trên cùng */
                .authentication {
                    position: relative;
                    z-index: 1;
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                }

                /* Hiệu ứng thẻ card nổi và làm mờ nền */
                .card {
                    background-color: rgba(255, 255, 255, 0.95);
                    backdrop-filter: blur(10px);
                    border: 1px solid rgba(255, 255, 255, 0.2);
                }

                /* Responsive cho thiết bị nhỏ */
                @media (max-width: 575.98px) {
                    .container {
                        padding-left: 15px;
                        padding-right: 15px;
                    }

                    .card-body {
                        padding: 2rem !important;
                    }
                }
            </style>
        @endif

    </head>

    <body class="has-background">

        <div class="container">
            <div class="row justify-content-center align-items-center authentication authentication-basic h-100">
                <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-6 col-sm-8 col-12">
                    <div class="card custom-card">
                        <div class="card-body p-5">
                            <div class="text-center">
                                <img src="{{ Storage::url($generalSettings['image_horizontal_light'] ?? '') }}"
                                    width="70%" alt="">
                            </div>
                            <p class="h5 fw-semibold mb-2 text-center">Đăng Nhập</p>
                            <p class="mb-4 text-muted op-7 fw-normal text-center">Hệ Thống Quản Lý Câu Lạc Bộ</p>

                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="row gy-3">
                                    <div class="col-xl-12">
                                        <label for="signin-email" class="form-label text-default">Địa chỉ Email</label>
                                        <input type="email"
                                            class="form-control form-control-lg @error('email') is-invalid @enderror"
                                            id="signin-email" name="email" value="{{ old('email') }}"
                                            placeholder="Nhập địa chỉ email" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-xl-12 mb-2">
                                        <label for="signin-password" class="form-label text-default d-block">Mật khẩu<a
                                                href="" class="float-end text-danger">Quên mật khẩu
                                                ?</a></label>
                                        <div class="input-group">
                                            <input type="password"
                                                class="form-control form-control-lg @error('password') is-invalid @enderror"
                                                id="signin-password" name="password" placeholder="Nhập mật khẩu"
                                                required>
                                            <button class="btn btn-light" type="button"
                                                onclick="createpassword('signin-password',this)" id="button-addon2"><i
                                                    class="ri-eye-off-line align-middle"></i></button>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-xl-12 d-grid mt-2">
                                        <button type="submit" class="btn btn-lg btn-primary">Đăng Nhập</button>
                                    </div>
                                </div>
                            </form>
                            @php
                                $githubEnabled =
                                    ($oauthSettings['github_oauth_enabled'] ?? '0') === '1' &&
                                    !empty($oauthSettings['github_client_id']) &&
                                    !empty($oauthSettings['github_client_secret']);
                                $microsoftEnabled =
                                    ($oauthSettings['microsoft_oauth_enabled'] ?? '0') === '1' &&
                                    !empty($oauthSettings['microsoft_client_id']) &&
                                    !empty($oauthSettings['microsoft_client_secret']);
                            @endphp

                            @if ($githubEnabled || $microsoftEnabled)
                                <div class="text-center my-3 authentication-barrier">
                                    <span>HOẶC</span>
                                </div>
                                <div class="text-center">

                                    @if ($githubEnabled)
                                        <a href="{{ route('auth.github') }}" class="d-block mb-2 mx-auto"
                                            style="width: 240px;
               background-color: #24292f;
               color: #ffffff;
               border-radius: 6px;
               padding: 6px 12px;
               text-decoration: none;
               font-weight: 500;
               display: flex;
               align-items: center;
               border: none;
               font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif;
               transition: background-color 0.2s;">

                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 16 16" fill="#ffffff" style="margin-right: 8px;">
                                                <path fill-rule="evenodd" d="M8 0C3.58 0 0 3.58 0 8c0 3.54
                2.29 6.53 5.47 7.59.4.07.55-.17.55-.38
                0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52
                0-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87
                2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95
                0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12
                0 0 .67-.21 2.2.82.64-.18 1.32-.27
                2-.27.68 0 1.36.09 2 .27 1.53-1.04
                2.2-.82 2.2-.82.44 1.1.16 1.92.08
                2.12.51.56.82 1.27.82 2.15
                0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54
                1.48 0 1.07-.01 1.93-.01 2.2
                0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z">
                                                </path>
                                            </svg>

                                            <span>Đăng nhập với GitHub</span>
                                        </a>
                                    @endif


                                    @if ($microsoftEnabled)
                                        <a href="{{ route('auth.microsoft') }}" class="d-block mb-2 mx-auto"
                                            style="width: 240px;
               background-color: #ffffff;
               color: #000000;
               border-radius: 4px;
               padding: 6px 12px;
               text-decoration: none;
               font-weight: 500;
               display: flex;
               align-items: center;
               border: 1px solid #8C8C8C;
               font-family: 'Segoe UI', sans-serif;
               transition: background-color 0.2s;">

                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                viewBox="0 0 23 23" style="margin-right: 8px;">
                                                <rect width="10" height="10" x="0" y="0" fill="#F35325" />
                                                <rect width="10" height="10" x="12" y="0" fill="#81BC06" />
                                                <rect width="10" height="10" x="0" y="12" fill="#05A6F0" />
                                                <rect width="10" height="10" x="12" y="12" fill="#FFBA08" />
                                            </svg>

                                            <span>Đăng nhập với Microsoft</span>
                                        </a>
                                    @endif

                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

        <script src="{{ asset('assets/js/show-password.js') }}"></script>

    </body>

    </html>
