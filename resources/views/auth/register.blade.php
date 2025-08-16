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
        <title>Đăng Ký - {{ $generalSettings['site_name'] ?? '-' }}</title>
        <meta name="Description" content="Đăng Ký - {{ $generalSettings['site_name'] ?? '-' }}">
        <meta name="Author" content="YNEX">
        <meta name="keywords" content="Đăng Ký, {{ $generalSettings['site_name'] ?? '-' }}">

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
                <div class="col-xxl-6 col-xl-7 col-lg-8 col-md-10 col-sm-12 col-12">
                    <div class="card custom-card">
                        <div class="card-body p-5">
                            <div class="text-center">
                                @if ($club->image_horizontal_light)
                                    <img src="{{ Storage::url($club->image_horizontal_light) }}" width="50%"
                                        alt="Logo {{ $club->name }}">
                                @elseif($club->image_square_light)
                                    <img src="{{ Storage::url($club->image_square_light) }}" width="50%"
                                        alt="Logo {{ $club->name }}">
                                @else
                                    <img src="{{ Storage::url($generalSettings['image_horizontal_light'] ?? '') }}"
                                        width="50%" alt="Logo {{ $generalSettings['site_name'] ?? 'Site' }}">
                                @endif
                            </div>
                            <h5 class="text-center mt-4">Hệ Thống Quản Lý Câu Lạc Bộ
                            </h5>

                            <hr>

                            <h3 class="fw-semibold text-center">Đăng Ký Thành Viên</h3>
                            <h6 class=" text-center mb-4">
                                {{ $club->name }}
                            </h6>

                            @if (session('info'))
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    {{ session('info') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('register', $club->registration_token) }}">
                                @csrf
                                <div class="row gy-3">
                                    <!-- Thông tin cá nhân -->
                                    <div class="col-xl-6">
                                        <label for="name" class="form-label text-default">Họ và tên <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control form-control-lg @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name') }}"
                                            placeholder="Nhập họ và tên" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-xl-6">
                                        <label for="email" class="form-label text-default">Địa chỉ Email <span
                                                class="text-danger">*</span></label>
                                        <input type="email"
                                            class="form-control form-control-lg @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ old('email') }}"
                                            placeholder="Nhập địa chỉ email" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-xl-6">
                                        <label for="gender" class="form-label text-default">Giới tính <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select form-select-lg @error('gender') is-invalid @enderror"
                                            id="gender" name="gender" required>
                                            <option value="">Chọn giới tính</option>
                                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Nam
                                            </option>
                                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Nữ
                                            </option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-xl-6">
                                        <label for="birthday" class="form-label text-default">Ngày sinh <span
                                                class="text-danger">*</span></label>
                                        <input type="date"
                                            class="form-control form-control-lg @error('birthday') is-invalid @enderror"
                                            id="birthday" name="birthday" value="{{ old('birthday') }}" required>
                                        @error('birthday')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-xl-6">
                                        <label for="phone" class="form-label text-default">Số điện thoại <span
                                                class="text-danger">*</span></label>
                                        <input type="tel"
                                            class="form-control form-control-lg @error('phone') is-invalid @enderror"
                                            id="phone" name="phone" value="{{ old('phone') }}"
                                            placeholder="Nhập số điện thoại" required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-xl-6">
                                        <label for="student_code" class="form-label text-default">Mã sinh viên <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control form-control-lg @error('student_code') is-invalid @enderror"
                                            id="student_code" name="student_code" value="{{ old('student_code') }}"
                                            placeholder="Nhập mã sinh viên" required>
                                        @error('student_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-xl-6">
                                        <label for="major_id" class="form-label text-default">Chuyên ngành <span
                                                class="text-danger">*</span></label>
                                        <select
                                            class="form-select form-select-lg @error('major_id') is-invalid @enderror"
                                            id="major_id" name="major_id" required>
                                            <option value="">Chọn chuyên ngành</option>
                                            @foreach ($majors as $major)
                                                <option value="{{ $major->id }}"
                                                    {{ old('major_id') == $major->id ? 'selected' : '' }}>
                                                    {{ $major->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('major_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-xl-6">
                                        <label for="course_id" class="form-label text-default">Khóa nhập học <span
                                                class="text-danger">*</span></label>
                                        <select
                                            class="form-select form-select-lg @error('course_id') is-invalid @enderror"
                                            id="course_id" name="course_id" required>
                                            <option value="">Chọn khóa nhập học</option>
                                            @foreach ($courses as $course)
                                                <option value="{{ $course->id }}"
                                                    {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                    {{ $course->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('course_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-xl-6">
                                        <label for="password" class="form-label text-default">Mật khẩu <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password"
                                                class="form-control form-control-lg @error('password') is-invalid @enderror"
                                                id="password" name="password" placeholder="Nhập mật khẩu" required>
                                            <button class="btn btn-light" type="button"
                                                onclick="createpassword('password',this)" id="button-addon2"><i
                                                    class="ri-eye-off-line align-middle"></i></button>
                                        </div>
                                        <small class="text-muted">Mật khẩu phải có ít nhất 8 ký tự.</small>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-xl-6">
                                        <label for="password_confirmation" class="form-label text-default">Xác nhận
                                            mật khẩu <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password"
                                                class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror"
                                                id="password_confirmation" name="password_confirmation"
                                                placeholder="Xác nhận mật khẩu" required>
                                            <button class="btn btn-light" type="button"
                                                onclick="createpassword('password_confirmation',this)"
                                                id="button-addon2"><i
                                                    class="ri-eye-off-line align-middle"></i></button>
                                        </div>
                                        <small class="text-muted">Mật khẩu phải có ít nhất 8 ký tự.</small>
                                        @error('password_confirmation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-xl-12 d-grid mt-3">
                                        <button type="submit" class="btn btn-lg btn-primary">Đăng Ký</button>
                                    </div>
                                </div>
                            </form>

                            <div class="text-center">
                                <p class="fs-12 text-muted mt-3">Bạn đã đăng ký thành viên? <a
                                        href="{{ route('login') }}" class="text-primary">Đăng Nhập</a></p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

        <script src="{{ asset('assets/js/show-password.js') }}"></script>

    </body>

    </html>
