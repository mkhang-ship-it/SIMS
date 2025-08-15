<!DOCTYPE html>
<html lang="vi" dir="ltr" data-nav-layout="horizontal" data-nav-style="menu-click" data-menu-position="fixed"
    data-theme-mode="light">

<head>

    <!-- Meta Data -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Hệ thống quản lý câu lạc bộ - BTEC FPT</title>
    <meta name="Description"
        content="Hệ thống quản lý câu lạc bộ thuộc phòng công tác sinh viên, trường cao đẳng anh quốc BTEC FPT">
    <meta name="Author" content="BTEC FPT">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/brand-logos/favicon.ico') }}" type="image/x-icon">

    <!-- Bootstrap Css -->
    <link id="style" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Style Css -->
    <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet">

    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">

    <!-- Node Waves Css -->
    <link href="{{ asset('assets/libs/node-waves/waves.min.css') }}" rel="stylesheet">

    <!-- SwiperJS Css -->
    <link rel="stylesheet" href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}">

    <!-- Color Picker Css -->
    <link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/@simonwep/pickr/themes/nano.min.css') }}">

    <!-- Choices Css -->
    <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">

    <script>
        if (localStorage.ynexlandingdarktheme) {
            document.querySelector("html").setAttribute("data-theme-mode", "dark")
        }
        if (localStorage.ynexlandingrtl) {
            document.querySelector("html").setAttribute("dir", "rtl")
            document.querySelector("#style")?.setAttribute("href", "../assets/libs/bootstrap/css/bootstrap.rtl.min.css");
        }
    </script>


</head>

<body class="landing-body">

    <div class="landing-page-wrapper">

        <!-- app-header -->
        <header class="app-header">

            <!-- Start::main-header-container -->
            <div class="main-header-container container-fluid">

                <!-- Start::header-content-left -->
                <div class="header-content-left">

                    <!-- Start::header-element -->
                    <div class="header-element">
                        <div class="horizontal-logo">
                            <a href="#" class="header-logo">
                                <img src="{{ asset('assets/images/brand-logos/toggle-logo.png') }}" alt="BTEC FPT Logo"
                                    class="toggle-logo">
                                <img src="{{ asset('assets/images/brand-logos/toggle-dark.png') }}" alt="BTEC FPT Logo"
                                    class="toggle-dark">
                            </a>
                        </div>
                    </div>
                    <!-- End::header-element -->

                    <!-- Start::header-element -->
                    <div class="header-element">
                        <!-- Start::header-link -->
                        <a href="javascript:void(0);" class="sidemenu-toggle header-link" data-bs-toggle="sidebar">
                            <span class="open-toggle">
                                <i class="ri-menu-3-line fs-20"></i>
                            </span>
                        </a>
                        <!-- End::header-link -->
                    </div>
                    <!-- End::header-element -->

                </div>
                <!-- End::header-content-left -->

                <!-- Start::header-content-right -->
                <div class="header-content-right">

                    <!-- Start::header-element -->
                    <div class="header-element align-items-center">
                        <!-- Start::header-link|switcher-icon -->
                        <div class="btn-list d-lg-none d-block">
                            <a href="#" class="btn btn-primary-light">
                                Đăng nhập
                            </a>
                            <button class="btn btn-icon btn-success" data-bs-toggle="offcanvas"
                                data-bs-target="#switcher-canvas">
                                <i class="ri-settings-3-line"></i>
                            </button>
                        </div>
                        <!-- End::header-link|switcher-icon -->
                    </div>
                    <!-- End::header-element -->

                </div>
                <!-- End::header-content-right -->

            </div>
            <!-- End::main-header-container -->

        </header>
        <!-- /app-header -->

        <!-- Start::app-sidebar -->
        <aside class="app-sidebar sticky" id="sidebar">

            <div class="container p-0">
                <!-- Start::main-sidebar -->
                <div class="main-sidebar">

                    <!-- Start::nav -->
                    <nav class="main-menu-container nav nav-pills sub-open">
                        <div class="landing-logo-container">
                            <div class="horizontal-logo">
                                <a href="#" class="header-logo">
                                    <img src="../assets/images/brand-logos/desktop-logo.png" alt="BTEC FPT Logo"
                                        class="desktop-logo">
                                    <img src="../assets/images/brand-logos/desktop-white.png" alt="BTEC FPT Logo"
                                        class="desktop-white">
                                </a>
                            </div>
                        </div>
                        <div class="slide-left" id="slide-left"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191"
                                width="24" height="24" viewBox="0 0 24 24">
                                <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
                            </svg></div>
                        <ul class="main-menu">
                            <!-- Start::slide -->
                            <li class="slide">
                                <a class="side-menu__item" href="#home">
                                    <span class="side-menu__label">Trang chủ</span>
                                </a>
                            </li>
                            <!-- End::slide -->
                            <!-- Start::slide -->
                            <li class="slide">
                                <a href="#about" class="side-menu__item">
                                    <span class="side-menu__label">Giới thiệu</span>
                                </a>
                            </li>
                            <!-- End::slide -->
                            <!-- Start::slide -->
                            <li class="slide">
                                <a href="#features" class="side-menu__item">
                                    <span class="side-menu__label">Tính năng</span>
                                </a>
                            </li>
                            <!-- End::slide -->
                            <!-- Start::slide -->
                            <li class="slide">
                                <a href="#contact" class="side-menu__item">
                                    <span class="side-menu__label">Liên hệ</span>
                                </a>
                            </li>
                            <!-- End::slide -->

                        </ul>
                        <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg"
                                fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                                <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z">
                                </path>
                            </svg></div>
                        <div class="d-lg-flex d-none">
                            <div class="btn-list d-lg-flex d-none mt-lg-2 mt-xl-0 mt-0">
                                <a href="#" class="btn btn-wave btn-primary">
                                    Đăng nhập
                                </a>
                            </div>
                        </div>
                    </nav>
                    <!-- End::nav -->

                </div>
                <!-- End::main-sidebar -->
            </div>

        </aside>
        <!-- End::app-sidebar -->

        <!-- Start::app-content -->
        <div class="main-content landing-main">

            <!-- Start:: Section-1 -->
            <div class="landing-banner" id="home">
                <section class="section">
                    <div class="container main-banner-container">
                        <div class="row">
                            <div class="col-xxl-7 col-xl-7 col-lg-7 col-md-8">
                                <div class="py-lg-5">
                                    <div class="mb-3">
                                        <h5 class="fw-semibold text-fixed-white op-9">PHÒNG CÔNG TÁC SINH VIÊN</h5>
                                    </div>
                                    <p class="landing-banner-heading mb-3">Hệ thống quản lý câu lạc bộ <span
                                            class="text-secondary">BTEC FPT</span></p>
                                    <div class="fs-16 mb-5 text-fixed-white op-7">Giải pháp toàn diện cho việc quản lý
                                        hoạt động câu lạc bộ, sự kiện và sinh viên tại trường cao đẳng anh quốc BTEC
                                        FPT.</div>
                                    <a href="#" class="m-1 btn btn-primary">
                                        Khám phá ngay
                                        <i class="ri-eye-line ms-2 align-middle"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-xxl-5 col-xl-5 col-lg-5 col-md-4">
                                <div class="text-end landing-main-image landing-heading-img">
                                    <img src="../assets/images/media/landing/1.png" alt="Club Management System"
                                        class="img-fluid">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <!-- End:: Section-1 -->

            <!-- Start:: Section-2 -->
            <section class="section section-bg " id="statistics">
                <div class="container text-center position-relative">
                    <p class="fs-12 fw-semibold text-success mb-1"><span class="landing-section-heading">THỐNG
                            KÊ</span></p>
                    <h3 class="fw-semibold mb-2">Hơn 50+ câu lạc bộ đang hoạt động</h3>
                    <div class="row justify-content-center">
                        <div class="col-xl-7">
                            <p class="text-muted fs-15 mb-5 fw-normal">Hệ thống đang phục vụ hàng nghìn sinh viên và
                                cán bộ tại 4 cơ sở của BTEC FPT.</p>
                        </div>
                    </div>
                    <div class="row  g-2 justify-content-center">
                        <div class="col-xl-12">
                            <div class="row justify-content-evenly">
                                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 col-12 mb-3">
                                    <div class="p-3 text-center rounded-2 bg-white border">
                                        <span class="mb-3 avatar avatar-lg avatar-rounded bg-primary-transparent">
                                            <i class='fs-24 bx bx-group'></i>
                                        </span>
                                        <h3 class="fw-semibold mb-0 text-dark">50+</h3>
                                        <p class="mb-1 fs-14 op-7 text-muted ">
                                            Câu lạc bộ
                                        </p>
                                    </div>
                                </div>
                                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 col-12 mb-3">
                                    <div class="p-3 text-center rounded-2 bg-white border">
                                        <span class="mb-3 avatar avatar-lg avatar-rounded bg-primary-transparent">
                                            <i class='fs-24 bx bx-user-plus'></i>
                                        </span>
                                        <h3 class="fw-semibold mb-0 text-dark">5000+</h3>
                                        <p class="mb-1 fs-14 op-7 text-muted ">
                                            Sinh viên
                                        </p>
                                    </div>
                                </div>
                                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 col-12 mb-3">
                                    <div class="p-3 text-center rounded-2 bg-white border">
                                        <span class="mb-3 avatar avatar-lg avatar-rounded bg-primary-transparent">
                                            <i class='fs-24 bx bx-calendar-event'></i>
                                        </span>
                                        <h3 class="fw-semibold mb-0 text-dark">200+</h3>
                                        <p class="mb-1 fs-14 op-7 text-muted ">
                                            Sự kiện/năm
                                        </p>
                                    </div>
                                </div>
                                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 col-12 mb-3">
                                    <div class="p-3 text-center rounded-2 bg-white border">
                                        <span class="mb-3 avatar avatar-lg avatar-rounded bg-primary-transparent">
                                            <i class='bx bx-building-house'></i>
                                        </span>
                                        <h3 class="fw-semibold mb-0 text-dark">4</h3>
                                        <p class="mb-1 fs-14 op-7 text-muted ">
                                            Cơ sở
                                        </p>
                                    </div>
                                </div>
                                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 col-12 mb-3">
                                    <div class="p-3 text-center rounded-2 bg-white border">
                                        <span class="mb-3 avatar avatar-lg avatar-rounded bg-primary-transparent">
                                            <i class='bx bx-calendar'></i>
                                        </span>
                                        <h3 class="fw-semibold mb-0 text-dark">10+</h3>
                                        <p class="mb-1 fs-14 op-7 text-muted ">
                                            Năm kinh nghiệm
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>
            <!-- End:: Section-2 -->

            <!-- Start:: Section-3 -->
            <section class="section " id="about">
                <div class="container text-center">
                    <p class="fs-12 fw-semibold text-success mb-1"><span class="landing-section-heading">GIỚI
                            THIỆU</span></p>
                    <h3 class="fw-semibold mb-2">Hệ thống quản lý câu lạc bộ chuyên nghiệp</h3>
                    <div class="row justify-content-center">
                        <div class="col-xl-7">
                            <p class="text-muted fs-15 mb-3 fw-normal">Được thiết kế đặc biệt cho phòng công tác sinh
                                viên BTEC FPT, giúp quản lý hiệu quả các hoạt động câu lạc bộ và sự kiện.</p>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center align-items-center">
                    <div class="col-xxl-5 col-xl-5 col-lg-5 customize-image text-center">
                        <div class="text-lg-end">
                            <img src="../assets/images/media/landing/3.png" alt="Club Management Features"
                                class="img-fluid">
                        </div>
                    </div>
                    <div class="col-xxl-5 col-xl-5 col-lg-5 pt-5 pb-0 px-lg-2 px-5 text-start">
                        <h5 class="text-lg-start fw-semibold mb-0">Quản lý toàn diện câu lạc bộ</h5>
                        <p class=" text-muted">Hệ thống cung cấp các công cụ cần thiết để quản lý thành viên, sự kiện,
                            tài chính và báo cáo một cách hiệu quả.</p>
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="d-flex">
                                    <span>
                                        <i class='bx bxs-badge-check text-primary fs-18'></i>
                                    </span>
                                    <div class="ms-2">
                                        <h6 class="fw-semibold mb-0">Quản lý thành viên và quyền truy cập</h6>
                                        <p class=" text-muted">Hỗ trợ đăng ký thành viên, phân quyền và quản lý thông
                                            tin cá nhân một cách an toàn</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-12">
                                <div class="d-flex">
                                    <span>
                                        <i class='bx bxs-badge-check text-primary fs-18'></i>
                                    </span>
                                    <div class="ms-2">
                                        <h6 class="fw-semibold mb-0">Lập kế hoạch và tổ chức sự kiện</h6>
                                        <p class=" text-muted">Công cụ lập kế hoạch, đăng ký tham gia và theo dõi tiến
                                            độ sự kiện</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-12">
                                <div class="d-flex">
                                    <span>
                                        <i class='bx bxs-badge-check text-primary fs-18'></i>
                                    </span>
                                    <div class="ms-2">
                                        <h6 class="fw-semibold mb-0">Báo cáo và thống kê chi tiết</h6>
                                        <p class=" text-muted">Tạo báo cáo tự động, thống kê hoạt động và đánh giá hiệu
                                            quả câu lạc bộ</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- End:: Section-3 -->

            <!-- Start:: Section-4 -->
            <section class="section section-bg " id="features">
                <div class="container text-center">
                    <p class="fs-12 fw-semibold text-success mb-1"><span class="landing-section-heading">TÍNH
                            NĂNG</span></p>
                    <h2 class="fw-semibold mb-2">Các tính năng chính của hệ thống</h2>
                    <div class="row justify-content-center mb-5">
                        <div class="col-xl-7">
                            <p class="text-muted fs-15 mb-5 fw-normal">Hệ thống được thiết kế với giao diện thân thiện
                                và dễ sử dụng, phù hợp với nhu cầu của sinh viên và cán bộ.</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card custom-card text-start landing-missions">
                                <div class="card-body">
                                    <div class="align-items-top">
                                        <div class="mb-2">
                                            <span class="avatar avatar-lg avatar-rounded bg-primary-transparent">
                                                <i class='bx bx-group fs-25'></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="fw-semibold mb-1">
                                                Quản lý thành viên
                                            </h6>
                                            <p class="mb-0 text-muted">Đăng ký, quản lý thông tin và phân quyền thành
                                                viên câu lạc bộ một cách hiệu quả</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card custom-card text-start landing-missions">
                                <div class="card-body">
                                    <div class="align-items-top">
                                        <div class="mb-2">
                                            <span class="avatar avatar-lg avatar-rounded bg-primary-transparent">
                                                <i class='bx bx-calendar-event fs-25'></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="fw-semibold mb-1">
                                                Quản lý sự kiện
                                            </h6>
                                            <p class="mb-0 text-muted">Lập kế hoạch, tổ chức và theo dõi các hoạt động,
                                                sự kiện của câu lạc bộ</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card custom-card text-start landing-missions">
                                <div class="card-body">
                                    <div class="align-items-top">
                                        <div class="mb-2">
                                            <span class="avatar avatar-lg avatar-rounded  bg-primary-transparent">
                                                <i class='bx bx-money fs-25'></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="fw-semibold mb-1">
                                                Quản lý tài chính
                                            </h6>
                                            <p class="mb-0 text-muted">Theo dõi thu chi, ngân sách và báo cáo tài chính
                                                của câu lạc bộ</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card custom-card text-start landing-missions">
                                <div class="card-body">
                                    <div class="align-items-top">
                                        <div class="mb-2">
                                            <span class="avatar avatar-lg avatar-rounded bg-primary-transparent">
                                                <i class='bx bx-bar-chart-alt-2 fs-25'></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="fw-semibold mb-1">
                                                Báo cáo & Thống kê
                                            </h6>
                                            <p class="mb-0 text-muted">Tạo báo cáo tự động và thống kê chi tiết về hoạt
                                                động câu lạc bộ</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card custom-card text-start landing-missions">
                                <div class="card-body">
                                    <div class="align-items-top">
                                        <div class="mb-2">
                                            <span class="avatar avatar-lg avatar-rounded bg-primary-transparent">
                                                <i class='bx bx-mobile-alt fs-25'></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="fw-semibold mb-1">
                                                Giao diện responsive
                                            </h6>
                                            <p class="mb-0 text-muted">Hỗ trợ đầy đủ trên các thiết bị di động và máy
                                                tính</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card custom-card text-start landing-missions">
                                <div class="card-body">
                                    <div class="align-items-top">
                                        <div class="mb-2">
                                            <span class="avatar avatar-lg avatar-rounded bg-primary-transparent">
                                                <i class='bx bx-shield-check fs-25'></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="fw-semibold mb-1">
                                                Bảo mật cao
                                            </h6>
                                            <p class="mb-0 text-muted">Hệ thống bảo mật đa lớp, đảm bảo an toàn thông
                                                tin</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card custom-card text-start landing-missions">
                                <div class="card-body">
                                    <div class="align-items-top">
                                        <div class="mb-2">
                                            <span class="avatar avatar-lg avatar-rounded bg-primary-transparent">
                                                <i class='bx bx-sync fs-25'></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="fw-semibold mb-1">
                                                Đồng bộ thời gian thực
                                            </h6>
                                            <p class="mb-0 text-muted">Cập nhật thông tin và thông báo theo thời gian
                                                thực</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card custom-card text-start landing-missions">
                                <div class="card-body">
                                    <div class="align-items-top">
                                        <div class="mb-2">
                                            <span class="avatar avatar-lg avatar-rounded bg-primary-transparent">
                                                <i class='bx bx-support fs-25'></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="fw-semibold mb-1">
                                                Hỗ trợ 24/7
                                            </h6>
                                            <p class="mb-0 text-muted">Đội ngũ hỗ trợ kỹ thuật sẵn sàng hỗ trợ mọi lúc
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- End:: Section-4 -->

            <!-- Start:: Section-11 -->
            <section class="section landing-footer text-fixed-white" id="contact">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-4">
                            <div class="px-4">
                                <p class="fw-semibold mb-3"><a href="#"><img
                                            src="../assets/images/brand-logos/desktop-dark.png"
                                            alt="BTEC FPT Logo"></a>
                                </p>
                                <p class="mb-2 op-6 fw-normal">
                                    Hệ thống quản lý câu lạc bộ thuộc phòng công tác sinh viên, trường cao đẳng anh quốc
                                    BTEC FPT. Giải pháp toàn diện cho việc quản lý hoạt động câu lạc bộ và sự kiện.
                                </p>
                                <p class="mb-0 op-6 fw-normal">Được thiết kế đặc biệt để phục vụ sinh viên và cán bộ
                                    tại 4 cơ sở của BTEC FPT.</p>
                            </div>
                        </div>
                        <div class="col-xl-2">
                            <div class="px-4">
                                <h6 class="fw-semibold mb-3">TRANG</h6>
                                <ul class="list-unstyled op-6 fw-normal landing-footer-list">
                                    <li>
                                        <a href="#home" class="text-fixed-white">Trang chủ</a>
                                    </li>
                                    <li>
                                        <a href="#about" class="text-fixed-white">Giới thiệu</a>
                                    </li>
                                    <li>
                                        <a href="#features" class="text-fixed-white">Tính năng</a>
                                    </li>
                                    <li>
                                        <a href="#contact" class="text-fixed-white">Liên hệ</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-xl-2">
                            <div class="px-4">
                                <h6 class="fw-semibold">THÔNG TIN</h6>
                                <ul class="list-unstyled op-6 fw-normal landing-footer-list">
                                    <li>
                                        <a href="#" class="text-fixed-white">Về chúng tôi</a>
                                    </li>
                                    <li>
                                        <a href="#" class="text-fixed-white">Liên hệ</a>
                                    </li>
                                    <li>
                                        <a href="#" class="text-fixed-white">Dịch vụ</a>
                                    </li>
                                    <li>
                                        <a href="#" class="text-fixed-white">Điều khoản</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="px-4">
                                <h6 class="fw-semibold">LIÊN HỆ</h6>
                                <ul class="list-unstyled fw-normal landing-footer-list">
                                    <li>
                                        <a href="javascript:void(0);" class="text-fixed-white op-6"><i
                                                class="ri-home-4-line me-1 align-middle"></i> 4 cơ sở trên toàn
                                            quốc</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="text-fixed-white op-6"><i
                                                class="ri-mail-line me-1 align-middle"></i> info@btec.fpt.edu.vn</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="text-fixed-white op-6"><i
                                                class="ri-phone-line me-1 align-middle"></i> 1900 636 636</a>
                                    </li>
                                    <li class="mt-3">
                                        <p class="mb-2 fw-semibold op-8">THEO DÕI CHÚNG TÔI:</p>
                                        <div class="mb-0">
                                            <div class="btn-list">
                                                <button
                                                    class="btn btn-sm btn-icon btn-primary-light btn-wave waves-effect waves-light">
                                                    <i class="ri-facebook-line fw-bold"></i>
                                                </button>
                                                <button
                                                    class="btn btn-sm btn-icon btn-secondary-light btn-wave waves-effect waves-light">
                                                    <i class="ri-twitter-line fw-bold"></i>
                                                </button>
                                                <button
                                                    class="btn btn-sm btn-icon btn-warning-light btn-wave waves-effect waves-light">
                                                    <i class="ri-instagram-line fw-bold"></i>
                                                </button>
                                                <button
                                                    class="btn btn-sm btn-icon btn-success-light btn-wave waves-effect waves-light">
                                                    <i class="ri-youtube-line fw-bold"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- End:: Section-11 -->

            <!-- Start:: Section-12 - Campus Information -->
            <section class="section section-bg">
                <div class="container">
                    <div class="text-center mb-5">
                        <p class="fs-12 fw-semibold text-success mb-1"><span class="landing-section-heading">CÁC CƠ
                                SỞ</span></p>
                        <h3 class="fw-semibold mb-2">BTEC FPT tại 4 thành phố lớn</h3>
                        <p class="text-muted fs-15 mb-0">Hệ thống phục vụ sinh viên tại tất cả các cơ sở của BTEC FPT
                        </p>
                    </div>
                    <div class="row g-4">
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <div class="card custom-card text-center h-100">
                                <div class="card-body p-4">
                                    <div class="mb-3">
                                        <span class="avatar avatar-lg avatar-rounded bg-primary-transparent">
                                            <i class='bx bx-building-house fs-25'></i>
                                        </span>
                                    </div>
                                    <h5 class="fw-semibold mb-2">BTEC FPT Hà Nội</h5>
                                    <p class="text-muted mb-2">Toà BTEC FPT, Trịnh Văn Bô, Nam Từ Liêm, TP. Hà Nội</p>
                                    <p class="text-primary fw-semibold mb-0">
                                        <i class="ri-phone-line me-1"></i>098 109 05 13
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <div class="card custom-card text-center h-100">
                                <div class="card-body p-4">
                                    <div class="mb-3">
                                        <span class="avatar avatar-lg avatar-rounded bg-success-transparent">
                                            <i class='bx bx-building-house fs-25'></i>
                                        </span>
                                    </div>
                                    <h5 class="fw-semibold mb-2">BTEC FPT Đà Nẵng</h5>
                                    <p class="text-muted mb-2">66 Võ Văn Tần, Q. Thanh Khê, TP. Đà Nẵng</p>
                                    <p class="text-success fw-semibold mb-0">
                                        <i class="ri-phone-line me-1"></i>032 757 57 27
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <div class="card custom-card text-center h-100">
                                <div class="card-body p-4">
                                    <div class="mb-3">
                                        <span class="avatar avatar-lg avatar-rounded bg-warning-transparent">
                                            <i class='bx bx-building-house fs-25'></i>
                                        </span>
                                    </div>
                                    <h5 class="fw-semibold mb-2">BTEC FPT Hồ Chí Minh</h5>
                                    <p class="text-muted mb-2">Tòa Genpacific - Công viên phần mềm Quang Trung, Phường
                                        Trung Mỹ Tây, Q. 12, TP. Hồ Chí Minh</p>
                                    <p class="text-warning fw-semibold mb-0">
                                        <i class="ri-phone-line me-1"></i>035 385 21 38
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <div class="card custom-card text-center h-100">
                                <div class="card-body p-4">
                                    <div class="mb-3">
                                        <span class="avatar avatar-lg avatar-rounded bg-info-transparent">
                                            <i class='bx bx-building-house fs-25'></i>
                                        </span>
                                    </div>
                                    <h5 class="fw-semibold mb-2">BTEC FPT Cần Thơ</h5>
                                    <p class="text-muted mb-2">Đường số 22, Phường Thường Thạnh, Quận Cái Răng, TP. Cần
                                        Thơ</p>
                                    <p class="text-info fw-semibold mb-0">
                                        <i class="ri-phone-line me-1"></i>096 705 76 05
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- End:: Section-12 -->

            <div class="text-center landing-main-footer py-3">
                <span class="text-muted fs-15"> Copyright © <span id="year"></span> <a
                        href="javascript:void(0);" class="text-primary fw-semibold"><u>BTEC FPT</u></a>.
                    Hệ thống quản lý câu lạc bộ - Phòng công tác sinh viên. Tất cả quyền được bảo lưu.
                </span>
            </div>

        </div>
        <!-- End::app-content -->

    </div>

    <div class="scrollToTop">
        <span class="arrow"><i class="ri-arrow-up-s-fill fs-20"></i></span>
    </div>
    <div id="responsive-overlay"></div>

    <!-- Popper JS -->
    <script src="{{ asset('assets/libs/@popperjs/core/umd/popper.min.js') }}"></script>

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Color Picker JS -->
    <script src="{{ asset('assets/libs/@simonwep/pickr/pickr.es5.min.js') }}"></script>

    <!-- Choices JS -->
    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>

    <!-- Swiper JS -->
    <script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>

    <!-- Defaultmenu JS -->
    <script src="{{ asset('assets/js/defaultmenu.min.js') }}"></script>

    <!-- Internal Landing JS -->
    <script src="{{ asset('assets/js/landing.js') }}"></script>

    <!-- Node Waves JS-->
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>

    <!-- Sticky JS -->
    <script src="{{ asset('assets/js/sticky.js') }}"></script>

</body>

</html>
