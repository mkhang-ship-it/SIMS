<header class="app-header">

    <div class="main-header-container container-fluid">

        <div class="header-content-left">

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
                        <a href="{{ route('backend.dashboard') }}" class="header-logo">
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
                        <a href="{{ route('backend.club.dashboard') }}" class="header-logo">
                            <img src="{{ $clubLogoLight }}" alt="logo" class="desktop-logo">
                            <img src="{{ $clubToggleLogoLight }}" alt="logo" class="toggle-logo">
                            <img src="{{ $clubLogoDark }}" alt="logo" class="desktop-dark">
                            <img src="{{ $clubToggleLogoDark }}" alt="logo" class="toggle-dark">
                        </a>
                    </div>
                </div>
            @endif

            <div class="header-element d-flex align-items-center">
                <a aria-label="Hide Sidebar"
                    class="sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle me-3"
                    data-bs-toggle="sidebar" href="javascript:void(0);">
                    <span></span>
                </a>

                @if (session('club_id'))
                    @php
                        $club = Club::find(session('club_id'));
                    @endphp
                    <span class="fw-semibold d-none d-md-inline">
                        Xin chào, {{ $club->name }}!
                    </span>
                @else
                    <span class="fw-semibold d-none d-md-inline">
                        Xin chào, {{ Auth::user()->name }}!
                    </span>
                @endif

            </div>
        </div>

        <div class="header-content-right">

            @php
                $user = Auth::user();
                $clubs = $user->clubs ?? collect();
            @endphp

            @php
                $isImpersonating = \App\Http\Controllers\ImpersonationController::isImpersonating();
                $impersonator = \App\Http\Controllers\ImpersonationController::getImpersonator();
            @endphp

            @if ($isImpersonating)
                <div class="header-element">
                    <a href="javascript:void(0);" class="header-link" data-bs-toggle="offcanvas"
                        data-bs-target="#impersonationOffcanvas" aria-controls="impersonationOffcanvas">
                        <i class="bx bx-user-check header-link-icon text-warning"></i>
                        <span class="badge bg-warning rounded-pill header-icon-badge pulse pulse-warning">!</span>
                    </a>
                </div>
            @endif

            @if ($clubs->count() > 1)
                <div class="header-element club-selector">
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
            @endif

            @if (session('club_id') && auth()->user()->hasPermissionForModuleCode('feature_request_management', 'create', 'branch'))
                <div class="header-element">
                    <a href="javascript:void(0);" class="header-link" data-bs-toggle="offcanvas"
                        data-bs-target="#featureRequestOffcanvas" aria-controls="featureRequestOffcanvas">
                        <i class="bx bx-plus-circle header-link-icon"></i>
                    </a>
                </div>
            @endif

            @if (session('club_id') && auth()->user()->hasPermissionForModuleCode('notification_management', 'view', 'branch'))
                <div class="header-element">
                    <a href="javascript:void(0);" class="header-link dropdown-toggle" data-bs-toggle="dropdown"
                        data-bs-auto-close="outside" id="messageDropdown" aria-expanded="false">
                        <i class="bx bx-bell header-link-icon"></i>
                        <span
                            class="badge bg-secondary rounded-pill header-icon-badge pulse pulse-secondary notification-badge d-none"
                            id="notification-icon-badge">0</span>
                    </a>

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
                </div>
            @endif

            <div class="header-element">
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
                <ul class="main-header-dropdown dropdown-menu pt-0 overflow-hidden header-profile-dropdown dropdown-menu-end"
                    aria-labelledby="mainHeaderProfile">
                    <li><a class="dropdown-item d-flex"
                            href="@if (session('club_id')) {{ route('backend.club.my-profile.index') }}@else{{ route('backend.my-profile.index') }} @endif"><i
                                class="ti ti-user-circle fs-18 me-2 op-7"></i>Thông Tin Cá Nhân</a></li>
                    <li><a class="dropdown-item d-flex text-danger"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            style="cursor: pointer;"><i class="ti ti-logout fs-18 me-2 op-7"></i>Đăng Xuất</a></li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </ul>
            </div>

        </div>

    </div>

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

    @if ($isImpersonating)
        <div class="offcanvas offcanvas-end" tabindex="-1" id="impersonationOffcanvas"
            aria-labelledby="impersonationOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="impersonationOffcanvasLabel">
                    <i class="bx bx-user-check me-2 text-warning"></i>
                    Đang Mạo Danh
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="alert alert-warning">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-info-circle me-2 fs-4"></i>
                        <div>
                            <h6 class="alert-heading mb-1">Phiên Mạo Danh Đang Hoạt Động</h6>
                            <p class="mb-0">Bạn đang đăng nhập với tài khoản của người khác.</p>
                        </div>
                    </div>
                </div>

                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="bx bx-user me-2"></i>
                            Thông Tin Mạo Danh
                        </h6>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label text-muted small">Đang mạo danh:</label>
                                <div class="d-flex align-items-center">
                                    <img src="{{ !empty(Auth::user()->avatar) ? asset('storage/' . Auth::user()->avatar) : asset('assets/images/avatar_example.png') }}"
                                        alt="avatar" class="avatar avatar-sm rounded-circle me-2">
                                    <div>
                                        <div class="fw-semibold">{{ Auth::user()->name }}</div>
                                        <small class="text-muted">{{ Auth::user()->email }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label text-muted small">Được mạo danh bởi:</label>
                                <div class="fw-semibold">{{ $impersonator['name'] }}</div>
                                <small class="text-muted">
                                    @if ($impersonator['type'] === 'central')
                                        Admin Trung Tâm
                                    @elseif($impersonator['type'] === 'branch')
                                        Admin Câu Lạc Bộ
                                    @else
                                        {{ ucfirst($impersonator['type']) }}
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle me-2"></i>
                        <strong>Lưu ý:</strong> Tất cả hành động bạn thực hiện sẽ được ghi nhận dưới tên của tài khoản
                        được mạo danh.
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <form action="{{ route('backend.impersonate.stop') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bx bx-log-out me-2"></i>
                            Thoát Mạo Danh
                        </button>
                    </form>
                    <button type="button" class="btn btn-outline-secondary w-100" data-bs-dismiss="offcanvas">
                        <i class="bx bx-x me-2"></i>
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    @endif

</header>
