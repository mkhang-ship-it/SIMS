@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Văn Bản Có Thể Xem</h1>

            <!-- Navigation buttons -->
            <div class="d-flex gap-2">
                <a href="{{ route('backend.club.my-documents.requests') }}" class="btn btn-warning">
                    <i class="ti ti-clock"></i>
                    Yêu Cầu Nộp
                </a>
                <a href="{{ route('backend.club.my-documents.index') }}" class="btn btn-info">
                    <i class="ti ti-file-text"></i>
                    Văn Bản Của Tôi
                </a>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="row">
            <div class="col-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('backend.club.my-documents.accessible') }}" class="row g-3">
                            <div class="col-md-3">
                                <select name="document_type_id" class="form-select">
                                    <option value="">Tất cả loại văn bản</option>
                                    @foreach ($documentTypes as $type)
                                        <option value="{{ $type->id }}"
                                            {{ request('document_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Tìm kiếm tên tài liệu..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-5">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search"></i>
                                    Tìm kiếm
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents List -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">Văn Bản Được Phép Xem</div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mb-4">
                            <table id="data-table" class="table text-nowrap table-bordered">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên Văn Bản</th>
                                        <th>Loại Văn Bản</th>
                                        <th>Người Tạo</th>
                                        <th>Ngày Tạo</th>
                                        <th style="text-align: center; !important">Quyền Xem</th>
                                        <th style="text-align: center; !important">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($documents as $document)
                                        <tr>
                                            <td class="text-center">
                                                {{ $loop->iteration }}
                                            </td>
                                            <td>
                                                {{ $document->title }}
                                            </td>
                                            <td>{{ $document->documentType->name }}</td>
                                            <td>
                                                {{ $document->uploadedByUser->name }}
                                                @if ($document->uploaded_by === auth()->id())
                                                    <span class="badge bg-primary ms-1">Của tôi</span>
                                                @endif
                                            </td>
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
                                                <div class="d-flex justify-content-center gap-1">
                                                    <a href="{{ route('backend.club.my-documents.show', $document) }}"
                                                        class="btn btn-sm btn-primary-light" title="Xem chi tiết">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                    <a href="{{ route('backend.club.my-documents.download', $document) }}"
                                                        class="btn btn-sm btn-info-light" title="Tải xuống">
                                                        <i class="ri-download-line"></i>
                                                    </a>
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
    </div>
@endsection
