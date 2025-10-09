{{--
Created: Document detail view showing document information and access management
Features: Document metadata display, download button, access permissions viewer, edit link
--}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <div>
                <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Chi Tiết Tài Liệu</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('backend.club.my-documents.index') }}">Tài liệu của
                                tôi</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                    </ol>
                </nav>
            </div>

            <!-- Action buttons -->
            <div class="d-flex gap-2">
                <a href="{{ route('backend.club.my-documents.index') }}" class="btn btn-light">
                    <i class="ti ti-arrow-left"></i>
                    Quay lại
                </a>
                @if ($document->uploaded_by === auth()->id())
                    <a href="{{ route('backend.club.my-documents.edit', $document) }}" class="btn btn-primary">
                        <i class="ti ti-edit"></i>
                        Chỉnh sửa
                    </a>
                @endif
                <a href="{{ route('backend.club.my-documents.download', $document) }}" class="btn btn-success">
                    <i class="ti ti-download"></i>
                    Tải xuống
                </a>
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
                                <h6 class="mb-0 text-muted">Tên tài liệu:</h6>
                            </div>
                            <div class="col-sm-9">
                                <h5 class="mb-0">{{ $document->title }}</h5>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <h6 class="mb-0 text-muted">Loại văn bản:</h6>
                            </div>
                            <div class="col-sm-9">
                                <span class="badge bg-info">{{ $document->documentType->name }}</span>
                                @if ($document->documentType->description)
                                    <small class="text-muted d-block">{{ $document->documentType->description }}</small>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <h6 class="mb-0 text-muted">Người tạo:</h6>
                            </div>
                            <div class="col-sm-9">
                                {{ $document->uploadedByUser->name }}
                                <small class="text-muted">({{ $document->uploadedByUser->email }})</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <h6 class="mb-0 text-muted">Trạng thái:</h6>
                            </div>
                            <div class="col-sm-9">
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Đang chờ duyệt',
                                        'approved' => 'Đã duyệt',
                                        'rejected' => 'Bị từ chối',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$document->status] ?? 'secondary' }}">
                                    {{ $statusLabels[$document->status] ?? $document->status }}
                                </span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <h6 class="mb-0 text-muted">Quyền xem:</h6>
                            </div>
                            <div class="col-sm-9">
                                @php
                                    $visibilityColors = [
                                        'public' => 'success',
                                        'restricted' => 'warning',
                                        'private' => 'danger',
                                    ];
                                    $visibilityLabels = [
                                        'public' => 'Công khai',
                                        'restricted' => 'Hạn chế',
                                        'private' => 'Riêng tư',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $visibilityColors[$document->visibility] ?? 'secondary' }}">
                                    {{ $visibilityLabels[$document->visibility] ?? $document->visibility }}
                                </span>
                            </div>
                        </div>

                        @if ($document->reason_rejected)
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <h6 class="mb-0 text-muted">Lý do từ chối:</h6>
                                </div>
                                <div class="col-sm-9">
                                    <div class="alert alert-danger">
                                        {{ $document->reason_rejected }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <h6 class="mb-0 text-muted">Ngày tạo:</h6>
                            </div>
                            <div class="col-sm-9">
                                {{ $document->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <h6 class="mb-0 text-muted">Cập nhật gần nhất:</h6>
                            </div>
                            <div class="col-sm-9">
                                {{ $document->updated_at->format('d/m/Y H:i') }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Access Information -->
            <div class="col-xl-4">
                @if ($document->visibility === 'restricted')
                    <div class="card custom-card">
                        <div class="card-header">
                            <div class="card-title">Danh Sách Được Phép Xem</div>
                        </div>
                        <div class="card-body">
                            @if ($document->accessUsers->count() > 0)
                                @foreach ($document->accessUsers as $user)
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar avatar-sm me-3">
                                            <img src="{{ $user->avatar ?? '/assets/images/faces/1.jpg' }}" alt="avatar"
                                                class="rounded-circle">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">{{ $user->name }}</h6>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted mb-0">Chưa có ai được phép xem tài liệu này.</p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- File Information -->
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">Thông Tin File</div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-file fs-3 text-primary me-3"></i>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ basename($document->file_path) }}</h6>
                                <small class="text-muted">
                                    @if (file_exists(storage_path('app/public/' . $document->file_path)))
                                        {{ number_format(filesize(storage_path('app/public/' . $document->file_path)) / 1024, 2) }}
                                        KB
                                    @else
                                        Kích thước không xác định
                                    @endif
                                </small>
                            </div>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('backend.club.my-documents.download', $document) }}"
                                class="btn btn-success btn-sm w-100">
                                <i class="ti ti-download me-2"></i>
                                Tải xuống
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
