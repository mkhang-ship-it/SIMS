{{-- Fixed: Corrected null property access error by changing $accessUser->user->name to $accessUser->name
     since accessUsers relationship returns User models directly, not DocumentUserAccess models --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <div>
                <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Chi Tiết Tài Liệu</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('backend.club.documents.index') }}">Quản lý tài
                                liệu</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $document->title }}</li>
                    </ol>
                </nav>
            </div>

            <!-- Action buttons -->
            <div class="d-flex gap-2">
                <a href="{{ route('backend.club.documents.index') }}" class="btn btn-light">
                    <i class="ti ti-arrow-left"></i>
                    Quay lại
                </a>

                @if (auth()->user()->hasPermissionForModuleCode('document_management', 'view', 'branch'))
                    <a href="{{ route('backend.club.documents.download', $document) }}" class="btn btn-info">
                        <i class="ti ti-download"></i>
                        Tải xuống
                    </a>
                @endif
            </div>
        </div>

        <div class="row">
            <!-- Document Information -->
            <div class="col-xl-8">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">Thông Tin Tài Liệu</div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Tên tài liệu:</strong>
                            </div>
                            <div class="col-sm-9">
                                {{ $document->title }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Loại văn bản:</strong>
                            </div>
                            <div class="col-sm-9">
                                {{ $document->documentType->name }}
                                @if ($document->documentType->description)
                                    <br><small class="text-muted">{{ $document->documentType->description }}</small>
                                @endif
                            </div>
                        </div>


                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Người nộp:</strong>
                            </div>
                            <div class="col-sm-9">
                                {{ $document->uploadedByUser->name }}
                                <br><small class="text-muted">{{ $document->uploadedByUser->email }}</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Ngày nộp:</strong>
                            </div>
                            <div class="col-sm-9">
                                {{ $document->created_at->format('d/m/Y H:i:s') }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <strong>Quyền xem:</strong>
                            </div>
                            <div class="col-sm-9">
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

                                @if (auth()->user()->hasPermissionForModuleCode('document_management', 'edit', 'branch'))
                                    <button class="btn btn-sm btn-outline-primary ms-2" data-bs-toggle="modal"
                                        data-bs-target="#changeVisibilityModal">
                                        <i class="ti ti-edit"></i>
                                        Thay đổi
                                    </button>
                                @endif
                            </div>
                        </div>

                        @if ($document->visibility === 'restricted')
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong>Người được xem:</strong>
                                </div>
                                <div class="col-sm-9">
                                    @if ($document->accessUsers->count() > 0)
                                        @foreach ($document->accessUsers as $accessUser)
                                            <span class="badge bg-secondary me-1">{{ $accessUser->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">Văn bản này chưa được cấp quyền cho ai.</span>
                                    @endif

                                    @if (auth()->user()->hasPermissionForModuleCode('document_management', 'edit', 'branch'))
                                        <br>
                                        <a href="{{ route('backend.club.documents.manage-access', $document) }}"
                                            class="btn btn-sm btn-outline-info mt-2">
                                            <i class="ti ti-users"></i>
                                            Quản lý quyền truy cập
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if ($document->reason_rejected)
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong>Ghi chú của admin:</strong>
                                </div>
                                <div class="col-sm-9">
                                    <div class="alert alert-info">
                                        {{ $document->reason_rejected }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Status and Actions -->
            <div class="col-xl-4">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">Trạng Thái & Thao Tác</div>
                    </div>
                    <div class="card-body">
                        <div class="mb-4 text-center">
                            @php
                                $statusColor =
                                    ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'][
                                        $document->status
                                    ] ?? 'secondary';
                                $statusText =
                                    ['pending' => 'Chờ duyệt', 'approved' => 'Đã duyệt', 'rejected' => 'Từ chối'][
                                        $document->status
                                    ] ?? $document->status;
                            @endphp
                            <div class="mb-2">
                                <span class="badge bg-{{ $statusColor }} fs-6 px-3 py-2">{{ $statusText }}</span>
                            </div>

                            @if ($document->reviewed_by && $document->reviewed_at)
                                <small class="text-muted">
                                    {{ $document->status === 'approved' ? 'Đã duyệt' : 'Từ chối' }} bởi
                                    {{ $document->reviewedByUser->name ?? 'N/A' }}
                                    <br>{{ $document->reviewed_at->format('d/m/Y H:i:s') }}
                                </small>
                            @endif
                        </div>

                        @if (
                            $document->status === 'pending' &&
                                auth()->user()->hasPermissionForModuleCode('document_management', 'approve', 'branch'))
                            <div class="d-grid gap-2">
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                    <i class="ti ti-check"></i>
                                    Duyệt tài liệu
                                </button>
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    <i class="ti ti-x"></i>
                                    Từ chối tài liệu
                                </button>
                            </div>
                        @endif

                        @if (
                            $document->status === 'approved' &&
                                auth()->user()->hasPermissionForModuleCode('document_management', 'approve', 'branch'))
                            <div class="d-grid">
                                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    <i class="ti ti-x"></i>
                                    Hủy duyệt
                                </button>
                            </div>
                        @endif

                        @if (
                            $document->status === 'rejected' &&
                                auth()->user()->hasPermissionForModuleCode('document_management', 'approve', 'branch'))
                            <div class="d-grid">
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                    <i class="ti ti-check"></i>
                                    Duyệt lại
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Approve Modal -->
    @if (
        $document->status !== 'approved' &&
            auth()->user()->hasPermissionForModuleCode('document_management', 'approve', 'branch'))
        <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Duyệt Tài Liệu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('backend.club.documents.update-status', $document) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="approved">
                        <div class="modal-body">
                            <p>Bạn có chắc chắn muốn duyệt tài liệu <strong>{{ $document->title }}</strong>?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-success">
                                <i class="ti ti-check"></i>
                                Duyệt
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Reject Modal -->
    @if (
        $document->status !== 'rejected' &&
            auth()->user()->hasPermissionForModuleCode('document_management', 'approve', 'branch'))
        <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $document->status === 'approved' ? 'Hủy Duyệt' : 'Từ Chối' }} Tài Liệu
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('backend.club.documents.update-status', $document) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="rejected">
                        <div class="modal-body">
                            <p>Bạn có chắc chắn muốn {{ $document->status === 'approved' ? 'hủy duyệt' : 'từ chối' }} tài
                                liệu <strong>{{ $document->title }}</strong>?</p>
                            <div class="mb-3">
                                <label for="admin_notes_reject" class="form-label">Lý do
                                    {{ $document->status === 'approved' ? 'hủy duyệt' : 'từ chối' }} <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="admin_notes_reject" name="admin_notes" rows="3"
                                    placeholder="Nhập lý do..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="ti ti-x"></i>
                                {{ $document->status === 'approved' ? 'Hủy Duyệt' : 'Từ Chối' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Change Visibility Modal -->
    @if (auth()->user()->hasPermissionForModuleCode('document_management', 'edit', 'branch'))
        <div class="modal fade" id="changeVisibilityModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Thay Đổi Quyền Xem</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('backend.club.documents.update-visibility', $document) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="visibility" class="form-label">Quyền xem <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="visibility" name="visibility" required>
                                    <option value="public" {{ $document->visibility === 'public' ? 'selected' : '' }}>
                                        Công khai - Tất cả thành viên có thể xem
                                    </option>
                                    <option value="private" {{ $document->visibility === 'private' ? 'selected' : '' }}>
                                        Riêng tư - Chỉ người nộp và admin có thể xem
                                    </option>
                                    <option value="restricted"
                                        {{ $document->visibility === 'restricted' ? 'selected' : '' }}>
                                        Hạn chế - Chỉ những người được cấp quyền có thể xem
                                    </option>
                                </select>
                            </div>
                            <div class="alert alert-info">
                                <i class="ti ti-info-circle"></i>
                                <strong>Lưu ý:</strong> Nếu chọn "Hạn chế", bạn sẽ cần quản lý quyền truy cập riêng biệt.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy"></i>
                                Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
