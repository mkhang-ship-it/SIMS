<header class="app-header">

    <!-- Start::main-header-container -->
    <div class="main-header-container container-fluid">

        <!-- Start::header-content-left -->
        <div class="header-content-left">

            <!-- Start::header-element -->
            <div class="header-element">
                <div class="horizontal-logo">
                    <a href="index.html" class="header-logo">
                        <img src="{{ asset('assets/images/brand-logos/desktop-logo.png') }}" alt="logo"
                            class="desktop-logo">
                        <img src="{{ asset('assets/images/brand-logos/toggle-logo.png') }}" alt="logo"
                            class="toggle-logo">
                        <img src="{{ asset('assets/images/brand-logos/desktop-dark.png') }}" alt="logo"
                            class="desktop-dark">
                        <img src="{{ asset('assets/images/brand-logos/toggle-dark.png') }}" alt="logo"
                            class="toggle-dark">
                    </a>
                </div>
            </div>
            <!-- End::header-element -->

            <!-- Start::header-element -->
            <div class="header-element d-flex align-items-center">
                <!-- Sidebar Toggle Button -->
                <a aria-label="Hide Sidebar"
                    class="sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle me-3"
                    data-bs-toggle="sidebar" href="javascript:void(0);">
                    <span></span>
                </a>
                <!-- User/Club Greeting -->
                @php
                    use Carbon\Carbon;

                    $hour = Carbon::now()->format('H');

                    if ($hour >= 5 && $hour < 11) {
                        $greeting = '🌞 Chào buổi sáng rạng rỡ,';
                    } elseif ($hour >= 11 && $hour < 13) {
                        $greeting = '🍽️ Chúc bạn một buổi trưa an lành,';
                    } elseif ($hour >= 13 && $hour < 17) {
                        $greeting = '🌤️ Buổi chiều tốt lành,';
                    } elseif ($hour >= 17 && $hour < 21) {
                        $greeting = '🌇 Chúc bạn một buổi tối thư thái,';
                    } else {
                        $greeting = '🌙 Đêm yên bình nhé,';
                    }

                    $displayName =  Auth::user()->name;
                @endphp

                <span class="fw-semibold">
                    {{ $greeting }} {{ $displayName }}!
                </span>

                @php
                    $user = Auth::user();
                    $clubs = $user->clubs ?? collect();
                @endphp
                @if ($clubs->count() > 1)
                    <form id="club-switch-form" action="{{ route('switch.default.club') }}" method="POST"
                        style="display:inline-block; margin-left: 16px;">
                        @csrf
                        <select name="default_club_id" class="form-select form-select-sm d-inline-block w-auto"
                            onchange="document.getElementById('club-switch-form').submit()">
                            @foreach ($clubs as $club)
                                <option value="{{ $club->id }}" @if ($user->default_club_id == $club->id) selected @endif>
                                    {{ $club->name }}</option>
                            @endforeach
                        </select>
                    </form>
                @endif


            </div>
            <!-- End::header-element -->
            <!-- End::header-element -->

        </div>
        <!-- End::header-content-left -->

        <!-- Start::header-content-right -->
        <div class="header-content-right">


            <!-- Start::header-element -->
            <div class="header-element country-selector">
                <!-- Start::header-link|dropdown-toggle -->
                <a href="javascript:void(0);" class="header-link dropdown-toggle" data-bs-auto-close="outside"
                    data-bs-toggle="dropdown">
                    <img src="../assets/images/flags/us_flag.jpg" alt="img" class="rounded-circle">
                </a>
                <!-- End::header-link|dropdown-toggle -->
                <ul class="main-header-dropdown dropdown-menu dropdown-menu-end" data-popper-placement="none">
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="javascript:void(0);">
                            <span class="avatar avatar-xs lh-1 me-2">
                                <img src="../assets/images/flags/us_flag.jpg" alt="img">
                            </span>
                            English
                        </a>
                    </li>
                </ul>
            </div>
            <!-- End::header-element -->

            <!-- Start::header-element -->
            <div class="header-element">
                <!-- Start::header-link|dropdown-toggle -->
                <a href="#" class="header-link dropdown-toggle" id="mainHeaderProfile" data-bs-toggle="dropdown"
                    data-bs-auto-close="outside" aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <div class="me-sm-2 me-0">
                            <img src="{{ !empty(Auth::user()->avatar) ? asset('storage/' . Auth::user()->avatar) : asset('assets/images/avatar_example.png') }}"
                                alt="img" width="32" height="32" class="rounded-circle">
                        </div>
                        <div class="d-sm-block d-none">
                            <p class="fw-semibold mb-0 lh-1">
                                {{ Auth::user()->name }}
                            </p>
                            <span class="op-7 fw-normal d-block fs-11">
                                {{ Auth::user()->email }}
                            </span>
                        </div>
                    </div>
                </a>
                <!-- End::header-link|dropdown-toggle -->
                <ul class="main-header-dropdown dropdown-menu pt-0 overflow-hidden header-profile-dropdown dropdown-menu-end"
                    aria-labelledby="mainHeaderProfile">
                    <li><a class="dropdown-item d-flex"
                            href="@if (session('club_id')) {{ route('backend.club.my-profile.index') }}@else{{ route('backend.my-profile.index') }} @endif"><i
                                class="ti ti-user-circle fs-18 me-2 op-7"></i>Thông tin cá nhân</a></li>
                    <li><a class="dropdown-item d-flex text-danger"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            style="cursor: pointer;"><i class="ti ti-logout fs-18 me-2 op-7"></i>Đăng xuất</a></li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </ul>
            </div>
            <!-- End::header-element -->

        </div>
        <!-- End::header-content-right -->

    </div>
    <!-- End::main-header-container -->

</header>
