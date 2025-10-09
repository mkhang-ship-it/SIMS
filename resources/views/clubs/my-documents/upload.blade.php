@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Nộp Văn Bản</h1>

        </div>

        <!-- Action buttons -->
        <div class="d-flex gap-2">
            <a href="{{ route('backend.club.my-documents.requests') }}" class="btn btn-light">
                <i class="ti ti-arrow-left"></i>
                Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Request Information -->
        <div class="col-xl-4">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Thông Tin Yêu Cầu</div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Loại văn bản:</strong><br>
                        {{ $documentRequestUser->documentRequest->documentType->name }}
                    </div>

                    <div class="mb-3">
                        <strong>Hạn nộp:</strong><br>
                        <span
                            class="fs-5 {{ $documentRequestUser->documentRequest->deadline < now() ? 'text-danger' : 'text-success' }}">
                            {{ $documentRequestUser->documentRequest->deadline->format('d/m/Y H:i') }}
                        </span>
                        <br>
                        @if ($documentRequestUser->documentRequest->deadline < now()) <span class="badge bg-danger">Đã
                            hết hạn</span>
                            @else
                            <span class="badge bg-success">Còn
                                {{ $documentRequestUser->documentRequest->deadline->diffForHumans() }}</span>
                            @endif
                    </div>

                    @if ($documentRequestUser->documentRequest->documentType->template_file_path)
                    <div class="mb-3">
                        <strong>File mẫu:</strong><br>
                        <a href="{{ route('backend.club.document-types.download-template', $documentRequestUser->documentRequest->documentType->id) }}"
                            class="btn btn-outline-primary btn-sm">
                            <i class="ti ti-download"></i>
                            Tải xuống file mẫu
                        </a>
                    </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Upload Form -->
        <div class="col-xl-8">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Nộp Văn Bản</div>
                </div>
                <div class="card-body">

                    @if ($documentRequestUser->documentRequest->deadline < now()) <div class="text-center py-5">
                        <i class="ti ti-clock-x fs-1 text-danger"></i>
                        <h5 class="mt-3 text-danger">Yêu cầu đã hết hạn nộp</h5>
                        <p class="text-muted">Hạn nộp đã hết hạn vào
                            {{ $documentRequestUser->documentRequest->deadline->format('d/m/Y H:i') }}</p>
                        <a href="{{ route('backend.club.my-documents.requests') }}" class="btn btn-primary">
                            Quay lại danh sách
                        </a>
                </div>
                @elseif($documentRequestUser->document_id)
                <div class="text-center py-5">
                    <i class="ti ti-check-circle fs-1 text-success"></i>
                    <h5 class="mt-3 text-success">Đã nộp văn bản</h5>
                    <p class="text-muted">Bạn đã nộp văn bản cho yêu cầu này vào
                        {{ $documentRequestUser->created_at->format('d/m/Y H:i') }}</p>
                    <a href="{{ route('backend.club.my-documents.show', $documentRequestUser->document) }}"
                        class="btn btn-primary">
                        Xem văn bản đã nộp
                    </a>
                </div>
                @else
                <form action="{{ route('backend.club.my-documents.upload', $documentRequestUser) }}" method="POST"
                    enctype="multipart/form-data" id="uploadForm">
                    @csrf

                    <div class="mb-4">
                        <label for="title" class="form-label">
                            Tên Văn Bản <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}"
                            required placeholder="VD: Bản cam kết tuân thủ nội quy - Nguyễn Văn A">
                        @error('title')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="document_file" class="form-label">
                            File Văn Bản <span class="text-danger">*</span>
                        </label>
                        <div>
                            <input type="file" class="form-control" id="document_file" name="document_file" required
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        </div>
                        @error('document_file')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror


                    </div>



                    <div class="alert alert-danger">
                        <h6><i class="ti ti-alert-triangle"></i> Chú ý:</h6>
                        <p class="mb-0">Bạn chỉ có thể nộp <strong>một lần duy nhất</strong> cho yêu cầu này.
                        </p>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('backend.club.my-documents.requests') }}" class="btn btn-light">
                            Hủy
                        </a>
                        <button type="submit" class="btn btn-success" id="submitBtn">
                            <i class="ti ti-upload"></i>
                            Nộp Văn Bản
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

</div>
@endsection