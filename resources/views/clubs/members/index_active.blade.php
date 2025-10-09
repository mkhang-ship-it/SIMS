@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Thành Viên</h1>

            <!-- Nút trên desktop -->
            <div class="d-none d-md-block">
                @if (auth()->user()->hasPermissionForModuleCode('member_management', 'view', 'branch'))
                    <button class="btn btn-secondary me-2" onclick="exportMembers()">
                        <i class="ti ti-download"></i>
                        Xuất Excel
                    </button>
                @endif
                @if (auth()->user()->hasPermissionForModuleCode('member_management', 'create', 'branch'))
                    <button class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#importMemberModal">
                        <i class="ti ti-file-import"></i>
                        Import Excel
                    </button>
                    <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                        <i class="ti ti-plus"></i>
                        Thêm Mới
                    </button>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#assignToClubModal">
                        <i class="ti ti-user-plus"></i>
                        Gán Thành Viên
                    </button>
                @endif
            </div>
            <!-- Dropdown trên mobile -->
            <div class="dropdown d-md-none">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Hành động
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @if (auth()->user()->hasPermissionForModuleCode('member_management', 'view', 'branch'))
                        <li>
                            <a class="dropdown-item" href="#" onclick="exportMembers()">
                                <i class="ti ti-download"></i> Xuất Excel
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                    @endif
                    @if (auth()->user()->hasPermissionForModuleCode('member_management', 'create', 'branch'))
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                data-bs-target="#importMemberModal">
                                <i class="ti ti-file-import"></i> Import Excel
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                                <i class="ti ti-plus"></i> Thêm Thành Viên
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                data-bs-target="#assignToClubModal">
                                <i class="ti ti-user-plus"></i> Gán Thành Viên
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="table-responsive mb-4">
                            <table class="table text-nowrap table-bordered" id="data-table">
                                <thead>
                                    <tr>
                                        <th style="text-align: center !important;">STT</th>
                                        <th style="text-align: center !important;">Avatar</th>
                                        <th>Tên Thành Viên</th>
                                        <th>Email/Số Điện Thoại</th>
                                        <th style="text-align: center !important;">Chuyên Ngành</th>
                                        <th style="text-align: center !important;">Khóa Học</th>
                                        <th style="text-align: center !important;">Vai Trò</th>
                                        <th style="text-align: center !important;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Updated: Hiển thị thành viên theo bảng UserClubRole, truy cập thông tin user qua $member->user, role qua $member->role, status qua $member->status --}}
                                    @foreach ($members as $member)
                                        <tr>
                                            <td class="text-center">
                                                {{ ($members->currentPage() - 1) * $members->perPage() + $loop->iteration }}
                                            </td>
                                            <td class="text-center">
                                                <img src="{{ !empty($member->user->avatar) ? asset('storage/' . $member->user->avatar) : asset('assets/images/avatar_example.png') }}"
                                                    alt="{{ $member->user->name }}" class="avatar rounded-circle"
                                                    style="width: 40px; height: 40px; object-fit: cover;">
                                            </td>
                                            <td class="fw-semibold">{{ $member->user->name }} <br>
                                                <span class="text-muted small">{{ $member->user->student_code }}</span>
                                            </td>
                                            <td>{{ $member->user->email }} <br>
                                                <span class="text-muted small">{{ $member->user->phone }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if ($member->user->major)
                                                    <span class="badge bg-secondary">{{ $member->user->major->name }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($member->user->course)
                                                    <span class="badge bg-primary">{{ $member->user->course->name }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($member->role)
                                                    <span class="badge bg-info">{{ $member->role->name }}</span>
                                                @else
                                                    <span class="badge bg-secondary">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="gap-2 fs-15">
                                                    @if (auth()->user()->hasPermissionForModuleCode('member_management', 'edit', 'branch'))
                                                        <button class="btn btn-icon btn-sm btn-info-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editMemberModal{{ $member->user->id }}"
                                                            title="Chỉnh sửa">
                                                            <i class="ri-edit-line"></i>
                                                        </button>
                                                    @endif

                                                    @php
                                                        $canImpersonate =
                                                            !\App\Http\Controllers\ImpersonationController::isImpersonating() &&
                                                            $member->user->id !== auth()->user()->id;
                                                        $currentUserRole = auth()
                                                            ->user()
                                                            ->userClubRoles()
                                                            ->where('club_id', session('club_id'))
                                                            ->with('role')
                                                            ->first();
                                                        $isClubAdmin =
                                                            $currentUserRole &&
                                                            $currentUserRole->role &&
                                                            $currentUserRole->role->is_admin;
                                                    @endphp

                                                    @if ($canImpersonate && $isClubAdmin && $member->role && !$member->role->is_admin)
                                                        <button class="btn btn-icon btn-sm btn-warning-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#impersonateModal{{ $member->user->id }}"
                                                            title="Đăng nhập mạo danh">
                                                            <i class="ri-user-shared-line"></i>
                                                        </button>
                                                    @endif
                                                    @if ($member->role && $member->role->is_admin == false)
                                                        @if (auth()->user()->hasPermissionForModuleCode('member_management', 'delete', 'branch'))
                                                            <button class="btn btn-icon btn-sm btn-danger-light"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#deleteMemberModal{{ $member->user->id }}"
                                                                title="Xóa">
                                                                <i class="ri-delete-bin-line"></i>
                                                            </button>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination Links -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Hiển thị {{ $members->firstItem() ?? 0 }} đến {{ $members->lastItem() ?? 0 }}
                                trong {{ number_format($members->total()) }} bản ghi
                            </div>
                            <div class="d-flex justify-content-center">
                                {{ $members->links('vendor.pagination.bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End::row-1 -->

    </div>

    @foreach ($members as $member)
        <!-- Edit Member Modal -->
        @if (auth()->user()->hasPermissionForModuleCode('member_management', 'edit', 'branch'))
            <div class="modal fade" id="editMemberModal{{ $member->user->id }}" tabindex="-1"
                aria-labelledby="editMemberModalLabel{{ $member->user->id }}" aria-hidden="true"
                data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editMemberModalLabel{{ $member->user->id }}">Chỉnh Sửa
                                Thành
                                Viên
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form action="{{ route('backend.club.members.update', $member->user->id) }}" method="POST"
                            id="editMemberForm{{ $member->user->id }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_name{{ $member->user->id }}" class="form-label">Tên Thành
                                                Viên <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control"
                                                id="edit_name{{ $member->user->id }}" name="name"
                                                value="{{ $member->user->name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_email{{ $member->user->id }}" class="form-label">Email <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" class="form-control"
                                                id="edit_email{{ $member->user->id }}" name="email"
                                                value="{{ $member->user->email }}" required>
                                        </div>
                                    </div>
                                </div>
                                <!-- Thông tin cá nhân bổ sung -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_gender{{ $member->user->id }}" class="form-label">Giới Tính
                                                <span class="text-danger">*</span></label>
                                            <select class="form-select" id="edit_gender{{ $member->user->id }}"
                                                name="gender" required>
                                                <option value="">Chọn giới tính
                                                </option>
                                                <option value="male"
                                                    {{ $member->user->gender == 'male' ? 'selected' : '' }}>
                                                    Nam</option>
                                                <option value="female"
                                                    {{ $member->user->gender == 'female' ? 'selected' : '' }}>
                                                    Nữ</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_birthday{{ $member->user->id }}" class="form-label">Ngày
                                                Sinh <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control"
                                                id="edit_birthday{{ $member->user->id }}" name="birthday"
                                                value="{{ $member->user->birthday ? $member->user->birthday->format('Y-m-d') : '' }}"
                                                required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_identity_card_code{{ $member->user->id }}"
                                                class="form-label">CCCD/CMND</label>
                                            <input type="text" class="form-control"
                                                id="edit_identity_card_code{{ $member->user->id }}"
                                                name="identity_card_code"
                                                value="{{ $member->user->identity_card_code }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_phone{{ $member->user->id }}" class="form-label">Số Điện
                                                Thoại <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control"
                                                id="edit_phone{{ $member->user->id }}" name="phone"
                                                value="{{ $member->user->phone }}" required>
                                        </div>
                                    </div>
                                </div>
                                <!-- Thông tin học tập -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_student_code{{ $member->user->id }}" class="form-label">Mã
                                                Sinh Viên <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control"
                                                id="edit_student_code{{ $member->user->id }}" name="student_code"
                                                value="{{ $member->user->student_code }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_major_id{{ $member->user->id }}" class="form-label">Chuyên
                                                Ngành <span class="text-danger">*</span></label>
                                            <select class="form-select" id="edit_major_id{{ $member->user->id }}"
                                                name="major_id" required>
                                                <option value="">Chọn chuyên ngành
                                                </option>
                                                @foreach ($majors as $major)
                                                    <option value="{{ $major->id }}"
                                                        {{ $member->user->major_id == $major->id ? 'selected' : '' }}>
                                                        {{ $major->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_course_id{{ $member->user->id }}" class="form-label">Khóa
                                                Học <span class="text-danger">*</span></label>
                                            <select class="form-select" id="edit_course_id{{ $member->user->id }}"
                                                name="course_id" required>
                                                <option value="">Chọn khóa học
                                                </option>
                                                @foreach ($courses as $course)
                                                    <option value="{{ $course->id }}"
                                                        {{ $member->user->course_id == $course->id ? 'selected' : '' }}>
                                                        {{ $course->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_avatar{{ $member->user->id }}" class="form-label">Avatar
                                                <small class="text-muted">Để trống nếu không
                                                    muốn thay đổi.</small></label>

                                            <input type="file" class="form-control"
                                                id="edit_avatar{{ $member->user->id }}" name="avatar"
                                                accept="image/jpeg,image/png,image/jpg,image/gif">
                                            <small class="text-muted">Tối đa 2MB. Định
                                                dạng:
                                                JPG, PNG, GIF</small>

                                            @if ($member->user->avatar)
                                                <div class="mb-2">
                                                    <img src="{{ asset('storage/' . $member->user->avatar) }}"
                                                        alt="Current avatar" class="img-thumbnail"
                                                        style="width: 80px; height: 80px; object-fit: cover;">
                                                    <small class="text-muted d-block">Avatar
                                                        hiện tại</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <!-- Vai trò và trạng thái -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_role_id{{ $member->user->id }}" class="form-label">Vai Trò
                                                <span class="text-danger">*</span></label>
                                            <select class="form-select" id="edit_role_id{{ $member->user->id }}"
                                                name="role_id" required>
                                                <option value="">Chọn vai trò
                                                </option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}"
                                                        {{ $member->role_id == $role->id ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_status{{ $member->user->id }}" class="form-label">Trạng Thái
                                                <span class="text-danger">*</span></label>
                                            <select class="form-select" id="edit_status{{ $member->user->id }}"
                                                name="status" required>
                                                <option value="">Chọn trạng thái
                                                </option>
                                                <option value="active"
                                                    {{ $member->user->status == 'active' ? 'selected' : '' }}>
                                                    Hoạt Động</option>
                                                <option value="pending"
                                                    {{ $member->user->status == 'pending' ? 'selected' : '' }}>
                                                    Chờ Phê Duyệt</option>
                                                <option value="inactive"
                                                    {{ $member->user->status == 'inactive' ? 'selected' : '' }}>
                                                    Tạm Ngưng</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_password{{ $member->user->id }}" class="form-label">Mật Khẩu Mới
                                        <small class="text-muted">Để trống nếu không muốn
                                            thay
                                            đổi. Tối thiểu 8 ký tự</small>
                                    </label>
                                    <input type="password" class="form-control"
                                        id="edit_password{{ $member->user->id }}" name="password">

                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-primary">Cập
                                    Nhật</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Delete Member Modal -->
        @if (auth()->user()->hasPermissionForModuleCode('member_management', 'delete', 'branch'))
            <div class="modal fade" id="deleteMemberModal{{ $member->user->id }}" tabindex="-1"
                aria-labelledby="deleteMemberModalLabel{{ $member->user->id }}" aria-hidden="true"
                data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-danger" id="deleteMemberModalLabel{{ $member->user->id }}">
                                Xác Nhận Xóa</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center">
                                <p class="mb-2">Bạn có chắc chắn muốn xóa thành viên
                                    <span class="fw-bold text-danger">{{ $member->user->name }}</span>
                                    khỏi câu lạc bộ không?
                                </p>
                                <p class="text-muted small">Email:
                                    {{ $member->user->email }}
                                </p>
                                <div class="alert alert-warning mt-3">
                                    <i class="ri-information-line me-2"></i>
                                    <strong>Lưu ý:</strong> Thành viên sẽ chỉ bị xóa khỏi
                                    câu lạc bộ này. Tài khoản của họ vẫn tồn tại trong hệ
                                    thống.
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('backend.club.members.destroy', $member->user->id) }}" method="POST"
                            id="deleteMemberForm{{ $member->user->id }}">
                            @csrf
                            @method('DELETE')
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-danger">Xóa</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Impersonate Modal -->
        @php
            $canImpersonate =
                !\App\Http\Controllers\ImpersonationController::isImpersonating() &&
                $member->user->id !== auth()->user()->id;
            $currentUserRole = auth()
                ->user()
                ->userClubRoles()
                ->where('club_id', session('club_id'))
                ->with('role')
                ->first();
            $isClubAdmin = $currentUserRole && $currentUserRole->role && $currentUserRole->role->is_admin;
        @endphp

        @if ($canImpersonate && $isClubAdmin && $member->role && !$member->role->is_admin)
            <div class="modal fade" id="impersonateModal{{ $member->user->id }}" tabindex="-1"
                aria-labelledby="impersonateModalLabel{{ $member->user->id }}" aria-hidden="true"
                data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-warning" id="impersonateModalLabel{{ $member->user->id }}">
                                <i class="ri-user-shared-line me-2"></i>
                                Xác Nhận Mạo Danh
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center">
                                <div class="avatar avatar-xl avatar-rounded bg-warning-transparent mb-3">
                                    <i class="ri-user-shared-line fs-2"></i>
                                </div>
                                <p class="mb-2">
                                    Bạn có chắc chắn muốn đăng nhập với tài khoản của
                                    <strong>{{ $member->user->name }}</strong>?
                                </p>
                                <p class="text-muted small mb-2">Email:
                                    {{ $member->user->email }}</p>
                                <div class="alert alert-warning mt-3">
                                    <i class="ri-information-line me-2"></i>
                                    <strong>Lưu ý:</strong> Bạn sẽ được chuyển đến phiên làm
                                    việc của thành viên này.
                                    Để quay lại tài khoản của mình, hãy sử dụng nút "Thoát
                                    mạo danh" trên thanh công cụ.
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <form action="{{ route('backend.impersonate.start', $member->user->id) }}" method="POST"
                                style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-warning">
                                    <i class="ri-user-shared-line me-2"></i>
                                    Mạo Danh
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <!-- Add Member Modal -->
    @if (auth()->user()->hasPermissionForModuleCode('member_management', 'create', 'branch'))
        <div class="modal fade" id="addMemberModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="addMemberModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addMemberModalLabel">Thêm Thành Viên Mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('backend.club.members.store') }}" method="POST" id="addMemberForm"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div id="email-exists-alert" class="alert alert-warning d-none" role="alert">
                                Thành viên với email này đã tồn tại, vui lòng gán thành viên vào câu lạc bộ thay vì tạo
                                tài khoản mới cho thành viên này.
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_name" class="form-label">Tên Thành Viên <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="add_name" name="name"
                                            required value="{{ old('name') }}" disabled>
                                        @error('name')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_email" class="form-label">Email <span
                                                class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="add_email" name="email"
                                            required value="{{ old('email') }}">
                                        @error('email')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- Thông tin cá nhân bổ sung -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_gender" class="form-label">Giới Tính <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="add_gender" name="gender" required disabled>
                                            <option value="">Chọn giới tính</option>
                                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Nam
                                            </option>
                                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Nữ
                                            </option>
                                        </select>
                                        @error('gender')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_birthday" class="form-label">Ngày Sinh <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="add_birthday" name="birthday"
                                            value="{{ old('birthday') }}" required disabled>
                                        @error('birthday')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_identity_card_code" class="form-label">CCCD/CMND</label>
                                        <input type="text" class="form-control" id="add_identity_card_code"
                                            name="identity_card_code" value="{{ old('identity_card_code') }}" disabled>
                                        @error('identity_card_code')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_phone" class="form-label">Số Điện Thoại <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="add_phone" name="phone"
                                            value="{{ old('phone') }}" required disabled>
                                        @error('phone')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- Thông tin học tập -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_student_code" class="form-label">Mã Sinh Viên <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="add_student_code"
                                            name="student_code" value="{{ old('student_code') }}" required disabled>
                                        @error('student_code')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_major_id" class="form-label">Chuyên Ngành <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="add_major_id" name="major_id" required disabled>
                                            <option value="">Chọn chuyên ngành</option>
                                            @foreach ($majors as $major)
                                                <option value="{{ $major->id }}"
                                                    {{ old('major_id') == $major->id ? 'selected' : '' }}>
                                                    {{ $major->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('major_id')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_course_id" class="form-label">Khóa Học <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="add_course_id" name="course_id" required
                                            disabled>
                                            <option value="">Chọn khóa học</option>
                                            @foreach ($courses as $course)
                                                <option value="{{ $course->id }}"
                                                    {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                    {{ $course->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('course_id')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_avatar" class="form-label">Avatar</label>
                                        <input type="file" class="form-control" id="add_avatar" name="avatar"
                                            accept="image/jpeg,image/png,image/jpg,image/gif" disabled>
                                        <small class="text-muted">Tối đa 2MB. Định dạng: JPG, PNG, GIF</small>
                                        @error('avatar')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- Vai trò và trạng thái -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_role_id" class="form-label">Vai Trò <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="add_role_id" name="role_id" required disabled>
                                            <option value="">Chọn vai trò</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}"
                                                    {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('role_id')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_status" class="form-label">Trạng Thái <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="add_status" name="status" required disabled>
                                            <option value="">Chọn trạng thái</option>
                                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hoạt
                                                Động
                                            </option>
                                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>
                                                Chờ Phê Duyệt</option>
                                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                                Tạm Ngưng
                                            </option>
                                        </select>
                                        @error('status')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_password" class="form-label">Mật Khẩu <span
                                                class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="add_password" name="password"
                                            required disabled>
                                        @error('password')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_password_confirmation" class="form-label">Xác Nhận Mật Khẩu <span
                                                class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="add_password_confirmation"
                                            name="password_confirmation" required disabled>
                                        @error('password_confirmation')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary">Thêm Mới</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Assign To Club Modal -->
    @if (auth()->user()->hasPermissionForModuleCode('member_management', 'create', 'branch'))
        <div class="modal fade" id="assignToClubModal" tabindex="-1" aria-labelledby="assignToClubModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignToClubModalLabel">
                            Gán Thành Viên Vào Câu Lạc Bộ
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('backend.club.members.assign-to-club') }}" method="POST"
                        id="assignToClubForm">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="assign_user_id" class="form-label">Thành Viên <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="assign_user_id" name="user_id" required>
                                    <option value="">Chọn thành viên</option>
                                    @foreach ($availableUsers as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="assign_role_id" class="form-label">Vai Trò <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="assign_role_id" name="role_id" required>
                                    <option value="">Chọn vai trò</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-success">
                                <i class="ti ti-user-plus me-1"></i>Gán Thành Viên
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Import Members Modal -->
    @if (auth()->user()->hasPermissionForModuleCode('member_management', 'create', 'branch'))
        <div class="modal fade" id="importMemberModal" tabindex="-1" aria-labelledby="importMemberModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('backend.club.members.import') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="importMemberModalLabel">
                                <i class="ti ti-file-import"></i>
                                Import Thành Viên từ Excel
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center mb-3">
                                <a href="{{ route('backend.club.members.export-template') }}" class="btn btn-info">
                                    <i class="ti ti-download"></i>
                                    Tải File Mẫu
                                </a>
                            </div>
                            <div class="mb-3">
                                <label for="import_file" class="form-label">
                                    Chọn File Excel <span class="text-danger">*</span>
                                </label>
                                <input type="file" class="form-control" id="import_file" name="import_file"
                                    accept=".xlsx,.xls,.csv" required>
                                <small class="form-text text-muted">
                                    Chỉ hỗ trợ file Excel (.xlsx, .xls) hoặc CSV (.csv).
                                    <br>Nếu tài khoản đã tồn tại, sẽ chỉ thêm vào câu lạc bộ.
                                </small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-success">
                                <i class="ti ti-upload"></i>
                                Import Dữ Liệu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Function to set form fields disabled/enabled
            function setAddMemberFieldsDisabled(disabled) {
                const fields = [
                    '#add_name', '#add_gender', '#add_birthday', '#add_identity_card_code',
                    '#add_phone', '#add_student_code', '#add_major_id', '#add_course_id',
                    '#add_avatar', '#add_role_id', '#add_status', '#add_password', '#add_password_confirmation'
                ];

                fields.forEach(field => {
                    $(field).prop('disabled', disabled);
                });

                // Disable submit button if email exists
                $('button[type="submit"]').prop('disabled', disabled);
            }

            // Email checking functionality
            $('#add_email').on('blur', function() {
                const email = $(this).val();
                if (email) {
                    $.get(`{{ route('backend.club.members.check-email') }}?email=${email}`, function(
                        data) {
                        if (data.exists) {
                            $('#add_email').addClass('is-invalid');
                            setAddMemberFieldsDisabled(true);
                            $('#email-exists-alert').removeClass('d-none');
                        } else {
                            $('#add_email').removeClass('is-invalid');
                            setAddMemberFieldsDisabled(false);
                            $('#email-exists-alert').addClass('d-none');
                        }
                    });
                } else {
                    setAddMemberFieldsDisabled(true);
                    $('#add_email').removeClass('is-invalid');
                    $('#email-exists-alert').addClass('d-none');
                }
            });

            // Reset form when modal is closed
            $('#addMemberModal').on('hidden.bs.modal', function() {
                $('#addMemberForm')[0].reset();
                $('#add_email').removeClass('is-invalid');
                $('#email-exists-alert').addClass('d-none');
                setAddMemberFieldsDisabled(false);
            });

            // Export members function
            window.exportMembers = function() {
                const url = '{{ route('backend.club.members.export') }}';
                window.open(url, '_self');
            };
        });
    </script>
@endsection
