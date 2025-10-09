@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Tài Liệu</h1>

        <!-- Nút trên desktop -->
        <div class="d-none d-md-block">
            <a href="{{ route('backend.club.document-requests.index') }}" class="btn btn-info me-2">
                <i class="ti ti-list"></i>
                Yêu Cầu Nộp
            </a>
        </div>

        <!-- Dropdown trên mobile -->
        <div class="dropdown d-md-none">
            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                Hành động
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('backend.club.document-requests.index') }}">
                        <i class="ti ti-list"></i> Yêu Cầu Nộp
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="row">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-body">
                    <form method="GET" action="{{ route('backend.club.documents.index') }}" class="row g-3">
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
                                <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Chờ
                                    duyệt</option>
                                <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Đã
                                    duyệt</option>
                                <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>Từ
                                    chối</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tên tài liệu..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-search"></i>
                                Tìm kiếm
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents Table -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="table-responsive mb-4">
                        <table class="table text-nowrap table-bordered" id="data-table">
                            <thead>
                                <tr>
                                    <th style="text-align: center !important;">STT</th>
                                    <th>Tên Tài Liệu</th>
                                    <th>Loại Văn Bản</th>
                                    <th>Người Nộp</th>
                                    <th>Ngày Nộp</th>
                                    <th style="text-align: center !important;">Quyền Xem</th>
                                    <th style="text-align: center !important;">Trạng Thái</th>
                                    <th style="text-align: center !important;">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($documents as $index => $document)
                                <tr>
                                    <td class="text-center">
                                        {{ ($documents->currentPage() - 1) * $documents->perPage() + $loop->iteration }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <p class="mb-0 fw-semibold">{{ $document->title }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $document->documentType->name }}</td>
                                    <td>{{ $document->uploadedByUser->name }}</td>
                                    <td>{{ $document->created_at->format('d/m/Y H:i') }}</td>
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
                                    </td>
                                    <td class="text-center">
                                        <div class="gap-2 fs-15">
                                            @if (auth()->user()->hasPermissionForModuleCode('document_management',
                                            'view', 'branch'))
                                            <a href="{{ route('backend.club.documents.show', $document) }}"
                                                class="btn btn-icon btn-sm btn-primary-light" title="Xem chi tiết">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            @endif

                                            @if (auth()->user()->hasPermissionForModuleCode('document_management',
                                            'view', 'branch'))
                                            <a href="{{ route('backend.club.documents.download', $document) }}"
                                                class="btn btn-icon btn-sm btn-info-light" title="Tải xuống">
                                                <i class="ri-download-line"></i>
                                            </a>
                                            @endif

                                            @if (auth()->user()->hasPermissionForModuleCode('document_management',
                                            'delete', 'branch'))
                                            <button class="btn btn-icon btn-sm btn-danger-light" data-bs-toggle="modal"
                                                data-bs-target="#deleteDocumentModal{{ $document->id }}" title="Xóa">
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

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $documents->appends(request()->query())->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@foreach ($documents as $document)
<!-- Delete Document Modal -->
@if (auth()->user()->hasPermissionForModuleCode('document_management', 'delete', 'branch'))
<div class="modal fade" id="deleteDocumentModal{{ $document->id }}" tabindex="-1"
    aria-labelledby="deleteDocumentModalLabel{{ $document->id }}" aria-hidden="true" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteDocumentModalLabel{{ $document->id }}">
                    Xác Nhận Xóa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mb-2">Bạn có chắc chắn muốn xóa tài liệu
                    <strong class="text-danger">
                        {{ $document->title }}
                    </strong> này?
                </p>
                <p class="text-danger">
                    <i class="ri-alert-line"></i>
                    Thao tác này không thể hoàn tác và sẽ xóa cả file tài liệu.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                <form action="{{ route('backend.club.documents.destroy', $document) }}" method="POST"
                    style="display: inline;">
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
@endsection