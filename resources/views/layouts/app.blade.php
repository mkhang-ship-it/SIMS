{{--
    Updated: Added Flasher notification system integration
    - Added @include('flasher::message') directive to render notifications
    - Fixed z-index issue to display notifications above header
--}}
<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light"
    data-menu-styles="dark" data-toggled="close">

<head>

    <!-- Meta Data -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=no'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> YNEX - Bootstrap 5 Premium Admin & Dashboard Template </title>
    <meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template">
    <meta name="Author" content="Spruko Technologies Private Limited">
    <meta name="keywords"
        content="admin,admin dashboard,admin panel,admin template,bootstrap,clean,dashboard,flat,jquery,modern,responsive,premium admin templates,responsive admin,ui,ui kit.">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/brand-logos/favicon.ico') }}" type="image/x-icon">

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

    @include('layouts.js')
    @yield('scripts')

</body>

</html>
