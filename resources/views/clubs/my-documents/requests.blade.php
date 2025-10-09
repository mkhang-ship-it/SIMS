@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Yêu Cầu Nộp Tài Liệu Của Tôi</h1>

        <!-- Navigation buttons -->
        <div class="d-flex gap-2">
            <a href="{{ route('backend.club.my-documents.index') }}" class="btn btn-info">
                <i class="ti ti-file-text"></i>
                Tài liệu của tôi
            </a>
            <a href="{{ route('backend.club.my-documents.accessible') }}" class="btn btn-secondary">
                <i class="ti ti-eye"></i>
                Tài liệu có thể xem
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="row">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-body">
                    <form method="GET" action="{{ route('backend.club.my-documents.requests') }}" class="row g-3">
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Chưa nộp
                                </option>
                                <option value="completed" {{ request('status')=='completed' ? 'selected' : '' }}>Đã
                                    nộp</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="deadline_status" class="form-select">
                                <option value="">Tất cả hạn nộp</option>
                                <option value="active" {{ request('deadline_status')=='active' ? 'selected' : '' }}>
                                    Còn hạn</option>
                                <option value="expired" {{ request('deadline_status')=='expired' ? 'selected' : '' }}>
                                    Hết hạn</option>
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
                            <h6 class="card-title mb-1">Tổng Yêu Cầu</h6>
                            <h4 class="mb-0">{{ $documentRequestUsers->count() }}</h4>
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
                            <h6 class="card-title mb-1">Chưa Nộp</h6>
                            <h4 class="mb-0">{{ $documentRequestUsers->whereNull('document_id')->count() }}</h4>
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
                            <h6 class="card-title mb-1">Đã Nộp</h6>
                            <h4 class="mb-0">{{ $documentRequestUsers->whereNotNull('document_id')->count() }}</h4>
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
                            <i class="ti ti-alert-triangle fs-1"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">Quá Hạn</h6>
                            <h4 class="mb-0">
                                {{ $documentRequestUsers->filter(function ($item) {return
                                $item->documentRequest->deadline < now() && !$item->document_id;})->count() }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Requests List -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="table-responsive mb-4">
                        <table id="data-table" class="table text-nowrap table-bordered">
                            <thead>
                                <tr>
                                    <th style="text-align: center; !important;">STT</th>
                                    <th>Loại Văn Bản</th>
                                    <th style="text-align: center; !important;">Hạn Nộp</th>
                                    <th style="text-align: center; !important;">Trạng Thái</th>
                                    <th style="text-align: center; !important;">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($documentRequestUsers as $requestUser)
                                @php
                                $isExpired = $requestUser->documentRequest->deadline < now();
                                    $isCompleted=!is_null($requestUser->document_id);
                                    @endphp
                                    <tr>
                                        <td class="text-center">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td>
                                            <div>
                                                <span class="fw-semibold">{{
                                                    $requestUser->documentRequest->documentType->name }}</span>
                                                @if ($requestUser->documentRequest->documentType->template_file_path)
                                                <br>
                                                <a href="{{ route('backend.club.document-types.download-template', $requestUser->documentRequest->documentType->id) }}"
                                                    class="btn btn-sm btn-outline-info mt-1">
                                                    <i class="ti ti-download"></i>
                                                    File mẫu
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="mb-1">
                                                <strong class="{{ $isExpired ? 'text-danger' : 'text-success' }}">
                                                    {{ $requestUser->documentRequest->deadline->format('d/m/Y H:i') }}
                                                </strong>
                                            </div>
                                            @if ($isExpired)
                                            <span class="badge bg-danger">Đã hết hạn</span>
                                            @else
                                            <span class="badge bg-success">{{
                                                $requestUser->documentRequest->deadline->diffForHumans() }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($isCompleted)
                                            <div class="mb-2">
                                                <span class="badge bg-success">Đã nộp</span>
                                            </div>
                                            <small class="text-muted">
                                                vào lúc {{ $requestUser->created_at->format('d/m/Y H:i') }}
                                            </small>
                                            @if ($requestUser->document)
                                            <br>
                                            @php
                                            $docStatusColor =
                                            [
                                            'pending' => 'warning',
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            ][$requestUser->document->status] ?? 'secondary';
                                            $docStatusText =
                                            [
                                            'pending' => 'Chờ duyệt',
                                            'approved' => 'Đã duyệt',
                                            'rejected' => 'Từ chối',
                                            ][$requestUser->document->status] ??
                                            $requestUser->document->status;
                                            @endphp
                                            <span class="badge bg-{{ $docStatusColor }} mt-1">{{ $docStatusText
                                                }}</span>
                                            @endif
                                            @else
                                            @if ($isExpired)
                                            <span class="badge bg-danger">Quá hạn</span>
                                            @else
                                            <span class="badge bg-warning">Chưa nộp</span>
                                            @endif
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($isCompleted)
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('backend.club.my-documents.download', $requestUser->document) }}"
                                                    class="btn btn-sm btn-info-light" title="Tải xuống">
                                                    <i class="ri-download-line"></i>
                                                </a>
                                            </div>
                                            @else
                                            @if (!$isExpired)
                                            <a href="{{ route('backend.club.my-documents.upload-form', $requestUser) }}"
                                                class="btn btn-sm btn-success">
                                                <i class="ti ti-upload"></i>
                                                Nộp tài liệu
                                            </a>
                                            @else
                                            <span class="text-muted">Đã quá hạn</span>
                                            @endif
                                            @endif
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
</div>
@endsection