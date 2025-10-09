@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Văn Bản Của Tôi</h1>

        <!-- Navigation buttons -->
        <div class="d-flex gap-2">
            <a href="{{ route('backend.club.my-documents.requests') }}" class="btn btn-warning">
                <i class="ti ti-clock"></i>
                Yêu Cầu Nộp
            </a>
            <a href="{{ route('backend.club.my-documents.accessible') }}" class="btn btn-info">
                <i class="ti ti-eye"></i>
                Văn Bản Được Phân Quyền
            </a>
            @if (auth()->user()->hasPermissionForModuleCode('document_management', 'create_own', 'branch'))
            <a href="{{ route('backend.club.my-documents.create') }}" class="btn btn-primary">
                <i class="ti ti-plus"></i>
                Thêm Văn Bản Mới
            </a>
            @endif
        </div>
    </div>

    <!-- Filter Form -->
    <div class="row text-white">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-body">
                    <form method="GET" action="{{ route('backend.club.my-documents.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <select name="document_type_id" class="form-select">
                                <option value="">Tất cả loại văn bản</option>
                                @foreach ($documentTypes as $type)
                                <option value="{{ $type->id }}" {{ request('document_type_id')==$type->id ? 'selected' :
                                    '' }}>
                                    {{ $type->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Chờ duyệt
                                </option>
                                <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Đã
                                    duyệt</option>
                                <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>Từ chối
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-search"></i>
                                Lọc
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row">
        <div class="col-md-3">
            <div class="card custom-card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="ti ti-file-text fs-1"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">Tổng Văn Bản</h6>
                            <h4 class="mb-0">{{ $documents->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card custom-card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="ti ti-clock fs-1"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">Chờ Duyệt</h6>
                            <h4 class="mb-0">{{ $documents->where('status', 'pending')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card custom-card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="ti ti-check fs-1"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">Đã Duyệt</h6>
                            <h4 class="mb-0">{{ $documents->where('status', 'approved')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card custom-card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="ti ti-x fs-1"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">Từ Chối</h6>
                            <h4 class="mb-0">{{ $documents->where('status', 'rejected')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents List -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-body">


                    @if ($documents->isEmpty())
                    <div class="text-center py-5">
                        <i class="ti ti-file-x fs-1 text-muted"></i>
                        <h5 class="mt-3 text-muted">Chưa có văn bản nào.</h5>
                        <p class="text-muted">Bạn chưa nộp tài liệu nào. Hãy kiểm tra các yêu cầu cần nộp.</p>
                        <div class="mt-3">
                            <a href="{{ route('backend.club.my-documents.requests') }}" class="btn btn-primary me-2">
                                <i class="ti ti-clock"></i>
                                Xem Yêu Cầu Nộp
                            </a>
                            @if (auth()->user()->hasPermissionForModuleCode('document_management', 'create_own',
                            'branch'))
                            <a href="{{ route('backend.club.my-documents.create') }}" class="btn btn-outline-primary">
                                <i class="ti ti-plus"></i>
                                Thêm Văn Bản Mới
                            </a>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="table-responsive mb-4">
                        <table id="data-table" class="table text-nowrap table-bordered">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Tên Văn Bản</th>
                                    <th>Loại Văn Bản</th>
                                    <th style="text-align: center; !important">Quyền Xem</th>
                                    <th style="text-align: center; !important">Trạng Thái</th>
                                    <th style="text-align: center; !important">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($documents as $document)
                                <tr
                                    class="{{ $document->status === 'approved' ? 'table-success' : ($document->status === 'rejected' ? 'table-danger' : 'table-warning') }}">
                                    <td class="text-center">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td>
                                        <div>
                                            <p class="mb-1 fw-semibold">{{ $document->title }}</p>
                                        </div>
                                    </td>
                                    <td>{{ $document->documentType->name }}</td>
                                    <td class="text-center">
                                        @php
                                        $visibilityColor =
                                        [
                                        'public' => 'success',
                                        'private' => 'warning',
                                        'restricted' => 'info',
                                        ][$document->visibility] ?? 'secondary';
                                        $visibilityText =
                                        [
                                        'public' => 'Công khai',
                                        'private' => 'Riêng tư',
                                        'restricted' => 'Hạn chế',
                                        ][$document->visibility] ?? $document->visibility;
                                        @endphp
                                        <span class="badge bg-{{ $visibilityColor }}">{{ $visibilityText }}</span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                        $statusColor =
                                        [
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        ][$document->status] ?? 'secondary';
                                        $statusText =
                                        [
                                        'pending' => 'Chờ duyệt',
                                        'approved' => 'Đã duyệt',
                                        'rejected' => 'Từ chối',
                                        ][$document->status] ?? $document->status;
                                        @endphp
                                        <span class="badge bg-{{ $statusColor }}">{{ $statusText }}</span>

                                        @if ($document->status === 'rejected' && $document->reason_rejected)
                                        <br>
                                        <button class="btn btn-sm btn-outline-info mt-1" data-bs-toggle="popover"
                                            data-bs-title="Lý do từ chối"
                                            data-bs-content="{{ $document->reason_rejected }}" data-bs-trigger="hover">
                                            <i class="ti ti-info-circle"></i>
                                            Xem lý do
                                        </button>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('backend.club.my-documents.show', $document) }}"
                                                class="btn btn-sm btn-primary-light" title="Xem chi tiết">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            <a href="{{ route('backend.club.my-documents.edit', $document) }}"
                                                class="btn btn-sm btn-warning-light" title="Chỉnh sửa">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            <a href="{{ route('backend.club.my-documents.download', $document) }}"
                                                class="btn btn-sm btn-info-light" title="Tải xuống">
                                                <i class="ri-download-line"></i>
                                            </a>
                                            @php
                                            // Check if document is NOT related to a document request
                                            $isRequestDocument = $document->requestUser()->exists();
                                            @endphp
                                            @if (!$isRequestDocument &&
                                            auth()->user()->hasPermissionForModuleCode('document_management',
                                            'create_own', 'branch'))
                                            <button type="button" class="btn btn-sm btn-danger-light"
                                                title="Xóa tài liệu" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $document->id }}">
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
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

@foreach ($documents as $document)
@php
// Check if document is NOT related to a document request
$isRequestDocument = $document->requestUser()->exists();
@endphp
{{-- Delete Modal for each document --}}
@if (!$isRequestDocument && auth()->user()->hasPermissionForModuleCode('document_management', 'create_own', 'branch'))
<div class="modal fade" id="deleteModal{{ $document->id }}" tabindex="-1"
    aria-labelledby="deleteModalLabel{{ $document->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel{{ $document->id }}">
                    <i class="ri-delete-bin-line me-2"></i>
                    Xác Nhận Xóa Văn Bản
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="ri-delete-bin-line text-danger fs-1"></i>
                </div>
                <p class="text-center mb-3">
                    Bạn có chắc chắn muốn xóa tài liệu <strong class="text-danger">{{ $document->title }}</strong> này?
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i>
                    Hủy bỏ
                </button>
                <form method="POST" action="{{ route('backend.club.my-documents.destroy', $document) }}"
                    class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="ri-delete-bin-line me-1"></i>
                        Xóa Văn Bản
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach


@endsection