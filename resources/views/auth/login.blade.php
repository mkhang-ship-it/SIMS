<?php

use App\Models\Setting;

$generalSettings = Setting::getMany(null, ['image_horizontal_light', 'image_horizontal_dark', 'image_square_light', 'image_square_dark', 'site_name']);
$oauthSettings = Setting::getMany(null, ['github_oauth_enabled', 'github_client_id', 'github_client_secret']);
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-vertical-style="overlay" data-theme-mode="light"
    data-header-styles="light" data-menu-styles="light" data-toggled="close">

<head>

    <!-- Meta Data -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Đăng Nhập - {{ $generalSettings['site_name'] ?? '-' }}</title>
    <meta name="Description" content="Đăng Nhập - {{ $generalSettings['site_name'] ?? '-' }}">
    <meta name="Author" content="YNEX">
    <meta name="keywords" content="Đăng Nhập, {{ $generalSettings['site_name'] ?? '-' }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ Storage::url($generalSettings['image_square_light'] ?? '') }}" type="image/x-icon">

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
                                            id="signin-password" name="password" placeholder="Nhập mật khẩu" required>
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
                        <div class="text-center">
                            <p class="fs-12 text-muted mt-3">Không có tài khoản? <a href=""
                                    class="text-primary">Đăng Ký</a></p>
                        </div>
                        @if (
                            ($oauthSettings['github_oauth_enabled'] ?? '0') === '1' &&
                                !empty($oauthSettings['github_client_id']) &&
                                !empty($oauthSettings['github_client_secret']))
                            <div class="text-center my-3 authentication-barrier">
                                <span>HOẶC</span>
                            </div>
                            <!-- GitHub OAuth Login -->
                            <div class="text-center">
                                <a href="{{ route('auth.github') }}"
                                    class="d-block mb-2 mx-auto d-flex align-items-center justify-content-center"
                                    style="width: 220px; background-color: #24292f; color: #fff; border-radius: 6px; padding: 8px 12px; text-decoration: none; font-weight: 500; display: flex; align-items: center; justify-content: center; transition: background-color 0.2s;">
                                    <i class="ri-github-line fw-bold me-2" style="color: #fff; font-size: 18px;"></i>
                                    <span>Đăng nhập với GitHub</span>
                                </a>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Show Password JS -->
    <script src="{{ asset('assets/js/show-password.js') }}"></script>

</body>

</html>
