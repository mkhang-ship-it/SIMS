{{-- Updated events index view with datatable structure matching other index views
Features: Dynamic status options based on user role and current event status
Add/Edit modals now respect the new workflow (pending, active, cancelled, completed)
QR Code modal implementation with time-based validation:
- Only shows QR code during active event time (using shouldShowQRCode() method)
- Shows appropriate messages for different event states (not approved, not started, ended, etc.)
- Displays event details and check-in list link when QR is available
Updated: Added image management button for accessing event image gallery from the main list --}}

@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Sự Kiện</h1>

            <!-- Nút trên desktop -->
            <div class="d-flex gap-2 align-items-center">
                @if (auth()->user()->hasPermissionForModuleCode('event_management', 'view', 'branch'))
                    <div class="d-none d-md-block">
                        <a href="{{ route('backend.club.events.timetable') }}" class="btn btn-primary-light">
                            <i class="ti ti-calendar"></i> Xem Lịch Sự Kiện
                        </a>
                    </div>
                @endif
                @if (auth()->user()->hasPermissionForModuleCode('event_management', 'create', 'branch'))
                    <div class="d-none d-md-block">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
                            <i class="ti ti-plus"></i>
                            Thêm Mới
                        </button>
                    </div>
                @endif

            </div>

            <!-- Dropdown trên mobile -->
            <div class="dropdown d-md-none">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Hành động
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @if (auth()->user()->hasPermissionForModuleCode('event_management', 'create', 'branch'))
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addEventModal">
                                <i class="ti ti-plus"></i> Thêm Mới
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasPermissionForModuleCode('event_management', 'view', 'branch'))
                        <li>
                            <a class="dropdown-item" href="{{ route('backend.club.events.timetable') }}">
                                <i class="ti ti-calendar"></i> Xem Lịch Sự Kiện
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
                            <table class="table text-nowrap table-bordered" id="data-table">
                                <thead>
                                    <tr>
                                        <th style="text-align: center !important;">STT</th>
                                        <th>Mã Sự Kiện</th>
                                        <th>Tên Sự Kiện</th>
                                        <th style="text-align: center !important;">Ngày Diễn Ra</th>
                                        <th style="text-align: center !important;">Thời Gian</th>
                                        <th style="text-align: center !important;">Địa Điểm</th>
                                        <th style="text-align: center !important;">QR Code</th>
                                        <th style="text-align: center !important;">Trạng Thái</th>
                                        <th style="text-align: center !important;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($events as $index => $event)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                {{ $event->event_code }}
                                            </td>
                                            <td>
                                                {{ $event->name }}
                                            </td>
                                            <td class="text-center">
                                                <div class="fw-medium">{{ $event->formatted_event_date }}</div>
                                                <small class="text-muted">{{ $event->semester->name ?? '-' }}</small>
                                            </td>
                                            <td class="text-center">
                                                <div>{{ $event->slot->name ?? '-' }}</div>
                                                <small class="text-muted">{{ $event->time_range }}</small>
                                            </td>
                                            <td class="text-center">{{ $event->classRoom->name ?? '-' }}</td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-icon btn-sm btn-primary-light"
                                                    data-bs-toggle="modal" data-bs-target="#qrCodeModal{{ $event->id }}"
                                                    title="Xem QR Code">
                                                    <i class="bi bi-qr-code-scan"></i>
                                                </button>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $event->status_color }}">
                                                    {{ $event->status_label }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="gap-2 fs-15">
                                                    @if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'view', 'branch'))
                                                        <a href="{{ route('backend.club.event-plans.show', $event->id) }}"
                                                            class="btn btn-icon btn-sm btn-success-light"
                                                            title="Kế hoạch sự kiện">
                                                            <i class="ti ti-file-text"></i>
                                                        </a>
                                                    @endif
                                                    @if (auth()->user()->hasPermissionForModuleCode('event_image_management', 'view', 'branch'))
                                                        <a href="{{ route('backend.club.events.images.index', $event->id) }}"
                                                            class="btn btn-icon btn-sm btn-primary-light"
                                                            title="Quản lý ảnh">
                                                            <i class="ri-image-line"></i>
                                                        </a>
                                                    @endif
                                                    @if (auth()->user()->hasPermissionForModuleCode('event_management', 'view', 'branch'))
                                                        <a href="{{ route('backend.club.events.show', $event->id) }}"
                                                            class="btn btn-icon btn-sm btn-warning-light"
                                                            title="Xem chi tiết">
                                                            <i class="ri-eye-line"></i>
                                                        </a>
                                                    @endif

                                                    @if (auth()->user()->hasPermissionForModuleCode('event_management', 'edit', 'branch') && $event->canBeEdited())
                                                        <button type="button" class="btn btn-icon btn-sm btn-info-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editEventModal{{ $event->id }}"
                                                            title="Chỉnh sửa">
                                                            <i class="ri-edit-line"></i>
                                                        </button>
                                                    @endif

                                                    @if (auth()->user()->hasPermissionForModuleCode('event_management', 'delete', 'branch') && $event->canBeDeleted())
                                                        <button type="button" class="btn btn-icon btn-sm btn-danger-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteEventModal{{ $event->id }}"
                                                            title="Xóa">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Edit Event Modal -->
                                        @if (auth()->user()->hasPermissionForModuleCode('event_management', 'edit', 'branch') && $event->canBeEdited())
                                            @php
                                                $availableStatuses = $event->getAvailableStatusOptions(auth()->user());
                                            @endphp
                                            <div class="modal fade" id="editEventModal{{ $event->id }}" tabindex="-1"
                                                aria-labelledby="editEventModalLabel{{ $event->id }}" aria-hidden="true"
                                                data-bs-backdrop="static" data-bs-keyboard="false">
                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="editEventModalLabel{{ $event->id }}">
                                                                Chỉnh Sửa Sự Kiện
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <form
                                                            action="{{ route('backend.club.events.update', $event->id) }}"
                                                            method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="edit_name{{ $event->id }}"
                                                                                class="form-label">
                                                                                Tên Sự Kiện <span
                                                                                    class="text-danger">*</span>
                                                                            </label>
                                                                            <input type="text" class="form-control"
                                                                                id="edit_name{{ $event->id }}"
                                                                                name="name"
                                                                                value="{{ $event->name }}" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label
                                                                                for="edit_semester_id{{ $event->id }}"
                                                                                class="form-label">
                                                                                Học Kỳ <span class="text-danger">*</span>
                                                                            </label>
                                                                            <select class="form-select"
                                                                                id="edit_semester_id{{ $event->id }}"
                                                                                name="semester_id" required>
                                                                                @foreach ($semesters as $semester)
                                                                                    <option value="{{ $semester->id }}"
                                                                                        {{ $event->semester_id === $semester->id ? 'selected' : '' }}>
                                                                                        {{ $semester->name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label
                                                                                for="edit_event_date{{ $event->id }}"
                                                                                class="form-label">
                                                                                Ngày Diễn Ra <span
                                                                                    class="text-danger">*</span>
                                                                            </label>
                                                                            <input type="date" class="form-control"
                                                                                id="edit_event_date{{ $event->id }}"
                                                                                name="event_date"
                                                                                value="{{ $event->event_date ? $event->event_date->format('Y-m-d') : '' }}"
                                                                                required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="edit_slot_id{{ $event->id }}"
                                                                                class="form-label">
                                                                                Thời Gian Diễn Ra <span
                                                                                    class="text-danger">*</span>
                                                                            </label>
                                                                            <select class="form-select"
                                                                                id="edit_slot_id{{ $event->id }}"
                                                                                name="slot_id" required>
                                                                                @foreach ($slots as $slot)
                                                                                    <option value="{{ $slot->id }}"
                                                                                        {{ $event->slot_id === $slot->id ? 'selected' : '' }}>
                                                                                        {{ $slot->name }}
                                                                                        ({{ $slot->start_time }} -
                                                                                        {{ $slot->end_time }})
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label
                                                                                for="edit_class_room_id{{ $event->id }}"
                                                                                class="form-label">
                                                                                Địa Điểm <span class="text-danger">*</span>
                                                                            </label>
                                                                            <select class="form-select"
                                                                                id="edit_class_room_id{{ $event->id }}"
                                                                                name="class_room_id" required>
                                                                                @foreach ($classRooms as $classRoom)
                                                                                    <option value="{{ $classRoom->id }}"
                                                                                        {{ $event->class_room_id === $classRoom->id ? 'selected' : '' }}>
                                                                                        {{ $classRoom->name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="edit_status{{ $event->id }}"
                                                                                class="form-label">
                                                                                Trạng Thái <span
                                                                                    class="text-danger">*</span>
                                                                            </label>
                                                                            <select class="form-select"
                                                                                id="edit_status{{ $event->id }}"
                                                                                name="status" required>
                                                                                @forelse ($availableStatuses as $statusValue => $statusLabel)
                                                                                    <option value="{{ $statusValue }}"
                                                                                        {{ $event->status === $statusValue ? 'selected' : '' }}>
                                                                                        {{ $statusLabel }}
                                                                                    </option>
                                                                                @empty
                                                                                    <option value="{{ $event->status }}"
                                                                                        selected>
                                                                                        {{ $event->status_label }}
                                                                                    </option>
                                                                                @endforelse
                                                                            </select>
                                                                            @if (!empty($availableStatuses))
                                                                                <div class="form-text">
                                                                                    <small class="text-muted">
                                                                                        <i class="ti ti-info-circle"></i>
                                                                                        Bạn chỉ có thể chọn "Chờ Duyệt"
                                                                                        hoặc "Hủy Sự Kiện"
                                                                                    </small>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="edit_description{{ $event->id }}"
                                                                        class="form-label">Mô Tả Chi Tiết</label>
                                                                    <textarea class="form-control" id="edit_description{{ $event->id }}" name="description" rows="3">{{ $event->description }}</textarea>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="edit_image{{ $event->id }}"
                                                                        class="form-label">Hình Ảnh</label>
                                                                    <input type="file" class="form-control"
                                                                        id="edit_image{{ $event->id }}" name="image"
                                                                        accept="image/*">
                                                                    @if ($event->image)
                                                                        <div class="mt-2">
                                                                            <small class="text-muted">Hình ảnh hiện
                                                                                tại:</small>
                                                                            <br>
                                                                            <img src="{{ asset('storage/' . $event->image) }}"
                                                                                alt="Current image" class="img-thumbnail"
                                                                                style="max-width: 150px;">
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Hủy</button>
                                                                <button type="submit" class="btn btn-primary">
                                                                    <i class="ti ti-device-floppy"></i>
                                                                    Cập Nhật
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Delete Event Modal -->
                                        @if (auth()->user()->hasPermissionForModuleCode('event_management', 'delete', 'branch') && $event->canBeDeleted())
                                            <div class="modal fade" id="deleteEventModal{{ $event->id }}"
                                                tabindex="-1" aria-labelledby="deleteEventModalLabel{{ $event->id }}"
                                                aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title text-danger"
                                                                id="deleteEventModalLabel{{ $event->id }}">
                                                                Xác Nhận Xóa
                                                            </h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <p class="mb-2">Bạn có chắc chắn muốn xóa sự kiện
                                                                <strong class="text-danger">
                                                                    {{ $event->name }}
                                                                </strong> này?
                                                            </p>
                                                            <p class="text-danger">
                                                                <i class="ri-alert-line"></i>
                                                                Thao tác này không thể hoàn tác và sẽ xóa tất cả thông tin
                                                                liên quan.
                                                            </p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Hủy</button>
                                                            <form
                                                                action="{{ route('backend.club.events.destroy', $event->id) }}"
                                                                method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Xóa</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- QR Code Modal -->
                                        <div class="modal fade" id="qrCodeModal{{ $event->id }}" tabindex="-1"
                                            aria-labelledby="qrCodeModalLabel{{ $event->id }}" aria-hidden="true"
                                            data-bs-backdrop="static" data-bs-keyboard="false">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="qrCodeModalLabel{{ $event->id }}">
                                                            QR Code Check-in -
                                                            {{ $event->name }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        @if ($event->shouldShowQRCode())
                                                            <!-- Show QR Code when event is active and within time range -->
                                                            <div class="alert alert-success mb-4">
                                                                <i class="bi bi-check-circle me-2"></i>
                                                                <strong>Sự kiện đang diễn ra!</strong>
                                                                Quét mã QR bên dưới để check-in.
                                                            </div>

                                                            <div class="qr-code-container">
                                                                <img src="{{ route('backend.club.events.qrcode', $event->id) }}"
                                                                    alt="QR Code cho {{ $event->name }}"
                                                                    class="img-fluid border rounded shadow-sm"
                                                                    style="max-width: 300px; width: 100%;">
                                                            </div>
                                                        @else
                                                            <!-- Show message when event is not available for QR code -->
                                                            @if ($event->status !== 'active')
                                                                <div class="alert alert-warning">
                                                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                                                    <strong>Sự kiện chưa được duyệt!</strong><br>
                                                                    QR Code chỉ hiển thị khi sự kiện đã được duyệt và trong
                                                                    thời gian diễn ra.
                                                                </div>
                                                            @elseif($event->event_date && $event->slot)
                                                                @php
                                                                    $now = \Carbon\Carbon::now();
                                                                    $eventDate = \Carbon\Carbon::parse(
                                                                        $event->event_date,
                                                                    );
                                                                    $eventStartTime = \Carbon\Carbon::parse(
                                                                        $eventDate->format('Y-m-d') .
                                                                            ' ' .
                                                                            $event->slot->start_time,
                                                                    );
                                                                    $eventEndTime = \Carbon\Carbon::parse(
                                                                        $eventDate->format('Y-m-d') .
                                                                            ' ' .
                                                                            $event->slot->end_time,
                                                                    );
                                                                @endphp

                                                                @if ($now->isBefore($eventStartTime))
                                                                    <div class="alert alert-info">
                                                                        <i class="bi bi-clock me-2"></i>
                                                                        <strong>Sự kiện chưa bắt đầu!</strong><br>
                                                                        QR Code sẽ khả dụng từ
                                                                        <strong>{{ $event->slot->start_time }}</strong>
                                                                        ngày
                                                                        <strong>{{ $event->formatted_event_date }}</strong>.
                                                                    </div>
                                                                @elseif($now->isAfter($eventEndTime))
                                                                    <div class="alert alert-secondary">
                                                                        <i class="bi bi-clock-history me-2"></i>
                                                                        <strong>Sự kiện đã kết thúc!</strong><br>
                                                                        Thời gian check-in đã hết lúc
                                                                        <strong>{{ $event->slot->end_time }}</strong>
                                                                        ngày
                                                                        <strong>{{ $event->formatted_event_date }}</strong>.
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Đóng</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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

    <!-- Add Event Modal -->
    @if (auth()->user()->hasPermissionForModuleCode('event_management', 'create', 'branch'))
        @php
            $eventModel = new \App\Models\Event();
            $allowedStatuses = $eventModel->getAvailableStatusOptions(auth()->user());
        @endphp
        <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addEventModalLabel">Thêm Sự Kiện Mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('backend.club.events.store') }}" method="POST" id="addEventForm"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_name" class="form-label">
                                            Tên Sự Kiện <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="add_name" name="name"
                                            placeholder="VD: Hội thảo khởi nghiệp" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_semester_id" class="form-label">
                                            Học Kỳ <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="add_semester_id" name="semester_id" required>
                                            <option value="">Chọn học kỳ</option>
                                            @foreach ($semesters as $semester)
                                                <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_event_date" class="form-label">
                                            Ngày Diễn Ra <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" class="form-control" id="add_event_date" name="event_date"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_slot_id" class="form-label">
                                            Thời Gian Diễn Ra <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="add_slot_id" name="slot_id" required>
                                            <option value="">Chọn thời gian diễn ra</option>
                                            @foreach ($slots as $slot)
                                                <option value="{{ $slot->id }}">{{ $slot->name }}
                                                    ({{ $slot->start_time }} - {{ $slot->end_time }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_class_room_id" class="form-label">
                                            Địa Điểm <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="add_class_room_id" name="class_room_id" required>
                                            <option value="">Chọn địa điểm</option>
                                            @foreach ($classRooms as $classRoom)
                                                <option value="{{ $classRoom->id }}">{{ $classRoom->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_status" class="form-label">
                                            Trạng Thái <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="add_status" name="status" required>
                                            <option value="">Chọn trạng thái</option>
                                            @foreach ($allowedStatuses as $statusValue => $statusLabel)
                                                <option value="{{ $statusValue }}"
                                                    {{ $statusValue === 'pending' ? 'selected' : '' }}>
                                                    {{ $statusLabel }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">
                                            <small class="text-muted">
                                                <i class="ti ti-info-circle"></i>
                                                Mặc định sự kiện sẽ ở trạng thái "Chờ Duyệt"
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="add_description" class="form-label">Mô Tả Chi Tiết</label>
                                <textarea class="form-control" id="add_description" name="description" rows="3"
                                    placeholder="Mô tả chi tiết về sự kiện"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="add_image" class="form-label">Hình Ảnh</label>
                                <input type="file" class="form-control" id="add_image" name="image"
                                    accept="image/*">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy"></i>
                                Thêm Mới
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
