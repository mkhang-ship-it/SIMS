{{-- Members index view - Created for admin member management with multi-club support --}}
{{-- Handles listing, creating, editing, and deleting members with modal-based interface --}}
{{-- Supports assigning existing users to clubs, updating member information, and removing members from clubs --}}
{{-- ĐÃ CẬP NHẬT: Modal Thêm Thành Viên - kiểm tra email tồn tại, disable/enable các trường tương ứng, hiển thị thông báo nếu email đã tồn tại --}}
{{-- ĐÃ CẬP NHẬT: Sửa lỗi nút thao tác câu lạc bộ bị trùng lặp - thay thế bằng dropdown menu để gọn gàng hơn --}}

@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Thành Viên</h1>

            <!-- Nút trên desktop -->
            @if (auth()->user()->hasPermissionForModuleCode('member_management', 'create', 'central'))
                <div class="d-none d-md-block">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                        <i class="ti ti-plus"></i>
                        Thêm Thành Viên
                    </button>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#assignToClubModal">
                        <i class="ti ti-user-plus"></i>
                        Thêm Thành Viên Vào Câu Lạc Bộ
                    </button>
                </div>
            @endif
            <!-- Dropdown trên mobile -->
            <div class="dropdown d-md-none">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Hành động
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @if (auth()->user()->hasPermissionForModuleCode('member_management', 'create', 'central'))
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                                <i class="ti ti-plus"></i> Thêm Thành Viên
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                data-bs-target="#assignToClubModal">
                                <i class="ti ti-user-plus"></i> Thêm Thành Viên Vào Câu Lạc Bộ
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
                                        <th style="text-align: center !important;">Ảnh</th>
                                        <th>Thông Tin Cá Nhân</th>
                                        <th style="text-align: center !important;">Thông Tin Học Tập</th>
                                        <th style="text-align: center !important;">Câu Lạc Bộ</th>
                                        <th style="text-align: center !important;">Trạng Thái</th>
                                        <th style="text-align: center !important;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($members as $index => $member)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="text-center">
                                                <img src="{{ !empty($member->avatar) ? asset('storage/' . $member->avatar) : asset('assets/images/avatar_example.png') }}"
                                                    alt="{{ $member->name }}" class="avatar rounded-circle"
                                                    style="width: 40px; height: 40px; object-fit: cover;">
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $member->name }}</div>
                                                <div class="text-muted small">{{ $member->email }}</div>
                                                @if ($member->phone)
                                                    <div class="text-muted small">{{ $member->phone }}</div>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($member->major)
                                                    <div class="fw-semibold">{{ $member->major->name }}</div>
                                                @endif
                                                @if ($member->course)
                                                    <div class="text-muted small">{{ $member->course->name }}</div>
                                                @endif
                                                @if ($member->student_code)
                                                    <div class="text-muted small">MSSV: {{ $member->student_code }}</div>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($member->userClubRoles->count() > 0)
                                                    @foreach ($member->userClubRoles as $userClubRole)
                                                        <div class="mb-1">
                                                            <span
                                                                class="badge bg-primary">{{ $userClubRole->club->name }}</span>
                                                            <br>
                                                            <small
                                                                class="text-muted">{{ $userClubRole->role->name }}</small>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">Chưa gán</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @switch($member->status)
                                                    @case('active')
                                                        <span class="badge bg-success">Hoạt Động</span>
                                                    @break

                                                    @case('inactive')
                                                        <span class="badge bg-warning">Tạm Ngưng</span>
                                                    @break

                                                    @default
                                                        <span class="badge bg-light text-dark">{{ $member->status }}</span>
                                                @endswitch
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex gap-2 justify-content-center">
                                                    @if (auth()->user()->hasPermissionForModuleCode('member_management', 'edit', 'central'))
                                                        <button class="btn btn-icon btn-sm btn-info-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editMemberModal{{ $member->id }}"
                                                            title="Chỉnh sửa">
                                                            <i class="ri-edit-line"></i>
                                                        </button>
                                                    @endif
                                                    @if ($member->userClubRoles->count() > 0)
                                                        <!-- Dropdown for club actions -->
                                                        <div class="dropdown">
                                                            <button
                                                                class="btn btn-icon btn-sm btn-warning-light dropdown-toggle"
                                                                type="button" data-bs-toggle="dropdown"
                                                                aria-expanded="false" title="Thao tác câu lạc bộ">
                                                                <i class="ri-user-settings-line"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                @foreach ($member->userClubRoles as $userClubRole)
                                                                    <li>
                                                                        <h6 class="dropdown-header">
                                                                            {{ $userClubRole->club->name }}</h6>
                                                                    </li>
                                                                    <li>
                                                                        <a class="dropdown-item" href="#"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#updateRoleModal{{ $userClubRole->id }}">
                                                                            <i class="ri-user-settings-line me-2"></i>
                                                                            Cập nhật vai trò
                                                                            ({{ $userClubRole->role->name }})
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a class="dropdown-item text-danger" href="#"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#removeFromClubModal{{ $userClubRole->id }}">
                                                                            <i class="ri-user-unfollow-line me-2"></i>
                                                                            Gỡ khỏi câu lạc bộ
                                                                        </a>
                                                                    </li>
                                                                    @if (!$loop->last)
                                                                        <li>
                                                                            <hr class="dropdown-divider">
                                                                        </li>
                                                                    @endif
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                    @if (auth()->user()->hasPermissionForModuleCode('member_management', 'delete', 'central'))
                                                        <button class="btn btn-icon btn-sm btn-danger-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteMemberModal{{ $member->id }}"
                                                            title="Xóa">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Edit Member Modal -->
                                        @if (auth()->user()->hasPermissionForModuleCode('member_management', 'edit', 'central'))
                                            <div class="modal fade" id="editMemberModal{{ $member->id }}" tabindex="-1"
                                                aria-labelledby="editMemberModalLabel{{ $member->id }}"
                                                aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="editMemberModalLabel{{ $member->id }}">
                                                                Chỉnh Sửa Thành Viên
                                                            </h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('backend.members.update', $member->id) }}"
                                                            method="POST" id="editMemberForm{{ $member->id }}"
                                                            enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="edit_name{{ $member->id }}"
                                                                                class="form-label">Họ và Tên <span
                                                                                    class="text-danger">*</span></label>
                                                                            <input type="text" class="form-control"
                                                                                id="edit_name{{ $member->id }}"
                                                                                name="name"
                                                                                value="{{ $member->name }}" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="edit_email{{ $member->id }}"
                                                                                class="form-label">Email <span
                                                                                    class="text-danger">*</span></label>
                                                                            <input type="email" class="form-control"
                                                                                id="edit_email{{ $member->id }}"
                                                                                name="email"
                                                                                value="{{ $member->email }}" required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="edit_phone{{ $member->id }}"
                                                                                class="form-label">Điện Thoại</label>
                                                                            <input type="text" class="form-control"
                                                                                id="edit_phone{{ $member->id }}"
                                                                                name="phone"
                                                                                value="{{ $member->phone }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="edit_gender{{ $member->id }}"
                                                                                class="form-label">Giới Tính</label>
                                                                            <select class="form-select"
                                                                                id="edit_gender{{ $member->id }}"
                                                                                name="gender">
                                                                                <option value="">Chọn giới tính
                                                                                </option>
                                                                                <option value="male"
                                                                                    {{ $member->gender == 'male' ? 'selected' : '' }}>
                                                                                    Nam</option>
                                                                                <option value="female"
                                                                                    {{ $member->gender == 'female' ? 'selected' : '' }}>
                                                                                    Nữ</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="edit_birthday{{ $member->id }}"
                                                                                class="form-label">Ngày Sinh</label>
                                                                            <input type="date" class="form-control"
                                                                                id="edit_birthday{{ $member->id }}"
                                                                                name="birthday"
                                                                                value="{{ $member->birthday }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label
                                                                                for="edit_identity_card_code{{ $member->id }}"
                                                                                class="form-label">CMND/CCCD</label>
                                                                            <input type="text" class="form-control"
                                                                                id="edit_identity_card_code{{ $member->id }}"
                                                                                name="identity_card_code"
                                                                                value="{{ $member->identity_card_code }}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label
                                                                                for="edit_student_code{{ $member->id }}"
                                                                                class="form-label">Mã Sinh Viên</label>
                                                                            <input type="text" class="form-control"
                                                                                id="edit_student_code{{ $member->id }}"
                                                                                name="student_code"
                                                                                value="{{ $member->student_code }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="edit_status{{ $member->id }}"
                                                                                class="form-label">Trạng Thái <span
                                                                                    class="text-danger">*</span></label>
                                                                            <select class="form-select"
                                                                                id="edit_status{{ $member->id }}"
                                                                                name="status" required>
                                                                                <option value="">Chọn trạng thái
                                                                                </option>
                                                                                <option value="active"
                                                                                    {{ $member->status == 'active' ? 'selected' : '' }}>
                                                                                    Hoạt Động</option>
                                                                                <option value="inactive"
                                                                                    {{ $member->status == 'inactive' ? 'selected' : '' }}>
                                                                                    Tạm Ngưng</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="edit_major_id{{ $member->id }}"
                                                                                class="form-label">Ngành Học</label>
                                                                            <select class="form-select"
                                                                                id="edit_major_id{{ $member->id }}"
                                                                                name="major_id">
                                                                                <option value="">Chọn ngành học
                                                                                </option>
                                                                                @foreach ($majors as $major)
                                                                                    <option value="{{ $major->id }}"
                                                                                        {{ $member->major_id == $major->id ? 'selected' : '' }}>
                                                                                        {{ $major->name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="edit_course_id{{ $member->id }}"
                                                                                class="form-label">Khóa Học</label>
                                                                            <select class="form-select"
                                                                                id="edit_course_id{{ $member->id }}"
                                                                                name="course_id">
                                                                                <option value="">Chọn khóa học
                                                                                </option>
                                                                                @foreach ($courses as $course)
                                                                                    <option value="{{ $course->id }}"
                                                                                        {{ $member->course_id == $course->id ? 'selected' : '' }}>
                                                                                        {{ $course->name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="edit_avatar{{ $member->id }}"
                                                                        class="form-label">Ảnh Đại Diện</label>
                                                                    <input type="file" class="form-control"
                                                                        id="edit_avatar{{ $member->id }}"
                                                                        name="avatar" accept="image/*">
                                                                    @if ($member->avatar)
                                                                        <small class="text-muted">Ảnh hiện tại:</small>
                                                                        <br>
                                                                        <img src="{{ asset('storage/' . $member->avatar) }}"
                                                                            alt="{{ $member->name }}"
                                                                            class="mt-2 rounded"
                                                                            style="width: 80px; height: 80px; object-fit: cover;">
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Hủy</button>
                                                                <button type="submit" class="btn btn-primary">Cập
                                                                    Nhật</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Delete Member Modal -->
                                        @if (auth()->user()->hasPermissionForModuleCode('member_management', 'delete', 'central'))
                                            <div class="modal fade" id="deleteMemberModal{{ $member->id }}"
                                                tabindex="-1"
                                                aria-labelledby="deleteMemberModalLabel{{ $member->id }}"
                                                aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title text-danger"
                                                                id="deleteMemberModalLabel{{ $member->id }}">
                                                                Xác Nhận Xóa</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="text-center">
                                                                <p class="mb-2">Bạn có chắc chắn muốn xóa thành viên
                                                                    <strong>{{ $member->name }}</strong>?
                                                                </p>
                                                                <p class="text-danger small">Hành động này sẽ xóa tất cả dữ
                                                                    liệu liên quan và không thể hoàn tác!</p>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Hủy</button>
                                                            <form
                                                                action="{{ route('backend.members.destroy', $member->id) }}"
                                                                method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Xóa</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Update Role Modals -->
                                        @foreach ($member->userClubRoles as $userClubRole)
                                            <div class="modal fade" id="updateRoleModal{{ $userClubRole->id }}"
                                                tabindex="-1"
                                                aria-labelledby="updateRoleModalLabel{{ $userClubRole->id }}"
                                                aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="updateRoleModalLabel{{ $userClubRole->id }}">
                                                                Cập Nhật Vai Trò
                                                            </h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('backend.members.update-club-role') }}"
                                                            method="POST">
                                                            @csrf
                                                            <input type="hidden" name="user_club_role_id"
                                                                value="{{ $userClubRole->id }}">
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Thành Viên</label>
                                                                    <input type="text" class="form-control"
                                                                        value="{{ $member->name }}" readonly>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Câu Lạc Bộ</label>
                                                                    <input type="text" class="form-control"
                                                                        value="{{ $userClubRole->club->name }}" readonly>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="update_role_id{{ $userClubRole->id }}"
                                                                        class="form-label">Vai Trò <span
                                                                            class="text-danger">*</span></label>
                                                                    <select class="form-select"
                                                                        id="update_role_id{{ $userClubRole->id }}"
                                                                        name="role_id" required>
                                                                        <option value="">Chọn vai trò</option>
                                                                        @foreach ($roles->where('club_id', $userClubRole->club_id) as $role)
                                                                            <option value="{{ $role->id }}"
                                                                                {{ $userClubRole->role_id == $role->id ? 'selected' : '' }}>
                                                                                {{ $role->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Hủy</button>
                                                                <button type="submit" class="btn btn-primary">Cập
                                                                    Nhật</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Remove From Club Modal -->
                                            <div class="modal fade" id="removeFromClubModal{{ $userClubRole->id }}"
                                                tabindex="-1"
                                                aria-labelledby="removeFromClubModalLabel{{ $userClubRole->id }}"
                                                aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title text-warning"
                                                                id="removeFromClubModalLabel{{ $userClubRole->id }}">
                                                                Gỡ Khỏi Câu Lạc Bộ</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="text-center">
                                                                <p class="mb-2">Bạn có chắc chắn muốn gỡ thành viên
                                                                    <strong>{{ $member->name }}</strong> khỏi câu lạc bộ
                                                                    <strong>{{ $userClubRole->club->name }}</strong>?
                                                                </p>
                                                                <p class="text-danger small">Hành động này sẽ xóa tất cả dữ
                                                                    liệu liên quan của thành viên trong câu lạc bộ này!</p>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Hủy</button>
                                                            <form action="{{ route('backend.members.remove-from-club') }}"
                                                                method="POST" style="display: inline;">
                                                                @csrf
                                                                <input type="hidden" name="user_club_role_id"
                                                                    value="{{ $userClubRole->id }}">
                                                                <button type="submit" class="btn btn-warning">Gỡ
                                                                    Khỏi</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- End::row-1 -->

    </div>

    <!-- Add Member Modal -->
    @if (auth()->user()->hasPermissionForModuleCode('member_management', 'create', 'central'))
        <div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addMemberModalLabel">
                            Thêm Thành Viên Mới
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('backend.members.store') }}" method="POST" id="addMemberForm"
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
                                        <label for="name" class="form-label">Họ và Tên <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            required disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <span
                                                class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Mật Khẩu <span
                                                class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="password" name="password"
                                            required disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">Xác Nhận Mật Khẩu <span
                                                class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" required disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Điện Thoại</label>
                                        <input type="text" class="form-control" id="phone" name="phone"
                                            disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="gender" class="form-label">Giới Tính</label>
                                        <select class="form-select" id="gender" name="gender" disabled>
                                            <option value="">Chọn giới tính</option>
                                            <option value="male">Nam</option>
                                            <option value="female">Nữ</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="birthday" class="form-label">Ngày Sinh</label>
                                        <input type="date" class="form-control" id="birthday" name="birthday"
                                            disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="identity_card_code" class="form-label">CMND/CCCD</label>
                                        <input type="text" class="form-control" id="identity_card_code"
                                            name="identity_card_code" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="student_code" class="form-label">Mã Sinh Viên</label>
                                        <input type="text" class="form-control" id="student_code" name="student_code"
                                            disabled required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Trạng Thái <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="status" name="status" required disabled>
                                            <option value="">Chọn trạng thái</option>
                                            <option value="active">Hoạt Động</option>
                                            <option value="inactive">Tạm Ngưng</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="major_id" class="form-label">Ngành Học</label>
                                        <select class="form-select" id="major_id" name="major_id" disabled>
                                            <option value="">Chọn ngành học</option>
                                            @foreach ($majors as $major)
                                                <option value="{{ $major->id }}">{{ $major->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="course_id" class="form-label">Khóa Học</label>
                                        <select class="form-select" id="course_id" name="course_id" disabled>
                                            <option value="">Chọn khóa học</option>
                                            @foreach ($courses as $course)
                                                <option value="{{ $course->id }}">{{ $course->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="avatar" class="form-label">Ảnh Đại Diện</label>
                                <input type="file" class="form-control" id="avatar" name="avatar"
                                    accept="image/*" disabled>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary" id="addMemberSubmitBtn" disabled>Thêm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Assign To Club Modal -->
    @if (auth()->user()->hasPermissionForModuleCode('member_management', 'create', 'central'))
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
                    <form action="{{ route('backend.members.assign-to-club') }}" method="POST" id="assignToClubForm">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="assign_user_id" class="form-label">Thành Viên <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="assign_user_id" name="user_id" required>
                                    <option value="">Chọn thành viên</option>
                                    @foreach ($members as $member)
                                        <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="assign_club_id" class="form-label">Câu Lạc Bộ <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="assign_club_id" name="club_id" required>
                                    <option value="">Chọn câu lạc bộ</option>
                                    @foreach ($clubs as $club)
                                        <option value="{{ $club->id }}">{{ $club->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="assign_role_id" class="form-label">Vai Trò <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="assign_role_id" name="role_id" required>
                                    <option value="">Chọn câu lạc bộ trước</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary">Gán</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        // Load roles when club is selected
        $('#assign_club_id').change(function() {
            const clubId = $(this).val();
            const roleSelect = $('#assign_role_id');

            roleSelect.html('<option value="">Đang tải...</option>');

            if (clubId) {
                $.get(`{{ route('backend.members.get-club-roles') }}?club_id=${clubId}`, function(data) {
                    roleSelect.html('<option value="">Chọn vai trò</option>');
                    data.forEach(function(role) {
                        roleSelect.append(`<option value="${role.id}">${role.name}</option>`);
                    });
                });
            } else {
                roleSelect.html('<option value="">Chọn câu lạc bộ trước</option>');
            }
        });

        // Check email uniqueness and enable/disable fields accordingly
        function setAddMemberFieldsDisabled(disabled) {
            $('#addMemberForm input:not([name="email"]), #addMemberForm select, #addMemberSubmitBtn').prop('disabled',
                disabled);
        }
        // Initially disable all fields except email
        setAddMemberFieldsDisabled(true);

        $('#email').on('blur', function() {
            const email = $(this).val();
            if (email) {
                $.get(`{{ route('backend.members.check-email') }}?email=${email}`, function(data) {
                    if (data.exists) {
                        $('#email').addClass('is-invalid');
                        setAddMemberFieldsDisabled(true);
                        $('#email-exists-alert').removeClass('d-none');
                    } else {
                        $('#email').removeClass('is-invalid');
                        setAddMemberFieldsDisabled(false);
                        $('#email-exists-alert').addClass('d-none');
                    }
                });
            } else {
                setAddMemberFieldsDisabled(true);
                $('#email').removeClass('is-invalid');
                $('#email-exists-alert').addClass('d-none');
            }
        });
    </script>
@endsection
