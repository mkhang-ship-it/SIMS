@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Thành Viên</h1>

            <!-- Nút trên desktop -->
            @if (auth()->user()->hasPermissionForModuleCode('member_management', 'create', 'branch'))
                <div class="d-none d-md-block">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                        <i class="ti ti-plus"></i>
                        Thêm Mới
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
                    @if (auth()->user()->hasPermissionForModuleCode('member_management', 'create', 'branch'))
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                                <i class="ti ti-plus"></i> Thêm Thành Viên
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
                                        <th style="text-align: center !important;">Trạng Thái</th>
                                        <th style="text-align: center !important;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Updated: Hiển thị thành viên theo bảng UserClubRole, truy cập thông tin user qua $member->user, role qua $member->role, status qua $member->status --}}
                                    @foreach ($members as $index => $member)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
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
                                                <div class="gap-2 fs-15">
                                                    @if (auth()->user()->hasPermissionForModuleCode('member_management', 'edit', 'branch'))
                                                        <button class="btn btn-icon btn-sm btn-info-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editMemberModal{{ $member->user->id }}"
                                                            title="Chỉnh sửa">
                                                            <i class="ri-edit-line"></i>
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
                                        <!-- Edit Member Modal -->
                                        @if (auth()->user()->hasPermissionForModuleCode('member_management', 'edit', 'branch'))
                                            <div class="modal fade" id="editMemberModal{{ $member->user->id }}"
                                                tabindex="-1"
                                                aria-labelledby="editMemberModalLabel{{ $member->user->id }}"
                                                aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                                <div
                                                    class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="editMemberModalLabel{{ $member->user->id }}">Chỉnh Sửa
                                                                Thành
                                                                Viên
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <form
                                                            action="{{ route('backend.club.members.update', $member->user->id) }}"
                                                            method="POST" id="editMemberForm{{ $member->user->id }}"
                                                            enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="edit_name{{ $member->user->id }}"
                                                                                class="form-label">Tên Thành Viên <span
                                                                                    class="text-danger">*</span></label>
                                                                            <input type="text" class="form-control"
                                                                                id="edit_name{{ $member->user->id }}"
                                                                                name="name"
                                                                                value="{{ $member->user->name }}" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="edit_email{{ $member->user->id }}"
                                                                                class="form-label">Email <span
                                                                                    class="text-danger">*</span></label>
                                                                            <input type="email" class="form-control"
                                                                                id="edit_email{{ $member->user->id }}"
                                                                                name="email"
                                                                                value="{{ $member->user->email }}"
                                                                                required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- Thông tin cá nhân bổ sung -->
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label
                                                                                for="edit_gender{{ $member->user->id }}"
                                                                                class="form-label">Giới Tính</label>
                                                                            <select class="form-select"
                                                                                id="edit_gender{{ $member->user->id }}"
                                                                                name="gender">
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
                                                                            <label
                                                                                for="edit_birthday{{ $member->user->id }}"
                                                                                class="form-label">Ngày Sinh</label>
                                                                            <input type="date" class="form-control"
                                                                                id="edit_birthday{{ $member->user->id }}"
                                                                                name="birthday"
                                                                                value="{{ $member->user->birthday }}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label
                                                                                for="edit_identity_card_code{{ $member->user->id }}"
                                                                                class="form-label">CCCD/CMND</label>
                                                                            <input type="text" class="form-control"
                                                                                id="edit_identity_card_code{{ $member->user->id }}"
                                                                                name="identity_card_code"
                                                                                value="{{ $member->user->identity_card_code }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="edit_phone{{ $member->user->id }}"
                                                                                class="form-label">Số Điện Thoại</label>
                                                                            <input type="text" class="form-control"
                                                                                id="edit_phone{{ $member->user->id }}"
                                                                                name="phone"
                                                                                value="{{ $member->user->phone }}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- Thông tin học tập -->
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label
                                                                                for="edit_student_code{{ $member->user->id }}"
                                                                                class="form-label">Mã Sinh Viên</label>
                                                                            <input type="text" class="form-control"
                                                                                id="edit_student_code{{ $member->user->id }}"
                                                                                name="student_code"
                                                                                value="{{ $member->user->student_code }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label
                                                                                for="edit_major_id{{ $member->user->id }}"
                                                                                class="form-label">Chuyên Ngành</label>
                                                                            <select class="form-select"
                                                                                id="edit_major_id{{ $member->user->id }}"
                                                                                name="major_id">
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
                                                                            <label
                                                                                for="edit_course_id{{ $member->user->id }}"
                                                                                class="form-label">Khóa Học</label>
                                                                            <select class="form-select"
                                                                                id="edit_course_id{{ $member->user->id }}"
                                                                                name="course_id">
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
                                                                            <label
                                                                                for="edit_avatar{{ $member->user->id }}"
                                                                                class="form-label">Avatar <small
                                                                                    class="text-muted">Để trống nếu không
                                                                                    muốn thay đổi.</small></label>

                                                                            <input type="file" class="form-control"
                                                                                id="edit_avatar{{ $member->user->id }}"
                                                                                name="avatar"
                                                                                accept="image/jpeg,image/png,image/jpg,image/gif">
                                                                            <small class="text-muted">Tối đa 2MB. Định
                                                                                dạng:
                                                                                JPG, PNG, GIF</small>

                                                                            @if ($member->user->avatar)
                                                                                <div class="mb-2">
                                                                                    <img src="{{ asset('storage/' . $member->user->avatar) }}"
                                                                                        alt="Current avatar"
                                                                                        class="img-thumbnail"
                                                                                        style="width: 80px; height: 80px; object-fit: cover;">
                                                                                    <small
                                                                                        class="text-muted d-block">Avatar
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
                                                                            <label
                                                                                for="edit_role_id{{ $member->user->id }}"
                                                                                class="form-label">Vai Trò <span
                                                                                    class="text-danger">*</span></label>
                                                                            <select class="form-select"
                                                                                id="edit_role_id{{ $member->user->id }}"
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
                                                                            <label
                                                                                for="edit_status{{ $member->user->id }}"
                                                                                class="form-label">Trạng Thái <span
                                                                                    class="text-danger">*</span></label>
                                                                            <select class="form-select"
                                                                                id="edit_status{{ $member->user->id }}"
                                                                                name="status" required>
                                                                                <option value="">Chọn trạng thái
                                                                                </option>
                                                                                <option value="active"
                                                                                    {{ $member->user->status == 'active' ? 'selected' : '' }}>
                                                                                    Hoạt Động</option>
                                                                                <option value="inactive"
                                                                                    {{ $member->user->status == 'inactive' ? 'selected' : '' }}>
                                                                                    Tạm Ngưng</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="edit_password{{ $member->user->id }}"
                                                                        class="form-label">Mật Khẩu Mới
                                                                        <small class="text-muted">Để trống nếu không muốn
                                                                            thay
                                                                            đổi. Tối thiểu 8 ký tự</small>
                                                                    </label>
                                                                    <input type="password" class="form-control"
                                                                        id="edit_password{{ $member->user->id }}"
                                                                        name="password">

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
                                        @if (auth()->user()->hasPermissionForModuleCode('member_management', 'delete', 'branch'))
                                            <div class="modal fade" id="deleteMemberModal{{ $member->user->id }}"
                                                tabindex="-1"
                                                aria-labelledby="deleteMemberModalLabel{{ $member->user->id }}"
                                                aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title text-danger"
                                                                id="deleteMemberModalLabel{{ $member->user->id }}">
                                                                Xác Nhận Xóa</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="text-center">
                                                                <p class="mb-2">Bạn có chắc chắn muốn xóa thành viên
                                                                    <span
                                                                        class="fw-bold text-danger">{{ $member->user->name }}</span>
                                                                    không?
                                                                </p>
                                                                <p class="text-muted small">Email:
                                                                    {{ $member->user->email }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <form
                                                            action="{{ route('backend.club.members.destroy', $member->user->id) }}"
                                                            method="POST" id="deleteMemberForm{{ $member->user->id }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Hủy</button>
                                                                <button type="submit" class="btn btn-danger">Xóa</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End::row-1 -->

    </div>

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
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_name" class="form-label">Tên Thành Viên <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="add_name" name="name"
                                            required value="{{ old('name') }}">
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
                                        <label for="add_gender" class="form-label">Giới Tính</label>
                                        <select class="form-select" id="add_gender" name="gender">
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
                                        <label for="add_birthday" class="form-label">Ngày Sinh</label>
                                        <input type="date" class="form-control" id="add_birthday" name="birthday"
                                            value="{{ old('birthday') }}">
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
                                            name="identity_card_code" value="{{ old('identity_card_code') }}">
                                        @error('identity_card_code')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_phone" class="form-label">Số Điện Thoại</label>
                                        <input type="text" class="form-control" id="add_phone" name="phone"
                                            value="{{ old('phone') }}">
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
                                        <label for="add_student_code" class="form-label">Mã Sinh Viên</label>
                                        <input type="text" class="form-control" id="add_student_code"
                                            name="student_code" value="{{ old('student_code') }}">
                                        @error('student_code')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_major_id" class="form-label">Chuyên Ngành</label>
                                        <select class="form-select" id="add_major_id" name="major_id">
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
                                        <label for="add_course_id" class="form-label">Khóa Học</label>
                                        <select class="form-select" id="add_course_id" name="course_id">
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
                                            accept="image/jpeg,image/png,image/jpg,image/gif">
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
                                        <select class="form-select" id="add_role_id" name="role_id" required>
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
                                        <select class="form-select" id="add_status" name="status" required>
                                            <option value="">Chọn trạng thái</option>
                                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hoạt
                                                Động
                                            </option>
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
                            <div class="mb-3">
                                <label for="add_password" class="form-label">Mật Khẩu <span class="text-danger">*</span>
                                    <small class="text-muted">Tối thiểu 8 ký tự</small>
                                </label>
                                <input type="password" class="form-control" id="add_password" name="password" required>

                                @error('password')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
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
@endsection
