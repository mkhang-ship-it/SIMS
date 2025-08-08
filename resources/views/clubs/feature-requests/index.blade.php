{{--
    Club Feature Requests Index View - Updated with proper permissions and database structure
    Changes made:
    - Fixed status values to match database enum (new_request, acknowledged, in_progress, completed, rejected)
    - Removed 'improvement' type option (only 'feature', 'bug' allowed)
    - Removed 'critical' priority option (only 'low', 'medium', 'high' allowed)
    - Updated status display labels to match Vietnamese translations
    - Added permission checks for view and create actions
--}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Yêu Cầu Tính Năng / Báo Lỗi</h1>

            @if (auth()->user()->hasPermissionForModuleCode('feature_request_management', 'create', 'branch'))
                <!-- Nút trên desktop -->
                <div class="d-none d-md-block">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFeatureRequestModal">
                        <i class="ti ti-plus"></i>
                        Gửi Yêu Cầu Mới
                    </button>
                </div>
                <!-- Dropdown trên mobile -->
                <div class="dropdown d-md-none">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Hành động
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                data-bs-target="#addFeatureRequestModal">
                                <i class="ti ti-plus"></i> Gửi Yêu Cầu Mới
                            </a>
                        </li>
                    </ul>
                </div>
            @endif
        </div>

        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="row">
            <div class="col-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            Danh Sách Yêu Cầu Tính Năng / Báo Lỗi
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mb-4">
                            <table class="table text-nowrap table-bordered" id="data-table">
                                <thead>
                                    <tr>
                                        <th style="text-align: center !important;">STT</th>
                                        <th>Tiêu Đề</th>
                                        <th>Mô Tả</th>
                                        <th style="text-align: center !important;">Loại</th>
                                        <th style="text-align: center !important;">Độ Ưu Tiên</th>
                                        <th style="text-align: center !important;">Trạng Thái</th>
                                        <th style="text-align: center !important;">Người Gửi</th>
                                        <th style="text-align: center !important;">Ngày Gửi</th>
                                        <th style="text-align: center !important;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($featureRequests as $index => $featureRequest)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="fw-semibold">{{ $featureRequest->title }}</td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 200px;"
                                                    title="{{ $featureRequest->description }}">
                                                    {{ Str::limit($featureRequest->description, 100) }}
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @switch($featureRequest->type)
                                                    @case('bug')
                                                        <span class="badge bg-danger">Lỗi</span>
                                                    @break

                                                    @case('feature')
                                                        <span class="badge bg-primary">Tính Năng</span>
                                                    @break

                                                    @default
                                                        <span class="badge bg-light text-dark">{{ $featureRequest->type }}</span>
                                                @endswitch
                                            </td>
                                            <td class="text-center">
                                                @switch($featureRequest->priority)
                                                    @case('low')
                                                        <span class="badge bg-success">Thấp</span>
                                                    @break

                                                    @case('medium')
                                                        <span class="badge bg-warning">Trung Bình</span>
                                                    @break

                                                    @case('high')
                                                        <span class="badge bg-orange">Cao</span>
                                                    @break

                                                    @default
                                                        <span
                                                            class="badge bg-light text-dark">{{ $featureRequest->priority }}</span>
                                                @endswitch
                                            </td>
                                            <td class="text-center">
                                                @switch($featureRequest->status)
                                                    @case('new_request')
                                                        <span class="badge bg-secondary">Yêu Cầu Mới</span>
                                                    @break

                                                    @case('acknowledged')
                                                        <span class="badge bg-info">Đã Nhận</span>
                                                    @break

                                                    @case('in_progress')
                                                        <span class="badge bg-warning">Đang Thực Hiện</span>
                                                    @break

                                                    @case('completed')
                                                        <span class="badge bg-success">Hoàn Thành</span>
                                                    @break

                                                    @case('rejected')
                                                        <span class="badge bg-danger">Từ Chối</span>
                                                    @break

                                                    @default
                                                        <span class="badge bg-light text-dark">{{ $featureRequest->status }}</span>
                                                @endswitch
                                            </td>
                                            <td class="text-center">
                                                @if ($featureRequest->createdBy)
                                                    <span class="fw-semibold">{{ $featureRequest->createdBy->name }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ $featureRequest->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="text-center">
                                                <div class="gap-2 fs-15">
                                                    @if (auth()->user()->hasPermissionForModuleCode('feature_request_management', 'view', 'branch'))
                                                        <button class="btn btn-icon btn-sm btn-info-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#viewFeatureRequestModal{{ $featureRequest->id }}"
                                                            title="Xem chi tiết">
                                                            <i class="ri-eye-line"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>

                                        @if (auth()->user()->hasPermissionForModuleCode('feature_request_management', 'view', 'branch'))
                                            <!-- View Feature Request Modal -->
                                            <div class="modal fade" id="viewFeatureRequestModal{{ $featureRequest->id }}"
                                                tabindex="-1"
                                                aria-labelledby="viewFeatureRequestModalLabel{{ $featureRequest->id }}"
                                                aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="viewFeatureRequestModalLabel{{ $featureRequest->id }}">
                                                                Chi
                                                                Tiết Yêu Cầu Tính Năng
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold">Tiêu
                                                                            Đề:</label>
                                                                        <p class="form-control-plaintext">
                                                                            {{ $featureRequest->title }}</p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold">Loại:</label>
                                                                        <p class="form-control-plaintext">
                                                                            @switch($featureRequest->type)
                                                                                @case('bug')
                                                                                    <span class="badge bg-danger">Lỗi</span>
                                                                                @break

                                                                                @case('feature')
                                                                                    <span class="badge bg-primary">Tính Năng</span>
                                                                                @break
                                                                            @endswitch
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold">Độ Ưu
                                                                            Tiên:</label>
                                                                        <p class="form-control-plaintext">
                                                                            @switch($featureRequest->priority)
                                                                                @case('low')
                                                                                    <span class="badge bg-success">Thấp</span>
                                                                                @break

                                                                                @case('medium')
                                                                                    <span class="badge bg-warning">Trung
                                                                                        Bình</span>
                                                                                @break

                                                                                @case('high')
                                                                                    <span class="badge bg-orange">Cao</span>
                                                                                @break
                                                                            @endswitch
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold">Trạng
                                                                            Thái:</label>
                                                                        <p class="form-control-plaintext">
                                                                            @switch($featureRequest->status)
                                                                                @case('new_request')
                                                                                    <span class="badge bg-secondary">Yêu Cầu
                                                                                        Mới</span>
                                                                                @break

                                                                                @case('acknowledged')
                                                                                    <span class="badge bg-info">Đã Nhận</span>
                                                                                @break

                                                                                @case('in_progress')
                                                                                    <span class="badge bg-warning">Đang Thực
                                                                                        Hiện</span>
                                                                                @break

                                                                                @case('completed')
                                                                                    <span class="badge bg-success">Hoàn
                                                                                        Thành</span>
                                                                                @break

                                                                                @case('rejected')
                                                                                    <span class="badge bg-danger">Từ Chối</span>
                                                                                @break
                                                                            @endswitch
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Mô Tả:</label>
                                                                <div class="form-control-plaintext"
                                                                    style="min-height: 100px; background-color: #f8f9fa; padding: 10px; border-radius: 5px;">
                                                                    {{ $featureRequest->description }}
                                                                </div>
                                                            </div>
                                                            @if ($featureRequest->rejection_reason)
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-semibold text-danger">Lý Do
                                                                        Từ
                                                                        Chối:</label>
                                                                    <div class="form-control-plaintext text-danger"
                                                                        style="min-height: 60px; background-color: #fff5f5; padding: 10px; border-radius: 5px;">
                                                                        {{ $featureRequest->rejection_reason }}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold">Người
                                                                            Gửi:</label>
                                                                        <p class="form-control-plaintext">
                                                                            {{ $featureRequest->createdBy->name ?? 'N/A' }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold">Ngày
                                                                            Gửi:</label>
                                                                        <p class="form-control-plaintext">
                                                                            {{ $featureRequest->created_at->format('d/m/Y H:i') }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Đóng</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End::row-1 -->

    </div>

    @if (auth()->user()->hasPermissionForModuleCode('feature_request_management', 'create', 'branch'))
        <!-- Add Feature Request Modal -->
        <div class="modal fade" id="addFeatureRequestModal" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="addFeatureRequestModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addFeatureRequestModalLabel">Gửi Yêu Cầu Tính Năng Mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('backend.club.feature-requests.store') }}" method="POST"
                        id="addFeatureRequestForm">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Tiêu Đề <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title" name="title"
                                            required value="{{ old('title') }}" placeholder="Nhập tiêu đề yêu cầu">
                                        @error('title')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Loại <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="type" name="type" required>
                                            <option value="">Chọn loại</option>
                                            <option value="bug" {{ old('type') == 'bug' ? 'selected' : '' }}>Lỗi
                                            </option>
                                            <option value="feature" {{ old('type') == 'feature' ? 'selected' : '' }}>Tính
                                                Năng
                                            </option>
                                        </select>
                                        @error('type')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="priority" class="form-label">Độ Ưu Tiên <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="priority" name="priority" required>
                                            <option value="">Chọn độ ưu tiên</option>
                                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Thấp
                                            </option>
                                            <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>
                                                Trung
                                                Bình</option>
                                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Cao
                                            </option>
                                        </select>
                                        @error('priority')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Mô Tả <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="6" required
                                    placeholder="Mô tả chi tiết yêu cầu tính năng của bạn...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary">Gửi Yêu Cầu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
