{{--
    Changes made:
    - Added "Mark All as Read" button in the page header
    - Added JavaScript functionality to handle marking all notifications as read
    - Added AJAX call to mark all notifications as read
    - Added modal confirmation for mark all as read action
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
                        Đánh dấu tất cả đã đọc
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
                                <i class="ri-check-double-line me-1"></i> Đánh dấu tất cả đã đọc
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
        <!-- Page Header Close -->
        <!-- Start::row-1 -->
        <div class="row justify-content-center">
            <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12">
                <ul class="list-unstyled mb-0 notification-container">
                    @if ($notifications->count() > 0)
                        @foreach ($notifications as $notification)
                            <li>
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
                            </li>
                        @endforeach
                    @else
                        <li>
                            <div class="card custom-card">
                                <div class="card-body p-3 text-center">
                                    <div class="mb-3">
                                        <i class="ri-notification-off-line fs-48 text-muted"></i>
                                    </div>
                                    <h5 class="text-muted">No notifications</h5>
                                    <p class="text-muted">You'll see new notifications here when they arrive.</p>
                                </div>
                            </div>
                        </li>
                    @endif
                </ul>

                @if ($notifications->hasPages())
                    <div class="text-center">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
        <!--End::row-1 -->

    </div>

    <!-- Mark All as Read Confirmation Modal -->
    <div class="modal fade" id="markAllReadModal" tabindex="-1" aria-labelledby="markAllReadModalLabel" aria-hidden="true">
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
            // Mark notification as read when clicked
            $('.mark-as-read-link').on('click', function(e) {
                e.preventDefault();
                const notificationId = $(this).data('notification-id');
                const cardElement = $(this).closest('.card');

                // If already read, don't make AJAX call
                if (!cardElement.hasClass('un-read')) {
                    return;
                }

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

                            // Show success message if you have a notification system
                            // showToast('success', 'Notification marked as read');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error marking notification as read:', xhr);
                    }
                });
            });

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
