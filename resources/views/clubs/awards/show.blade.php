@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Chi Tiết Giải Thưởng</h1>

            <div class="d-none d-md-block">
                <a class="btn btn-primary" href="{{ route('backend.club.awards.index') }}">
                    <i class="ti ti-arrow-left"></i>
                    Quay Lại
                </a>
            </div>

            <div class="dropdown d-md-none">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Hành động
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('backend.club.awards.index') }}">
                            <i class="ti ti-arrow-left"></i> Quay Lại
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Page Header Close -->

        <!-- Award Information Section -->
        <div class="row">
            <div class="col-12">
                <div class="card custom-card">
                    <div class="card-header bg-primary-transparent">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-lg bg-primary me-3">
                                <i class="ri-award-line fs-18"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-1">{{ $award->title }}</h5>
                                <p class="text-muted mb-0">Mã: {{ $award->award_code }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-6 col-md-3">
                                <div class="text-center">
                                    <div class="avatar avatar-xl bg-info-transparent mx-auto mb-2">
                                        <i class="ri-calendar-line fs-24"></i>
                                    </div>
                                    <h6 class="mb-1">Ngày Trao</h6>
                                    <p class="text-muted mb-0">{{ $award->award_date->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="text-center">
                                    <div class="avatar avatar-xl bg-success-transparent mx-auto mb-2">
                                        <i class="ri-user-line fs-24"></i>
                                    </div>
                                    <h6 class="mb-1">Số Người Nhận</h6>
                                    <p class="text-muted mb-0">{{ $award->awardRecipients->count() }} người</p>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="text-center">
                                    <div class="avatar avatar-xl bg-secondary-transparent mx-auto mb-2">
                                        <i class="ri-book-line fs-24"></i>
                                    </div>
                                    <h6 class="mb-1">Học Kỳ</h6>
                                    <p class="text-muted mb-0">
                                        @if ($award->semester)
                                            {{ $award->semester->name }}
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="text-center">
                                    <div class="avatar avatar-xl bg-warning-transparent mx-auto mb-2">
                                        <i class="ri-star-line fs-24"></i>
                                    </div>
                                    <h6 class="mb-1">Trạng Thái</h6>
                                    <span class="badge bg-{{ $award->status_color }}">{{ $award->status_label }}</span>
                                </div>
                            </div>
                        </div>

                        @if ($award->notes)
                            <div class="mt-4 pt-3 border-top">
                                <h6 class="mb-2">
                                    <i class="ri-file-text-line me-2"></i>
                                    Ghi Chú
                                </h6>
                                <p class="text-muted mb-0">{{ $award->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recipients Section -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom-0 pb-0">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            {{-- Bên trái --}}
                            <div class="d-flex align-items-center flex-grow-1">
                                <div class="avatar avatar-md bg-primary-transparent me-3">
                                    <i class="ri-team-line fs-18"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-1">Danh Sách Người Nhận</h5>
                                    <p class="text-muted mb-0 small">Quản lý người nhận giải thưởng</p>
                                </div>
                                <span class="badge bg-info-transparent text-info ms-3 fs-12">
                                    {{ $award->awardRecipients->count() }} người
                                </span>
                            </div>

                            {{-- Bên phải --}}
                            @if (auth()->user()->hasPermissionForModuleCode('award_management', 'create', 'branch'))
                                <div class="mt-2 mt-md-0">
                                    <button type="button" class="btn btn-primary btn-wave" data-bs-toggle="modal"
                                        data-bs-target="#addRecipientModal">
                                        <i class="ri-user-add-line me-2"></i>
                                        Thêm Người Nhận
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card-body pt-4">
                        @if ($award->awardRecipients->count() > 0)
                            <div class="row g-4" id="recipientsList">
                                @foreach ($award->awardRecipients as $index => $recipient)
                                    <div class="col-lg-6 col-xl-4" id="recipient-{{ $recipient->id }}">
                                        <div class="card border-0 shadow-sm h-100 recipient-card"
                                            style="transition: all 0.3s ease;">
                                            <div class="card-body p-4">
                                                <!-- User Info Header -->
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="position-relative me-3">
                                                        <img src="{{ !empty($recipient->user->avatar) ? asset('storage/' . $recipient->user->avatar) : asset('assets/images/avatar_example.png') }}"
                                                            alt="{{ $recipient->user->name }}"
                                                            class="avatar avatar-lg rounded-circle border border-3 border-white shadow-sm"
                                                            style="object-fit: cover;">
                                                        <span
                                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
                                                            {{ $index + 1 }}
                                                        </span>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1 fw-semibold">{{ $recipient->user->name }}</h6>
                                                        @if ($recipient->user->role)
                                                            <div class="d-flex align-items-center mb-1">
                                                                <i class="ri-shield-user-line text-primary me-1 fs-12"></i>
                                                                <span
                                                                    class="text-muted small">{{ $recipient->user->role->name }}</span>
                                                            </div>
                                                        @endif
                                                        @if ($recipient->user->student_code)
                                                            <div class="d-flex align-items-center">
                                                                <i class="ri-user-3-line text-info me-1 fs-12"></i>
                                                                <span
                                                                    class="text-muted small">{{ $recipient->user->student_code }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- PDF Attachment Section -->
                                                @if ($recipient->attachment)
                                                    <div class="border rounded-3 p-3 mb-3 bg-light">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <div class="d-flex align-items-center flex-grow-1">
                                                                <div class="avatar avatar-sm bg-danger-transparent me-2">
                                                                    <i class="ri-file-pdf-line text-danger fs-14"></i>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <p class="mb-0 fw-medium text-dark small">
                                                                        {{ basename($recipient->attachment) }}</p>
                                                                    <p class="mb-0 text-muted" style="font-size: 11px;">
                                                                        PDF
                                                                        Document</p>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex gap-1">

                                                                <button type="button" class="btn btn-sm btn-info-light"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#pdfPreviewModal{{ $recipient->id }}"
                                                                    title="Xem PDF">
                                                                    <i class="ri-eye-line fs-12"></i>
                                                                </button>
                                                                <a href="{{ asset('storage/' . $recipient->attachment) }}"
                                                                    download="{{ basename($recipient->attachment) }}"
                                                                    class="btn btn-sm btn-success-light"
                                                                    title="Tải xuống">
                                                                    <i class="ri-download-line fs-12"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="border rounded-3 p-3 mb-3 bg-light border-dashed">
                                                        <div class="text-center text-muted">
                                                            <i class="ri-file-add-line fs-24 mb-1 opacity-50"></i>
                                                            <p class="mb-0 small">Chưa có file PDF</p>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Action Buttons -->
                                                <div class="d-flex justify-content-center gap-2">
                                                    @if (auth()->user()->hasPermissionForModuleCode('award_management', 'edit', 'branch'))
                                                        <button type="button"
                                                            class="btn btn-sm btn-warning-light flex-fill"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editRecipientModal{{ $recipient->id }}"
                                                            title="Chỉnh sửa">
                                                            <i class="ri-edit-line me-1 fs-12"></i>
                                                            <span class="small">Sửa</span>
                                                        </button>
                                                    @endif

                                                    @if (auth()->user()->hasPermissionForModuleCode('award_management', 'delete', 'branch'))
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger-light flex-fill"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteRecipientModal{{ $recipient->id }}"
                                                            title="Xóa">
                                                            <i class="ri-delete-bin-line me-1 fs-12"></i>
                                                            <span class="small">Xóa</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- PDF Preview Modal -->
                                    @if ($recipient->attachment)
                                        <div class="modal fade" id="pdfPreviewModal{{ $recipient->id }}" tabindex="-1"
                                            aria-labelledby="pdfPreviewModalLabel{{ $recipient->id }}" aria-hidden="true"
                                            data-bs-backdrop="static" data-bs-keyboard="false">
                                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header ">
                                                        <h6 class="modal-title"
                                                            id="pdfPreviewModalLabel{{ $recipient->id }}">
                                                            <i class="ri-file-pdf-line me-2"></i>
                                                            {{ $recipient->user->name }} -
                                                            {{ basename($recipient->attachment) }}
                                                        </h6>
                                                        <div class="d-flex gap-2">
                                                            <a href="{{ asset('storage/' . $recipient->attachment) }}"
                                                                download="{{ basename($recipient->attachment) }}"
                                                                class="btn btn-sm btn-primary">
                                                                <i class="ri-download-line me-1"></i>
                                                                Tải xuống
                                                            </a>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                    </div>
                                                    <div class="modal-body p-0" style="height: 80vh;">
                                                        <iframe src="{{ asset('storage/' . $recipient->attachment) }}"
                                                            width="100%" height="100%" style="border: none;">
                                                            <div class="text-center p-4">
                                                                <i class="ri-file-pdf-line fs-48 text-danger mb-3"></i>
                                                                <h6>Trình duyệt không hỗ trợ hiển thị PDF</h6>
                                                                <a href="{{ asset('storage/' . $recipient->attachment) }}"
                                                                    target="_blank" class="btn btn-primary">
                                                                    <i class="ri-external-link-line me-1"></i>
                                                                    Mở trong tab mới
                                                                </a>
                                                            </div>
                                                        </iframe>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Edit Recipient Modal -->
                                    <div class="modal fade" id="editRecipientModal{{ $recipient->id }}" tabindex="-1"
                                        aria-labelledby="editRecipientModalLabel{{ $recipient->id }}" aria-hidden="true"
                                        data-bs-backdrop="static" data-bs-keyboard="false">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h6 class="modal-title"
                                                        id="editRecipientModalLabel{{ $recipient->id }}">
                                                        <i class="ri-edit-line me-2"></i>
                                                        Chỉnh Sửa Người Nhận
                                                    </h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form
                                                    action="{{ route('backend.club.awards.recipients.update', ['award' => $award->id, 'recipient' => $recipient->id]) }}"
                                                    method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="mb-4">
                                                            <label class="form-label fw-semibold">
                                                                <i class="ri-user-line me-1"></i>
                                                                Người nhận
                                                            </label>
                                                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                                                <img src="{{ !empty($recipient->user->avatar) ? asset('storage/' . $recipient->user->avatar) : asset('assets/images/avatar_example.png') }}"
                                                                    alt="{{ $recipient->user->name }}"
                                                                    class="avatar rounded-circle me-3"
                                                                    style="width: 40px; height: 40px; object-fit: cover;">
                                                                <div>
                                                                    <h6 class="mb-0">{{ $recipient->user->name }}</h6>
                                                                    @if ($recipient->user->student_code)
                                                                        <small
                                                                            class="text-muted">{{ $recipient->user->student_code }}</small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-4">
                                                            <label class="form-label fw-semibold">
                                                                <i class="ri-file-pdf-line text-danger me-1"></i>
                                                                PDF hiện tại
                                                            </label>
                                                            @if ($recipient->attachment)
                                                                <div class="border rounded p-3 bg-success-transparent">
                                                                    <div
                                                                        class="d-flex align-items-center justify-content-between">
                                                                        <div class="d-flex align-items-center">
                                                                            <i
                                                                                class="ri-file-pdf-line text-danger fs-20 me-2"></i>
                                                                            <div>
                                                                                <p class="mb-0 fw-medium">
                                                                                    {{ basename($recipient->attachment) }}
                                                                                </p>
                                                                                <small class="text-muted">PDF
                                                                                    Document</small>
                                                                            </div>
                                                                        </div>
                                                                        <div class="d-flex gap-1">
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-info"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#pdfPreviewModal{{ $recipient->id }}"
                                                                                title="Xem PDF">
                                                                                <i class="ri-eye-line"></i>
                                                                            </button>
                                                                            <a href="{{ asset('storage/' . $recipient->attachment) }}"
                                                                                download="{{ basename($recipient->attachment) }}"
                                                                                class="btn btn-sm btn-success"
                                                                                title="Tải xuống">
                                                                                <i class="ri-download-line"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <div class="text-center p-3 border rounded border-dashed">
                                                                    <i class="ri-file-add-line fs-24 text-muted mb-2"></i>
                                                                    <p class="text-muted mb-0">Chưa có file PDF</p>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">
                                                                <i class="ri-upload-line text-primary me-1"></i>
                                                                Tải lên PDF mới
                                                            </label>
                                                            <input type="file" class="form-control" name="attachment"
                                                                accept=".pdf">
                                                            <div class="form-text">
                                                                <i class="ri-information-line me-1"></i>
                                                                Chỉ chấp nhận file PDF (tối đa 5MB). Để trống nếu không
                                                                muốn thay đổi.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light"
                                                            data-bs-dismiss="modal">
                                                            <i class="ri-close-line me-1"></i>
                                                            Hủy
                                                        </button>
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="ri-save-line me-1"></i>
                                                            Cập nhật
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete Confirmation Modal -->
                                    <div class="modal fade" id="deleteRecipientModal{{ $recipient->id }}" tabindex="-1"
                                        aria-labelledby="deleteRecipientModalLabel{{ $recipient->id }}"
                                        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header border-bottom-0">
                                                    <h6 class="modal-title text-danger"
                                                        id="deleteRecipientModalLabel{{ $recipient->id }}">
                                                        <i class="ri-delete-bin-line me-2"></i>
                                                        Xác Nhận Xóa
                                                    </h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <div class="avatar avatar-xl bg-danger-transparent mx-auto mb-3">
                                                        <i class="ri-error-warning-line fs-24 text-danger"></i>
                                                    </div>
                                                    <h6 class="mb-2">Bạn có chắc chắn muốn xóa?</h6>
                                                    <p class="text-muted mb-3">
                                                        Người nhận <strong>{{ $recipient->user->name }}</strong>
                                                        sẽ bị xóa khỏi giải thưởng này
                                                    </p>
                                                    <div class="alert alert-danger-transparent">
                                                        <i class="ri-alert-line me-1"></i>
                                                        Hành động này không thể hoàn tác!
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top-0 pt-0">
                                                    <button type="button" class="btn btn-light flex-fill"
                                                        data-bs-dismiss="modal">
                                                        <i class="ri-close-line me-1"></i>
                                                        Hủy
                                                    </button>
                                                    <form
                                                        action="{{ route('backend.club.awards.recipients.destroy', ['award' => $award->id, 'recipient' => $recipient->id]) }}"
                                                        method="POST" class="flex-fill">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger w-100">
                                                            <i class="ri-delete-bin-line me-1"></i>
                                                            Xác nhận xóa
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5" id="noRecipientsMessage">
                                <div class="avatar avatar-xxl bg-primary-transparent mx-auto mb-4">
                                    <i class="ri-user-add-line fs-48 text-primary"></i>
                                </div>
                                <h5 class="mb-2">Chưa có người nhận nào</h5>
                                <p class="text-muted mb-4">Hãy thêm người nhận đầu tiên cho giải thưởng này để bắt đầu quản
                                    lý danh sách</p>
                                @if (auth()->user()->hasPermissionForModuleCode('award_management', 'create', 'branch'))
                                    <button type="button" class="btn btn-primary btn-wave" data-bs-toggle="modal"
                                        data-bs-target="#addRecipientModal">
                                        <i class="ri-user-add-line me-2"></i>
                                        Thêm Người Nhận Đầu Tiên
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Add Recipient Modal -->
    <div class="modal fade" id="addRecipientModal" tabindex="-1" aria-labelledby="addRecipientModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="addRecipientModalLabel">Thêm Người Nhận</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addRecipientForm" action="{{ route('backend.club.awards.recipients.store', $award->id) }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Chọn người nhận <span class="text-danger">*</span></label>
                            <select class="form-control" name="user_id" id="add_user_id" required>
                                <option value="">-- Chọn người dùng --</option>
                                @foreach ($availableUsers as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->name }}
                                        @if ($user->student_code)
                                            ({{ $user->student_code }})
                                        @endif
                                        @if ($user->role)
                                            - {{ $user->role->name }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="ri-file-pdf-line text-danger me-1"></i>
                                Tệp PDF đính kèm
                            </label>
                            <input type="file" class="form-control" name="attachment" id="add_attachment"
                                accept=".pdf">
                            <small class="text-muted">
                                <i class="ri-information-line me-1"></i>
                                Chỉ chấp nhận file PDF (tối đa 5MB)
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line"></i>
                            Thêm người nhận
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection
