{{-- Create Role Page - Separate page for creating new roles with permission assignment --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <div>
                <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Thêm Vai Trò Mới</h1>
            </div>
            <div>
                <a href="{{ route('backend.roles.index') }}" class="btn btn-secondary">
                    <i class="ti ti-arrow-left"></i>
                    Quay Lại
                </a>
            </div>
        </div>

        <!-- Start::row-1 -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            Thông Tin Vai Trò
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('backend.roles.store') }}" method="POST" id="createRoleForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Tên Vai Trò <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Trạng Thái <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status"
                                            name="status" required>
                                            <option value="">Chọn trạng thái</option>
                                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hoạt
                                                Động</option>
                                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tạm
                                                Ngưng</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Permissions Section -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="form-label mb-0">Phân Quyền Theo Module</label>
                                    <button type="button" class="btn btn-sm btn-outline-primary select-all-permissions">
                                        <i class="ti ti-check-box"></i> Chọn Tất Cả
                                    </button>
                                </div>
                                <div class="row g-3">
                                    @foreach ($modules as $module)
                                        <div class="col-12 col-lg-6">
                                            <div class="card border h-100">
                                                <div class="card-header bg-light">
                                                    <h6 class="card-title mb-0 fw-semibold text-primary">
                                                        <i class="ti ti-folder me-1"></i>{{ $module->name }}
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        @foreach ($module->permissions as $permission)
                                                            <div class="col-12 col-sm-6 mb-2">
                                                                <div class="form-check">
                                                                    <input class="form-check-input permission-checkbox"
                                                                        type="checkbox" name="permissions[]"
                                                                        value="{{ $permission->id }}"
                                                                        id="permission{{ $permission->id }}"
                                                                        data-module-id="{{ $module->id }}"
                                                                        {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                                    <label class="form-check-label small"
                                                                        for="permission{{ $permission->id }}">
                                                                        {{ $permission->display_name }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('permissions')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('backend.roles.index') }}" class="btn btn-secondary">Hủy</a>
                                <button type="submit" class="btn btn-primary">Tạo Mới</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End::row-1 -->

    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

            // Function to update select all button text
            function updateSelectAllButton() {
                const allCheckboxes = $('.permission-checkbox');
                const checkedCheckboxes = $('.permission-checkbox:checked');
                const selectAllBtn = $('.select-all-permissions');

                if (checkedCheckboxes.length === allCheckboxes.length && allCheckboxes.length > 0) {
                    selectAllBtn.html('<i class="ti ti-checkbox"></i> Bỏ Chọn Tất Cả');
                } else {
                    selectAllBtn.html('<i class="ti ti-check-box"></i> Chọn Tất Cả');
                }
            }



            // Select/Deselect all permissions
            $('.select-all-permissions').on('click', function() {
                const allCheckboxes = $('.permission-checkbox');
                const checkedCheckboxes = $('.permission-checkbox:checked');

                if (checkedCheckboxes.length === allCheckboxes.length) {
                    // Uncheck all
                    allCheckboxes.prop('checked', false);
                } else {
                    // Check all
                    allCheckboxes.prop('checked', true);
                }

                updateSelectAllButton();
            });



            // Update button states when individual checkboxes are clicked
            $('.permission-checkbox').on('change', function() {
                updateSelectAllButton();
            });

            // Initialize button states
            updateSelectAllButton();
        });
    </script>
@endsection
