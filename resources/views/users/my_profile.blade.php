@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Hồ Sơ</h1>
            <!-- Nút trên desktop -->
            <div class="d-none d-md-block">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                    <i class="ti ti-edit"></i>
                    Cập Nhật Thông Tin
                </button>
            </div>
            <!-- Dropdown trên mobile -->
            <div class="dropdown d-md-none">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Hành động
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="ti ti-edit"></i> Cập Nhật Thông Tin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="row">
            <div class="col-xxl-4 col-xl-12">
                <div class="card custom-card overflow-hidden">
                    <div class="card-body p-0">
                        <div class="d-sm-flex align-items-top p-4 border-bottom border-block-end-dashed main-profile-cover">
                            <div>
                                <span class="avatar avatar-xxl avatar-rounded online me-3">
                                    <img src="{{ !empty(Auth::user()->avatar) ? asset('storage/' . Auth::user()->avatar) : asset('assets/images/avatar_example.png') }}"
                                        alt="">
                                </span>
                            </div>
                            <div class="flex-fill main-profile-info">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h6 class="fw-semibold mb-1 text-fixed-white">{{ $user->name }}</h6>
                                </div>
                                <p class="mb-1 text-muted text-fixed-white op-7">{{ $user->role->name }}</p>
                                <p class="fs-12 text-fixed-white mb-4 op-5">
                                    <span class="me-3"><i
                                            class="ti ti-school me-1 align-middle"></i>{{ $user->major->name ?? '-' }}</span>
                                    <span><i
                                            class="ti ti-calendar-event me-1 align-middle"></i>{{ $user->course->name ?? '-' }}</span>
                                </p>
                                <div class="d-flex mb-0">
                                    <div class="me-4">
                                        <p class="fw-bold fs-20 text-fixed-white text-shadow mb-0">113</p>
                                        <p class="mb-0 fs-11 op-5 text-fixed-white">Projects</p>
                                    </div>
                                    <div class="me-4">
                                        <p class="fw-bold fs-20 text-fixed-white text-shadow mb-0">12.2k</p>
                                        <p class="mb-0 fs-11 op-5 text-fixed-white">Followers</p>
                                    </div>
                                    <div class="me-4">
                                        <p class="fw-bold fs-20 text-fixed-white text-shadow mb-0">128</p>
                                        <p class="mb-0 fs-11 op-5 text-fixed-white">Following</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 border-bottom border-block-end-dashed">
                            <div class="mb-4">
                                <p class="fs-15 mb-2 fw-semibold">Professional Bio :</p>
                                <p class="fs-12 text-muted op-7 mb-0">
                                    I am <b class="text-default">Sonya Taylor,</b> here by conclude that,i am the founder
                                    and managing director of the prestigeous company name laugh at all and acts as the cheif
                                    executieve officer of the company.
                                </p>
                            </div>

                        </div>
                        <div class="p-4 border-bottom border-block-end-dashed">
                            <p class="fs-15 mb-2 me-4 fw-semibold">Contact Information :</p>
                            <div class="text-muted">
                                <p class="mb-2">
                                    <span class="avatar avatar-sm avatar-rounded me-2 bg-light text-muted">
                                        <i class="ri-mail-line align-middle fs-14"></i>
                                    </span>
                                    sonyataylor2531@gmail.com
                                </p>
                                <p class="mb-2">
                                    <span class="avatar avatar-sm avatar-rounded me-2 bg-light text-muted">
                                        <i class="ri-phone-line align-middle fs-14"></i>
                                    </span>
                                    +(555) 555-1234
                                </p>
                                <p class="mb-0">
                                    <span class="avatar avatar-sm avatar-rounded me-2 bg-light text-muted">
                                        <i class="ri-map-pin-line align-middle fs-14"></i>
                                    </span>
                                    MIG-1-11, Monroe Street, Georgetown, Washington D.C, USA,20071
                                </p>
                            </div>
                        </div>
                        <div class="p-4 border-bottom border-block-end-dashed d-flex align-items-center">
                            <p class="fs-15 mb-2 me-4 fw-semibold">Social Networks :</p>
                            <div class="btn-list mb-0">
                                <button class="btn btn-sm btn-icon btn-primary-light btn-wave waves-effect waves-light">
                                    <i class="ri-facebook-line fw-semibold"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-secondary-light btn-wave waves-effect waves-light">
                                    <i class="ri-twitter-line fw-semibold"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-warning-light btn-wave waves-effect waves-light">
                                    <i class="ri-instagram-line fw-semibold"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-success-light btn-wave waves-effect waves-light">
                                    <i class="ri-github-line fw-semibold"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-danger-light btn-wave waves-effect waves-light">
                                    <i class="ri-youtube-line fw-semibold"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-xxl-8 col-xl-12">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-body p-0">
                                <div
                                    class="p-3 border-bottom border-block-end-dashed d-flex align-items-center justify-content-between">
                                    <div>
                                        <ul class="nav nav-tabs mb-0 tab-style-6 justify-content-start" id="myTab"
                                            role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="info-tab" data-bs-toggle="tab"
                                                    data-bs-target="#info-tab-pane" type="button" role="tab"
                                                    aria-controls="info-tab-pane" aria-selected="true">
                                                    <i class="ri-user-line me-1 align-middle d-inline-block"></i>Thông Tin
                                                    Cá Nhân
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="security-tab" data-bs-toggle="tab"
                                                    data-bs-target="#security-tab-pane" type="button" role="tab"
                                                    aria-controls="security-tab-pane" aria-selected="false">
                                                    <i class="ri-lock-line me-1 align-middle d-inline-block"></i>Bảo Mật
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="sessions-tab" data-bs-toggle="tab"
                                                    data-bs-target="#sessions-tab-pane" type="button" role="tab"
                                                    aria-controls="sessions-tab-pane" aria-selected="false">
                                                    <i class="ri-device-line me-1 align-middle d-inline-block"></i>Quản Lý
                                                    Phiên
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="p-3">
                                    <div class="tab-content" id="myTabContent">
                                        <div class="tab-pane show active fade p-0 border-0" id="info-tab-pane"
                                            role="tabpanel" aria-labelledby="info-tab" tabindex="0">
                                            {{-- Thông Tin Cá Nhân --}}
                                            <div class="row justify-content-center">
                                                <div class="col-lg-12 col-md-12">
                                                    <ul class="list-group list-group-flush rounded-3 overflow-hidden">
                                                        <li class="list-group-item d-flex align-items-center py-3">
                                                            <i class="ri-mail-line fs-5 text-primary me-3"></i>
                                                            <span class="fw-semibold w-30"
                                                                style="min-width:120px;">Email</span>
                                                            <span class="ms-auto">{{ $user->email }}</span>
                                                        </li>
                                                        <li class="list-group-item d-flex align-items-center py-3">
                                                            <i class="ri-user-3-line fs-5 text-info me-3"></i>
                                                            <span class="fw-semibold w-30" style="min-width:120px;">Giới
                                                                Tính</span>
                                                            <span class="ms-auto">
                                                                @if ($user->gender === 'male')
                                                                    Nam
                                                                @elseif($user->gender === 'female')
                                                                    Nữ
                                                                @else
                                                                    -
                                                                @endif
                                                            </span>
                                                        </li>
                                                        <li class="list-group-item d-flex align-items-center py-3">
                                                            <i class="ri-calendar-line fs-5 text-success me-3"></i>
                                                            <span class="fw-semibold w-30" style="min-width:120px;">Ngày
                                                                Sinh</span>
                                                            <span
                                                                class="ms-auto">{{ $user->birthday ? \Carbon\Carbon::parse($user->birthday)->format('d/m/Y') : '-' }}</span>
                                                        </li>
                                                        <li class="list-group-item d-flex align-items-center py-3">
                                                            <i class="ri-profile-line fs-5 text-warning me-3"></i>
                                                            <span class="fw-semibold w-30"
                                                                style="min-width:120px;">CCCD/CMND</span>
                                                            <span
                                                                class="ms-auto">{{ $user->identity_card_code ?? '-' }}</span>
                                                        </li>
                                                        <li class="list-group-item d-flex align-items-center py-3">
                                                            <i class="ri-phone-line fs-5 text-danger me-3"></i>
                                                            <span class="fw-semibold w-30" style="min-width:120px;">Số
                                                                Điện Thoại</span>
                                                            <span class="ms-auto">{{ $user->phone ?? '-' }}</span>
                                                        </li>
                                                        <li class="list-group-item d-flex align-items-center py-3">
                                                            <i class="ri-barcode-line fs-5 text-secondary me-3"></i>
                                                            <span class="fw-semibold w-30" style="min-width:120px;">Mã
                                                                Sinh Viên</span>
                                                            <span class="ms-auto">{{ $user->student_code ?? '-' }}</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade p-0 border-0" id="security-tab-pane" role="tabpanel"
                                            aria-labelledby="security-tab" tabindex="0">
                                            {{-- Bảo Mật (Đặt lại mật khẩu) --}}
                                            <div class="mb-3">
                                                <form method="POST"
                                                    action="{{ route('backend.my-profile.update-password') }}"
                                                    class="w-100" style="max-width:400px">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label for="current_password" class="form-label">Mật Khẩu Hiện Tại
                                                            <span class="text-danger">*</span></label>
                                                        <input type="password" class="form-control" id="current_password"
                                                            name="current_password" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="password" class="form-label">Mật Khẩu Mới
                                                            <span class="text-danger">*</span></label>
                                                        <input type="password" class="form-control" id="password"
                                                            name="password" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="password_confirmation" class="form-label">Xác Nhận Mật
                                                            Khẩu Mới
                                                            <span class="text-danger">*</span></label>
                                                        <input type="password" class="form-control"
                                                            id="password_confirmation" name="password_confirmation"
                                                            required>
                                                    </div>
                                                    <button type="submit" class="btn btn-warning">Đổi mật khẩu</button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade p-0 border-0" id="sessions-tab-pane" role="tabpanel"
                                            aria-labelledby="sessions-tab" tabindex="0">
                                            {{-- Quản Lý Phiên --}}
                                            <div class="mb-3">
                                                @php
                                                    $userSessions = DB::table('sessions')
                                                        ->where('user_id', Auth::user()->id)
                                                        ->orderBy('last_activity', 'desc')
                                                        ->limit(5)
                                                        ->get();

                                                    $totalSessions = DB::table('sessions')
                                                        ->where('user_id', Auth::user()->id)
                                                        ->count();
                                                    $activeSessions = DB::table('sessions')
                                                        ->where('user_id', Auth::user()->id)
                                                        ->where(
                                                            'last_activity',
                                                            '>=',
                                                            time() - config('session.lifetime') * 60,
                                                        )
                                                        ->count();
                                                @endphp

                                                <!-- Thông tin đơn giản -->
                                                <div class="alert alert-info">
                                                    <div class="d-flex align-items-center">
                                                        <i class="ti ti-device-laptop me-3 fs-4"></i>
                                                        <div>
                                                            <h6 class="mb-1">Phiên Đăng Nhập</h6>
                                                            <p class="mb-0">Bạn đang có
                                                                <strong>{{ $activeSessions }}</strong> phiên hoạt động
                                                                trong tổng số <strong>{{ $totalSessions }}</strong> phiên
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Danh sách phiên gần đây -->
                                                <div class="card">
                                                    <div
                                                        class="card-header d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0">Phiên Gần Đây</h6>
                                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                            data-bs-target="#destroyAllSessionsModal">
                                                            <i class="ti ti-trash me-1"></i>
                                                            Đăng Xuất Tất Cả
                                                        </button>
                                                    </div>
                                                    <div class="card-body p-0">
                                                        @if ($userSessions->count() > 0)
                                                            <div class="list-group list-group-flush">
                                                                @foreach ($userSessions as $session)
                                                                    @php
                                                                        $isActive =
                                                                            $session->last_activity >=
                                                                            time() - config('session.lifetime') * 60;
                                                                        $isCurrent =
                                                                            $session->id === session()->getId();
                                                                    @endphp
                                                                    <div
                                                                        class="list-group-item d-flex justify-content-between align-items-center">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="me-3">
                                                                                @if ($isCurrent)
                                                                                    <i
                                                                                        class="ti ti-device-laptop text-primary fs-5"></i>
                                                                                @else
                                                                                    <i
                                                                                        class="ti ti-device-mobile text-muted fs-5"></i>
                                                                                @endif
                                                                            </div>
                                                                            <div>
                                                                                <div class="fw-semibold">
                                                                                    {{ $session->ip_address ?? 'N/A' }}
                                                                                    @if ($isCurrent)
                                                                                        <span
                                                                                            class="badge bg-primary ms-2">Hiện
                                                                                            tại</span>
                                                                                    @endif
                                                                                </div>
                                                                                <small class="text-muted">
                                                                                    {{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->format('d/m/Y H:i') }}
                                                                                    @if ($isActive)
                                                                                        <span
                                                                                            class="badge bg-success ms-1">Hoạt
                                                                                            động</span>
                                                                                    @else
                                                                                        <span
                                                                                            class="badge bg-warning ms-1">Hết
                                                                                            hạn</span>
                                                                                    @endif
                                                                                </small>
                                                                            </div>
                                                                        </div>
                                                                        @if (!$isCurrent)
                                                                            <button class="btn btn-sm btn-outline-danger"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#destroySessionModal"
                                                                                data-session-id="{{ $session->id }}">
                                                                                <i class="ti ti-x"></i>
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div class="text-center py-4">
                                                                <i class="ti ti-users fs-1 text-muted mb-3"></i>
                                                                <p class="text-muted">Không có phiên nào</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="mt-3">
                                                    <small class="text-muted">
                                                        <i class="ti ti-info-circle me-1"></i>
                                                        Xóa phiên sẽ đăng xuất bạn khỏi thiết bị đó
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End::row-1 -->

    </div>

    {{-- Modal cập nhật thông tin cá nhân --}}
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('backend.my-profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProfileModalLabel">Cập nhật thông tin cá nhân</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 text-center">
                            @if ($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="rounded-circle"
                                    width="100" height="100">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}" alt="Avatar"
                                    class="rounded-circle" width="100" height="100">
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="avatar" class="form-label">Avatar</label>
                            <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên Thành Viên *</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ $user->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ $user->email }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">Giới Tính</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="">Chọn giới tính</option>
                                <option value="male" @if ($user->gender == 'male') selected @endif>Nam</option>
                                <option value="female" @if ($user->gender == 'female') selected @endif>Nữ</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="birthday" class="form-label">Ngày Sinh</label>
                            <input type="date" class="form-control" id="birthday" name="birthday"
                                value="{{ $user->birthday ? $user->birthday->format('Y-m-d') : '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="identity_card_code" class="form-label">CCCD/CMND</label>
                            <input type="text" class="form-control" id="identity_card_code" name="identity_card_code"
                                value="{{ $user->identity_card_code }}">
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số Điện Thoại</label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                value="{{ $user->phone }}">
                        </div>
                        <div class="mb-3">
                            <label for="student_code" class="form-label">Mã Sinh Viên</label>
                            <input type="text" class="form-control" id="student_code" name="student_code"
                                value="{{ $user->student_code }}">
                        </div>
                        <div class="mb-3">
                            <label for="major_id" class="form-label">Chuyên Ngành</label>
                            <select class="form-select" id="major_id" name="major_id">
                                <option value="">Chọn chuyên ngành</option>
                                @foreach ($majors as $major)
                                    <option value="{{ $major->id }}"
                                        @if ($user->major_id == $major->id) selected @endif>
                                        {{ $major->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="course_id" class="form-label">Khóa Học</label>
                            <select class="form-select" id="course_id" name="course_id">
                                <option value="">Chọn khóa học</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}"
                                        @if ($user->course_id == $course->id) selected @endif>
                                        {{ $course->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="role_id" class="form-label">Vai Trò *</label>
                            <select class="form-select" id="role_id" name="role_id" required>
                                <option value="">Chọn vai trò</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"
                                        @if ($user->role_id == $role->id) selected @endif>
                                        {{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- [AI CHANGE 2024-07-14] Bổ sung select chọn câu lạc bộ mặc định nếu user có nhiều club --}}
                        @if (isset($clubs) && $clubs->count() > 1)
                            <div class="mb-3">
                                <label for="default_club_id" class="form-label">Câu lạc bộ mặc định</label>
                                <select name="default_club_id" id="default_club_id" class="form-select">
                                    @foreach ($clubs as $club)
                                        <option value="{{ $club->id }}"
                                            @if ($user->default_club_id == $club->id) selected @endif>{{ $club->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal xóa phiên cụ thể --}}
    <div class="modal fade" id="destroySessionModal" tabindex="-1" aria-labelledby="destroySessionModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" id="destroySessionForm">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title" id="destroySessionModalLabel">Xác Nhận Xóa Phiên</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Bạn có chắc chắn muốn xóa phiên này?</p>
                        <p class="text-muted">Hành động này sẽ đăng xuất bạn khỏi thiết bị này.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger">Xóa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal xóa tất cả phiên --}}
    <div class="modal fade" id="destroyAllSessionsModal" tabindex="-1" aria-labelledby="destroyAllSessionsModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('backend.my-profile.destroy-all-sessions') }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title" id="destroyAllSessionsModalLabel">Xác Nhận Xóa Tất Cả Phiên</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="ti ti-alert-triangle me-2"></i>
                            <strong>Cảnh báo!</strong> Hành động này sẽ đăng xuất bạn khỏi tất cả thiết bị.
                        </div>
                        <p>Bạn có chắc chắn muốn xóa tất cả phiên?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger">Xóa Tất Cả</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Xử lý modal xóa phiên cụ thể
            const destroySessionModal = document.getElementById('destroySessionModal');
            if (destroySessionModal) {
                destroySessionModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const sessionId = button.getAttribute('data-session-id');
                    const form = document.getElementById('destroySessionForm');
                    form.action =
                        `{{ route('backend.my-profile.destroy-session', ['sessionId' => ':sessionId']) }}`
                        .replace(':sessionId', sessionId);
                });
            }
        });
    </script>

    {{-- XÓA Modal đổi mật khẩu --}}
@endsection
