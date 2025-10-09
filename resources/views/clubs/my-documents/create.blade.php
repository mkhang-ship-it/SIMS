{{--
Created: New form for creating independent documents (not tied to specific requests)
Features: Document type selection, file upload with drag-and-drop, visibility settings
Updated: Added missing "restricted" visibility option for document access control
Updated: Added user selection interface for restricted visibility with JavaScript toggle
--}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <div>
                <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Thêm Văn Bản Mới</h1>
            </div>

            <!-- Action buttons -->
            <div class="d-flex gap-2">
                <a href="{{ route('backend.club.my-documents.index') }}" class="btn btn-light">
                    <i class="ti ti-arrow-left"></i>
                    Quay Lại
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Create Form -->
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">Thông Tin Văn Bản</div>
                    </div>
                    <div class="card-body">

                        <form action="{{ route('backend.club.my-documents.store') }}" method="POST"
                            enctype="multipart/form-data" id="createForm">
                            @csrf

                            <div class="mb-4">
                                <label for="document_type_id" class="form-label">
                                    Loại Văn Bản <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="document_type_id" name="document_type_id" required>
                                    <option value="">-- Chọn loại văn bản --</option>
                                    @foreach ($documentTypes as $type)
                                        <option value="{{ $type->id }}"
                                            {{ old('document_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                            @if ($type->description)
                                                - {{ $type->description }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('document_type_id')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="title" class="form-label">
                                    Tên Văn Bản <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="title" name="title"
                                    value="{{ old('title') }}" required placeholder="VD: Báo cáo hoạt động tháng 10/2025">
                                @error('title')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="document_file" class="form-label">
                                    File Văn Bản <span class="text-danger">*</span>
                                </label>
                                <div>
                                    <input type="file" class="form-control" id="document_file" name="document_file"
                                        required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                </div>
                                @error('document_file')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror

                                <!-- File preview -->
                                <div id="filePreview" class="mt-3" style="display: none;">
                                    <div class="card">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-file fs-3 text-primary me-3"></i>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0" id="fileName"></h6>
                                                    <small class="text-muted" id="fileSize"></small>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="removeFile()">
                                                    <i class="ti ti-x"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="visibility" class="form-label">
                                    Quyền Xem <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="visibility" name="visibility" required>
                                    <option value="public" {{ old('visibility') === 'public' ? 'selected' : '' }}>
                                        Công khai - Tất cả thành viên có thể xem
                                    </option>
                                    <option value="restricted" {{ old('visibility') === 'restricted' ? 'selected' : '' }}>
                                        Hạn chế - Chỉ thành viên được chỉ định có thể xem
                                    </option>
                                    <option value="private" {{ old('visibility') === 'private' ? 'selected' : '' }}>
                                        Riêng tư - Chỉ tôi và admin có thể xem
                                    </option>
                                </select>
                                @error('visibility')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- User selection for restricted visibility -->
                            <div class="mb-4" id="userSelectionDiv" style="display: none;">
                                <label for="allowed_users" class="form-label">
                                    Chọn thành viên được phép xem <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="allowed_users" name="allowed_users[]" multiple>
                                    @foreach ($clubUsers as $user)
                                        <option value="{{ $user->id }}"
                                            {{ in_array($user->id, old('allowed_users', [])) ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Giữ Ctrl/Cmd để chọn nhiều thành viên</div>
                                @error('allowed_users')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('backend.club.my-documents.index') }}" class="btn btn-light">
                                    Hủy Bỏ
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="ti ti-plus"></i>
                                    Thêm Văn Bản
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const visibilitySelect = document.getElementById('visibility');
            const userSelectionDiv = document.getElementById('userSelectionDiv');
            const allowedUsersSelect = document.getElementById('allowed_users');

            function toggleUserSelection() {
                if (visibilitySelect.value === 'restricted') {
                    userSelectionDiv.style.display = 'block';
                    allowedUsersSelect.setAttribute('required', 'required');
                } else {
                    userSelectionDiv.style.display = 'none';
                    allowedUsersSelect.removeAttribute('required');
                }
            }

            // Initial check
            toggleUserSelection();

            // Listen for changes
            visibilitySelect.addEventListener('change', toggleUserSelection);
        });
    </script>
@endsection
