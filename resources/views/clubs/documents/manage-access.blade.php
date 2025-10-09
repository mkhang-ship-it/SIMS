@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <div>
                <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Quyền Truy Cập</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('backend.club.documents.index') }}">Quản lý tài liệu</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('backend.club.documents.show', $document) }}">{{ $document->name }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Quản lý quyền truy cập</li>
                    </ol>
                </nav>
            </div>

            <!-- Action buttons -->
            <div class="d-flex gap-2">
                <a href="{{ route('backend.club.documents.show', $document) }}" class="btn btn-light">
                    <i class="ti ti-arrow-left"></i>
                    Quay lại
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Document Info -->
            <div class="col-xl-4">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">Thông Tin Tài Liệu</div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Tên tài liệu:</strong><br>
                            {{ $document->name }}
                        </div>

                        <div class="mb-3">
                            <strong>Loại văn bản:</strong><br>
                            {{ $document->documentType->name }}
                        </div>

                        <div class="mb-3">
                            <strong>Người nộp:</strong><br>
                            {{ $document->uploadedByUser->name }}
                        </div>

                        <div class="mb-3">
                            <strong>Quyền xem hiện tại:</strong><br>
                            @php
                                $visibilityColor =
                                    ['public' => 'success', 'private' => 'warning', 'restricted' => 'info'][
                                        $document->visibility
                                    ] ?? 'secondary';
                                $visibilityText =
                                    ['public' => 'Công khai', 'private' => 'Riêng tư', 'restricted' => 'Hạn chế'][
                                        $document->visibility
                                    ] ?? $document->visibility;
                            @endphp
                            <span class="badge bg-{{ $visibilityColor }}">{{ $visibilityText }}</span>
                        </div>

                        @if ($document->visibility !== 'restricted')
                            <div class="alert alert-warning">
                                <i class="ti ti-alert-triangle"></i>
                                <strong>Lưu ý:</strong> Tài liệu này không ở chế độ "Hạn chế". Các cài đặt quyền truy cập sẽ
                                không có hiệu lực.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Access Management -->
            <div class="col-xl-8">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">Cấp Quyền Truy Cập</div>
                    </div>
                    <div class="card-body">

                        @if ($document->visibility === 'restricted')
                            <form action="{{ route('backend.club.documents.manage-access.store', $document) }}"
                                method="POST">
                                @csrf

                                <div class="mb-4">
                                    <label for="user_ids" class="form-label">Chọn thành viên được phép xem tài liệu này
                                        <span class="text-danger">*</span></label>
                                    <select class="form-select" id="user_ids" name="user_ids[]" multiple required>
                                        @foreach ($clubMembers as $member)
                                            <option value="{{ $member->id }}"
                                                {{ in_array($member->id, $accessUsers->pluck('user_id')->toArray()) ? 'selected' : '' }}>
                                                {{ $member->name }} ({{ $member->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Giữ Ctrl (Windows) hoặc Cmd (Mac) để chọn nhiều thành viên</div>
                                </div>

                                <div class="mb-4">
                                    <div class="alert alert-info">
                                        <i class="ti ti-info-circle"></i>
                                        <strong>Thông tin:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li>Người nộp tài liệu và admin luôn có quyền xem</li>
                                            <li>Những thành viên được chọn sẽ có thể xem và tải xuống tài liệu này</li>
                                            <li>Bạn có thể thay đổi danh sách này bất cứ lúc nào</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-device-floppy"></i>
                                        Cập nhật quyền truy cập
                                    </button>

                                    <a href="{{ route('backend.club.documents.show', $document) }}" class="btn btn-light">
                                        Hủy
                                    </a>
                                </div>
                            </form>
                        @else
                            <div class="text-center py-5">
                                <i class="ti ti-lock-off fs-1 text-muted"></i>
                                <h5 class="mt-3 text-muted">Tài liệu không ở chế độ hạn chế</h5>
                                <p class="text-muted">Để quản lý quyền truy cập, vui lòng thay đổi quyền xem thành "Hạn chế"
                                    trước.</p>
                                <a href="{{ route('backend.club.documents.show', $document) }}" class="btn btn-primary">
                                    Quay lại tài liệu
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Current Access List -->
                @if ($document->visibility === 'restricted' && $accessUsers->count() > 0)
                    <div class="card custom-card mt-4">
                        <div class="card-header">
                            <div class="card-title">Danh Sách Hiện Tại ({{ $accessUsers->count() }} người)</div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Tên thành viên</th>
                                            <th>Email</th>
                                            <th>Ngày cấp quyền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($accessUsers as $accessUser)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td>{{ $accessUser->name }}</td>
                                                <td>{{ $accessUser->email }}</td>
                                                <td>{{ $accessUser->created_at->format('d/m/Y H:i:s') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced multi-select with search functionality
            const userSelect = document.getElementById('user_ids');
            if (userSelect) {
                // Simple enhancement - you might want to use a library like Select2 for better UX
                userSelect.style.height = '200px';
            }
        });
    </script>
@endsection
