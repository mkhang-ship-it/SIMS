@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Người Dùng</h1>

            <!-- Desktop actions -->
            @if (auth()->user()->hasPermissionForModuleCode('user_management', 'create', 'central'))
                <div class="d-none d-md-block">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="ti ti-plus"></i>
                        Thêm Người Dùng
                    </button>
                </div>
            @endif

            <!-- Mobile dropdown -->
            <div class="dropdown d-md-none">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Hành động
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @if (auth()->user()->hasPermissionForModuleCode('user_management', 'create', 'central'))
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                <i class="ti ti-plus"></i> Thêm Người Dùng
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

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
                                        <th>Địa chỉ Email</th>
                                        <th style="text-align: center !important;">Vai Trò</th>
                                        <th style="text-align: center !important;">Trạng Thái</th>
                                        <th style="text-align: center !important;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $index => $user)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="text-center">
                                                <img src="{{ !empty($user->avatar) ? asset('storage/' . $user->avatar) : asset('assets/images/avatar_example.png') }}"
                                                    alt="{{ $user->name }}" class="avatar rounded-circle"
                                                    style="width: 40px; height: 40px; object-fit: cover;">
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $user->name }}</div>
                                                <div class="text-muted small">{{ $user->email }}</div>
                                            </td>
                                            <td class="text-center">
                                                <div class="fw-semibold">{{ $user->role->name ?? '-' }}</div>
                                            </td>
                                            <td class="text-center">
                                                @switch($user->status)
                                                    @case('active')
                                                        <span class="badge bg-success">Hoạt Động</span>
                                                    @break

                                                    @case('inactive')
                                                        <span class="badge bg-warning">Tạm Ngưng</span>
                                                    @break

                                                    @default
                                                        <span class="badge bg-light text-dark">{{ $user->status }}</span>
                                                @endswitch
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex gap-2 justify-content-center">
                                                    @if (auth()->user()->hasPermissionForModuleCode('user_management', 'edit', 'central'))
                                                        <button class="btn btn-icon btn-sm btn-info-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editUserModal{{ $user->id }}"
                                                            title="Chỉnh sửa">
                                                            <i class="ri-edit-line"></i>
                                                        </button>
                                                    @endif
                                                    @if (auth()->user()->hasPermissionForModuleCode('user_management', 'delete', 'central'))
                                                        <button class="btn btn-icon btn-sm btn-danger-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteUserModal{{ $user->id }}"
                                                            title="Xóa">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Edit User Modal -->
                                        @if (auth()->user()->hasPermissionForModuleCode('user_management', 'edit', 'central'))
                                            <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1"
                                                aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true"
                                                data-bs-backdrop="static" data-bs-keyboard="false">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="editUserModalLabel{{ $user->id }}">
                                                                Chỉnh Sửa Người Dùng
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('backend.users.update', $user->id) }}"
                                                            method="POST" id="editUserForm{{ $user->id }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="edit_name{{ $user->id }}"
                                                                        class="form-label">Họ và Tên <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control"
                                                                        id="edit_name{{ $user->id }}" name="name"
                                                                        value="{{ $user->name }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="edit_email{{ $user->id }}"
                                                                        class="form-label">Email <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="email" class="form-control"
                                                                        id="edit_email{{ $user->id }}" name="email"
                                                                        value="{{ $user->email }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="edit_password{{ $user->id }}"
                                                                        class="form-label">Mật Khẩu Mới</label>
                                                                    <div class="input-group">
                                                                        <input type="password" class="form-control"
                                                                            id="edit_password{{ $user->id }}"
                                                                            name="password"
                                                                            placeholder="Để trống nếu không thay đổi">
                                                                        <button class="btn btn-outline-secondary"
                                                                            type="button"
                                                                            onclick="togglePasswordVisibility('edit_password{{ $user->id }}', 'editPasswordIcon{{ $user->id }}')">
                                                                            <i class="ti ti-eye"
                                                                                id="editPasswordIcon{{ $user->id }}"></i>
                                                                        </button>
                                                                        <div class="form-text">Mật khẩu phải có ít nhất 8
                                                                            ký tự.</div>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label
                                                                        for="edit_password_confirmation{{ $user->id }}"
                                                                        class="form-label">Xác Nhận Mật Khẩu Mới</label>
                                                                    <div class="input-group">
                                                                        <input type="password" class="form-control"
                                                                            id="edit_password_confirmation{{ $user->id }}"
                                                                            name="password_confirmation"
                                                                            placeholder="Để trống nếu không thay đổi">
                                                                        <button class="btn btn-outline-secondary"
                                                                            type="button"
                                                                            onclick="togglePasswordVisibility('edit_password_confirmation{{ $user->id }}', 'editPasswordConfirmationIcon{{ $user->id }}')">
                                                                            <i class="ti ti-eye"
                                                                                id="editPasswordConfirmationIcon{{ $user->id }}"></i>
                                                                        </button>
                                                                        <div class="form-text">Mật khẩu phải có ít nhất 8
                                                                            ký tự.</div>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label for="edit_status{{ $user->id }}"
                                                                        class="form-label">Trạng Thái <span
                                                                            class="text-danger">*</span></label>
                                                                    <select class="form-select"
                                                                        id="edit_status{{ $user->id }}"
                                                                        name="status" required>
                                                                        <option value="">Chọn trạng thái</option>
                                                                        <option value="active"
                                                                            {{ $user->status == 'active' ? 'selected' : '' }}>
                                                                            Hoạt Động</option>
                                                                        <option value="inactive"
                                                                            {{ $user->status == 'inactive' ? 'selected' : '' }}>
                                                                            Tạm Ngưng</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="edit_role_id{{ $user->id }}"
                                                                        class="form-label">Vai Trò <span
                                                                            class="text-danger">*</span></label>
                                                                    <select class="form-select"
                                                                        id="edit_role_id{{ $user->id }}"
                                                                        name="role_id" required>
                                                                        <option value="">Chọn vai trò</option>
                                                                        @foreach ($roles as $role)
                                                                            <option value="{{ $role->id }}"
                                                                                {{ $user->role_id == $role->id ? 'selected' : '' }}>
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
                                        @endif

                                        <!-- Delete User Modal -->
                                        @if (auth()->user()->hasPermissionForModuleCode('user_management', 'delete', 'central'))
                                            <div class="modal fade" id="deleteUserModal{{ $user->id }}"
                                                tabindex="-1" aria-labelledby="deleteUserModalLabel{{ $user->id }}"
                                                aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title text-danger"
                                                                id="deleteUserModalLabel{{ $user->id }}">
                                                                Xác Nhận Xóa</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="text-center">
                                                                <p class="mb-2">Bạn có chắc chắn muốn xóa người dùng
                                                                    <strong>{{ $user->name }}</strong>?
                                                                </p>
                                                                <p class="text-danger small">Hành động này sẽ xóa tất cả dữ
                                                                    liệu liên quan và không thể hoàn tác!</p>
                                                                <div class="alert alert-danger mt-3">
                                                                    <i class="ri-error-warning-line me-2"></i>
                                                                    <strong>Cảnh báo:</strong> Người dùng sẽ bị xóa hoàn
                                                                    toàn khỏi hệ thống.
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Hủy</button>
                                                            <form action="{{ route('backend.users.destroy', $user->id) }}"
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

    <!-- Add User Modal -->
    @if (auth()->user()->hasPermissionForModuleCode('user_management', 'create', 'central'))
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">
                            Thêm Người Dùng Mới
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('backend.users.store') }}" method="POST" id="addUserForm">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Họ và Tên <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mật Khẩu <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePasswordVisibility('password', 'passwordIcon')">
                                        <i class="ti ti-eye" id="passwordIcon"></i>
                                    </button>
                                </div>
                                <div class="form-text">Mật khẩu phải có ít nhất 8 ký tự.</div>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Xác Nhận Mật Khẩu <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" required>
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePasswordVisibility('password_confirmation', 'passwordConfirmationIcon')">
                                        <i class="ti ti-eye" id="passwordConfirmationIcon"></i>
                                    </button>
                                </div>
                                <div class="form-text">Mật khẩu phải có ít nhất 8 ký tự.</div>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Trạng Thái <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="">Chọn trạng thái</option>
                                    <option value="active">Hoạt Động</option>
                                    <option value="inactive">Tạm Ngưng</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="role_id" class="form-label">Vai Trò <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="role_id" name="role_id" required>
                                    <option value="">Chọn vai trò</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary">Thêm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <script>
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('ti-eye');
                icon.classList.add('ti-eye-off');
            } else {
                input.type = 'password';
                icon.classList.remove('ti-eye-off');
                icon.classList.add('ti-eye');
            }
        }
    </script>
@endsection
