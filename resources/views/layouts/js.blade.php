{{--
Updated: Added Flasher JS integration
- Added flasher.min.js for notification functionality
- Fixed: Moved JavaScript files from CSS include and proper loading order
- Updated: Modified notification dropdown JavaScript to use real data with new design, including proper styling, icons, and mark-as-read functionality
- Updated: Modified to only display unread notifications in dropdown with improved empty state message
--}}

<!-- Popper JS -->
<script src="{{ asset('assets/libs/@popperjs/core/umd/popper.min.js') }}"></script>

<!-- Bootstrap JS -->
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- Choices JS -->
<script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>

<!-- Main Theme Js -->
<script src="{{ asset('assets/js/main.js') }}"></script>

<!-- Defaultmenu JS -->
<script src="{{ asset('assets/js/defaultmenu.min.js') }}"></script>

<!-- Node Waves JS-->
<script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>

<!-- Sticky JS -->
<script src="{{ asset('assets/js/sticky.js') }}"></script>

<!-- Simplebar JS -->
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/simplebar.js') }}"></script>

<!-- Color Picker JS -->
<script src="{{ asset('assets/libs/@simonwep/pickr/pickr.es5.min.js') }}"></script>

<!-- Custom-Switcher JS -->
{{-- <script src="{{ asset('assets/js/custom-switcher.min.js') }}"></script> --}}

<!-- Custom JS -->
{{-- <script src="{{ asset('assets/js/custom.js') }}"></script> --}}

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.js"></script>

<!-- Flasher JS -->
<script src="{{ asset('vendor/flasher/flasher.min.js') }}"></script>
<script src="{{ asset('assets/libs/glightbox/js/glightbox.min.js') }}"></script>
<script src="{{ asset('assets/js/gallery.js') }}"></script>
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>



<script>
    $(document).ready(function() {
        // Initialize data-table if it exists
        // Fixed DataTable initialization: set 'order' to an empty array to disable initial ordering
        if ($('#data-table').length) {
            $('#data-table').DataTable({
                scrollX: true,
                ordering: false
            });
        }

        // Initialize budget-items-table if it exists
        if ($('#budget-items-table').length) {
            $('#budget-items-table').DataTable({
                scrollX: true,
                searching: false,
                paging: false,
                info: false // disables "Showing 1 to 2 of 2 entries"
            });
        }

        // Initialize action-plans-table if it exists
        if ($('#action-plans-table').length) {
            $('#action-plans-table').DataTable({
                scrollX: true,
                searching: false,
                paging: false,
                info: false // disables "Showing 1 to 2 of 2 entries"
            });
        }

        // Initialize equipment-table if it exists
        if ($('#equipment-table').length) {
            $('#equipment-table').DataTable({
                scrollX: true,
                searching: false,
                paging: false,
                info: false // disables "Showing 1 to 2 of 2 entries"
            });
        }

        // Initialize objectives-table if it exists with disabled search, pagination, and info display
        if ($('#objectives-table').length) {
            $('#objectives-table').DataTable({
                scrollX: true,
                searching: false,
                paging: false,
                info: false // disables "Showing 1 to 2 of 2 entries"
            });
        }

        // Notification functionality for club users
        @if (session('club_id') && auth()->user()->hasPermissionForModuleCode('notification_management', 'view', 'branch'))
            // Load recent notifications when dropdown is opened
            $('#messageDropdown').on('show.bs.dropdown', function() {
                loadRecentNotifications();
            });

            // Load unread count on page load
            updateUnreadCount();

            // Auto-refresh unread count every 30 seconds
            setInterval(function() {
                updateUnreadCount();
            }, 30000);

            // Function to load recent notifications
            function loadRecentNotifications() {
                $.get('/club/notifications/recent', function(response) {
                    if (response.success) {
                        const container = $('#recentNotifications');
                        container.empty();

                        if (response.data.length > 0) {
                            response.data.forEach(function(notification) {
                                const timeAgo = getTimeAgo(notification.created_at);

                                // Determine icon and color based on notification type
                                let iconClass = 'ti ti-bell';
                                let bgClass = 'bg-primary-transparent';

                                // You can customize this based on notification content or add a type field
                                if (notification.title.toLowerCase().includes('thành công') ||
                                    notification.title.toLowerCase().includes('success')) {
                                    iconClass = 'ti ti-circle-check';
                                    bgClass = 'bg-success-transparent';
                                } else if (notification.title.toLowerCase().includes(
                                        'cảnh báo') || notification.title.toLowerCase()
                                    .includes(
                                        'warning')) {
                                    iconClass = 'ti ti-alert-triangle';
                                    bgClass = 'bg-warning-transparent';
                                } else if (notification.title.toLowerCase().includes('lỗi') ||
                                    notification.title.toLowerCase().includes('error')) {
                                    iconClass = 'ti ti-alert-circle';
                                    bgClass = 'bg-danger-transparent';
                                } else if (notification.title.toLowerCase().includes(
                                        'thông tin') || notification.title.toLowerCase()
                                    .includes('info')) {
                                    iconClass = 'ti ti-info-circle';
                                    bgClass = 'bg-info-transparent';
                                }

                                const item = `
                                    <li class="dropdown-item">
                                        <div class="d-flex align-items-start">
                                             <div class="pe-2">
                                                 <span class="avatar avatar-md ${bgClass} avatar-rounded"><i class="${iconClass} fs-18"></i></span>
                                             </div>
                                             <div class="flex-grow-1 d-flex align-items-center justify-content-between">
                                                <div>
                                                    <p class="mb-0 fw-semibold">
                                                        <a href="/club/notifications" class="text-decoration-none">${notification.title}</a>
                                                        <span class="badge bg-danger ms-1">Mới</span>
                                                    </p>
                                                    <span class="text-muted fw-normal fs-12 header-notification-text">${notification.content.substring(0, 60)}${notification.content.length > 60 ? '...' : ''}</span>
                                                </div>
                                                <div>
                                                    <a href="javascript:void(0);" class="min-w-fit-content text-muted me-1 dropdown-item-close1" onclick="markNotificationAsRead('${notification.id}')"></a>
                                                </div>
                                             </div>
                                        </div>
                                    </li>
                                `;
                                container.append(item);
                            });
                        } else {
                            container.html(`
                                <li class="dropdown-item">
                                    <div class="text-center py-3">
                                        <span class="avatar avatar-xl avatar-rounded bg-secondary-transparent">
                                            <i class="ri-notification-off-line fs-2"></i>
                                        </span>
                                        <h6 class="fw-semibold mt-3">Không Có Thông Báo</h6>
                                    </div>
                                </li>
                            `);
                        }
                    }
                }).fail(function() {
                    $('#recentNotifications').html(`
                        <li class="dropdown-item">
                            <div class="text-center py-3">
                                <p class="text-muted mb-0">Không thể tải thông báo</p>
                            </div>
                        </li>
                    `);
                });
            }

            // Function to update unread count
            function updateUnreadCount() {
                $.get('/club/notifications/unread-count', function(response) {
                    if (response.success) {
                        const badges = $('.notification-badge');
                        const notificationData = $('#notifiation-data');

                        badges.each(function() {
                            if (response.count > 0) {
                                $(this).text(response.count).removeClass('d-none');
                            } else {
                                $(this).addClass('d-none');
                            }
                        });

                        // Update the notification data text
                        if (response.count > 0) {
                            notificationData.text(response.count + ' Chưa đọc');
                        } else {
                            notificationData.text('0 Chưa đọc');
                        }
                    }
                });
            }

            // Function to mark notification as read
            function markNotificationAsRead(notificationId) {
                $.post(`/club/notifications/${notificationId}/mark-as-read`, {
                    _token: '{{ csrf_token() }}'
                }, function(response) {
                    if (response.success) {
                        // Reload notifications and update count
                        loadRecentNotifications();
                        updateUnreadCount();
                    }
                });
            }

            // Function to get time ago
            function getTimeAgo(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diffInSeconds = Math.floor((now - date) / 1000);

                if (diffInSeconds < 60) {
                    return 'Vừa xong';
                } else if (diffInSeconds < 3600) {
                    const minutes = Math.floor(diffInSeconds / 60);
                    return `${minutes} phút trước`;
                } else if (diffInSeconds < 86400) {
                    const hours = Math.floor(diffInSeconds / 3600);
                    return `${hours} giờ trước`;
                } else {
                    const days = Math.floor(diffInSeconds / 86400);
                    return `${days} ngày trước`;
                }
            }
        @endif
    });
</script>
