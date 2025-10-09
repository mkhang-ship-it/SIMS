{{--
Created: Document edit form for updating document information and access permissions
Features: Document title editing, visibility settings, user access management for restricted documents
--}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <div>
                <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Chỉnh Sửa Văn Bản</h1>
            </div>

            <!-- Action buttons -->
            <div class="d-flex gap-2">
                <a href="{{ route('backend.club.my-documents.show', $document) }}" class="btn btn-light">
                    <i class="ti ti-arrow-left"></i>
                    Quay Lại
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Edit Form -->
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">Chỉnh Sửa Thông Tin Văn Bản</div>
                    </div>
                    <div class="card-body">

                        <form action="{{ route('backend.club.my-documents.update', $document) }}" method="POST"
                            id="editForm">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="title" class="form-label">
                                            Tên Văn Bản <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="title" name="title"
                                            value="{{ old('title', $document->title) }}" required
                                            placeholder="VD: Báo cáo hoạt động tháng 10/2025">
                                        @error('title')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label">Loại Văn Bản</label>
                                        <input type="text" class="form-control"
                                            value="{{ $document->documentType->name }}" disabled>
                                        <small class="text-muted">Không thể thay đổi loại văn bản sau khi thêm văn bản.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="visibility" class="form-label">
                                    Quyền Xem <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="visibility" name="visibility" required>
                                    <option value="public"
                                        {{ old('visibility', $document->visibility) === 'public' ? 'selected' : '' }}>
                                        Công khai - Tất cả thành viên có thể xem
                                    </option>
                                    <option value="restricted"
                                        {{ old('visibility', $document->visibility) === 'restricted' ? 'selected' : '' }}>
                                        Hạn chế - Chỉ thành viên được chỉ định có thể xem
                                    </option>
                                    <option value="private"
                                        {{ old('visibility', $document->visibility) === 'private' ? 'selected' : '' }}>
                                        Riêng tư - Chỉ tôi và admin có thể xem
                                    </option>
                                </select>
                                @error('visibility')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- User selection for restricted visibility -->
                            <div class="mb-4" id="userSelectionDiv"
                                style="display: {{ old('visibility', $document->visibility) === 'restricted' ? 'block' : 'none' }};">
                                <label for="allowed_users" class="form-label">
                                    Chọn thành viên được phép xem <span class="text-danger">*</span>
                                </label>

                                @php
                                    $currentAllowedUsers = old(
                                        'allowed_users',
                                        $document->accessUsers->pluck('id')->toArray(),
                                    );
                                @endphp

                                <select class="form-select" id="allowed_users" name="allowed_users[]" multiple
                                    size="8">
                                    @foreach ($clubUsers as $user)
                                        <option value="{{ $user->id }}"
                                            {{ in_array($user->id, $currentAllowedUsers) ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>

                                <div class="form-text">
                                    <i class="ti ti-info-circle"></i>
                                    Giữ Ctrl/Cmd để chọn nhiều thành viên. Hiện tại có {{ count($currentAllowedUsers) }}
                                    thành viên được chọn.
                                </div>

                                @error('allowed_users')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Current file info (read-only) -->
                            <div class="mb-4">
                                <label class="form-label">File Văn Bản Hiện Tại</label>
                                <div class="card">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-file fs-3 text-primary me-3"></i>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">{{ basename($document->file_path) }}</h6>
                                                <small class="text-muted">
                                                    Tải lên: {{ $document->created_at->format('d/m/Y H:i') }}
                                                    @if (file_exists(storage_path('app/public/' . $document->file_path)))
                                                        |
                                                        {{ number_format(filesize(storage_path('app/public/' . $document->file_path)) / 1024, 2) }}
                                                        KB
                                                    @endif
                                                </small>
                                            </div>
                                            <a href="{{ route('backend.club.my-documents.download', $document) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="ti ti-download"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-muted">
                                    <i class="ti ti-info-circle"></i>
                                    Không thể thay đổi file sau khi thêm văn bản. Nếu cần thay đổi file, vui lòng thêm văn bản mới.
                                </small>
                            </div>

                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('backend.club.my-documents.show', $document) }}" class="btn btn-light">
                                    Hủy Bỏ
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="ti ti-check"></i>
                                    Lưu Thay Đổi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
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

            // Update selected count display
            function updateSelectedCount() {
                const selectedCount = allowedUsersSelect.selectedOptions.length;
                const infoText = userSelectionDiv.querySelector('.form-text');
                if (infoText) {
                    infoText.innerHTML =
                        `<i class="ti ti-info-circle"></i> Giữ Ctrl/Cmd để chọn nhiều thành viên. Hiện tại có ${selectedCount} thành viên được chọn.`;
                }
            }

            allowedUsersSelect.addEventListener('change', updateSelectedCount);
        });
    </script>
@endsection
