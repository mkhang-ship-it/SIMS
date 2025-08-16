@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Thông Báo</h1>

            <!-- Nút trên desktop -->
            <div class="d-none d-md-block">
                @if (auth()->user()->hasPermissionForModuleCode('notification_management', 'create', 'central'))
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createNotificationModal">
                        <i class="ti ti-plus"></i>
                        Gửi Thông Báo Mới
                    </button>
                @endif
            </div>

            <!-- Dropdown trên mobile -->
            <div class="dropdown d-md-none">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Hành động
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @if (auth()->user()->hasPermissionForModuleCode('notification_management', 'create', 'central'))
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                data-bs-target="#createNotificationModal">
                                <i class="ti ti-plus"></i> Gửi Thông Báo Mới
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="table-responsive mb-4">
                            <table id="data-table" class="table text-nowrap table-bordered">
                                <thead>
                                    <tr>
                                        <th style="text-align: center !important;">STT</th>
                                        <th>Tiêu đề</th>
                                        <th>Người gửi</th>
                                        <th style="text-align: center !important;">Đối tượng</th>
                                        <th style="text-align: center !important;">Trạng thái</th>
                                        <th style="text-align: center !important;">Ngày gửi</th>
                                        <th style="text-align: center !important;">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($notifications as $index => $notification)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2">
                                                        <span class="avatar avatar-sm bg-primary">
                                                            <i class="ti ti-bell text-white"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-semibold">
                                                            {{ $notification->title }}
                                                            @if ($notification->image_path || ($notification->files_path && count($notification->files_path) > 0))
                                                                <i class="ti ti-paperclip text-muted ms-1"
                                                                    title="Có file đính kèm"></i>
                                                            @endif
                                                        </h6>
                                                        <p class="mb-0 text-muted fs-12">
                                                            {{ Str::limit($notification->content, 50) }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2">
                                                        <span class="avatar avatar-sm bg-light">
                                                            <i class="ti ti-user text-muted"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 fw-semibold">{{ $notification->sender->name }}</p>
                                                        <p class="mb-0 text-muted fs-12">{{ $notification->sender->email }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if ($notification->target_type === 'all_clubs')
                                                    <span class="badge bg-success">Tất cả câu lạc bộ</span>
                                                @else
                                                    <span class="badge bg-info">Câu lạc bộ cụ thể</span>
                                                    <small class="d-block text-muted">
                                                        {{ count($notification->target_club_ids ?? []) }} câu lạc bộ
                                                    </small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($notification->status === 'active')
                                                    <span class="badge bg-success">Hoạt động</span>
                                                @else
                                                    <span class="badge bg-secondary">Vô hiệu</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div>
                                                    <p class="mb-0 fw-semibold">
                                                        {{ $notification->created_at->format('d/m/Y') }}
                                                    </p>
                                                    <p class="mb-0 text-muted fs-12">
                                                        {{ $notification->created_at->format('H:i') }}
                                                    </p>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="gap-2 fs-15">
                                                    <button class="btn btn-icon btn-sm btn-info-light"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#viewNotificationModal{{ $notification->id }}"
                                                        title="Xem chi tiết">
                                                        <i class="ri-eye-line"></i>
                                                    </button>
                                                    <button class="btn btn-icon btn-sm btn-warning-light"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editNotificationModal{{ $notification->id }}"
                                                        title="Chỉnh sửa">
                                                        <i class="ri-edit-line"></i>
                                                    </button>
                                                    <button class="btn btn-icon btn-sm btn-danger-light"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteNotificationModal{{ $notification->id }}"
                                                        title="Xóa">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
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
        <!-- End::row-1 -->

    </div> <!-- Create Notification Modal -->
    <div class="modal fade" id="createNotificationModal" tabindex="-1" aria-labelledby="createNotificationModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createNotificationModalLabel">
                        <i class="ti ti-plus me-1"></i>Gửi Thông Báo Mới
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createNotificationForm" method="POST" action="{{ route('backend.notifications.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="title" class="form-label">Tiêu đề thông báo <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title"
                                    placeholder="Nhập tiêu đề thông báo" required>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="content" class="form-label">Nội dung thông báo <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="content" name="content" rows="4" placeholder="Nhập nội dung thông báo"
                                    required></textarea>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="image" class="form-label">Hình ảnh (tùy chọn)</label>
                                <input type="file" class="form-control" id="image" name="image"
                                    accept="image/*">
                                <small class="text-muted">Định dạng: JPEG, PNG, JPG, GIF, WEBP. Tối đa 2MB</small>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="files" class="form-label">File đính kèm (tùy chọn)</label>
                                <input type="file" class="form-control" id="files" name="files[]" multiple
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar">
                                <small class="text-muted">Định dạng: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, ZIP, RAR.
                                    Tối đa 10MB/file</small>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="target_type" class="form-label">Đối tượng nhận thông báo <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="target_type" name="target_type" required>
                                    <option value="">Chọn đối tượng</option>
                                    <option value="all_clubs">Tất cả câu lạc bộ</option>
                                    <option value="specific_clubs">Câu lạc bộ cụ thể</option>
                                </select>
                            </div>

                            <div class="col-12 mb-3" id="specific_clubs_section" style="display: none;">
                                <label class="form-label">Chọn câu lạc bộ <span class="text-danger">*</span></label>
                                <div class="border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                                    @foreach ($clubs ?? [] as $club)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="target_club_ids[]"
                                                value="{{ $club->id }}" id="club_{{ $club->id }}">
                                            <label class="form-check-label" for="club_{{ $club->id }}">
                                                {{ $club->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <small class="text-muted">Chọn các câu lạc bộ bạn muốn gửi thông báo</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-send me-1"></i>Gửi Thông Báo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View/Edit/Delete Modals for each notification -->
    @foreach ($notifications as $notification)
        <!-- View Notification Modal -->
        @if (auth()->user()->hasPermissionForModuleCode('notification_management', 'view', 'central'))
            <div class="modal fade" id="viewNotificationModal{{ $notification->id }}" tabindex="-1"
                aria-labelledby="viewNotificationModalLabel{{ $notification->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="viewNotificationModalLabel{{ $notification->id }}">
                                <i class="ti ti-eye me-1"></i>Chi Tiết Thông Báo
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <!-- Notification Header -->
                                <div class="mb-3">
                                    <h4 id="notificationDetailTitle" class="text-primary mb-2">{{ $notification->title }}
                                    </h4>
                                    <div class="d-flex justify-content-between text-muted small">
                                        <span>
                                            <i class="ri-user-line me-1"></i>
                                            Người gửi: <span
                                                id="notificationDetailSender">{{ $notification->sender->name }}</span>
                                        </span>
                                        <span>
                                            <i class="ri-time-line me-1"></i>
                                            <span
                                                id="notificationDetailDate">{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                                        </span>
                                    </div>
                                </div>

                                <div class="col-12 mb-3 border rounded p-3">
                                    <div class="text-center">
                                        <img src="{{ asset('storage/' . $notification->image_path) }}"
                                            alt="Hình ảnh thông báo" class="img-fluid rounded"
                                            style="max-width: 100%; max-height: 300px;">

                                    </div>
                                    <div class="mt-3">{!! nl2br(e($notification->content)) !!}</div>
                                </div>

                                @if ($notification->files_path && count($notification->files_path) > 0)
                                    <div class="col-12 mb-3">
                                        <label class="form-label fw-semibold">File đính kèm:</label>
                                        @foreach ($notification->files_path as $file)
                                            <div
                                                class="d-flex align-items-center justify-content-between mb-2 p-2 border rounded">
                                                <div class="d-flex align-items-center">
                                                    <i class="ti ti-file me-2 text-primary"></i>
                                                    <span>{{ $file['name'] }}</span>
                                                    <small
                                                        class="text-muted ms-2">({{ number_format($file['size'] / 1024, 2) }}
                                                        KB)</small>
                                                </div>
                                                <a href="{{ asset('storage/' . $file['path']) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="ti ti-download me-1"></i>Tải xuống
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif


                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Edit Notification Modal -->
        @if (auth()->user()->hasPermissionForModuleCode('notification_management', 'edit', 'central'))
            <div class="modal fade" id="editNotificationModal{{ $notification->id }}" tabindex="-1"
                aria-labelledby="editNotificationModalLabel{{ $notification->id }}" aria-hidden="true"
                data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editNotificationModalLabel{{ $notification->id }}">
                                <i class="ti ti-edit me-1"></i>Cập Nhật Thông Báo
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form method="POST" action="{{ route('backend.notifications.update', $notification->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label for="edit_title_{{ $notification->id }}" class="form-label">Tiêu đề thông
                                            báo
                                            <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control"
                                            id="edit_title_{{ $notification->id }}" name="title"
                                            value="{{ $notification->title }}" required>
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label for="edit_content_{{ $notification->id }}" class="form-label">Nội dung
                                            thông
                                            báo <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="edit_content_{{ $notification->id }}" name="content" rows="4" required>{{ $notification->content }}</textarea>
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label for="edit_image_{{ $notification->id }}" class="form-label">Hình ảnh (tùy
                                            chọn)</label>
                                        @if ($notification->image_path)
                                            <div class="mb-2">
                                                <img src="{{ asset('storage/' . $notification->image_path) }}"
                                                    alt="Hình ảnh hiện tại" class="img-fluid rounded"
                                                    style="max-width: 200px; max-height: 150px;">
                                                <small class="d-block text-muted">Hình ảnh hiện tại</small>
                                            </div>
                                        @endif
                                        <input type="file" class="form-control"
                                            id="edit_image_{{ $notification->id }}" name="image" accept="image/*">
                                        <small class="text-muted">Định dạng: JPEG, PNG, JPG, GIF, WEBP. Tối đa 2MB</small>
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label for="edit_files_{{ $notification->id }}" class="form-label">File đính kèm
                                            (tùy
                                            chọn)</label>
                                        @if ($notification->files_path && count($notification->files_path) > 0)
                                            <div class="mb-2">
                                                <label class="form-label fw-semibold">File hiện tại:</label>
                                                @foreach ($notification->files_path as $index => $file)
                                                    <div
                                                        class="d-flex align-items-center justify-content-between mb-1 p-2 border rounded">
                                                        <div class="d-flex align-items-center">
                                                            <i class="ti ti-file me-2 text-primary"></i>
                                                            <span>{{ $file['name'] }}</span>
                                                            <small
                                                                class="text-muted ms-2">({{ number_format($file['size'] / 1024, 2) }}
                                                                KB)</small>
                                                        </div>
                                                        <a href="{{ asset('storage/' . $file['path']) }}" target="_blank"
                                                            class="btn btn-sm btn-outline-primary">
                                                            <i class="ti ti-download me-1"></i>Tải xuống
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        <input type="file" class="form-control"
                                            id="edit_files_{{ $notification->id }}" name="files[]" multiple
                                            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar">
                                        <small class="text-muted">Định dạng: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT,
                                            ZIP,
                                            RAR. Tối đa 10MB/file</small>
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label for="edit_target_type_{{ $notification->id }}" class="form-label">Đối
                                            tượng
                                            nhận thông báo <span class="text-danger">*</span></label>
                                        <select class="form-select" id="edit_target_type_{{ $notification->id }}"
                                            name="target_type" required>
                                            <option value="all_clubs"
                                                {{ $notification->target_type === 'all_clubs' ? 'selected' : '' }}>
                                                Tất cả câu
                                                lạc bộ</option>
                                            <option value="specific_clubs"
                                                {{ $notification->target_type === 'specific_clubs' ? 'selected' : '' }}>
                                                Câu lạc
                                                bộ cụ thể</option>
                                        </select>
                                    </div>

                                    <div class="col-12 mb-3" id="edit_specific_clubs_section_{{ $notification->id }}"
                                        style="display: {{ $notification->target_type === 'specific_clubs' ? 'block' : 'none' }};">
                                        <label class="form-label">Chọn câu lạc bộ <span
                                                class="text-danger">*</span></label>
                                        <div class="border rounded p-3 bg-light"
                                            style="max-height: 200px; overflow-y: auto;">
                                            @foreach ($clubs ?? [] as $club)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="target_club_ids[]" value="{{ $club->id }}"
                                                        id="edit_club_{{ $notification->id }}_{{ $club->id }}"
                                                        {{ in_array($club->id, $notification->target_club_ids ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="edit_club_{{ $notification->id }}_{{ $club->id }}">
                                                        {{ $club->name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label for="edit_status_{{ $notification->id }}" class="form-label">Trạng thái
                                            <span class="text-danger">*</span></label>
                                        <select class="form-select" id="edit_status_{{ $notification->id }}"
                                            name="status" required>
                                            <option value="active"
                                                {{ $notification->status === 'active' ? 'selected' : '' }}>
                                                Hoạt động</option>
                                            <option value="inactive"
                                                {{ $notification->status === 'inactive' ? 'selected' : '' }}>
                                                Vô
                                                hiệu</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-warning">
                                    <i class="ti ti-device-floppy me-1"></i>Cập Nhật
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Delete Notification Modal -->
        @if (auth()->user()->hasPermissionForModuleCode('notification_management', 'delete', 'central'))
            <div class="modal fade" id="deleteNotificationModal{{ $notification->id }}" tabindex="-1"
                aria-labelledby="deleteNotificationModalLabel{{ $notification->id }}" aria-hidden="true"
                data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-danger" id="deleteNotificationModalLabel{{ $notification->id }}">
                                Xác Nhận Xóa
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center mb-3">
                                <i class="ti ti-trash text-danger" style="font-size: 2.5rem;"></i>
                            </div>
                            <p class="text-center mb-2">Bạn có chắc chắn muốn xóa thông báo <span
                                    class="fw-bold text-danger">{{ $notification->title }}</span> không?
                            </p>
                            <div class="alert alert-warning">
                                <div class="d-flex align-items-start">
                                    <i class="ti ti-info-circle text-warning me-2 mt-1"></i>
                                    <div>
                                        <strong>Người gửi:</strong> {{ $notification->sender->name }}<br>
                                        <strong>Ngày gửi:</strong> {{ $notification->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted text-center small mb-0">
                                <i class="ti ti-alert-circle me-1"></i>
                                Hành động này không thể hoàn tác!
                            </p>
                        </div>
                        <form method="POST" action="{{ route('backend.notifications.destroy', $notification->id) }}"
                            id="deleteNotificationForm{{ $notification->id }}">
                            @csrf
                            @method('DELETE')
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-danger">Xóa</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle target type change for create modal
            const targetTypeSelect = document.getElementById('target_type');
            const specificClubsSection = document.getElementById('specific_clubs_section');

            if (targetTypeSelect) {
                targetTypeSelect.addEventListener('change', function() {
                    if (this.value === 'specific_clubs') {
                        specificClubsSection.style.display = 'block';
                    } else {
                        specificClubsSection.style.display = 'none';
                    }
                });
            }

            // Handle target type change for edit modals
            @if (count($notifications) > 0)
                @foreach ($notifications as $notification)
                    (function() {
                        const editTargetTypeSelect = document.getElementById(
                            'edit_target_type_{{ $notification->id }}');
                        const editSpecificClubsSection = document.getElementById(
                            'edit_specific_clubs_section_{{ $notification->id }}');

                        if (editTargetTypeSelect) {
                            editTargetTypeSelect.addEventListener('change', function() {
                                if (this.value === 'specific_clubs') {
                                    editSpecificClubsSection.style.display = 'block';
                                } else {
                                    editSpecificClubsSection.style.display = 'none';
                                }
                            });
                        }
                    })();
                @endforeach
            @endif
        });
    </script>
@endsection
