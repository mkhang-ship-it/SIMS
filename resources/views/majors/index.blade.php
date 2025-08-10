@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Chuyên Ngành</h1>

            <!-- Nút trên desktop -->
            @if (auth()->user()->hasPermissionForModuleCode('major_management', 'create', 'central'))
                <div class="d-none d-md-block">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMajorModal">
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
                    @if (auth()->user()->hasPermissionForModuleCode('major_management', 'create', 'central'))
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addMajorModal">
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
            <!-- Tree View Column (4/12) -->
            <div class="col-xl-4 col-lg-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            Cấu Trúc Cây Chuyên Ngành
                        </div>
                    </div>
                    <!--
                        Đã bỏ hiển thị status, chuyên ngành nào bị disable (inactive) thì tên sẽ có màu đỏ.
                    -->
                    <div class="card-body">
                        <div class="tree-view">
                            @php
                                // Organize majors into tree structure
                                $parentMajors = $allMajors->whereNull('parent_id');
                                function displayMajorTree($major, $allMajors, $level = 0)
                                {
                                    $children = $allMajors->where('parent_id', $major->id);
                                    $hasChildren = $children->count() > 0;
                                    $indent = str_repeat('  ', $level);

                                    echo '<div class="tree-item" style="margin-left: ' . $level * 20 . 'px;">';
                                    echo '<div class="d-flex align-items-center py-1">';

                                    if ($hasChildren) {
                                        echo '<i class="ti ti-folder tree-icon text-warning me-2"></i>';
                                    } else {
                                        echo '<i class="ti ti-file tree-icon text-info me-2"></i>';
                                    }

                                    // Nếu status là inactive thì cho tên màu đỏ, ngược lại giữ mặc định
                                    $labelClass = $major->status === 'inactive' ? 'tree-label text-danger fw-bold' : 'tree-label';
                                    echo '<span class="' . $labelClass . '">' . $major->name . '</span>';

                                    echo '</div>';
                                    echo '</div>';

                                    // Display children
                                    foreach ($children as $child) {
                                        displayMajorTree($child, $allMajors, $level + 1);
                                    }
                                }
                            @endphp

                            @if ($parentMajors->count() > 0)
                                @foreach ($parentMajors as $parentMajor)
                                    @php displayMajorTree($parentMajor, $allMajors); @endphp
                                @endforeach
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="ti ti-folder-off fs-48 mb-2"></i>
                                    <p>Chưa có chuyên ngành nào</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table View Column (6/12) -->
            <div class="col-xl-8 col-lg-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            Danh Sách Chi Tiết
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mb-4">
                            <table class="table text-nowrap table-bordered" id="data-table">
                                <thead>
                                    <tr>
                                        <th style="text-align: center !important;">STT</th>
                                        <th>Tên Chuyên Ngành</th>
                                        <th style="text-align: center !important;">Phân Ngành Cấp Trên</th>
                                        <th style="text-align: center !important;">Trạng Thái</th>
                                        <th style="text-align: center !important;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($majors as $index => $major)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="fw-semibold">{{ $major->name }}</td>
                                            <td class="text-center">
                                                @if ($major->parent)
                                                    <span class="badge bg-info">{{ $major->parent->name }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @switch($major->status)
                                                    @case('active')
                                                        <span class="badge bg-success">Hoạt Động</span>
                                                    @break

                                                    @case('inactive')
                                                        <span class="badge bg-warning">Tạm Ngưng</span>
                                                    @break

                                                    @default
                                                        <span class="badge bg-light text-dark">{{ $major->status }}</span>
                                                @endswitch
                                            </td>
                                            <td class="text-center">
                                                <div class=" gap-2 fs-15">
                                                    @if (auth()->user()->hasPermissionForModuleCode('major_management', 'edit', 'central'))
                                                        <button class="btn btn-icon btn-sm btn-info-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editMajorModal{{ $major->id }}"
                                                            title="Chỉnh sửa">
                                                            <i class="ri-edit-line"></i>
                                                        </button>
                                                    @endif
                                                    @if (auth()->user()->hasPermissionForModuleCode('major_management', 'delete', 'central'))
                                                        <button class="btn btn-icon btn-sm btn-danger-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteMajorModal{{ $major->id }}"
                                                            title="Xóa">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Edit Major Modal -->
                                        @if (auth()->user()->hasPermissionForModuleCode('major_management', 'edit', 'central'))
                                            <div class="modal fade" id="editMajorModal{{ $major->id }}" tabindex="-1"
                                                aria-labelledby="editMajorModalLabel{{ $major->id }}" aria-hidden="true"
                                                data-bs-backdrop="static" data-bs-keyboard="false">
                                                <div class="modal-dialog modal-dialog-centered ">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="editMajorModalLabel{{ $major->id }}">Chỉnh Sửa Chuyên
                                                                Ngành
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('backend.majors.update', $major->id) }}"
                                                            method="POST" id="editMajorForm{{ $major->id }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="edit_name{{ $major->id }}"
                                                                        class="form-label">Tên Chuyên Ngành <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control"
                                                                        id="edit_name{{ $major->id }}" name="name"
                                                                        value="{{ $major->name }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="edit_parent_id{{ $major->id }}"
                                                                        class="form-label">Phân Ngành Cấp Trên</label>
                                                                    <select class="form-select"
                                                                        id="edit_parent_id{{ $major->id }}"
                                                                        name="parent_id">
                                                                        <option value="">-- Không có phân ngành cấp
                                                                            trên
                                                                            --</option>
                                                                        @foreach ($allMajors->whereNull('parent_id') as $parentMajor)
                                                                            @if ($parentMajor->id !== $major->id)
                                                                                <option value="{{ $parentMajor->id }}"
                                                                                    {{ $major->parent_id == $parentMajor->id ? 'selected' : '' }}>
                                                                                    {{ $parentMajor->name }}
                                                                                </option>
                                                                            @endif
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="edit_status{{ $major->id }}"
                                                                        class="form-label">Trạng Thái <span
                                                                            class="text-danger">*</span></label>
                                                                    <select class="form-select"
                                                                        id="edit_status{{ $major->id }}"
                                                                        name="status" required>
                                                                        <option value="">Chọn trạng thái</option>
                                                                        <option value="active"
                                                                            {{ $major->status == 'active' ? 'selected' : '' }}>
                                                                            Hoạt Động</option>
                                                                        <option value="inactive"
                                                                            {{ $major->status == 'inactive' ? 'selected' : '' }}>
                                                                            Tạm Ngưng</option>
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
                                        <!-- Delete Major Modal -->
                                        @if (auth()->user()->hasPermissionForModuleCode('major_management', 'delete', 'central'))
                                            <div class="modal fade" id="deleteMajorModal{{ $major->id }}"
                                                tabindex="-1" aria-labelledby="deleteMajorModalLabel{{ $major->id }}"
                                                aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title text-danger"
                                                                id="deleteMajorModalLabel{{ $major->id }}">
                                                                Xác Nhận Xóa</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="text-center">
                                                                <p class="mb-2">Bạn có chắc chắn muốn xóa chuyên ngành
                                                                    <span
                                                                        class="fw-bold text-danger">{{ $major->name }}</span>
                                                                    không?
                                                                </p>
                                                                @if ($major->children()->count() > 0)
                                                                    <div class="alert alert-warning">
                                                                        <small>Chuyên ngành này có
                                                                            {{ $major->children()->count() }} chuyên ngành
                                                                            con. Cần xóa các chuyên ngành con trước!</small>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <form action="{{ route('backend.majors.destroy', $major->id) }}"
                                                            method="POST" id="deleteMajorForm{{ $major->id }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Hủy</button>
                                                                <button type="submit" class="btn btn-danger"
                                                                    {{ $major->children()->count() > 0 ? 'disabled' : '' }}>Xóa</button>
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

    <!-- Add Major Modal -->
    @if (auth()->user()->hasPermissionForModuleCode('major_management', 'create', 'central'))
        <div class="modal fade" id="addMajorModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="addMajorModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addMajorModalLabel">Thêm Chuyên Ngành Mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('backend.majors.store') }}" method="POST" id="addMajorForm">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="add_name" class="form-label">Tên Chuyên Ngành <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_name" name="name" required
                                    value="{{ old('name') }}">
                                @error('name')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="add_parent_id" class="form-label">Phân Ngành Cấp Trên</label>
                                <select class="form-select" id="add_parent_id" name="parent_id">
                                    <option value="">-- Không có phân ngành cấp trên --</option>
                                    @foreach ($allMajors->whereNull('parent_id') as $parentMajor)
                                        <option value="{{ $parentMajor->id }}"
                                            {{ old('parent_id') == $parentMajor->id ? 'selected' : '' }}>
                                            {{ $parentMajor->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="add_status" class="form-label">Trạng Thái <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="add_status" name="status" required>
                                    <option value="">Chọn trạng thái</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hoạt Động
                                    </option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tạm Ngưng
                                    </option>
                                </select>
                                @error('status')
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

    <!-- Custom CSS for Tree View -->
    <style>
        .tree-view {
            max-height: 600px;
            overflow-y: auto;
        }

        .tree-item {
            transition: background-color 0.2s ease;
        }

        .tree-item:hover {
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .tree-icon {
            font-size: 14px;
        }

        .tree-label {
            font-weight: 500;
            color: #495057;
        }

        .tree-item .badge {
            font-size: 10px;
        }
    </style>
@endsection
