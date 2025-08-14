<script src="{{ asset('assets/libs/@popperjs/core/umd/popper.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>
<script src="{{ asset('assets/js/defaultmenu.min.js') }}"></script>
<script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ asset('assets/js/sticky.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/simplebar.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.js"></script>
<script src="{{ asset('vendor/flasher/flasher.min.js') }}"></script>
<script src="{{ asset('assets/libs/glightbox/js/glightbox.min.js') }}"></script>
<script src="{{ asset('assets/js/gallery.js') }}"></script>
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/libs/chart.js/chart.min.js') }}"></script>
<script>
    $(document).ready(function() {
        if ($('#data-table').length) {
            $('#data-table').DataTable({
                scrollX: true,
                ordering: false
            });
        }
        if ($('#budget-items-table').length) {
            $('#budget-items-table').DataTable({
                scrollX: true,
                searching: false,
                paging: false,
                info: false
            });
        }
        if ($('#action-plans-table').length) {
            $('#action-plans-table').DataTable({
                scrollX: true,
                searching: false,
                paging: false,
                info: false
            });
        }
        if ($('#equipment-table').length) {
            $('#equipment-table').DataTable({
                scrollX: true,
                searching: false,
                paging: false,
                info: false
            });
        }
        if ($('#objectives-table').length) {
            $('#objectives-table').DataTable({
                scrollX: true,
                searching: false,
                paging: false,
                info: false
            });
        }
        @if (session('club_id') && auth()->user()->hasPermissionForModuleCode('notification_management', 'view', 'branch'))
            $('#messageDropdown').on('show.bs.dropdown', function() {
                loadRecentNotifications();
            });
            updateUnreadCount();
            setInterval(function() {
                updateUnreadCount();
            }, 30000);

            function loadRecentNotifications() {
                $.get('{{ route('backend.club.notifications.recent') }}', function(response) {
                    if (response.success) {
                        const container = $('#recentNotifications');
                        container.empty();
                        if (response.data.length > 0) {
                            response.data.forEach(function(notification) {
                                const timeAgo = getTimeAgo(notification.created_at);
                                let iconClass = 'ti ti-bell';
                                let bgClass = 'bg-primary-transparent';
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
                }).fail(function(xhr, status, error) {
                    console.error('Error loading recent notifications:', error, xhr.responseText);
                    $('#recentNotifications').html(`
                        <li class="dropdown-item">
                            <div class="text-center py-3">
                                <p class="text-muted mb-0">Không thể tải thông báo</p>
                                <small class="text-danger">Lỗi: ${error}</small>
                            </div>
                        </li>
                    `);
                });
            }

            function updateUnreadCount() {
                $.get('{{ route('backend.club.notifications.unread-count') }}', function(response) {
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
                        if (response.count > 0) {
                            notificationData.text(response.count + ' Chưa đọc');
                        } else {
                            notificationData.text('0 Chưa đọc');
                        }
                    }
                }).fail(function(xhr, status, error) {
                    console.error('Error updating unread count:', error, xhr.responseText);
                });
            }

            function markNotificationAsRead(notificationId) {
                const url = '{{ route('backend.club.notifications.mark-as-read', ':id') }}'.replace(':id',
                    notificationId);
                $.post(url, {
                    _token: '{{ csrf_token() }}'
                }, function(response) {
                    if (response.success) {
                        loadRecentNotifications();
                        updateUnreadCount();
                    }
                }).fail(function(xhr, status, error) {
                    console.error('Error marking notification as read:', error, xhr.responseText);
                });
            }

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
    @if (session('club_id') && auth()->user()->hasPermissionForModuleCode('notification_management', 'view', 'branch'))
        checkLatestNotification();

        function checkLatestNotification() {
            $.ajax({
                url: "{{ route('backend.club.notifications.latest') }}",
                type: 'GET',
                success: function(response) {
                    if (response.success && response.data) {
                        showNotificationModal(response.data);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error checking latest notification:', error, xhr.responseText);
                }
            });
        }

        function showNotificationModal(notification) {
            console.log('Showing notification modal for ID:', notification.id);
            $('#notificationModalTitle').text(notification.title);
            $('#notificationModalContent').html(notification.content);
            $('#notificationModalDate').text(notification.formatted_date);
            $('#notificationModalSender').text(notification.sender_name);
            if (notification.image_path) {
                $('#notificationModalImage').attr('src', "{{ asset('storage/') }}/" + notification.image_path).show();
            } else {
                $('#notificationModalImage').hide();
            }
            const filesContainer = $('#notificationModalFiles');
            filesContainer.empty();
            if (notification.files_path && notification.files_path.length > 0) {
                filesContainer.show();
                notification.files_path.forEach(function(file) {
                    const fileSize = formatFileSize(file.size);
                    const fileHtml = `
                        <div class="d-flex align-items-center justify-content-between p-2 border rounded mb-2">
                            <div class="d-flex align-items-center">
                                <i class="ri-file-line me-2 text-primary"></i>
                                <div>
                                    <div class="fw-medium">${file.name}</div>
                                    <small class="text-muted">${fileSize}</small>
                                </div>
                            </div>
                            <a href="{{ asset('storage/') }}/${file.path}"
                               class="btn btn-sm btn-outline-primary"
                               download="${file.name}"
                               target="_blank">
                                <i class="ri-download-line me-1"></i>Tải xuống
                            </a>
                        </div>
                    `;
                    filesContainer.append(fileHtml);
                });
            } else {
                filesContainer.hide();
            }
            $('#notificationModal').data('notification-id', notification.id);
            console.log('Stored notification ID in modal data:', notification.id);
            $('#notificationModal').modal('show');
            markNotificationAsReadModal(notification.id);
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        $('#notificationModal').on('hidden.bs.modal', function() {
            console.log('Modal hidden.bs.modal event fired - cleaning up');
            $(this).removeData('notification-id');
            console.log('Modal data cleared');
        });

        function markNotificationAsReadModal(notificationId) {
            console.log('markNotificationAsReadModal called with ID:', notificationId);
            const url = "{{ route('backend.club.notifications.mark-as-read', ':id') }}".replace(':id', notificationId);
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    console.log('Modal notification marked as read successfully:', response);
                    updateUnreadCount();
                },
                error: function(xhr, status, error) {
                    console.error('Error marking modal notification as read:', error, xhr.responseText);
                    console.error('Response status:', xhr.status);
                    console.error('Response text:', xhr.responseText);
                }
            });
        }
    @endif
</script>
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">
                    <i class="ri-notification-line me-2"></i>Thông Báo Mới
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <img id="notificationModalImage" src="" alt="Notification Image"
                        class="img-fluid rounded shadow-sm" style="max-height: 300px; display: none;">
                </div>
                <div class="mb-3">
                    <h4 id="notificationModalTitle" class="text-primary mb-2"></h4>
                    <div class="d-flex justify-content-between text-muted small">
                        <span>
                            <i class="ri-user-line me-1"></i>
                            Người gửi: <span id="notificationModalSender"></span>
                        </span>
                        <span>
                            <i class="ri-time-line me-1"></i>
                            <span id="notificationModalDate"></span>
                        </span>
                    </div>
                </div>
                <div class="mb-4">
                    <div id="notificationModalContent" class="text-dark lh-lg"></div>
                </div>
                <div id="notificationModalFiles" style="display: none;">
                    <h6 class="text-primary mb-2">
                        <i class="ri-attachment-line me-2"></i>File đính kèm
                    </h6>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i>Đóng
                </button>
            </div>
        </div>
    </div>
</div>
