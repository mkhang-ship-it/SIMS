<aside class="app-sidebar sticky" id="sidebar">

    <!-- Start::main-sidebar-header -->
    <div class="main-sidebar-header">
        <a href="index.html" class="header-logo">
            <img src="../assets/images/brand-logos/desktop-logo.png" alt="logo" class="desktop-logo">
            <img src="../assets/images/brand-logos/toggle-logo.png" alt="logo" class="toggle-logo">
            <img src="../assets/images/brand-logos/desktop-dark.png" alt="logo" class="desktop-dark">
            <img src="../assets/images/brand-logos/toggle-dark.png" alt="logo" class="toggle-dark">
        </a>
    </div>
    <!-- End::main-sidebar-header -->

    <!-- Start::main-sidebar -->
    <div class="main-sidebar" id="sidebar-scroll">

        <!-- Start::nav -->
        <nav class="main-menu-container nav nav-pills flex-column sub-open">
            <div class="slide-left" id="slide-left">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24"
                    viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
                </svg>
            </div>
            <ul class="main-menu">



                <!-- Start::slide__category -->
                <li class="slide__category"><span class="category-name">Main</span></li>
                <!-- End::slide__category -->

                <!-- Start::slide -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="bx bx-home side-menu__icon"></i>
                        <span class="side-menu__label">Dashboards<span
                                class="badge bg-warning-transparent ms-2">12</span></span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">Dashboards</a>
                        </li>
                        <li class="slide">
                            <a href="index.html" class="side-menu__item">CRM</a>
                        </li>
                    </ul>
                </li>
                <!-- End::slide -->

                @if (!session('club_id'))
                    @if (auth()->user()->hasPermissionForModuleCode('dashboard', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('backend.dashboard') }}"
                                class="side-menu__item {{ request()->routeIs('backend.dashboard') ? 'active' : '' }}">
                                <i class="bx bx-home side-menu__icon"></i>
                                <span class="side-menu__label">Tổng Quan</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('club_management', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.clubs.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.clubs.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.clubs.index') ? 'active' : '' }}">
                                <i class="las la-users side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Câu Lạc Bộ</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('course_management', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.courses.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.courses.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.courses.index') ? 'active' : '' }}">
                                <i class="las la-book-reader side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Khóa Học</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('semester_management', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.semesters.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.semesters.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.semesters.index') ? 'active' : '' }}">
                                <i class="las la-calendar side-menu__icon"></i>
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

                    @if (auth()->user()->hasPermissionForModuleCode('role_management', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.roles.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.roles.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.roles.index') ? 'active' : '' }}">
                                <i class="las la-user-shield side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Vai Trò</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('major_management', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.majors.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.majors.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.majors.index') ? 'active' : '' }}">
                                <i class="las la-book-reader side-menu__icon"></i>
                                <span class="side-menu__label"> Quản Lý Chuyên Ngành</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('member_management', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.members.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.members.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.members.index') ? 'active' : '' }}">
                                <i class="las la-users side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Thành Viên</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('event_management', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.events.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.events.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.events.index') ? 'active' : '' }}">
                                <i class="las la-calendar side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Sự Kiện</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('event_management', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.events.timetable') ? 'active' : '' }}">
                            <a href="{{ route('backend.events.timetable') }}"
                                class="side-menu__item {{ request()->routeIs('backend.events.timetable') ? 'active' : '' }}">
                                <i class="las la-calendar side-menu__icon"></i>
                                <span class="side-menu__label">Xem Lịch Sự Kiện</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('event_management', 'view_image', 'central'))
                        <li
                            class="slide {{ request()->routeIs('backend.events.images.gallery-view') ? 'active' : '' }}">
                            <a href="{{ route('backend.events.images.gallery-view') }}"
                                class="side-menu__item {{ request()->routeIs('backend.events.images.gallery-view') ? 'active' : '' }}">
                                <i class="las la-image side-menu__icon"></i>
                                <span class="side-menu__label">Xem Ảnh Sự Kiện</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('event_management', 'view_plan', 'central'))
                        <li
                            class="slide {{ request()->routeIs('backend.events.event-plans.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.events.event-plans.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.events.event-plans.index') ? 'active' : '' }}">
                                <i class="las la-calendar side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Kế Hoạch Sự Kiện</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('quote_management', 'view', 'central'))
                        <li class="slide {{ request()->routeIs('backend.quotes.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.quotes.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.quotes.index') ? 'active' : '' }}">
                                <i class="las la-quote-left side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Châm Ngôn</span>
                            </a>
                        </li>
                    @endif
                @else
                    {{-- CLUB USER (branch context) --}}
                    @if (auth()->user()->hasPermissionForModuleCode('dashboard', 'view', 'branch'))
                        <li class="slide {{ request()->routeIs('backend.club.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.dashboard') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.dashboard') ? 'active' : '' }}">
                                <i class="bx bx-home side-menu__icon"></i>
                                <span class="side-menu__label">Tổng Quan</span>
                            </a>
                        </li>
                    @endif

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
                                <i class="las la-users side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Thành Viên</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('award_management', 'view', 'branch'))
                        <li class="slide {{ request()->routeIs('backend.club.awards.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.awards.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.awards.index') ? 'active' : '' }}">
                                <i class="las la-award side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Giải Thưởng</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('event_management', 'view', 'branch'))
                        <li class="slide {{ request()->routeIs('backend.club.events.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.events.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.events.index') ? 'active' : '' }}">
                                <i class="las la-calendar-check side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Sự Kiện</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('event_management', 'view_timetable', 'branch'))
                        <li class="slide {{ request()->routeIs('backend.club.events.timetable') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.events.timetable') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.events.timetable') ? 'active' : '' }}">
                                <i class="las la-clock side-menu__icon"></i>
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

                    @if (auth()->user()->hasPermissionForModuleCode('event_image_management', 'view_image', 'branch'))
                        <li
                            class="slide {{ request()->routeIs('backend.club.events.images.gallery-view') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.events.images.gallery-view') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.events.images.gallery-view') ? 'active' : '' }}">
                                <i class="las la-image side-menu__icon"></i>
                                <span class="side-menu__label">Xem Ảnh Sự Kiện</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('invoice_management', 'view', 'branch'))
                        <li class="slide {{ request()->routeIs('backend.club.invoices.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.invoices.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.invoices.index') ? 'active' : '' }}">
                                <i class="las la-file-invoice side-menu__icon"></i>
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
                                <i class="las la-receipt side-menu__icon"></i>
                                <span class="side-menu__label">Quản Lý Chi Phí</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('expense_management', 'my_expenses', 'branch'))
                        <li
                            class="slide {{ request()->routeIs('backend.club.expenses.my-expenses') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.expenses.my-expenses') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.expenses.my-expenses') ? 'active' : '' }}">
                                <i class="las la-receipt side-menu__icon"></i>
                                <span class="side-menu__label">Yêu Cầu Thanh Toán</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasPermissionForModuleCode('setting_management', 'general', 'branch') ||
                            auth()->user()->hasPermissionForModuleCode('setting_management', 'word_template', 'branch') ||
                            auth()->user()->hasPermissionForModuleCode('setting_management', 'payment', 'branch'))
                        <li class="slide {{ request()->routeIs('backend.club.settings.index') ? 'active' : '' }}">
                            <a href="{{ route('backend.club.settings.index') }}"
                                class="side-menu__item {{ request()->routeIs('backend.club.settings.index') ? 'active' : '' }}">
                                <i class="las la-cog side-menu__icon"></i>
                                <span class="side-menu__label">Quản Trị Hệ Thống</span>
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
        <!-- End::nav -->

    </div>
    <!-- End::main-sidebar -->

</aside>
