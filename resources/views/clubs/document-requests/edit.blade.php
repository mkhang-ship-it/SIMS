{{--
Created edit view for document requests - allows editing document type, deadline, and selected users
Only allows editing if deadline hasn't passed and no documents have been submitted yet
--}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <div>
                <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Chỉnh Sửa Yêu Cầu Nộp Văn Bản</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('backend.club.document-requests.index') }}">Yêu cầu nộp
                                văn bản</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('backend.club.document-requests.show', $documentRequest) }}">
                                {{ $documentRequest->documentType->name }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
                    </ol>
                </nav>
            </div>

            <!-- Action buttons -->
            <div class="d-flex gap-2">
                <a href="{{ route('backend.club.document-requests.show', $documentRequest) }}" class="btn btn-light">
                    <i class="ti ti-arrow-left"></i>
                    Quay lại
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">Thông Tin Yêu Cầu Nộp Văn Bản</div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('backend.club.document-requests.update', $documentRequest) }}" method="POST"
                            id="editRequestForm">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="document_type_id" class="form-label">
                                            Loại Văn Bản <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="document_type_id" name="document_type_id" required>
                                            <option value="">Chọn loại văn bản</option>
                                            @foreach ($documentTypes as $type)
                                                <option value="{{ $type->id }}"
                                                    {{ old('document_type_id', $documentRequest->document_type_id) == $type->id ? 'selected' : '' }}>
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
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="deadline" class="form-label">
                                            Hạn Nộp <span class="text-danger">*</span>
                                        </label>
                                        <input type="datetime-local" class="form-control" id="deadline" name="deadline"
                                            value="{{ old('deadline', $documentRequest->deadline ? $documentRequest->deadline->format('Y-m-d\TH:i') : '') }}"
                                            required min="{{ now()->addDay()->format('Y-m-d\TH:i') }}">
                                        @error('deadline')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Member Selection -->
                            <div class="mb-4">
                                <label class="form-label">
                                    Chọn Thành Viên Cần Nộp <span class="text-danger">*</span>
                                </label>

                                <!-- Select All Options -->
                                <div class="mb-3">
                                    <button type="button" class="btn btn-outline-primary btn-sm me-2"
                                        onclick="selectAll()">
                                        <i class="ti ti-check-all"></i>
                                        Chọn Tất Cả
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearAll()">
                                        <i class="ti ti-x"></i>
                                        Bỏ Chọn Tất Cả
                                    </button>
                                </div>

                                <!-- Member List -->
                                <div class="row">
                                    @foreach ($clubMembers->chunk(ceil($clubMembers->count() / 3)) as $chunk)
                                        <div class="col-md-4">
                                            @foreach ($chunk as $member)
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input member-checkbox" type="checkbox"
                                                        id="member_{{ $member->id }}" name="user_ids[]"
                                                        value="{{ $member->id }}"
                                                        {{ in_array($member->id, old('user_ids', $selectedUserIds)) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="member_{{ $member->id }}">
                                                        <div>
                                                            <strong>{{ $member->name }}</strong>
                                                            <br><small class="text-muted">{{ $member->email }}</small>
                                                            @if ($member->student_code)
                                                                <br><small
                                                                    class="text-muted">{{ $member->student_code }}</small>
                                                            @endif
                                                        </div>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>

                                @error('user_ids')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                                @error('user_ids.*')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror

                                <!-- Selected Count -->
                                <div class="mt-3">
                                    <div class="alert alert-info d-flex align-items-center">
                                        <i class="ti ti-info-circle me-2"></i>
                                        <span>Đã chọn: <strong
                                                id="selectedCount">{{ count(old('user_ids', $selectedUserIds)) }}</strong>
                                            thành viên</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Warning about editing restrictions -->
                            <div class="alert alert-warning d-flex align-items-center mb-4">
                                <i class="ti ti-alert-triangle me-2"></i>
                                <div>
                                    <strong>Lưu ý:</strong> Chỉ có thể chỉnh sửa khi chưa hết hạn nộp và chưa có thành viên
                                    nào nộp văn bản.
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('backend.club.document-requests.show', $documentRequest) }}"
                                    class="btn btn-light">
                                    Hủy
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy"></i>
                                    Cập Nhật
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
            const checkboxes = document.querySelectorAll('.member-checkbox');
            const selectedCountElement = document.getElementById('selectedCount');

            // Update selected count
            function updateSelectedCount() {
                const checked = document.querySelectorAll('.member-checkbox:checked').length;
                selectedCountElement.textContent = checked;
            }

            // Add event listeners to checkboxes
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedCount);
            });

            // Initial count update
            updateSelectedCount();
        });

        function selectAll() {
            const checkboxes = document.querySelectorAll('.member-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            updateSelectedCount();
        }

        function clearAll() {
            const checkboxes = document.querySelectorAll('.member-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const checked = document.querySelectorAll('.member-checkbox:checked').length;
            document.getElementById('selectedCount').textContent = checked;
        }
    </script>

    <style>
        .form-check-label {
            cursor: pointer;
            width: 100%;
        }

        .form-check {
            border: 1px solid #e5e5e5;
            border-radius: 5px;
            padding: 8px 12px;
            transition: all 0.2s ease;
        }

        .form-check:hover {
            background-color: #f8f9fa;
            border-color: #007bff;
        }

        .form-check-input:checked+.form-check-label {
            color: #007bff;
        }

        .form-check-input:checked~* {
            background-color: #e7f3ff;
        }
    </style>
@endsection
