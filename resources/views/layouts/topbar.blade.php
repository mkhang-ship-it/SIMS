<header class="app-header">

    <!-- Start::main-header-container -->
    <div class="main-header-container container-fluid">

        <!-- Start::header-content-left -->
        <div class="header-content-left">

            <!-- Start::header-element -->

            @php
                use App\Models\Setting;
                $generalSettings = Setting::getMany(null, [
                    'image_horizontal_light',
                    'image_horizontal_dark',
                    'image_square_light',
                    'image_square_dark',
                ]);
                $logoLight = !empty($generalSettings['image_horizontal_light'])
                    ? Storage::url($generalSettings['image_horizontal_light'])
                    : asset('assets/images/brand-logos/desktop-logo.png');
                $logoDark = !empty($generalSettings['image_horizontal_dark'])
                    ? Storage::url($generalSettings['image_horizontal_dark'])
                    : asset('assets/images/brand-logos/desktop-dark.png');
                $toggleLogoLight = !empty($generalSettings['image_square_light'])
                    ? Storage::url($generalSettings['image_square_light'])
                    : asset('assets/images/brand-logos/toggle-logo.png');
                $toggleLogoDark = !empty($generalSettings['image_square_dark'])
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
                <div class="header-element">
                    <div class="horizontal-logo">
                        <a href="index.html" class="header-logo">
                            <img src="{{ $logoLight }}" alt="logo" class="desktop-logo">
                            <img src="{{ $toggleLogoLight }}" alt="logo" class="toggle-logo">
                            <img src="{{ $logoDark }}" alt="logo" class="desktop-dark">
                            <img src="{{ $toggleLogoDark }}" alt="logo" class="toggle-dark">
                        </a>
                    </div>
                </div>
            @else
                <div class="header-element">
                    <div class="horizontal-logo">
                        <a href="index.html" class="header-logo">
                            <img src="{{ $clubLogoLight }}" alt="logo" class="desktop-logo">
                            <img src="{{ $clubToggleLogoLight }}" alt="logo" class="toggle-logo">
                            <img src="{{ $clubLogoDark }}" alt="logo" class="desktop-dark">
                            <img src="{{ $clubToggleLogoDark }}" alt="logo" class="toggle-dark">
                        </a>
                    </div>
                </div>
            @endif
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
                    $displayName = Auth::user()->name;
                @endphp

                <span class="fw-semibold d-none d-md-inline">
                    {{ $greeting }} {{ $displayName }}!
                </span>

            </div>
            <!-- End::header-element -->
            <!-- End::header-element -->

        </div>
        <!-- End::header-content-left -->

        <!-- Start::header-content-right -->
        <div class="header-content-right">

            @php
                $user = Auth::user();
                $clubs = $user->clubs ?? collect();
            @endphp

            @if ($clubs->count() > 1)
                <!-- Start::header-element -->
                <div class="header-element club-selector">
                    <!-- Start::header-link|dropdown-toggle -->
                    @php
                        $defaultClub = $clubs->firstWhere('id', $user->default_club_id) ?? $clubs->first();
                        $defaultClubImage = !empty($defaultClub->image_square_light)
                            ? Storage::url($defaultClub->image_square_light)
                            : asset('assets/images/avatar_example.png');
                    @endphp
                    <a href="javascript:void(0);" class="header-link dropdown-toggle" data-bs-auto-close="outside"
                        data-bs-toggle="dropdown">
                        <img src="{{ $defaultClubImage }}" alt="img" class="rounded-circle"
                            style="width: 24px; height: 24px;">
                    </a>
                    <!-- End::header-link|dropdown-toggle -->

                    <ul class="main-header-dropdown dropdown-menu dropdown-menu-end" data-popper-placement="none">
                        <li class="dropdown-header text-muted fs-12 px-3 pt-2 pb-1">
                            Chuyển đổi Club
                        </li>
                        @foreach ($clubs as $club)
                            @php
                                $isActive = $user->default_club_id == $club->id;
                                $itemClass = $isActive ? 'bg-primary text-white' : '';
                            @endphp
                            <li>
                                <form action="{{ route('switch.default.club') }}" method="POST"
                                    id="club-switch-form-{{ $club->id }}">
                                    @csrf
                                    <input type="hidden" name="default_club_id" value="{{ $club->id }}">
                                    <a class="dropdown-item d-flex align-items-center {{ $itemClass }}"
                                        href="javascript:void(0);"
                                        onclick="document.getElementById('club-switch-form-{{ $club->id }}').submit();">
                                        <span class="avatar avatar-xs lh-1 me-2">
                                            <img src="{{ !empty($club->image_square_light) ? Storage::url($club->image_square_light) : asset('assets/images/avatar_example.png') }}"
                                                alt="img" class="rounded-circle">
                                        </span>
                                        {{ $club->name }}
                                    </a>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <!-- End::header-element -->
            @endif

            @if (session('club_id') && auth()->user()->hasPermissionForModuleCode('feature_request_management', 'create', 'branch'))
                <!-- Start::header-element -->
                <div class="header-element">
                    <!-- Start::header-link -->
                    <a href="javascript:void(0);" class="header-link" data-bs-toggle="offcanvas"
                        data-bs-target="#featureRequestOffcanvas" aria-controls="featureRequestOffcanvas">
                        <i class="bx bx-plus-circle header-link-icon"></i>
                    </a>
                    <!-- End::header-link -->
                </div>
                <!-- End::header-element -->
            @endif

            @if (session('club_id') && auth()->user()->hasPermissionForModuleCode('notification_management', 'view', 'branch'))
                <!-- Start::header-element -->
                <div class="header-element">
                    <!-- Start::header-link|dropdown-toggle -->
                    <a href="javascript:void(0);" class="header-link dropdown-toggle" data-bs-toggle="dropdown"
                        data-bs-auto-close="outside" id="messageDropdown" aria-expanded="false">
                        <i class="bx bx-bell header-link-icon"></i>
                        <span
                            class="badge bg-secondary rounded-pill header-icon-badge pulse pulse-secondary notification-badge d-none"
                            id="notification-icon-badge">0</span>
                    </a>
                    <!-- End::header-link|dropdown-toggle -->
                    <!-- Start::main-header-dropdown -->
                    <div class="main-header-dropdown dropdown-menu dropdown-menu-end" data-popper-placement="none">
                        <div class="p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <p class="mb-0 fs-17 fw-semibold">Thông Báo</p>
                                <span class="badge bg-secondary-transparent" id="notifiation-data">0 Chưa đọc</span>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <ul class="list-unstyled mb-0" id="header-notification-scroll"
                            style="max-height: 300px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #dee2e6 transparent;">
                            <div id="recentNotifications">
                                <li class="text-center py-3">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Đang tải...</span>
                                    </div>
                                </li>
                            </div>
                        </ul>
                        <div class="p-3 empty-header-item1 border-top">
                            <div class="d-grid">
                                <a href="{{ route('backend.club.notifications.index') }}" class="btn btn-primary">Xem
                                    Tất Cả</a>
                            </div>
                        </div>
                        <div class="p-5 empty-item1 d-none">
                            <div class="text-center">
                                <span class="avatar avatar-xl avatar-rounded bg-secondary-transparent">
                                    <i class="ri-notification-off-line fs-2"></i>
                                </span>
                                <h6 class="fw-semibold mt-3">Không Có Thông Báo Mới</h6>
                            </div>
                        </div>
                    </div>
                    <!-- End::main-header-dropdown -->
                </div>
                <!-- End::header-element -->
            @endif

            <!-- Start::header-element -->
            <div class="header-element">
                <!-- Start::header-link|dropdown-toggle -->
                <a href="#" class="header-link dropdown-toggle" id="mainHeaderProfile"
                    data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
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

    <!-- Feature Request Offcanvas -->
    @if (session('club_id') && auth()->user()->hasPermissionForModuleCode('feature_request_management', 'create', 'branch'))
        <div class="offcanvas offcanvas-end" tabindex="-1" id="featureRequestOffcanvas"
            aria-labelledby="featureRequestOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="featureRequestOffcanvasLabel">
                    <i class="bx bx-plus-circle me-2"></i>
                    Yêu Cầu Tính Năng / Báo Lỗi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <form action="{{ route('backend.club.feature-requests.store') }}" method="POST"
                    id="featureRequestOffcanvasForm">
                    @csrf
                    <div class="mb-3">
                        <label for="offcanvas_title" class="form-label">Tiêu Đề <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="offcanvas_title" name="title" required
                            value="{{ old('title') }}" placeholder="Nhập tiêu đề yêu cầu">
                        @error('title')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="offcanvas_type" class="form-label">Loại <span
                                class="text-danger">*</span></label>
                        <select class="form-select" id="offcanvas_type" name="type" required>
                            <option value="">Chọn loại</option>
                            <option value="feature" {{ old('type') == 'feature' ? 'selected' : '' }}>Tính Năng
                            </option>
                            <option value="bug" {{ old('type') == 'bug' ? 'selected' : '' }}>Lỗi</option>
                        </select>
                        @error('type')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="offcanvas_priority" class="form-label">Độ Ưu Tiên <span
                                class="text-danger">*</span></label>
                        <select class="form-select" id="offcanvas_priority" name="priority" required>
                            <option value="">Chọn độ ưu tiên</option>
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Thấp</option>
                            <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Trung Bình
                            </option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Cao</option>
                        </select>
                        @error('priority')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="offcanvas_description" class="form-label">Mô Tả <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control" id="offcanvas_description" name="description" rows="4" required
                            placeholder="Mô tả chi tiết yêu cầu tính năng của bạn...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-send me-2"></i>
                            Gửi Yêu Cầu
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">
                            <i class="bx bx-x me-2"></i>
                            Hủy
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</header>
