{{--
Updated: Added click-to-copy functionality for template variables in both Add and Edit forms
- Made all variables in the Word template section clickable in Add modal
- Added same click-to-copy functionality to Edit modal
- Added visual feedback with hover effects and highlight animations
- Added global toast notification when variable is successfully copied
- Fixed toast not appearing in Edit modal by moving it to global scope
- Implemented fallback copy method for older browsers
- Added Bootstrap styling for better UX
- Updated variable text labels to use full "câu lạc bộ" instead of "CLB"
--}}
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
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-{{ $documentType->status === 'active' ? 'success' : 'danger' }}">
                                                    {{ $documentType->status === 'active' ? 'Hoạt động' : 'Tạm ngưng' }}
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
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editDocumentTypeModalLabel{{ $documentType->id }}">
                                Chỉnh Sửa Danh Mục Văn Bản
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('backend.club.document-types.update', $documentType->id) }}" method="POST"
                            id="editDocumentTypeForm{{ $documentType->id }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="row">
                                    <!-- Cột trái: Form nhập liệu -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_name{{ $documentType->id }}" class="form-label">
                                                Tên Danh Mục Văn Bản <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                id="edit_name{{ $documentType->id }}" name="name"
                                                value="{{ $documentType->name }}" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="edit_code{{ $documentType->id }}" class="form-label">
                                                Mã Danh Mục Văn Bản
                                            </label>
                                            <input type="text" class="form-control"
                                                id="edit_code{{ $documentType->id }}" name="code"
                                                value="{{ $documentType->code }}" placeholder="Để trống để tự động tạo"
                                                disabled>
                                        </div>

                                        <div class="mb-3">
                                            <label for="edit_description{{ $documentType->id }}" class="form-label">Mô
                                                Tả</label>
                                            <textarea class="form-control" id="edit_description{{ $documentType->id }}" name="description" rows="3">{{ $documentType->description }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label for="edit_template_file{{ $documentType->id }}" class="form-label">
                                                File Template
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
                                            <select class="form-select" id="edit_status{{ $documentType->id }}"
                                                name="status" required>
                                                <option value="active"
                                                    {{ $documentType->status === 'active' ? 'selected' : '' }}>Hoạt động
                                                </option>
                                                <option value="inactive"
                                                    {{ $documentType->status === 'inactive' ? 'selected' : '' }}>Tạm ngưng
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Cột phải: Danh sách biến -->
                                    <div class="col-md-6">
                                        <div class="alert alert-info">
                                            <h6 class="fw-semibold mb-2">
                                                <i class="ti ti-code"></i> Các biến có thể sử dụng trong file Word (.docx):
                                            </h6>
                                            <div class="bg-light p-2 rounded small font-monospace"
                                                style="line-height: 1.8;">
                                                Họ và tên: <span class="variable-clickable text-primary fw-bold"
                                                    data-variable="{full_name}"
                                                    style="cursor: pointer; text-decoration: underline;">{full_name}</span><br>
                                                Email: <span class="variable-clickable text-primary fw-bold"
                                                    data-variable="{email_address}"
                                                    style="cursor: pointer; text-decoration: underline;">{email_address}</span><br>
                                                Số điện thoại: <span class="variable-clickable text-primary fw-bold"
                                                    data-variable="{phone_number}"
                                                    style="cursor: pointer; text-decoration: underline;">{phone_number}</span><br>
                                                Địa chỉ: <span class="variable-clickable text-primary fw-bold"
                                                    data-variable="{full_address}"
                                                    style="cursor: pointer; text-decoration: underline;">{full_address}</span><br>
                                                CCCD/CMND: <span class="variable-clickable text-primary fw-bold"
                                                    data-variable="{identity_card_code}"
                                                    style="cursor: pointer; text-decoration: underline;">{identity_card_code}</span><br>
                                                Mã sinh viên: <span class="variable-clickable text-primary fw-bold"
                                                    data-variable="{student_code}"
                                                    style="cursor: pointer; text-decoration: underline;">{student_code}</span><br>
                                                Ngày sinh: <span class="variable-clickable text-primary fw-bold"
                                                    data-variable="{birthday}"
                                                    style="cursor: pointer; text-decoration: underline;">{birthday}</span><br>
                                                Giới tính: <span class="variable-clickable text-primary fw-bold"
                                                    data-variable="{gender}"
                                                    style="cursor: pointer; text-decoration: underline;">{gender}</span><br>
                                                Chuyên ngành: <span class="variable-clickable text-primary fw-bold"
                                                    data-variable="{major}"
                                                    style="cursor: pointer; text-decoration: underline;">{major}</span><br>
                                                Khóa học: <span class="variable-clickable text-primary fw-bold"
                                                    data-variable="{course}"
                                                    style="cursor: pointer; text-decoration: underline;">{course}</span><br>
                                                <br>
                                                Tên câu lạc bộ: <span class="variable-clickable text-primary fw-bold"
                                                    data-variable="{club_name}"
                                                    style="cursor: pointer; text-decoration: underline;">{club_name}</span><br>
                                                Số điện thoại câu lạc bộ: <span
                                                    class="variable-clickable text-primary fw-bold"
                                                    data-variable="{club_phone}"
                                                    style="cursor: pointer; text-decoration: underline;">{club_phone}</span><br>
                                                Email câu lạc bộ: <span class="variable-clickable text-primary fw-bold"
                                                    data-variable="{club_email}"
                                                    style="cursor: pointer; text-decoration: underline;">{club_email}</span><br>
                                                Địa chỉ câu lạc bộ: <span class="variable-clickable text-primary fw-bold"
                                                    data-variable="{club_address}"
                                                    style="cursor: pointer; text-decoration: underline;">{club_address}</span>
                                            </div>
                                            <div class="form-text mt-2">
                                                <i class="ti ti-click me-1"></i>Nhấp vào biến để sao chép vào clipboard.
                                                Sau đó chèn
                                                các biến này vào nội dung file Word (.docx). Khi người dùng tải xuống,
                                                hệ thống sẽ tự động thay thế bằng dữ liệu thực tế.
                                            </div>
                                        </div>
                                    </div>
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
                            <p class="mb-2">Bạn có chắc chắn muốn xóa danh mục văn bản
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
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addDocumentTypeModalLabel">Thêm Danh Mục Văn Bản Mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('backend.club.document-types.store') }}" method="POST"
                        id="addDocumentTypeForm" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <!-- Cột trái: Các input -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_name" class="form-label">
                                            Tên Danh Mục Văn Bản <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="add_name" name="name"
                                            placeholder="VD: Quyết định" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="add_description" class="form-label">Mô Tả</label>
                                        <textarea class="form-control" id="add_description" name="description" rows="3"
                                            placeholder="Mô tả chi tiết về loại văn bản (nếu có)"></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="add_template_file" class="form-label">
                                            File Template
                                        </label>
                                        <input type="file" class="form-control" id="add_template_file"
                                            name="template_file" accept=".docx">
                                        <div class="form-text">
                                            Chỉ chấp nhận file: .docx (tối đa 10MB)
                                        </div>
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

                                <!-- Cột phải: Danh sách biến -->
                                <div class="col-md-6">
                                    <div class="alert alert-info">
                                        <h6 class="fw-semibold mb-2">
                                            <i class="ti ti-code"></i> Các biến có thể sử dụng trong file Word (.docx):
                                        </h6>
                                        <div class="bg-light p-2 rounded small font-monospace" style="line-height: 1.8;">
                                            Họ và tên: <span class="variable-clickable text-primary fw-bold"
                                                data-variable="{full_name}"
                                                style="cursor: pointer; text-decoration: underline;">
                                                {full_name}
                                            </span><br>
                                            Email: <span class="variable-clickable text-primary fw-bold"
                                                data-variable="{email_address}"
                                                style="cursor: pointer; text-decoration: underline;">
                                                {email_address}
                                            </span><br>
                                            Số điện thoại: <span class="variable-clickable text-primary fw-bold"
                                                data-variable="{phone_number}"
                                                style="cursor: pointer; text-decoration: underline;">
                                                {phone_number}
                                            </span><br>
                                            Địa chỉ: <span class="variable-clickable text-primary fw-bold"
                                                data-variable="{full_address}"
                                                style="cursor: pointer; text-decoration: underline;">
                                                {full_address}
                                            </span><br>
                                            CCCD/CMND: <span class="variable-clickable text-primary fw-bold"
                                                data-variable="{identity_card_code}"
                                                style="cursor: pointer; text-decoration: underline;">
                                                {identity_card_code}
                                            </span><br>
                                            Mã sinh viên: <span class="variable-clickable text-primary fw-bold"
                                                data-variable="{student_code}"
                                                style="cursor: pointer; text-decoration: underline;">
                                                {student_code}
                                            </span><br>
                                            Ngày sinh: <span class="variable-clickable text-primary fw-bold"
                                                data-variable="{birthday}"
                                                style="cursor: pointer; text-decoration: underline;">
                                                {birthday}
                                            </span><br>
                                            Giới tính: <span class="variable-clickable text-primary fw-bold"
                                                data-variable="{gender}"
                                                style="cursor: pointer; text-decoration: underline;">
                                                {gender}
                                            </span><br>
                                            Chuyên ngành: <span class="variable-clickable text-primary fw-bold"
                                                data-variable="{major}"
                                                style="cursor: pointer; text-decoration: underline;">
                                                {major}
                                            </span><br>
                                            Khóa học: <span class="variable-clickable text-primary fw-bold"
                                                data-variable="{course}"
                                                style="cursor: pointer; text-decoration: underline;">
                                                {course}
                                            </span><br>
                                            <br>
                                            Tên câu lạc bộ: <span class="variable-clickable text-primary fw-bold"
                                                data-variable="{club_name}"
                                                style="cursor: pointer; text-decoration: underline;">
                                                {club_name}
                                            </span><br>
                                            Số điện thoại câu lạc bộ: <span class="variable-clickable text-primary fw-bold"
                                                data-variable="{club_phone}"
                                                style="cursor: pointer; text-decoration: underline;">
                                                {club_phone}
                                            </span><br>
                                            Email câu lạc bộ: <span class="variable-clickable text-primary fw-bold"
                                                data-variable="{club_email}"
                                                style="cursor: pointer; text-decoration: underline;">
                                                {club_email}
                                            </span><br>
                                            Địa chỉ câu lạc bộ: <span class="variable-clickable text-primary fw-bold"
                                                data-variable="{club_address}"
                                                style="cursor: pointer; text-decoration: underline;">
                                                {club_address}
                                            </span>
                                        </div>
                                        <div class="form-text mt-2">
                                            <i class="ti ti-click me-1"></i>Nhấp vào biến để sao chép vào clipboard. Sau đó
                                            chèn
                                            các biến này vào nội dung file Word (.docx). Khi người dùng tải xuống,
                                            hệ thống sẽ tự động thay thế bằng dữ liệu thực tế.
                                        </div>
                                    </div>
                                </div>
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

    <!-- Global Copy success toast -->
    <div id="copyToast" class="position-fixed top-0 end-0 p-3" style="z-index: 9999; display: none;">
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="ti ti-check-circle text-success me-2"></i>
                <strong class="me-auto">Thành công</strong>
                <button type="button" class="btn-close" onclick="hideCopyToast()"></button>
            </div>
            <div class="toast-body" id="toastMessage">
                Đã sao chép biến vào clipboard!
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle variable click to copy to clipboard
            document.querySelectorAll('.variable-clickable').forEach(function(element) {
                element.addEventListener('click', function() {
                    const variable = this.getAttribute('data-variable');

                    // Copy to clipboard using the modern Clipboard API
                    if (navigator.clipboard && window.isSecureContext) {
                        navigator.clipboard.writeText(variable).then(function() {
                            showCopyToast(variable);
                            highlightVariable(element);
                        }).catch(function(err) {
                            console.error('Failed to copy: ', err);
                            fallbackCopyTextToClipboard(variable, element);
                        });
                    } else {
                        // Fallback for older browsers
                        fallbackCopyTextToClipboard(variable, element);
                    }
                });

                // Add hover effects
                element.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = 'rgba(13, 110, 253, 0.1)';
                    this.style.borderRadius = '3px';
                    this.style.padding = '2px 4px';
                });

                element.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                    this.style.borderRadius = '';
                    this.style.padding = '';
                });
            });
        });

        // Fallback copy method for older browsers
        function fallbackCopyTextToClipboard(text, element) {
            const textArea = document.createElement("textarea");
            textArea.value = text;

            // Avoid scrolling to bottom
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";

            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    showCopyToast(text);
                    highlightVariable(element);
                } else {
                    alert('Không thể sao chép. Vui lòng sao chép thủ công: ' + text);
                }
            } catch (err) {
                console.error('Fallback: Oops, unable to copy', err);
                alert('Không thể sao chép. Vui lòng sao chép thủ công: ' + text);
            }

            document.body.removeChild(textArea);
        }

        // Show success toast
        function showCopyToast(variable) {
            const toast = document.getElementById('copyToast');
            const message = document.getElementById('toastMessage');
            message.textContent = 'Đã sao chép "' + variable + '" vào clipboard!';

            toast.style.display = 'block';

            // Auto hide after 3 seconds
            setTimeout(function() {
                hideCopyToast();
            }, 3000);
        }

        // Hide toast
        function hideCopyToast() {
            const toast = document.getElementById('copyToast');
            toast.style.display = 'none';
        }

        // Highlight variable when copied
        function highlightVariable(element) {
            const originalColor = element.style.backgroundColor;
            const originalBorder = element.style.border;

            element.style.backgroundColor = '#d4edda';
            element.style.border = '1px solid #c3e6cb';
            element.style.borderRadius = '3px';
            element.style.padding = '2px 4px';

            setTimeout(function() {
                element.style.backgroundColor = originalColor;
                element.style.border = originalBorder;
                if (!element.matches(':hover')) {
                    element.style.borderRadius = '';
                    element.style.padding = '';
                }
            }, 500);
        }
    </script>
@endsection
