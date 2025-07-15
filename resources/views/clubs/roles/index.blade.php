{{-- Roles Index Page - Updated to use separate pages for create and edit instead of modals --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Vai Trò</h1>

            <!-- Nút trên desktop -->
            @if (auth()->user()->hasPermissionForModuleCode('role_management', 'create', 'branch'))
                <div class="d-none d-md-block">
                    <a href="{{ route('backend.club.roles.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus"></i>
                        Thêm Mới
                    </a>
                </div>
            @endif

            <!-- Dropdown trên mobile -->
            <div class="dropdown d-md-none">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Hành động
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @if (auth()->user()->hasPermissionForModuleCode('role_management', 'create', 'branch'))
                        <li>
                            <a class="dropdown-item" href="{{ route('backend.club.roles.create') }}">
                                <i class="ti ti-plus"></i> Thêm Mới
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
                                        <th>Tên Vai Trò</th>
                                        <th style="text-align: center !important;">Người Dùng</th>
                                        <th style="text-align: center !important;">Phân Quyền</th>
                                        <th style="text-align: center !important;">Trạng Thái</th>
                                        <th style="text-align: center !important;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($roles as $index => $role)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="fw-semibold">{{ $role->name }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ $userCounts[$role->id] ?? 0 }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-success">{{ $role->permissions_count ?? $role->permissions->count() }}</span>
                                            </td>
                                            <td class="text-center">
                                                @switch($role->status)
                                                    @case('active')
                                                        <span class="badge bg-success">Hoạt Động</span>
                                                    @break

                                                    @case('inactive')
                                                        <span class="badge bg-warning">Tạm Ngưng</span>
                                                    @break

                                                    @default
                                                        <span class="badge bg-light text-dark">{{ $role->status }}</span>
                                                @endswitch
                                            </td>
                                            <td class="text-center">
                                                <div class="gap-2 fs-15">

                                                    @if (!$role->is_admin)
                                                        {{-- Nếu không phải admin thì có thể xem xét sửa --}}
                                                        @if (auth()->user()->hasPermissionForModuleCode('role_management', 'edit', 'branch'))
                                                            <a href="{{ route('backend.club.roles.edit', $role->id) }}"
                                                                class="btn btn-icon btn-sm btn-info-light"
                                                                title="Chỉnh sửa">
                                                                <i class="ri-edit-line"></i>
                                                            </a>
                                                        @endif

                                                        {{-- Nếu không phải admin và không phải default thì mới được xóa --}}
                                                        @if (!$role->is_default && auth()->user()->hasPermissionForModuleCode('role_management', 'delete', 'branch'))
                                                            <button class="btn btn-icon btn-sm btn-danger-light"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#deleteRoleModal{{ $role->id }}"
                                                                title="Xóa">
                                                                <i class="ri-delete-bin-line"></i>
                                                            </button>
                                                        @endif
                                                    @endif

                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Delete Role Modal -->
                                        @if (auth()->user()->hasPermissionForModuleCode('role_management', 'delete', 'branch'))
                                            <div class="modal fade" id="deleteRoleModal{{ $role->id }}" tabindex="-1"
                                                aria-labelledby="deleteRoleModalLabel{{ $role->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="deleteRoleModalLabel{{ $role->id }}">
                                                                Xác Nhận Xóa
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Bạn có chắc chắn muốn xóa vai trò
                                                                <strong>{{ $role->name }}</strong>?
                                                            </p>
                                                            @if ($role->users->count() > 0)
                                                                <div class="alert alert-warning">
                                                                    <i class="ti ti-alert-triangle"></i>
                                                                    Vai trò này đã được gán cho
                                                                    {{ $role->users->count() }} người dùng. Không thể xóa!
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Hủy</button>
                                                            @if ($role->users->count() == 0)
                                                                <form
                                                                    action="{{ route('backend.club.roles.destroy', $role->id) }}"
                                                                    method="POST" style="display: inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-danger">Xóa</button>
                                                                </form>
                                                            @endif
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
@endsection
