{{--
    Changes made:
    - Updated notification modal layout to match admin design
    - Improved visual presentation with better styling
    - Enhanced image and file display sections
    - Added proper modal header with notification details
    - Maintained dynamic loading functionality via AJAX
--}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Thông Báo</h1>

            <!-- Nút trên desktop -->
            <div class="d-none d-md-block">
                @if ($notifications->count() > 0)
                    <button class="btn btn-primary" id="mark-all-read-btn">
                        <i class="ri-check-double-line me-1"></i>
                        Đánh Dấu Tất Cả Đã Đọc
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
                    @if ($notifications->count() > 0)
                        <li>
                            <a class="dropdown-item" href="#" id="mark-all-read-btn-mobile">
                                <i class="ri-check-double-line me-1"></i> Đánh Dấu Tất Cả Đã Đọc
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
        <!-- Page Header Close -->
        <!-- Start::row-1 -->
        {{--
            Changes made:
            - Chia thông báo thành 2 cột (2 columns for notifications)
        --}}
        <div class="row justify-content-center">
            <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12">
                @if ($notifications->count() > 0)
                    <div class="row">
                        @foreach ($notifications as $notification)
                            <div class="col-md-6 mb-4">
                                <div
                                    class="card custom-card {{ !$notification->isReadByClub(session('club_id')) ? 'un-read' : '' }}">
                                    <div class="card-body p-3">
                                        <a href="javascript:void(0);" class="mark-as-read-link"
                                            data-notification-id="{{ $notification->id }}">
                                            <div class="d-flex align-items-top mt-0 flex-wrap">
                                                <div class="lh-1">
                                                    <span
                                                        class="avatar avatar-md {{ $notification->sender ? 'online' : 'offline' }} me-3 avatar-rounded">
                                                        @if ($notification->sender && $notification->sender->avatar)
                                                            <img alt="avatar"
                                                                src="{{ asset('storage/' . $notification->sender->avatar) }}">
                                                        @else
                                                            <img alt="avatar"
                                                                src="{{ asset('assets/images/faces/1.jpg') }}">
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="flex-fill">
                                                    <div class="d-flex align-items-center">
                                                        <div class="mt-sm-0 mt-2">
                                                            <p class="mb-0 fs-14 fw-semibold">
                                                                {{ $notification->sender ? $notification->sender->name : 'System' }}
                                                            </p>
                                                            <p class="mb-0 text-muted">
                                                                {{ $notification->title }}
                                                                @if (!$notification->isReadByClub(session('club_id')))
                                                                    <span
                                                                        class="badge bg-primary-transparent fw-semibold mx-1">Mới</span>
                                                                @endif
                                                            </p>
                                                            <span class="mb-0 d-block text-muted fs-12">
                                                                {{ $notification->created_at->diffForHumans() }}
                                                            </span>
                                                        </div>
                                                        <div class="ms-auto">
                                                            <span class="float-end badge bg-light text-muted">
                                                                {{ $notification->created_at->format('d,M Y') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="row">
                        <div class="col-12">
                            <div class="card custom-card">
                                <div class="card-body p-3 text-center">
                                    <div class="mb-3">
                                        <i class="ri-notification-off-line fs-48 text-muted"></i>
                                    </div>
                                    <h5 class="text-muted">No notifications</h5>
                                    <p class="text-muted">You'll see new notifications here when they arrive.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($notifications->hasPages())
                    <div class="text-center">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
        <!--End::row-1 -->

    </div>

    <!-- Notification Detail Modal -->
    <div class="modal fade" id="notificationDetailModal" tabindex="-1" aria-labelledby="notificationDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationDetailModalLabel">
                        <i class="ti ti-eye me-1"></i>Chi Tiết Thông Báo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Loading State -->
                    <div id="notificationDetailLoading" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Đang tải thông báo...</p>
                    </div>

                    <!-- Notification Content -->
                    <div id="notificationDetailContent" style="display: none;">
                        <div class="row">
                            <!-- Notification Header -->
                            <div class="mb-3">
                                <h4 id="notificationDetailTitle" class="text-primary mb-2"></h4>
                                <div class="d-flex justify-content-between text-muted small">
                                    <span>
                                        <i class="ri-user-line me-1"></i>
                                        Người gửi: <span id="notificationDetailSender"></span>
                                    </span>
                                    <span>
                                        <i class="ri-time-line me-1"></i>
                                        <span id="notificationDetailDate"></span>
                                    </span>
                                </div>
                            </div>

                            <!-- Notification Content with Image -->
                            <div class="col-12 mb-3 border rounded p-3" id="notificationContentSection">
                                <!-- Image will be populated here if exists -->
                                <div class="text-center mb-3" id="notificationDetailImageContainer" style="display: none;">
                                    <img id="notificationDetailImage" src="" alt="Hình ảnh thông báo"
                                        class="img-fluid rounded" style="max-width: 100%; max-height: 300px;">
                                </div>

                                <!-- Content Text -->
                                <div id="notificationDetailContentText" class="text-dark"></div>
                            </div>

                            <!-- Files Section -->
                            <div id="notificationDetailFiles" class="col-12 mb-3" style="display: none;">
                                <label class="form-label fw-semibold">File đính kèm:</label>
                                <div id="notificationDetailFilesContainer">
                                    <!-- Files will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mark All as Read Confirmation Modal -->
    <div class="modal fade" id="markAllReadModal" tabindex="-1" aria-labelledby="markAllReadModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="markAllReadModalLabel">
                        <i class="ri-notification-line me-2 text-primary"></i>
                        Xác nhận đánh dấu đã đọc
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">
                        Bạn có chắc chắn muốn đánh dấu tất cả thông báo là đã đọc?
                    </p>
                    <p class="text-muted small mt-2 mb-0">
                        Hành động này sẽ đánh dấu tất cả thông báo chưa đọc thành đã đọc và không thể hoàn tác.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>
                        Hủy bỏ
                    </button>
                    <button type="button" class="btn btn-primary" id="confirmMarkAllRead">
                        <i class="ri-check-line me-1"></i>
                        Xác nhận
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Show notification detail modal when clicked
            $('.mark-as-read-link').on('click', function(e) {
                e.preventDefault();
                const notificationId = $(this).data('notification-id');
                const cardElement = $(this).closest('.card');

                showNotificationDetailModal(notificationId, cardElement);
            });

            function showNotificationDetailModal(notificationId, cardElement) {
                // Show modal and loading state
                $('#notificationDetailModal').modal('show');
                $('#notificationDetailLoading').show();
                $('#notificationDetailContent').hide();

                // Store notification info for later use
                $('#notificationDetailModal').data('notification-id', notificationId);
                $('#notificationDetailModal').data('card-element', cardElement);

                // Fetch notification details
                $.ajax({
                    url: `/club/notifications/${notificationId}`,
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            populateNotificationModal(response.data);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading notification details:', xhr);
                        $('#notificationDetailLoading').html(`
                            <div class="text-center py-4">
                                <i class="ri-error-warning-line fs-48 text-danger"></i>
                                <p class="mt-2 text-danger">Không thể tải thông báo</p>
                            </div>
                        `);
                    }
                });
            }

            function populateNotificationModal(notification) {
                // Hide loading and show content
                $('#notificationDetailLoading').hide();
                $('#notificationDetailContent').show();

                // Populate modal with notification data
                $('#notificationDetailTitle').text(notification.title);
                // Format content with line breaks (similar to admin view)
                $('#notificationDetailContentText').html(notification.content.replace(/\n/g, '<br>'));
                $('#notificationDetailDate').text(notification.formatted_date);
                $('#notificationDetailSender').text(notification.sender_name);

                // Handle image
                if (notification.image_path) {
                    $('#notificationDetailImage').attr('src', "{{ asset('storage/') }}/" + notification
                        .image_path);
                    $('#notificationDetailImageContainer').show();
                } else {
                    $('#notificationDetailImageContainer').hide();
                }

                // Handle files
                const filesContainer = $('#notificationDetailFilesContainer');
                filesContainer.empty();

                if (notification.files_path && notification.files_path.length > 0) {
                    $('#notificationDetailFiles').show();
                    notification.files_path.forEach(function(file) {
                        const fileSize = (file.size / 1024).toFixed(2) + ' KB';
                        const fileHtml = `
                            <div class="d-flex align-items-center justify-content-between mb-2 p-2 border rounded">
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-file me-2 text-primary"></i>
                                    <span>${file.name}</span>
                                    <small class="text-muted ms-2">(${fileSize})</small>
                                </div>
                                <a href="{{ asset('storage/') }}/${file.path}" target="_blank"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="ti ti-download me-1"></i>Tải xuống
                                </a>
                            </div>
                        `;
                        filesContainer.append(fileHtml);
                    });
                } else {
                    $('#notificationDetailFiles').hide();
                }
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Handle modal close and mark as read
            $('#notificationDetailModal').on('hidden.bs.modal', function() {
                const notificationId = $(this).data('notification-id');
                const cardElement = $(this).data('card-element');

                if (notificationId && cardElement && cardElement.hasClass('un-read')) {
                    markNotificationAsRead(notificationId, cardElement);
                }
            });

            function markNotificationAsRead(notificationId, cardElement) {
                $.ajax({
                    url: `/club/notifications/${notificationId}/mark-as-read`,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update UI to mark as read
                            cardElement.removeClass('un-read');
                            // Remove the "new" badge
                            cardElement.find('.badge.bg-primary-transparent').remove();
                        }
                    },
                    error: function(xhr) {
                        console.error('Error marking notification as read:', xhr);
                    }
                });
            }

            // Show modal when mark all as read button is clicked (desktop)
            $('#mark-all-read-btn').on('click', function(e) {
                e.preventDefault();
                $('#markAllReadModal').modal('show');
            });

            // Show modal when mark all as read button is clicked (mobile)
            $('#mark-all-read-btn-mobile').on('click', function(e) {
                e.preventDefault();
                $('#markAllReadModal').modal('show');
            });

            // Handle confirmation button click
            $('#confirmMarkAllRead').on('click', function() {
                const button = $(this);
                const originalText = button.html();

                // Disable button and show loading state
                button.prop('disabled', true);
                button.html('<i class="ri-loader-4-line me-1"></i>Đang xử lý...');

                $.ajax({
                    url: '/club/notifications/mark-all-as-read',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update all unread notifications to read
                            $('.notification-container .card.un-read').each(function() {
                                $(this).removeClass('un-read');
                                $(this).find('.badge.bg-primary-transparent').remove();
                            });

                            // Hide the buttons if no more unread notifications
                            if ($('.notification-container .card.un-read').length === 0) {
                                $('#mark-all-read-btn').hide();
                                $('#mark-all-read-btn-mobile').parent().parent().hide();
                            }

                            // Close the modal
                            $('#markAllReadModal').modal('hide');

                            // Show success message
                            if (typeof showToast !== 'undefined') {
                                showToast('success',
                                    'Tất cả thông báo đã được đánh dấu là đã đọc');
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error('Error marking all notifications as read:', xhr);

                        // Show error message
                        if (typeof showToast !== 'undefined') {
                            showToast('error', 'Không thể đánh dấu thông báo là đã đọc');
                        }
                    },
                    complete: function() {
                        // Re-enable button and restore original text
                        button.prop('disabled', false);
                        button.html(originalText);
                    }
                });
            });

            // Reset modal when it's hidden
            $('#markAllReadModal').on('hidden.bs.modal', function() {
                const confirmButton = $('#confirmMarkAllRead');
                confirmButton.prop('disabled', false);
                confirmButton.html('<i class="ri-check-line me-1"></i>Xác nhận');
            });
        });
    </script>
@endsection
