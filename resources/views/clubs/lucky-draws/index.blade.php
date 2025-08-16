{{-- Updated: Added modal_background_image field to lucky draw forms with preview functionality for customizing spinning wheel modal background. Also includes security_code field for public page protection --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Lucky Draw</h1>

            <!-- Nút trên desktop -->
            <div class="d-none d-md-block">
                @if (auth()->user()->hasPermissionForModuleCode('lucky_draw_management', 'create', 'branch'))
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLuckyDrawModal">
                        <i class="ti ti-plus"></i>
                        Tạo Lucky Draw mới
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
                    @if (auth()->user()->hasPermissionForModuleCode('lucky_draw_management', 'create', 'branch'))
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                data-bs-target="#addLuckyDrawModal">
                                <i class="ti ti-plus"></i> Tạo Lucky Draw mới
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
                        @if ($luckyDraws->count() > 0)
                            <div class="table-responsive mb-4">
                                <table class="table text-nowrap table-bordered" id="data-table">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center !important;">STT</th>
                                            <th>Tiêu đề</th>
                                            <th style="text-align: center !important;">Mã bảo mật</th>
                                            <th style="text-align: center !important;">Hình nền</th>
                                            <th style="text-align: center !important;">Thời gian</th>
                                            <th style="text-align: center !important;">Người tham gia</th>
                                            <th style="text-align: center !important;">Người trúng thưởng</th>
                                            <th style="text-align: center !important;">Link công khai</th>
                                            <th style="text-align: center !important;">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($luckyDraws as $index => $luckyDraw)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>
                                                    <div class="fw-semibold">{{ $luckyDraw->title }}</div>
                                                </td>
                                                <td class="text-center">
                                                    @if ($luckyDraw->security_code)
                                                        <div class="d-flex align-items-center justify-content-center">
                                                            <code
                                                                class="bg-light px-2 py-1 rounded">{{ $luckyDraw->security_code }}</code>
                                                            <button
                                                                class="btn btn-sm btn-outline-secondary ms-2 copy-security-code"
                                                                data-code="{{ $luckyDraw->security_code }}"
                                                                title="Copy mã bảo mật">
                                                                <i class="ti ti-copy"></i>
                                                            </button>
                                                        </div>
                                                    @else
                                                        <span class="text-muted small">Chưa có</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($luckyDraw->background_image)
                                                        <img src="{{ asset('storage/' . $luckyDraw->background_image) }}"
                                                            alt="Background" class="img-thumbnail rounded"
                                                            style="width: 60px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <span class="text-muted small">Không có</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <small>
                                                        <i class="ti ti-calendar-start"></i>
                                                        {{ $luckyDraw->start_date->format('d/m/Y H:i') }}<br>
                                                        <i class="ti ti-calendar-end"></i>
                                                        {{ $luckyDraw->end_date->format('d/m/Y H:i') }}
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge bg-info">{{ count($luckyDraw->participants ?? []) }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge bg-warning">{{ count($luckyDraw->winners ?? []) }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="gap-2 fs-15">
                                                        <button type="button"
                                                            class="btn btn-icon btn-sm btn-info-light copy-link"
                                                            data-link="{{ route('lucky-draw.show', $luckyDraw->slug) }}"
                                                            title="Copy link">
                                                            <i class="ti ti-copy"></i>
                                                        </button>
                                                        <a href="{{ route('lucky-draw.show', $luckyDraw->slug) }}"
                                                            target="_blank" class="btn btn-icon btn-sm btn-success-light"
                                                            title="Mở link">
                                                            <i class="ti ti-external-link"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                                {{-- [2024-06-19] Updated: Action buttons style to match notification index style
                                    --}}
                                                <td class="text-center">
                                                    <div class="gap-2 fs-15">
                                                        @if (auth()->user()->hasPermissionForModuleCode('lucky_draw_management', 'view', 'branch'))
                                                            <a href="{{ route('backend.club.lucky-draws.show', $luckyDraw->slug) }}"
                                                                class="btn btn-icon btn-sm btn-info-light"
                                                                title="Xem chi tiết">
                                                                <i class="ri-eye-line"></i>
                                                            </a>
                                                        @endif
                                                        @if (auth()->user()->hasPermissionForModuleCode('lucky_draw_management', 'edit', 'branch'))
                                                            <button class="btn btn-icon btn-sm btn-warning-light"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editLuckyDrawModal{{ $luckyDraw->id }}"
                                                                title="Chỉnh sửa">
                                                                <i class="ri-edit-line"></i>
                                                            </button>
                                                        @endif
                                                        @if (auth()->user()->hasPermissionForModuleCode('lucky_draw_management', 'view', 'branch'))
                                                            <a href="{{ route('backend.club.lucky-draws.prizes.index', $luckyDraw) }}"
                                                                class="btn btn-icon btn-sm btn-info-light"
                                                                title="Quản lý giải thưởng">
                                                                <i class="ri-gift-line"></i>
                                                            </a>
                                                        @endif
                                                        @if (auth()->user()->hasPermissionForModuleCode('lucky_draw_management', 'delete', 'branch'))
                                                            <button class="btn btn-icon btn-sm btn-danger-light"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#deleteLuckyDrawModal{{ $luckyDraw->id }}"
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
                        @else
                            <div class="text-center py-5">
                                <i class="ti ti-dice fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Chưa có Lucky Draw nào</h5>
                                <p class="text-muted">Tạo Lucky Draw đầu tiên để bắt đầu tổ chức quay số may mắn!</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLuckyDrawModal">
                                    <i class="ti ti-plus"></i> Tạo Lucky Draw mới
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!--End::row-1 -->

    </div>

    @foreach ($luckyDraws as $luckyDraw)
        @if (auth()->user()->hasPermissionForModuleCode('lucky_draw_management', 'edit', 'branch'))
            <!-- Edit Lucky Draw Modal -->
            <div class="modal fade" id="editLuckyDrawModal{{ $luckyDraw->id }}" tabindex="-1"
                aria-labelledby="editLuckyDrawModalLabel{{ $luckyDraw->id }}" aria-hidden="true"
                data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editLuckyDrawModalLabel{{ $luckyDraw->id }}">
                                Chỉnh sửa Lucky Draw
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form action="{{ route('backend.club.lucky-draws.update', $luckyDraw) }}" method="POST"
                            id="editLuckyDrawForm{{ $luckyDraw->id }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="edit_title{{ $luckyDraw->id }}" class="form-label">Tiêu đề <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control"
                                                id="edit_title{{ $luckyDraw->id }}" name="title"
                                                value="{{ $luckyDraw->title }}" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="edit_security_code{{ $luckyDraw->id }}" class="form-label">Mã bảo
                                                mật</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control"
                                                    id="edit_security_code{{ $luckyDraw->id }}" name="security_code"
                                                    value="{{ $luckyDraw->security_code }}">
                                                <button class="btn btn-outline-secondary" type="button"
                                                    onclick="generateSecurityCode('edit_security_code{{ $luckyDraw->id }}')">
                                                    <i class="ti ti-refresh"></i> Tạo tự
                                                    động
                                                </button>
                                            </div>
                                            <small class="form-text text-danger">Mã bảo mật
                                                để truy cập modal setting ở trang quay
                                                thưởng, bỏ trống nếu không cần mã bảo
                                                mật.</small>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="edit_background_image{{ $luckyDraw->id }}"
                                                        class="form-label">Hình nền</label>
                                                    <input type="file" class="form-control"
                                                        id="edit_background_image{{ $luckyDraw->id }}"
                                                        name="background_image" accept="image/*">
                                                    <small class="form-text text-muted">Để
                                                        trống
                                                        nếu không muốn thay đổi hình
                                                        nền</small>

                                                    @if ($luckyDraw->background_image)
                                                        <div class="mt-2">
                                                            <label class="form-label">Hình
                                                                nền hiện
                                                                tại:</label>
                                                            <img src="{{ asset('storage/' . $luckyDraw->background_image) }}"
                                                                alt="Current background" class="img-thumbnail rounded"
                                                                style="max-width: 200px; max-height: 120px; object-fit: cover;">
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="edit_modal_background_image{{ $luckyDraw->id }}"
                                                        class="form-label">Hình nền modal
                                                        quay
                                                        thưởng</label>
                                                    <input type="file" class="form-control"
                                                        id="edit_modal_background_image{{ $luckyDraw->id }}"
                                                        name="modal_background_image" accept="image/*">
                                                    <small class="form-text text-muted">Để
                                                        trống
                                                        nếu không muốn thay đổi hình nền
                                                        modal quay thưởng.</small>

                                                    @if ($luckyDraw->modal_background_image)
                                                        <div class="mt-2">
                                                            <label class="form-label">Hình
                                                                nền
                                                                modal hiện
                                                                tại:</label>
                                                            <img src="{{ asset('storage/' . $luckyDraw->modal_background_image) }}"
                                                                alt="Current modal background"
                                                                class="img-thumbnail rounded"
                                                                style="max-width: 200px; max-height: 120px; object-fit: cover;">
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="edit_start_date{{ $luckyDraw->id }}"
                                                        class="form-label">Thời
                                                        gian bắt
                                                        đầu <span class="text-danger">*</span></label>
                                                    <input type="datetime-local" class="form-control"
                                                        id="edit_start_date{{ $luckyDraw->id }}" name="start_date"
                                                        value="{{ $luckyDraw->start_date->format('Y-m-d\TH:i') }}"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="edit_end_date{{ $luckyDraw->id }}"
                                                        class="form-label">Thời
                                                        gian kết
                                                        thúc <span class="text-danger">*</span></label>
                                                    <input type="datetime-local" class="form-control"
                                                        id="edit_end_date{{ $luckyDraw->id }}" name="end_date"
                                                        value="{{ $luckyDraw->end_date->format('Y-m-d\TH:i') }}" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_participants{{ $luckyDraw->id }}" class="form-label">
                                        <i class="ti ti-users text-success"></i>
                                        Danh sách người tham gia
                                    </label>
                                    <textarea class="form-control" id="edit_participants{{ $luckyDraw->id }}" name="participants" rows="8"
                                        placeholder="Mỗi người tham gia một dòng">{{ $luckyDraw->participants ? implode("\n", $luckyDraw->participants) : '' }}</textarea>
                                    <small class="form-text text-muted">Mỗi người
                                        tham gia trên một dòng</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-warning">
                                    <i class="ti ti-device-floppy"></i> Cập nhật Lucky Draw
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        @if (auth()->user()->hasPermissionForModuleCode('lucky_draw_management', 'delete', 'branch'))
            <!-- Delete Lucky Draw Modal -->
            <div class="modal fade" id="deleteLuckyDrawModal{{ $luckyDraw->id }}" tabindex="-1"
                aria-labelledby="deleteLuckyDrawModalLabel{{ $luckyDraw->id }}" aria-hidden="true"
                data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-danger" id="deleteLuckyDrawModalLabel{{ $luckyDraw->id }}">
                                Xác nhận xóa
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center mb-3">
                                <i class="ti ti-alert-triangle text-danger" style="font-size: 2.5rem;"></i>
                            </div>
                            <p class="text-center mb-2">Bạn có chắc chắn muốn xóa Lucky
                                Draw <span class="fw-bold text-danger">{{ $luckyDraw->title }}</span>?
                            </p>
                            <div class="alert alert-warning">
                                <div class="d-flex align-items-start">
                                    <i class="ti ti-info-circle text-warning me-2 mt-1"></i>
                                    <div>
                                        <strong>Người tạo:</strong>
                                        {{ $luckyDraw->creator->name ?? 'N/A' }}<br>
                                        <strong>Ngày tạo:</strong>
                                        {{ $luckyDraw->created_at->format('d/m/Y H:i') }}<br>
                                        <strong>Người tham gia:</strong>
                                        {{ count($luckyDraw->participants ?? []) }}<br>
                                        <strong>Người trúng thưởng:</strong>
                                        {{ count($luckyDraw->winners ?? []) }}
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted text-center small mb-0">
                                <i class="ti ti-alert-circle me-1"></i>
                                Hành động này không thể hoàn tác!
                            </p>
                        </div>
                        <form action="{{ route('backend.club.lucky-draws.destroy', $luckyDraw) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-danger">
                                    <i class="ti ti-trash"></i> Xóa
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    @if (auth()->user()->hasPermissionForModuleCode('lucky_draw_management', 'create', 'branch'))
        <!-- Add Lucky Draw Modal -->
        <div class="modal fade" id="addLuckyDrawModal" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="addLuckyDrawModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addLuckyDrawModalLabel">
                            Tạo Lucky Draw mới
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('backend.club.lucky-draws.store') }}" method="POST" id="addLuckyDrawForm"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Tiêu đề <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title" name="title"
                                            required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="security_code" class="form-label">Mã bảo mật</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="security_code"
                                                name="security_code" placeholder="Để trống để tự động tạo">
                                            <button class="btn btn-outline-secondary" type="button"
                                                onclick="generateSecurityCode('security_code')">
                                                <i class="ti ti-refresh"></i> Tạo tự động
                                            </button>
                                        </div>
                                        <small class="form-text text-danger">Mã bảo mật để truy cập modal setting ở trang
                                            quay
                                            thưởng, bỏ trống nếu không cần mã bảo mật.</small>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="background_image" class="form-label">Hình nền <span
                                                        class="text-danger">*</span></label>
                                                <input type="file" class="form-control" id="background_image"
                                                    name="background_image" accept="image/*" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="modal_background_image" class="form-label">Hình nền modal quay
                                                    thưởng
                                                    <span class="text-danger">*</span></label>
                                                <input type="file" class="form-control" id="modal_background_image"
                                                    name="modal_background_image" accept="image/*">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="start_date" class="form-label">Thời gian bắt đầu <span
                                                        class="text-danger">*</span></label>
                                                <input type="datetime-local" class="form-control" id="start_date"
                                                    name="start_date" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="end_date" class="form-label">Thời gian kết thúc <span
                                                        class="text-danger">*</span></label>
                                                <input type="datetime-local" class="form-control" id="end_date"
                                                    name="end_date" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="participants" class="form-label">
                                            <i class="ti ti-users text-success"></i>
                                            Danh sách người tham gia (tùy chọn)
                                        </label>
                                        <textarea class="form-control" id="participants" name="participants" rows="6"
                                            placeholder="Mỗi người tham gia một dòng&#10;Ví dụ:&#10;Nguyễn Văn A&#10;Trần Thị B&#10;Lê Văn C"></textarea>
                                        <small class="form-text text-muted">Mỗi người tham gia trên một dòng.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy"></i> Tạo Lucky Draw
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <!-- Toastr CSS and JS for notifications -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            // Set default dates for new lucky draw
            const now = new Date();
            const tomorrow = new Date(now);
            tomorrow.setDate(tomorrow.getDate() + 1);
            tomorrow.setHours(now.getHours() + 1);

            const nextWeek = new Date(now);
            nextWeek.setDate(nextWeek.getDate() + 7);
            nextWeek.setHours(now.getHours() + 1);

            function formatDateTime(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');
                return `${year}-${month}-${day}T${hours}:${minutes}`;
            }

            // Set default dates when modal opens
            $('#addLuckyDrawModal').on('show.bs.modal', function() {
                if (!$('#start_date').val()) {
                    $('#start_date').val(formatDateTime(tomorrow));
                }
                if (!$('#end_date').val()) {
                    $('#end_date').val(formatDateTime(nextWeek));
                }
            });

            // Image preview functionality for background image
            function handleImagePreview(input, previewId) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $(previewId).attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            // Add image preview for new lucky draw form
            $('#background_image').on('change', function() {
                const previewId = '#background_image_preview';
                if (!$(previewId).length) {
                    $(this).after(
                        '<div class="mt-2"><label class="form-label">Xem trước:</label><br><img id="background_image_preview" src="" alt="Preview" class="img-thumbnail rounded" style="max-width: 200px; max-height: 120px; object-fit: cover; display: none;"></div>'
                    );
                }
                handleImagePreview(this, previewId);
            });

            // Add image preview for edit forms
            $('input[name="background_image"][id^="edit_background_image"]').on('change', function() {
                const luckyDrawId = $(this).attr('id').replace('edit_background_image', '');
                const previewId = '#edit_background_image_preview' + luckyDrawId;

                if (!$(previewId).length) {
                    $(this).closest('.mb-3').append(
                        '<div class="mt-2"><label class="form-label">Xem trước:</label><br><img id="edit_background_image_preview' +
                        luckyDrawId +
                        '" src="" alt="Preview" class="img-thumbnail rounded" style="max-width: 200px; max-height: 120px; object-fit: cover; display: none;"></div>'
                    );
                }
                handleImagePreview(this, previewId);
            });

            // Add image preview for modal background image in new form
            $('#modal_background_image').on('change', function() {
                const previewId = '#modal_background_image_preview';
                if (!$(previewId).length) {
                    $(this).after(
                        '<div class="mt-2"><label class="form-label">Xem trước:</label><br><img id="modal_background_image_preview" src="" alt="Preview" class="img-thumbnail rounded" style="max-width: 200px; max-height: 120px; object-fit: cover; display: none;"></div>'
                    );
                }
                handleImagePreview(this, previewId);
            });

            // Add image preview for modal background image in edit forms
            $('input[name="modal_background_image"][id^="edit_modal_background_image"]').on('change', function() {
                const luckyDrawId = $(this).attr('id').replace('edit_modal_background_image', '');
                const previewId = '#edit_modal_background_image_preview' + luckyDrawId;

                if (!$(previewId).length) {
                    $(this).closest('.mb-3').append(
                        '<div class="mt-2"><label class="form-label">Xem trước:</label><br><img id="edit_modal_background_image_preview' +
                        luckyDrawId +
                        '" src="" alt="Preview" class="img-thumbnail rounded" style="max-width: 200px; max-height: 120px; object-fit: cover; display: none;"></div>'
                    );
                }
                handleImagePreview(this, previewId);
            });

            // Validate end date is after start date for all forms
            $('input[type="datetime-local"]').on('change', function() {
                const form = $(this).closest('form');
                const startDate = new Date(form.find('input[name="start_date"]').val());
                const endDate = new Date(form.find('input[name="end_date"]').val());

                if (endDate <= startDate) {
                    toastr.warning('Thời gian kết thúc phải sau thời gian bắt đầu!');
                    if (this.name === 'end_date') {
                        const newEndDate = new Date(startDate);
                        newEndDate.setHours(startDate.getHours() + 1);
                        $(this).val(formatDateTime(newEndDate));
                    }
                }
            });

            // Form submission validation
            $('form[id^="addLuckyDrawForm"], form[id^="editLuckyDrawForm"]').on('submit', function(e) {
                const startDate = new Date($(this).find('input[name="start_date"]').val());
                const endDate = new Date($(this).find('input[name="end_date"]').val());

                if (endDate <= startDate) {
                    e.preventDefault();
                    toastr.error('Thời gian kết thúc phải sau thời gian bắt đầu!');
                    return false;
                }
            });

            // Copy link functionality
            $('.copy-link').on('click', function() {
                const link = $(this).data('link');
                navigator.clipboard.writeText(link).then(function() {
                    toastr.success('Đã copy link vào clipboard!');
                }, function() {
                    toastr.error('Không thể copy link!');
                });
            });

            // Copy security code functionality
            $('.copy-security-code').on('click', function() {
                const code = $(this).data('code');
                navigator.clipboard.writeText(code).then(function() {
                    toastr.success('Đã copy mã bảo mật vào clipboard!');
                }, function() {
                    toastr.error('Không thể copy mã bảo mật!');
                });
            });
        });

        // Function to generate random security code
        function generateSecurityCode(inputId) {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let result = '';
            for (let i = 0; i < 12; i++) {
                result += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById(inputId).value = result;
        }
    </script>
@endsection
