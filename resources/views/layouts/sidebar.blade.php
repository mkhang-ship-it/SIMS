<aside class="app-sidebar sticky" id="sidebar">
    @php
        use App\Models\Setting;
        $generalSettings = Setting::getMany(null, [
            'image_horizontal_light',
            'image_horizontal_dark',
            'image_square_light',
            'image_square_dark',
        ]);
        $logoLight = !empty($generalSettings['image_horizontal_light'] ?? null)
            ? Storage::url($generalSettings['image_horizontal_light'])
            : asset('assets/images/brand-logos/desktop-logo.png');
        $logoDark = !empty($generalSettings['image_horizontal_dark'] ?? null)
            ? Storage::url($generalSettings['image_horizontal_dark'])
            : asset('assets/images/brand-logos/desktop-dark.png');
        $toggleLogoLight = !empty($generalSettings['image_square_light'] ?? null)
            ? Storage::url($generalSettings['image_square_light'])
            : asset('assets/images/brand-logos/toggle-logo.png');
        $toggleLogoDark = !empty($generalSettings['image_square_dark'] ?? null)
            ? Storage::url($generalSettings['image_square_dark'])
            : asset('assets/images/brand-logos/toggle-dark.png');

        use App\Models\Club;
        $club = Club::find(session('club_id'));

        $clubLogoLight = !empty($club->image_horizontal_light)
            ? Storage::url($club->image_horizontal_light)
            : asset('assets/images/brand-logos/desktop-logo.png');
        $clubToggleLogoLight = !empty($club->image_square_light)
            ? Storage::url($club->image_square_light)
            : asset('assets/images/brand-logos/toggle-logo.png');
        $clubLogoDark = !empty($club->image_horizontal_dark)
            ? Storage::url($club->image_horizontal_dark)
            : asset('assets/images/brand-logos/desktop-dark.png');
        $clubToggleLogoDark = !empty($club->image_square_dark)
            ? Storage::url($club->image_square_dark)
            : asset('assets/images/brand-logos/toggle-dark.png');
    @endphp
    @if (!session('club_id'))
        <div class="main-sidebar-header">
            <a href="{{ route('backend.dashboard') }}" class="header-logo">
                <img src="{{ $logoLight }}" alt="logo" class="desktop-logo">
                <img src="{{ $toggleLogoLight }}" alt="logo" class="toggle-logo">
                <img src="{{ $logoDark }}" alt="logo" class="desktop-dark">
                <img src="{{ $toggleLogoDark }}" alt="logo" class="toggle-dark">
            </a>
        </div>
    @else
        <div class="main-sidebar-header">
            <a href="{{ route('backend.club.dashboard') }}" class="header-logo">
                <img src="{{ $clubLogoLight }}" alt="logo" class="desktop-logo">
                <img src="{{ $clubToggleLogoLight }}" alt="logo" class="toggle-logo">
                <img src="{{ $clubLogoDark }}" alt="logo" class="desktop-dark">
                <img src="{{ $clubToggleLogoDark }}" alt="logo" class="toggle-dark">
            </a>
        </div>
    @endif
    <div class="main-sidebar" id="sidebar-scroll">
        <nav class="main-menu-container nav nav-pills flex-column sub-open">
            <div class="slide-left" id="slide-left">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24"
                    viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
                </svg>
            </div>
            <ul class="main-menu">
                @if (!session('club_id'))
                    <li class="slide__category"><span class="category-name">Tổng Quan</span></li>
                    @if (auth()->user()->hasPermissionForModuleCode('dashboard', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('backend.dashboard') }}"
                                class="side-menu__item {{ request()->routeIs('backend.dashboard') ? 'active' : '' }}">
                                <i class="bx bx-home side-menu__icon"></i>
                                <span class="side-menu__label">Tổng Quan</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('notification_management', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.notifications.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.notifications.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.notifications.index') ? 'active' : '' }}">
                                <i class="las la-bell side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Thông Báo</span>
                            </a>
                        </li>
                    @endif
                    <li class="slide__category"><span class="category-name">Quản Lý Hệ Thống</span></li>
                    @if (auth()->user()->hasPermissionForModuleCode('club_management', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.clubs.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.clubs.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.clubs.index') ? 'active' : '' }}">
                                <i class="las la-users side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Câu Lạc Bộ</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('role_management', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.roles.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.roles.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.roles.index') ? 'active' : '' }}">
                                <i class="las la-user-shield side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Vai Trò</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('member_management', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.members.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.members.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.members.index') ? 'active' : '' }}">
                                <i class="las la-user-friends side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Thành Viên</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('discipline_record_management', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.discipline-records.*') ? 'active' : '' }}">
                            <a href="{{ route('backend.discipline-records.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.discipline-records.*') ? 'active' : '' }}">
                                <i class="las la-chart-line side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Điểm</span>
                            </a>
                        </li>
                    @endif
                    <li class="slide__category"><span class="category-name">Quản Lý Học Tập</span></li>

                    @if (auth()->user()->hasPermissionForModuleCode('course_management', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.courses.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.courses.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.courses.index') ? 'active' : '' }}">
                                <i class="las la-graduation-cap side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Khóa Học</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('major_management', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.majors.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.majors.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.majors.index') ? 'active' : '' }}">
                                <i class="las la-book-open side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Chuyên Ngành</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('semester_management', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.semesters.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.semesters.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.semesters.index') ? 'active' : '' }}">
                                <i class="las la-calendar-alt side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Học Kỳ</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('slot_management', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.slots.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.slots.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.slots.index') ? 'active' : '' }}">
                                <i class="las la-clock side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Ca Học</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('classroom_management', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.classrooms.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.classrooms.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.classrooms.index') ? 'active' : '' }}">
                                <i class="las la-building side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Phòng Học</span>
                            </a>
                        </li>
                    @endif

                    <li class="slide__category"><span class="category-name">Quản Lý Sự Kiện</span></li>

                    @if (auth()->user()->hasPermissionForModuleCode('event_management', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.events.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.events.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.events.index') ? 'active' : '' }}">
                                <i class="las la-calendar-plus side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Sự Kiện</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('event_management', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.events.timetable') ? 'active' : '' }}">
                            <a href="{{ route('backend.events.timetable') }}"
                                class="side-menu__item {{ request()->routeIs('backend.events.timetable') ? 'active' : '' }}">
                                <i class="las la-calendar-week side-menu__icon"></i>
                                <span class="side-menu__label">Xem Lịch Sự Kiện</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('event_management', 'view_plan', 'central'))
                        <li
                            class="slide {{ request()->routeIs('backend.events.event-plans.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.events.event-plans.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.events.event-plans.index') ? 'active' : '' }}">
                                <i class="las la-tasks side-menu__icon"></i>
                                <span class="side-menu__label">Kế Hoạch Sự Kiện</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('event_management', 'view_image', 'central'))
                        <li
                            class="slide {{ request()->routeIs('backend.events.images.gallery-view') ? 'active' : '' }}">
                            <a href="{{ route('backend.events.images.gallery-view') }}"
                                class="side-menu__item {{ request()->routeIs('backend.events.images.gallery-view') ? 'active' : '' }}">
                                <i class="las la-images side-menu__icon"></i>
                                <span class="side-menu__label">Xem Ảnh Sự Kiện</span>
                            </a>
                        </li>
                    @endif

                    <li class="slide__category"><span class="category-name">Nội Dung & Cấu Hình</span></li>

                    @if (auth()->user()->hasPermissionForModuleCode('quote_management', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.quotes.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.quotes.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.quotes.index') ? 'active' : '' }}">
                                <i class="las la-quote-left side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Châm Ngôn</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('setting_management', 'mail', 'central'))
                        <li class="slide {{ request()->routeIs('backend.settings.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.settings.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.settings.index') ? 'active' : '' }}">
                                <i class="las la-cogs side-menu__icon"></i>
                                <span class="side-menu__label">Cấu Hình Hệ Thống</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('session_management', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.sessions.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.sessions.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.sessions.index') ? 'active' : '' }}">
                                <i class="las la-desktop side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Phiên</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('log_management', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.logs.*') ? 'active' : '' }}">
                            <a href="{{ route('backend.logs.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.logs.*') ? 'active' : '' }}">
                                <i class="las la-file-alt side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Log</span>
                            </a>
                        </li>
                    @endif


                    @if (auth()->user()->hasPermissionForModuleCode('feature_request_management', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.feature-requests.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.feature-requests.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.feature-requests.index') ? 'active' : '' }}">
                                <i class="las la-lightbulb side-menu__icon"></i>
                                <span class="side-menu__label">Yêu Cầu Tính Năng</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('lucky_draw_management', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.lucky-draws.*') ? 'active' : '' }}">
                            <a href="{{ route('backend.lucky-draws.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.lucky-draws.*') ? 'active' : '' }}">
                                <i class="las la-dice side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Lucky Draw</span>
                            </a>
                        </li>
                    @endif
                @else
                    <li class="slide__category"><span class="category-name">Tổng Quan</span></li>

                    @if (auth()->user()->hasPermissionForModuleCode('dashboard', 'view', 'branch'))
                        <li class="slide {{ request()->routeIs('backend.club.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.dashboard') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.dashboard') ? 'active' : '' }}">
                                <i class="bx bx-home side-menu__icon"></i>
                                <span class="side-menu__label">Tổng Quan</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('notification_management', 'view', 'branch'))
                        <li
                            class="slide {{ request()->routeIs('backend.club.notifications.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.notifications.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.notifications.index') ? 'active' : '' }}">
                                <i class="las la-bell side-menu__icon"></i>
                                <span class="side-menu__label">Thông Báo<span
                                        class="badge bg-warning-transparent notification-badge ms-2">0</span></span>
                            </a>
                        </li>
                    @endif

                    <li class="slide__category"><span class="category-name">Quản Lý Câu Lạc Bộ</span></li>

                    @if (auth()->user()->hasPermissionForModuleCode('role_management', 'view', 'branch'))
                        <li class="slide {{ request()->routeIs('backend.club.roles.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.roles.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.roles.index') ? 'active' : '' }}">
                                <i class="las la-user-shield side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Vai Trò</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('member_management', 'view', 'branch'))
                        <li class="slide {{ request()->routeIs('backend.club.members.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.members.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.members.index') ? 'active' : '' }}">
                                <i class="las la-user-friends side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Thành Viên</span>
                            </a>
                        </li>
                    @endif





                    @if (auth()->user()->hasPermissionForModuleCode('award_management', 'view', 'branch'))
                        <li class="slide {{ request()->routeIs('backend.club.awards.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.awards.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.awards.index') ? 'active' : '' }}">
                                <i class="las la-trophy side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Giải Thưởng</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('discipline_management', 'view', 'branch'))
                        <li class="slide {{ request()->routeIs('backend.club.disciplines.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.disciplines.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.disciplines.index') ? 'active' : '' }}">
                                <i class="las la-exclamation-triangle side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Kỷ Luật</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('discipline_record_management', 'view', 'branch'))
                        <li
                            class="slide {{ request()->routeIs('backend.club.discipline-records.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.discipline-records.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.discipline-records.index') ? 'active' : '' }}">
                                <i class="las la-clipboard-list side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Điểm</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('discipline_record_management', 'my_points', 'branch'))
                        <li
                            class="slide {{ request()->routeIs('backend.club.discipline-records.my-points') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.discipline-records.my-points') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.discipline-records.my-points') ? 'active' : '' }}">
                                <i class="las la-chart-line side-menu__icon"></i>
                                <span class="side-menu__label">Điểm Của Tôi</span>
                            </a>
                        </li>
                    @endif

                    <li class="slide__category"><span class="category-name">Quản Lý Sự Kiện</span></li>

                    @if (auth()->user()->hasPermissionForModuleCode('event_management', 'view', 'branch'))
                        <li class="slide {{ request()->routeIs('backend.club.events.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.events.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.events.index') ? 'active' : '' }}">
                                <i class="las la-calendar-plus side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Sự Kiện</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('event_management', 'view_timetable', 'branch'))
                        <li class="slide {{ request()->routeIs('backend.club.events.timetable') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.events.timetable') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.events.timetable') ? 'active' : '' }}">
                                <i class="las la-calendar-week side-menu__icon"></i>
                                <span class="side-menu__label">Xem Lịch Sự Kiện</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('event_management', 'my_checkin', 'branch'))
                        <li
                            class="slide {{ request()->routeIs('backend.club.events.my-participated') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.events.my-participated') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.events.my-participated') ? 'active' : '' }}">
                                <i class="las la-calendar-check side-menu__icon"></i>
                                <span class="side-menu__label">Sự Kiện Đã Tham Gia</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('lucky_draw_management', 'view', 'branch'))
                        <li class="slide {{ request()->routeIs('backend.club.lucky-draws.*') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.lucky-draws.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.lucky-draws.*') ? 'active' : '' }}">
                                <i class="las la-dice side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Lucky Draw</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('event_image_management', 'view_image', 'branch'))
                        <li
                            class="slide {{ request()->routeIs('backend.club.events.images.gallery-view') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.events.images.gallery-view') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.events.images.gallery-view') ? 'active' : '' }}">
                                <i class="las la-images side-menu__icon"></i>
                                <span class="side-menu__label">Xem Ảnh Sự Kiện</span>
                            </a>
                        </li>
                    @endif

                    <li class="slide__category"><span class="category-name">Quản Lý Tài Chính</span></li>

                    @if (auth()->user()->hasPermissionForModuleCode('invoice_management', 'view', 'branch'))
                        <li class="slide {{ request()->routeIs('backend.club.invoices.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.invoices.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.invoices.index') ? 'active' : '' }}">
                                <i class="las la-file-invoice-dollar side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Hóa Đơn</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('invoice_management', 'my_invoices', 'branch'))
                        <li
                            class="slide {{ request()->routeIs('backend.club.invoices.my-invoices') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.invoices.my-invoices') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.invoices.my-invoices') ? 'active' : '' }}">
                                <i class="las la-receipt side-menu__icon"></i>
                                <span class="side-menu__label">Hóa Đơn Của Tôi</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('expense_management', 'view', 'branch'))
                        <li class="slide {{ request()->routeIs('backend.club.expenses.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.expenses.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.expenses.index') ? 'active' : '' }}">
                                <i class="las la-money-bill-wave side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Chi Phí</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('expense_management', 'my_expenses', 'branch'))
                        <li
                            class="slide {{ request()->routeIs('backend.club.expenses.my-expenses') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.expenses.my-expenses') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.expenses.my-expenses') ? 'active' : '' }}">
                                <i class="las la-hand-holding-usd side-menu__icon"></i>
                                <span class="side-menu__label">Yêu Cầu Thanh Toán</span>
                            </a>
                        </li>
                    @endif

                    <li class="slide__category"><span class="category-name">Hệ Thống</span></li>

                    @if (auth()->user()->hasPermissionForModuleCode('setting_management', 'general', 'branch') ||
                            auth()->user()->hasPermissionForModuleCode('setting_management', 'word_template', 'branch') ||
                            auth()->user()->hasPermissionForModuleCode('setting_management', 'payment', 'branch'))
                        <li class="slide {{ request()->routeIs('backend.club.settings.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.settings.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.settings.index') ? 'active' : '' }}">
                                <i class="las la-cogs side-menu__icon"></i>
                                <span class="side-menu__label">Quản Trị Hệ Thống</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('feature_request_management', 'view', 'branch'))
                        <li
                            class="slide {{ request()->routeIs('backend.club.feature-requests.*') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.feature-requests.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.feature-requests.*') ? 'active' : '' }}">
                                <i class="las la-lightbulb side-menu__icon"></i>
                                <span class="side-menu__label">Yêu Cầu Tính Năng</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('session_management', 'view', 'branch'))
                        <li class="slide {{ request()->routeIs('backend.club.sessions.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.sessions.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.sessions.index') ? 'active' : '' }}">
                                <i class="las la-desktop side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Phiên</span>
                            </a>
                        </li>
                    @endif
                @endif
            </ul>
            <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191"
                    width="24" height="24" viewBox="0 0 24 24">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path>
                </svg></div>
        </nav>
    </div>
</aside>
