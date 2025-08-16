@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Danh Mục Văn Bản</h1>

            <!-- Nút trên desktop -->
            @if (auth()->user()->hasPermissionForModuleCode('document_type_management', 'create', 'branch'))
                <div class="d-none d-md-block">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDocumentTypeModal">
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
                    @if (auth()->user()->hasPermissionForModuleCode('document_type_management', 'create', 'branch'))
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                data-bs-target="#addDocumentTypeModal">
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
                                        <th>Mã Loại Văn Bản</th>
                                        <th>Tên Loại Văn Bản</th>
                                        <th>Mô Tả</th>
                                        <th>File Template</th>
                                        <th style="text-align: center !important;">Trạng Thái</th>
                                        <th style="text-align: center !important;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($documentTypes as $index => $documentType)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="fw-semibold">{{ $documentType->code }}</td>
                                            <td>{{ $documentType->name }}</td>
                                            <td>{{ $documentType->description ?? '-' }}</td>
                                            <td>
                                                @if ($documentType->template_file_path)
                                                    <a href="{{ route('backend.club.document-types.download-template', $documentType->id) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="ti ti-download"></i>
                                                        Tải xuống
                                                    </a>
                                                @else
                                                    <span class="text-muted">Chưa có file</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-{{ $documentType->status === 'active' ? 'success' : 'danger' }}">
                                                    {{ $documentType->status === 'active' ? 'Hoạt động' : 'Không hoạt động' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="gap-2 fs-15">
                                                    @if (auth()->user()->hasPermissionForModuleCode('document_type_management', 'edit', 'branch'))
                                                        <button class="btn btn-icon btn-sm btn-info-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editDocumentTypeModal{{ $documentType->id }}"
                                                            title="Chỉnh sửa">
                                                            <i class="ri-edit-line"></i>
                                                        </button>
                                                    @endif
                                                    @if (auth()->user()->hasPermissionForModuleCode('document_type_management', 'delete', 'branch'))
                                                        <button class="btn btn-icon btn-sm btn-danger-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteDocumentTypeModal{{ $documentType->id }}"
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

    @foreach ($documentTypes as $documentType)
        <!-- Edit Document Type Modal -->
        @if (auth()->user()->hasPermissionForModuleCode('document_type_management', 'edit', 'branch'))
            <div class="modal fade" id="editDocumentTypeModal{{ $documentType->id }}" tabindex="-1"
                aria-labelledby="editDocumentTypeModalLabel{{ $documentType->id }}" aria-hidden="true"
                data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editDocumentTypeModalLabel{{ $documentType->id }}">
                                Chỉnh Sửa Loại Văn Bản
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('backend.club.document-types.update', $documentType->id) }}" method="POST"
                            id="editDocumentTypeForm{{ $documentType->id }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_name{{ $documentType->id }}" class="form-label">
                                                Tên Loại Văn Bản <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                id="edit_name{{ $documentType->id }}" name="name"
                                                value="{{ $documentType->name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_code{{ $documentType->id }}" class="form-label">
                                                Mã Loại Văn Bản
                                            </label>
                                            <input type="text" class="form-control"
                                                id="edit_code{{ $documentType->id }}" name="code"
                                                value="{{ $documentType->code }}" placeholder="Để trống để tự động tạo"
                                                disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_description{{ $documentType->id }}" class="form-label">Mô Tả</label>
                                    <textarea class="form-control" id="edit_description{{ $documentType->id }}" name="description" rows="3">{{ $documentType->description }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_template_file{{ $documentType->id }}" class="form-label">
                                        File Template <span class="text-danger">*</span>
                                    </label>
                                    @if ($documentType->template_file_path)
                                        <div class="mb-2">
                                            <small class="text-muted">File hiện tại:</small>
                                            <a href="{{ route('backend.club.document-types.download-template', $documentType->id) }}"
                                                class="btn btn-sm btn-outline-primary ms-2">
                                                <i class="ti ti-download"></i>
                                                {{ basename($documentType->template_file_path) }}
                                            </a>
                                        </div>
                                    @endif
                                    <input type="file" class="form-control"
                                        id="edit_template_file{{ $documentType->id }}" name="template_file"
                                        accept=".docx">
                                    <small class="form-text text-muted">Chỉ chấp nhận file: .docx (tối đa
                                        10MB)</small>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_status{{ $documentType->id }}" class="form-label">
                                        Trạng Thái <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="edit_status{{ $documentType->id }}" name="status"
                                        required>
                                        <option value="active" {{ $documentType->status === 'active' ? 'selected' : '' }}>
                                            Hoạt động
                                        </option>
                                        <option value="inactive"
                                            {{ $documentType->status === 'inactive' ? 'selected' : '' }}>
                                            Không hoạt động
                                        </option>
                                    </select>
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

        <!-- Delete Document Type Modal -->
        @if (auth()->user()->hasPermissionForModuleCode('document_type_management', 'delete', 'branch'))
            <div class="modal fade" id="deleteDocumentTypeModal{{ $documentType->id }}" tabindex="-1"
                aria-labelledby="deleteDocumentTypeModalLabel{{ $documentType->id }}" aria-hidden="true"
                data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteDocumentTypeModalLabel{{ $documentType->id }}">
                                Xác Nhận Xóa
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <p class="mb-2">Bạn có chắc chắn muốn xóa loại văn bản
                                <strong class="text-danger">
                                    {{ $documentType->name }}
                                </strong> này?
                            </p>
                            <p class="text-danger">
                                <i class="ri-alert-line"></i>
                                Thao tác này không thể hoàn tác.
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                            <form action="{{ route('backend.club.document-types.destroy', $documentType->id) }}"
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

    <!-- Add Document Type Modal -->
    @if (auth()->user()->hasPermissionForModuleCode('document_type_management', 'create', 'branch'))
        <div class="modal fade" id="addDocumentTypeModal" tabindex="-1" aria-labelledby="addDocumentTypeModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addDocumentTypeModalLabel">Thêm Loại Văn Bản Mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('backend.club.document-types.store') }}" method="POST"
                        id="addDocumentTypeForm" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="add_name" class="form-label">
                                            Tên Loại Văn Bản <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="add_name" name="name"
                                            placeholder="VD: Quyết định" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="add_description" class="form-label">Mô Tả</label>
                                <textarea class="form-control" id="add_description" name="description" rows="3"
                                    placeholder="Mô tả chi tiết về loại văn bản (nếu có)"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="add_template_file" class="form-label">
                                    File Template <span class="text-danger">*</span>
                                </label>
                                <input type="file" class="form-control" id="add_template_file" name="template_file"
                                    accept=".docx" required>
                                <div class="form-text">
                                    Chỉ chấp nhận file: .docx (tối đa 10MB)
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="add_status" class="form-label">Trạng Thái <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="add_status" name="status" required>
                                    <option value="active" selected>Hoạt động</option>
                                    <option value="inactive">Không hoạt động</option>
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
@endsection
