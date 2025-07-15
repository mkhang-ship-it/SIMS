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
                                @php
                                    $clubId = session('club_id') ?? $user->default_club_id;
                                    $userClubRole = $user->userClubRoles->where('club_id', $clubId)->first();
                                @endphp
                                <p class="mb-1 text-muted text-fixed-white op-7">
                                    {{ $userClubRole && $userClubRole->role ? $userClubRole->role->name : '-' }}
                                </p>
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
                                                <button class="nav-link" id="bank-tab" data-bs-toggle="tab"
                                                    data-bs-target="#bank-tab-pane" type="button" role="tab"
                                                    aria-controls="bank-tab-pane" aria-selected="false">
                                                    <i class="ri-bank-card-line me-1 align-middle d-inline-block"></i>Thông
                                                    Tin Ngân Hàng
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="address-tab" data-bs-toggle="tab"
                                                    data-bs-target="#address-tab-pane" type="button" role="tab"
                                                    aria-controls="address-tab-pane" aria-selected="false">
                                                    <i class="ri-map-pin-line me-1 align-middle d-inline-block"></i>Địa Chỉ
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
                                                        <li class="list-group-item d-flex align-items-center py-3">
                                                            <i class="ri-map-pin-line fs-5 text-info me-3"></i>
                                                            <span class="fw-semibold w-30" style="min-width:120px;">Địa
                                                                Chỉ</span>
                                                            <span class="ms-auto">
                                                                @if ($address)
                                                                    {{ $address->address }},
                                                                    {{ $address->commune->name }},
                                                                    {{ $address->province->name }}
                                                                @else
                                                                    -
                                                                @endif
                                                            </span>
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
                                        <div class="tab-pane fade p-0 border-0" id="bank-tab-pane" role="tabpanel"
                                            aria-labelledby="bank-tab" tabindex="0">
                                            {{-- Thông Tin Ngân Hàng --}}
                                            <div class="row justify-content-center">
                                                <div class="col-lg-12 col-md-12">
                                                    @if ($bankAccount)
                                                        <ul class="list-group list-group-flush rounded-3 overflow-hidden">
                                                            <li class="list-group-item d-flex align-items-center py-3">
                                                                <i class="ri-bank-line fs-5 text-primary me-3"></i>
                                                                <span class="fw-semibold w-30"
                                                                    style="min-width:120px;">Tên Ngân Hàng</span>
                                                                <span class="ms-auto">{{ $bankAccount->bank_name }}</span>
                                                            </li>
                                                            <li class="list-group-item d-flex align-items-center py-3">
                                                                <i class="ri-barcode-line fs-5 text-info me-3"></i>
                                                                <span class="fw-semibold w-30" style="min-width:120px;">Mã
                                                                    Ngân Hàng</span>
                                                                <span class="ms-auto">{{ $bankAccount->bank_code }}
                                                                    ({{ $bankAccount->bank_bin }})</span>
                                                            </li>
                                                            <li class="list-group-item d-flex align-items-center py-3">
                                                                <i class="ri-bank-card-line fs-5 text-warning me-3"></i>
                                                                <span class="fw-semibold w-30"
                                                                    style="min-width:120px;">Tên Viết Tắt</span>
                                                                <span
                                                                    class="ms-auto">{{ $bankAccount->bank_short_name }}</span>
                                                            </li>
                                                            <li class="list-group-item d-flex align-items-center py-3">
                                                                <i class="ri-global-line fs-5 text-danger me-3"></i>
                                                                <span class="fw-semibold w-30"
                                                                    style="min-width:120px;">Swift Code</span>
                                                                <span
                                                                    class="ms-auto">{{ $bankAccount->bank_swift_code }}</span>
                                                            </li>
                                                            <li class="list-group-item d-flex align-items-center py-3">
                                                                <i class="ri-user-line fs-5 text-secondary me-3"></i>
                                                                <span class="fw-semibold w-30"
                                                                    style="min-width:120px;">Tên Chủ Tài Khoản</span>
                                                                <span
                                                                    class="ms-auto">{{ $bankAccount->bank_account_name }}</span>
                                                            </li>
                                                            <li class="list-group-item d-flex align-items-center py-3">
                                                                <i class="ri-bank-card-2-line fs-5 text-info me-3"></i>
                                                                <span class="fw-semibold w-30" style="min-width:120px;">Số
                                                                    Tài Khoản</span>
                                                                <span
                                                                    class="ms-auto">{{ $bankAccount->bank_account_number }}</span>
                                                            </li>
                                                        </ul>
                                                    @else
                                                        <div class="text-center py-4">
                                                            <i class="ri-bank-card-line fs-1 text-muted mb-3"></i>
                                                            <p class="text-muted">Chưa có thông tin ngân hàng</p>
                                                        </div>
                                                    @endif

                                                    <div class="mt-4">
                                                        <button class="btn btn-primary" data-bs-toggle="modal"
                                                            data-bs-target="#editBankAccountModal">
                                                            <i class="ti ti-edit me-1"></i>
                                                            {{ $bankAccount ? 'Cập Nhật' : 'Thêm' }} Thông Tin Ngân Hàng
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade p-0 border-0" id="address-tab-pane" role="tabpanel"
                                            aria-labelledby="address-tab" tabindex="0">
                                            {{-- Thông Tin Địa Chỉ --}}
                                            <div class="row justify-content-center">
                                                <div class="col-lg-12 col-md-12">
                                                    @if ($address)
                                                        <ul class="list-group list-group-flush rounded-3 overflow-hidden">
                                                            <li class="list-group-item d-flex align-items-center py-3">
                                                                <i class="ri-map-pin-line fs-5 text-primary me-3"></i>
                                                                <span class="fw-semibold w-30"
                                                                    style="min-width:120px;">Địa Chỉ Chi Tiết</span>
                                                                <span class="ms-auto">{{ $address->address }}</span>
                                                            </li>
                                                            <li class="list-group-item d-flex align-items-center py-3">
                                                                <i class="ri-building-line fs-5 text-info me-3"></i>
                                                                <span class="fw-semibold w-30"
                                                                    style="min-width:120px;">Xã/Phường</span>
                                                                <span class="ms-auto">{{ $address->commune->name }}
                                                                    ({{ $address->commune->code }})</span>
                                                            </li>
                                                            <li class="list-group-item d-flex align-items-center py-3">
                                                                <i class="ri-map-2-line fs-5 text-warning me-3"></i>
                                                                <span class="fw-semibold w-30"
                                                                    style="min-width:120px;">Tỉnh/Thành</span>
                                                                <span class="ms-auto">{{ $address->province->name }}
                                                                    ({{ $address->province->code }})</span>
                                                            </li>
                                                        </ul>
                                                    @else
                                                        <div class="text-center py-4">
                                                            <i class="ri-map-pin-line fs-1 text-muted mb-3"></i>
                                                            <p class="text-muted">Chưa có thông tin địa chỉ</p>
                                                        </div>
                                                    @endif

                                                    <div class="mt-4">
                                                        <button class="btn btn-primary" data-bs-toggle="modal"
                                                            data-bs-target="#editAddressModal">
                                                            <i class="ti ti-edit me-1"></i>
                                                            {{ $address ? 'Cập Nhật' : 'Thêm' }} Địa Chỉ
                                                        </button>
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
        </div>
        <!--End::row-1 -->

    </div>

    {{-- Modal cập nhật thông tin cá nhân --}}
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('backend.club.my-profile.update') }}"
                    enctype="multipart/form-data">
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
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Tên Thành Viên <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ $user->name }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ $user->email }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Giới Tính</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="">Chọn giới tính</option>
                                    <option value="male" @if ($user->gender == 'male') selected @endif>Nam</option>
                                    <option value="female" @if ($user->gender == 'female') selected @endif>Nữ</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="birthday" class="form-label">Ngày Sinh</label>
                                <input type="date" class="form-control" id="birthday" name="birthday"
                                    value="{{ $user->birthday }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="identity_card_code" class="form-label">CCCD/CMND</label>
                                <input type="text" class="form-control" id="identity_card_code"
                                    name="identity_card_code" value="{{ $user->identity_card_code }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Số Điện Thoại</label>
                                <input type="text" class="form-control" id="phone" name="phone"
                                    value="{{ $user->phone }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="student_code" class="form-label">Mã Sinh Viên</label>
                                <input type="text" class="form-control" id="student_code" name="student_code"
                                    value="{{ $user->student_code }}">
                            </div>
                            <div class="col-md-6 mb-3">
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
                            <div class="col-md-6 mb-3">
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
                            <div class="col-md-6 mb-3">
                                <label for="role_id" class="form-label">Vai Trò </label>
                                <select class="form-select" id="role_id" name="role_id" disabled>
                                    <option value="">Chọn vai trò</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            @if ($user->role_id == $role->id) selected @endif>
                                            {{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Trong modal cập nhật thông tin cá nhân, sau trường email hoặc trước nút submit --}}
                            @if (isset($clubs) && $clubs->count() > 1)
                                <div class="mb-3">
                                    <label for="default_club_id" class="form-label">Câu lạc bộ mặc định</label>
                                    <select name="default_club_id" id="default_club_id" class="form-select">
                                        @foreach ($clubs as $club)
                                            <option value="{{ $club->id }}"
                                                @if ($user->default_club_id == $club->id) selected @endif>{{ $club->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal cập nhật thông tin ngân hàng --}}
    <div class="modal fade" id="editBankAccountModal" tabindex="-1" aria-labelledby="editBankAccountModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('backend.club.my-profile.update-bank-account') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editBankAccountModalLabel">Cập nhật thông tin ngân hàng</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Bank Selection -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Chọn Ngân Hàng <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="bank-select" name="bank_name" required>
                                    <option value="">-- Chọn ngân hàng --</option>
                                </select>
                            </div>
                        </div>

                        <!-- Bank Information (Auto-filled) -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Tên Ngân Hàng <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="bank_name" id="bank_name" class="form-control"
                                    value="{{ old('bank_name', $bankAccount->bank_name ?? '') }}" readonly required>
                                @error('bank_name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Mã Ngân Hàng <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="bank_code" id="bank_code" class="form-control"
                                    value="{{ old('bank_code', $bankAccount->bank_code ?? '') }}" readonly required>
                                @error('bank_code')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">BIN <span class="text-danger">*</span></label>
                                <input type="text" name="bank_bin" id="bank_bin" class="form-control"
                                    value="{{ old('bank_bin', $bankAccount->bank_bin ?? '') }}" readonly required>
                                @error('bank_bin')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Tên Viết Tắt <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="bank_short_name" id="bank_short_name" class="form-control"
                                    value="{{ old('bank_short_name', $bankAccount->bank_short_name ?? '') }}" readonly
                                    required>
                                @error('bank_short_name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Swift Code <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="bank_swift_code" id="bank_swift_code" class="form-control"
                                    value="{{ old('bank_swift_code', $bankAccount->bank_swift_code ?? '') }}" readonly
                                    required>
                                @error('bank_swift_code')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>

                        <!-- Account Information -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Tên Chủ Tài Khoản <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="bank_account_name" id="bank_account_name"
                                    class="form-control"
                                    value="{{ old('bank_account_name', $bankAccount->bank_account_name ?? '') }}"
                                    placeholder="Nhập tên chủ tài khoản" required>
                                @error('bank_account_name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Số Tài Khoản <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="bank_account_number" id="bank_account_number"
                                    class="form-control"
                                    value="{{ old('bank_account_number', $bankAccount->bank_account_number ?? '') }}"
                                    placeholder="Nhập số tài khoản" required>
                                @error('bank_account_number')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal cập nhật địa chỉ --}}
    <div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('backend.club.my-profile.update-address') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editAddressModalLabel">Cập nhật địa chỉ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="province_id" class="form-label fw-semibold">Tỉnh/Thành <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="province_id" name="province_id" required>
                                    <option value="">-- Chọn tỉnh/thành --</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province->id }}"
                                            @if ($address && $address->province_id == $province->id) selected @endif>
                                            {{ $province->name }} ({{ $province->code }})</option>
                                    @endforeach
                                </select>
                                @error('province_id')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="commune_id" class="form-label fw-semibold">Xã/Phường <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="commune_id" name="commune_id" required>
                                    <option value="">-- Chọn xã/phường --</option>
                                    @if ($address && $address->commune)
                                        <option value="{{ $address->commune->id }}" selected>
                                            {{ $address->commune->name }} ({{ $address->commune->code }})</option>
                                    @endif
                                </select>
                                @error('commune_id')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="address" class="form-label fw-semibold">Địa Chỉ Chi Tiết <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="address" name="address" rows="3"
                                    placeholder="Nhập địa chỉ chi tiết (số nhà, đường, phố...)" required>{{ old('address', $address->address ?? '') }}</textarea>
                                @error('address')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bank Settings JavaScript -->
    <script src="{{ asset('assets/js/bank-settings.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabKey = 'myProfileActiveTab';
            const tabTriggerList = [].slice.call(document.querySelectorAll(
                '#myTab button[data-bs-toggle="tab"]'));

            // Tab titles mapping
            const tabTitles = {
                'info-tab': 'Thông Tin Cá Nhân',
                'security-tab': 'Bảo Mật',
                'bank-tab': 'Thông Tin Ngân Hàng',
                'address-tab': 'Địa Chỉ'
            };

            // Function to update tab title
            function updateTabTitle(tabId) {
                const titleElement = document.querySelector('.page-title');
                if (titleElement && tabTitles[tabId]) {
                    titleElement.textContent = tabTitles[tabId];
                }
            }

            const savedTabId = localStorage.getItem(tabKey);
            if (savedTabId) {
                const savedTab = document.querySelector(`#myTab button#${savedTabId}`);
                if (savedTab) {
                    // Remove active class from all tabs
                    tabTriggerList.forEach(tab => {
                        tab.classList.remove('active');
                    });

                    // Remove active class from all tab panes
                    document.querySelectorAll('.tab-pane').forEach(pane => {
                        pane.classList.remove('show', 'active');
                    });

                    // Add active class to saved tab
                    savedTab.classList.add('active');

                    // Show saved tab pane
                    const targetPane = document.querySelector(savedTab.getAttribute('data-bs-target'));
                    if (targetPane) {
                        targetPane.classList.add('show', 'active');
                    }

                    // Update title
                    updateTabTitle(savedTabId);
                }
            }

            tabTriggerList.forEach(function(tabTriggerEl) {
                tabTriggerEl.addEventListener('shown.bs.tab', function(event) {
                    localStorage.setItem(tabKey, event.target.id);
                    updateTabTitle(event.target.id);
                });
            });

            // Xử lý chọn tỉnh và load xã
            const provinceSelect = document.getElementById('province_id');
            const communeSelect = document.getElementById('commune_id');

            if (provinceSelect) {
                provinceSelect.addEventListener('change', function() {
                    const provinceId = this.value;

                    // Reset commune select
                    communeSelect.innerHTML = '<option value="">-- Chọn xã/phường --</option>';

                    if (provinceId) {
                        // Load communes via AJAX
                        fetch(
                                `{{ route('backend.club.my-profile.get-communes') }}?province_id=${provinceId}`
                            )
                            .then(response => response.json())
                            .then(communes => {
                                communes.forEach(commune => {
                                    const option = document.createElement('option');
                                    option.value = commune.id;
                                    option.textContent = `${commune.name} (${commune.code})`;
                                    communeSelect.appendChild(option);
                                });
                            })
                            .catch(error => {
                                console.error('Error loading communes:', error);
                            });
                    }
                });
            }
        });
    </script>
@endsection
