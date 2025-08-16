{{-- Feature Requests Index View - Updated with proper permissions and database structure --}}
{{-- Added show modal for detailed view --}}
{{-- Added permission checks for view, edit, delete actions --}}
{{-- Fixed type, priority, and status options to match database schema --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Yêu Cầu Tính Năng / Báo Lỗi</h1>
        </div>

        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="row">
            <!-- Table View Column -->
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
                                        <th style="text-align: center !important;">Câu Lạc Bộ</th>
                                        <th style="text-align: center !important;">Người Tạo</th>
                                        <th style="text-align: center !important;">Ngày Tạo</th>
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
                                                @if ($featureRequest->club)
                                                    <span class="badge bg-primary">{{ $featureRequest->club->name }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
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
                                                    @if (auth()->user()->hasPermissionForModuleCode('feature_request_management', 'view', 'central'))
                                                        <button class="btn btn-icon btn-sm btn-primary-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#showFeatureRequestModal{{ $featureRequest->id }}"
                                                            title="Xem chi tiết">
                                                            <i class="ri-eye-line"></i>
                                                        </button>
                                                    @endif
                                                    @if (auth()->user()->hasPermissionForModuleCode('feature_request_management', 'edit', 'central'))
                                                        <button class="btn btn-icon btn-sm btn-info-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editFeatureRequestModal{{ $featureRequest->id }}"
                                                            title="Chỉnh sửa">
                                                            <i class="ri-edit-line"></i>
                                                        </button>
                                                    @endif
                                                    @if (auth()->user()->hasPermissionForModuleCode('feature_request_management', 'delete', 'central'))
                                                        <button class="btn btn-icon btn-sm btn-danger-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteFeatureRequestModal{{ $featureRequest->id }}"
                                                            title="Xóa">
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
                        @foreach ($featureRequests as $featureRequest)
                            @if (auth()->user()->hasPermissionForModuleCode('feature_request_management', 'edit', 'central'))
                                <!-- Edit Feature Request Modal -->
                                <div class="modal fade" id="editFeatureRequestModal{{ $featureRequest->id }}"
                                    tabindex="-1" aria-labelledby="editFeatureRequestModalLabel{{ $featureRequest->id }}"
                                    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="editFeatureRequestModalLabel{{ $featureRequest->id }}">
                                                    Chỉnh Sửa Yêu Cầu Tính Năng
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form
                                                action="{{ route('backend.feature-requests.update', $featureRequest->id) }}"
                                                method="POST" id="editFeatureRequestForm{{ $featureRequest->id }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="edit_title{{ $featureRequest->id }}"
                                                                    class="form-label">Tiêu Đề <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    id="edit_title{{ $featureRequest->id }}" name="title"
                                                                    value="{{ $featureRequest->title }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="edit_type{{ $featureRequest->id }}"
                                                                    class="form-label">Loại <span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-select"
                                                                    id="edit_type{{ $featureRequest->id }}"
                                                                    name="type" required>
                                                                    <option value="">Chọn loại</option>
                                                                    <option value="feature"
                                                                        {{ $featureRequest->type == 'feature' ? 'selected' : '' }}>
                                                                        Tính Năng</option>
                                                                    <option value="bug"
                                                                        {{ $featureRequest->type == 'bug' ? 'selected' : '' }}>
                                                                        Lỗi</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="edit_priority{{ $featureRequest->id }}"
                                                                    class="form-label">Độ Ưu Tiên <span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-select"
                                                                    id="edit_priority{{ $featureRequest->id }}"
                                                                    name="priority" required>
                                                                    <option value="">Chọn độ ưu tiên
                                                                    </option>
                                                                    <option value="low"
                                                                        {{ $featureRequest->priority == 'low' ? 'selected' : '' }}>
                                                                        Thấp</option>
                                                                    <option value="medium"
                                                                        {{ $featureRequest->priority == 'medium' ? 'selected' : '' }}>
                                                                        Trung Bình</option>
                                                                    <option value="high"
                                                                        {{ $featureRequest->priority == 'high' ? 'selected' : '' }}>
                                                                        Cao</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="edit_status{{ $featureRequest->id }}"
                                                                    class="form-label">Trạng Thái <span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-select"
                                                                    id="edit_status{{ $featureRequest->id }}"
                                                                    name="status" required>
                                                                    <option value="">Chọn trạng thái
                                                                    </option>
                                                                    <option value="new_request"
                                                                        {{ $featureRequest->status == 'new_request' ? 'selected' : '' }}>
                                                                        Yêu Cầu Mới</option>
                                                                    <option value="acknowledged"
                                                                        {{ $featureRequest->status == 'acknowledged' ? 'selected' : '' }}>
                                                                        Đã Nhận</option>
                                                                    <option value="in_progress"
                                                                        {{ $featureRequest->status == 'in_progress' ? 'selected' : '' }}>
                                                                        Đang Thực Hiện</option>
                                                                    <option value="completed"
                                                                        {{ $featureRequest->status == 'completed' ? 'selected' : '' }}>
                                                                        Hoàn Thành</option>
                                                                    <option value="rejected"
                                                                        {{ $featureRequest->status == 'rejected' ? 'selected' : '' }}>
                                                                        Từ Chối</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="edit_description{{ $featureRequest->id }}"
                                                            class="form-label">Mô Tả</label>
                                                        <textarea class="form-control" id="edit_description{{ $featureRequest->id }}" name="description" rows="4">{{ $featureRequest->description }}</textarea>
                                                    </div>
                                                    <div class="mb-3"
                                                        id="rejection_reason_container{{ $featureRequest->id }}"
                                                        style="display: none;">
                                                        <label for="edit_rejection_reason{{ $featureRequest->id }}"
                                                            class="form-label">Lý Do Từ Chối <span
                                                                class="text-danger">*</span></label>
                                                        <textarea class="form-control" id="edit_rejection_reason{{ $featureRequest->id }}" name="rejection_reason"
                                                            rows="3">{{ $featureRequest->rejection_reason }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Hủy</button>
                                                    <button type="submit" class="btn btn-primary">Cập
                                                        Nhật</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (auth()->user()->hasPermissionForModuleCode('feature_request_management', 'view', 'central'))
                                <!-- Show Feature Request Modal -->
                                <div class="modal fade" id="showFeatureRequestModal{{ $featureRequest->id }}"
                                    tabindex="-1"
                                    aria-labelledby="showFeatureRequestModalLabel{{ $featureRequest->id }}"
                                    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="showFeatureRequestModalLabel{{ $featureRequest->id }}">
                                                    Chi Tiết Yêu Cầu Tính Năng
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
                                                                    @case('feature')
                                                                        <span class="badge bg-primary">Tính Năng</span>
                                                                    @break

                                                                    @case('bug')
                                                                        <span class="badge bg-danger">Lỗi</span>
                                                                    @break

                                                                    @default
                                                                        <span
                                                                            class="badge bg-light text-dark">{{ $featureRequest->type }}</span>
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

                                                                    @default
                                                                        <span
                                                                            class="badge bg-light text-dark">{{ $featureRequest->priority }}</span>
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

                                                                    @default
                                                                        <span
                                                                            class="badge bg-light text-dark">{{ $featureRequest->status }}</span>
                                                                @endswitch
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Câu Lạc
                                                                Bộ:</label>
                                                            <p class="form-control-plaintext">
                                                                @if ($featureRequest->club)
                                                                    <span
                                                                        class="badge bg-primary">{{ $featureRequest->club->name }}</span>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Người
                                                                Tạo:</label>
                                                            <p class="form-control-plaintext">
                                                                @if ($featureRequest->createdBy)
                                                                    <span
                                                                        class="fw-semibold">{{ $featureRequest->createdBy->name }}</span>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Mô Tả:</label>
                                                    <div class="form-control-plaintext"
                                                        style="min-height: 80px; background-color: #f8f9fa; padding: 10px; border-radius: 5px;">
                                                        {{ $featureRequest->description ?: 'Không có mô tả' }}
                                                    </div>
                                                </div>
                                                @if ($featureRequest->rejection_reason)
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold text-danger">Lý Do
                                                            Từ
                                                            Chối:</label>
                                                        <div class="form-control-plaintext text-danger"
                                                            style="min-height: 60px; background-color: #fff5f5; padding: 10px; border-radius: 5px; border: 1px solid #fecaca;">
                                                            {{ $featureRequest->rejection_reason }}
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Ngày
                                                                Tạo:</label>
                                                            <p class="form-control-plaintext">
                                                                {{ $featureRequest->created_at->format('d/m/Y H:i') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    @if ($featureRequest->acknowledged_at)
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Ngày
                                                                    Nhận:</label>
                                                                <p class="form-control-plaintext">
                                                                    {{ $featureRequest->acknowledged_at->format('d/m/Y H:i') }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($featureRequest->started_at)
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Ngày Bắt
                                                                    Đầu:</label>
                                                                <p class="form-control-plaintext">
                                                                    {{ $featureRequest->started_at->format('d/m/Y H:i') }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($featureRequest->completed_at)
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Ngày Hoàn
                                                                    Thành:</label>
                                                                <p class="form-control-plaintext">
                                                                    {{ $featureRequest->completed_at->format('d/m/Y H:i') }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    @endif
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

                            @if (auth()->user()->hasPermissionForModuleCode('feature_request_management', 'delete', 'central'))
                                <!-- Delete Feature Request Modal -->
                                <div class="modal fade" id="deleteFeatureRequestModal{{ $featureRequest->id }}"
                                    tabindex="-1"
                                    aria-labelledby="deleteFeatureRequestModalLabel{{ $featureRequest->id }}"
                                    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title text-danger"
                                                    id="deleteFeatureRequestModalLabel{{ $featureRequest->id }}">
                                                    Xác Nhận Xóa</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="text-center">
                                                    <p class="mb-2">Bạn có chắc chắn muốn xóa yêu cầu tính
                                                        năng
                                                        <span
                                                            class="fw-bold text-danger">{{ $featureRequest->title }}</span>
                                                        không?
                                                    </p>
                                                </div>
                                            </div>
                                            <form
                                                action="{{ route('backend.feature-requests.destroy', $featureRequest->id) }}"
                                                method="POST" id="deleteFeatureRequestForm{{ $featureRequest->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Hủy</button>
                                                    <button type="submit" class="btn btn-danger">Xóa</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <!--End::row-1 -->

    </div>

    <!-- Custom JavaScript for rejection reason toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle rejection reason field visibility
            const statusSelects = document.querySelectorAll('select[name="status"]');
            statusSelects.forEach(select => {
                const featureRequestId = select.id.replace('edit_status', '');
                const rejectionContainer = document.getElementById('rejection_reason_container' +
                    featureRequestId);
                const rejectionField = document.getElementById('edit_rejection_reason' + featureRequestId);

                select.addEventListener('change', function() {
                    if (this.value === 'rejected') {
                        rejectionContainer.style.display = 'block';
                        rejectionField.required = true;
                    } else {
                        rejectionContainer.style.display = 'none';
                        rejectionField.required = false;
                    }
                });

                // Initialize on page load
                if (select.value === 'rejected') {
                    rejectionContainer.style.display = 'block';
                    rejectionField.required = true;
                }
            });
        });
    </script>
@endsection
