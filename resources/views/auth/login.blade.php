<?php

use App\Models\Setting;

$generalSettings = Setting::getMany(null, ['image_horizontal_light', 'image_horizontal_dark', 'image_square_light', 'image_square_dark', 'site_name']);
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
                <div class="my-5 d-flex justify-content-center">
                    <a href="{{ url('/') }}">
                        <img src="{{ Storage::url($generalSettings['image_horizontal_light'] ?? '') }}" alt="logo"
                            class="desktop-logo">
                        <img src="{{ Storage::url($generalSettings['image_horizontal_dark'] ?? '') }}" alt="logo"
                            class="desktop-dark">
                    </a>
                </div>
                <div class="card custom-card">
                    <div class="card-body p-5">
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
                        <div class="text-center my-3 authentication-barrier">
                            <span>OR</span>
                        </div>
                        <div class="btn-list text-center">
                            <button class="btn btn-icon btn-light">
                                <i class="ri-facebook-line fw-bold text-dark op-7"></i>
                            </button>
                            <button class="btn btn-icon btn-light">
                                <i class="ri-google-line fw-bold text-dark op-7"></i>
                            </button>
                            <button class="btn btn-icon btn-light">
                                <i class="ri-twitter-line fw-bold text-dark op-7"></i>
                            </button>
                        </div>
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
