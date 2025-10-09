@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Kỷ Luật</h1>

            <!-- Nút trên desktop -->
            @if (auth()->user()->hasPermissionForModuleCode('discipline_management', 'create', 'branch'))
                <div class="d-none d-md-block">
                    <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importDisciplineModal">
                        <i class="ti ti-file-import"></i>
                        Import Excel
                    </button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDisciplineModal">
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
                    @if (auth()->user()->hasPermissionForModuleCode('discipline_management', 'create', 'branch'))
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                data-bs-target="#importDisciplineModal">
                                <i class="ti ti-file-import"></i> Import Excel
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                data-bs-target="#addDisciplineModal">
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
                                        <th>Mã Kỷ Luật</th>
                                        <th>Tên Kỷ Luật</th>
                                        <th style="text-align: center !important;">Điểm Kỷ Luật</th>
                                        <th>Mô Tả</th>
                                        <th style="text-align: center !important;">Trạng Thái</th>
                                        <th style="text-align: center !important;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($disciplines as $index => $discipline)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="fw-semibold">{{ $discipline->code }}</td>
                                            <td>{{ $discipline->name }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="badge {{ $discipline->discipline_points >= 0 ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $discipline->formatted_points }}
                                                </span>
                                            </td>
                                            <td>{{ $discipline->description ?? '-' }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-{{ $discipline->status_color }}">{{ $discipline->status_label }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="gap-2 fs-15">
                                                    @if (auth()->user()->hasPermissionForModuleCode('discipline_management', 'edit', 'branch'))
                                                        <button class="btn btn-icon btn-sm btn-info-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editDisciplineModal{{ $discipline->id }}"
                                                            title="Chỉnh sửa">
                                                            <i class="ri-edit-line"></i>
                                                        </button>
                                                    @endif
                                                    @if (auth()->user()->hasPermissionForModuleCode('discipline_management', 'delete', 'branch'))
                                                        <button class="btn btn-icon btn-sm btn-danger-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteDisciplineModal{{ $discipline->id }}"
                                                            title="Xóa">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
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

    @foreach ($disciplines as $discipline)
        <!-- Edit Discipline Modal -->
        @if (auth()->user()->hasPermissionForModuleCode('discipline_management', 'edit', 'branch'))
            <div class="modal fade" id="editDisciplineModal{{ $discipline->id }}" tabindex="-1"
                aria-labelledby="editDisciplineModalLabel{{ $discipline->id }}" aria-hidden="true"
                data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editDisciplineModalLabel{{ $discipline->id }}">
                                Chỉnh Sửa Kỷ Luật
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('backend.club.disciplines.update', $discipline->id) }}" method="POST"
                            id="editDisciplineForm{{ $discipline->id }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_name{{ $discipline->id }}" class="form-label">
                                                Tên Kỷ Luật <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="edit_name{{ $discipline->id }}"
                                                name="name" value="{{ $discipline->name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_code{{ $discipline->id }}" class="form-label">
                                                Mã Kỷ Luật
                                            </label>
                                            <input type="text" class="form-control" id="edit_code{{ $discipline->id }}"
                                                name="code" value="{{ $discipline->code }}"
                                                placeholder="Để trống để tự động tạo" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_discipline_points{{ $discipline->id }}" class="form-label">
                                                Điểm Kỷ Luật <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-control"
                                                id="edit_discipline_points{{ $discipline->id }}" name="discipline_points"
                                                value="{{ $discipline->discipline_points }}" required>
                                            <small class="form-text text-muted">Số dương để
                                                tăng điểm, số âm để trừ điểm</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_status{{ $discipline->id }}" class="form-label">
                                                Trạng Thái <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" id="edit_status{{ $discipline->id }}"
                                                name="status" required>
                                                <option value="active"
                                                    {{ $discipline->status === 'active' ? 'selected' : '' }}>
                                                    Hoạt động
                                                </option>
                                                <option value="inactive"
                                                    {{ $discipline->status === 'inactive' ? 'selected' : '' }}>
                                                    Tạm ngưng
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_description{{ $discipline->id }}" class="form-label">Mô Tả</label>
                                    <textarea class="form-control" id="edit_description{{ $discipline->id }}" name="description" rows="3">{{ $discipline->description }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy"></i>
                                    Cập Nhật
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Delete Discipline Modal -->
        @if (auth()->user()->hasPermissionForModuleCode('discipline_management', 'delete', 'branch'))
            <div class="modal fade" id="deleteDisciplineModal{{ $discipline->id }}" tabindex="-1"
                aria-labelledby="deleteDisciplineModalLabel{{ $discipline->id }}" aria-hidden="true"
                data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteDisciplineModalLabel{{ $discipline->id }}">
                                Xác Nhận Xóa
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <p class="mb-2">Bạn có chắc chắn muốn xóa kỷ luật
                                <strong class="text-danger">
                                    {{ $discipline->name }}
                                </strong> này?
                            </p>
                            <p class="text-danger">
                                <i class="ri-alert-line"></i>
                                Thao tác này không thể hoàn tác.
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                            <form action="{{ route('backend.club.disciplines.destroy', $discipline->id) }}"
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

    <!-- Add Discipline Modal -->
    @if (auth()->user()->hasPermissionForModuleCode('discipline_management', 'create', 'branch'))
        <div class="modal fade" id="addDisciplineModal" tabindex="-1" aria-labelledby="addDisciplineModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addDisciplineModalLabel">Thêm Kỷ Luật Mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('backend.club.disciplines.store') }}" method="POST" id="addDisciplineForm">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="add_name" class="form-label">
                                            Tên Kỷ Luật <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="add_name" name="name"
                                            placeholder="VD: Đi muộn" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="add_discipline_points" class="form-label">
                                            Điểm Kỷ Luật <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control" id="add_discipline_points"
                                            name="discipline_points" required>
                                        <small class="form-text text-muted">Số dương để cộng điểm, số âm để trừ điểm.
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="add_description" class="form-label">Mô Tả</label>
                                <textarea class="form-control" id="add_description" name="description" rows="3"
                                    placeholder="Mô tả chi tiết về kỷ luật (nếu có)"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="add_status" class="form-label">Trạng Thái <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="add_status" name="status" required>
                                    <option value="active" selected>Hoạt động</option>
                                    <option value="inactive">Tạm ngưng</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy"></i>
                                Thêm Mới
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Import Discipline Modal -->
    @if (auth()->user()->hasPermissionForModuleCode('discipline_management', 'create', 'branch'))
        <div class="modal fade" id="importDisciplineModal" tabindex="-1" aria-labelledby="importDisciplineModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('backend.club.disciplines.import') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="importDisciplineModalLabel">
                                <i class="ti ti-file-import"></i>
                                Import Kỷ Luật từ Excel
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center mb-3">
                                <a href="{{ route('backend.club.disciplines.export-template') }}" class="btn btn-info">
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
                                <small class="form-text text-muted">Chỉ hỗ trợ file Excel (.xlsx, .xls) hoặc CSV (.csv),
                                    không giới hạn dung lượng</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
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
