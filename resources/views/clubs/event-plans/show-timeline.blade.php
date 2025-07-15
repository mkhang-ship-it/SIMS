{{--
Updated: 2025-01-23
Changes: Sửa lại giao diện tab Timeline theo mẫu tab Objectives (table layout, action buttons, empty state, permission checks, style đồng bộ)
Updated: Thêm group timeline theo ngày - hiển thị phân chia rõ ràng khi có nhiều ngày khác nhau với header ngày, thời lượng hoạt động
Updated: Thêm ID table động và DataTable initialization cho từng bảng timeline với disabled search, pagination, info display
Updated: Đánh lại STT - mỗi ngày bắt đầu từ số 1 thay vì đánh liên tục toàn bộ timeline
Updated: Hạn chế kéo thả chỉ trong cùng ngày - không cho phép kéo hoạt động từ ngày này sang ngày khác
--}}

<!-- 6. Timeline Sự Kiện -->
<div class="mb-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h6 class="fw-bold text-purple mb-0">
            <i class="ti ti-timeline me-1"></i>Timeline Sự Kiện
        </h6>
        @if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'create', 'branch'))
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTimelineModal">
                <i class="ti ti-plus me-1"></i>Thêm Timeline
            </button>
        @endif
    </div>
    <div class="mb-3">
        <small class="text-muted">
            <i class="ti ti-info-circle me-1"></i>
            Kéo thả để thay đổi thứ tự timeline trong cùng ngày. Sự kiện này có {{ $eventPlan->timeline->count() }} timeline.
        </small>
    </div>

    @if ($eventPlan->timeline && $eventPlan->timeline->count() > 0)
        @php
            $timelineByDate = $eventPlan->timeline->groupBy(function ($item) {
                return $item->date->format('Y-m-d');
            });

            // Sort dates by distance from today (nearest first)
            $today = now()->format('Y-m-d');
            $timelineByDate = $timelineByDate->sortKeysUsing(function ($dateA, $dateB) use ($today) {
                // Calculate distance from today for both dates
                $distanceA = abs(strtotime($dateA) - strtotime($today));
                $distanceB = abs(strtotime($dateB) - strtotime($today));

                // Sort by distance (closest first)
                return $distanceA <=> $distanceB;
            });
        @endphp

        <div class="timeline-container">
            @foreach ($timelineByDate as $date => $timelineItems)
                @php
                    $dateFormatted = \Carbon\Carbon::parse($date)->format('d/m/Y');
                    $dayName = \Carbon\Carbon::parse($date)->locale('vi')->isoFormat('dddd');
                @endphp

                <!-- Date Header -->
                <div class="timeline-date-header mb-3">
                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                        <h6 class="mb-0 text-primary">
                            <i class="ti ti-calendar-event me-2"></i>
                            <strong>{{ ucfirst($dayName) }}, {{ $dateFormatted }}</strong>
                        </h6>
                        <span class="badge bg-primary-transparent">{{ $timelineItems->count() }} hoạt động</span>
                    </div>
                </div>

                <!-- Timeline Items for this Date -->
                <div class="table-responsive mb-4">
                    <table id="timeline-table-{{ $loop->index }}"
                        class="table table-bordered table-hover align-middle timeline-table">
                        <thead>
                            <tr>
                                <th style="text-align: center !important;">STT</th>
                                <th style="text-align: center !important;">Thời gian</th>
                                <th>Nội dung</th>
                                <th style="text-align: center !important;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="timeline-day-list lh-base" data-date="{{ $date }}">
                            @foreach ($timelineItems as $dayIndex => $timelineItem)
                                <tr data-timeline-id="{{ $timelineItem->id }}" style="cursor: move;"
                                    class="timeline-item">
                                    <td class="text-center fw-bold">
                                        <i class="ti ti-grip-vertical text-muted me-1"></i>
                                        <span class="timeline-index">{{ $dayIndex + 1 }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $timelineItem->time_range }}</span>
                                        @php
                                            $duration = $timelineItem->duration_in_minutes;
                                            $hours = intval($duration / 60);
                                            $minutes = $duration % 60;
                                        @endphp
                                        <br><small class="text-muted">
                                            Thời lượng:
                                            @if ($hours > 0)
                                                {{ $hours }}h {{ $minutes }}p
                                            @else
                                                {{ $minutes }} phút
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <div class="timeline-content">
                                            <strong>{{ $timelineItem->content }}</strong>
                                            @if ($timelineItem->notes)
                                                <br><small class="text-muted">
                                                    <i
                                                        class="ti ti-note text-warning me-1"></i>{{ $timelineItem->notes }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="gap-2 fs-15">
                                            @if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'edit', 'branch'))
                                                <button type="button" class="btn btn-icon btn-sm btn-info-light"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editTimelineModal{{ $timelineItem->id }}"
                                                    title="Chỉnh sửa timeline">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                            @endif
                                            @if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'delete', 'branch'))
                                                <button type="button" class="btn btn-icon btn-sm btn-danger-light"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteTimelineModal{{ $timelineItem->id }}"
                                                    title="Xóa timeline">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="ti ti-timeline" style="font-size: 3rem; color: #6c757d; opacity: 0.5;"></i>
            <h6 class="text-muted mt-3 mb-1">Chưa có timeline nào</h6>
            <p class="text-muted small mb-0">Hãy thêm timeline đầu tiên cho kế hoạch sự kiện này</p>
        </div>
    @endif
</div>

{{-- Drag and Drop JavaScript --}}
{{-- Note: Requires jQuery and DataTables to be loaded in the main layout --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable for all timeline tables
        const timelineTables = document.querySelectorAll('.timeline-table');
        timelineTables.forEach(function(table) {
            if (table.id) {
                $('#' + table.id).DataTable({
                    scrollX: true,
                    searching: false,
                    paging: false,
                    info: false, // disables "Showing 1 to 2 of 2 entries"
                    ordering: false, // disable column sorting since we have drag & drop
                });
            }
        });

        const timelineDayLists = document.querySelectorAll('.timeline-day-list');

        timelineDayLists.forEach(function(dayList) {
            // Initialize Sortable for each day group (restricted to same day only)
            new Sortable(dayList, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                // Removed group property to prevent dragging between different days
                onEnd: function(evt) {
                    updateTimelineOrder();
                }
            });
        });

        function updateTimelineOrder() {
            // Get all timeline items across all days
            const allTimelineItems = document.querySelectorAll('.timeline-item[data-timeline-id]');
            const timelineIds = Array.from(allTimelineItems).map(item => item.getAttribute('data-timeline-id'));

            // Update STT numbers for each day separately
            const dayGroups = document.querySelectorAll('.timeline-day-list');
            dayGroups.forEach(function(dayGroup) {
                const dayItems = dayGroup.querySelectorAll('.timeline-item');
                dayItems.forEach((item, index) => {
                    const sttSpan = item.querySelector('.timeline-index');
                    if (sttSpan) {
                        sttSpan.textContent = index + 1;
                    }
                });
            });

            // Send AJAX request to update order
            fetch('{{ route('backend.club.event-plans.timeline.order', $eventPlan->event->id) }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        timeline_ids: timelineIds
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message (optional)
                        // console.log(data.message);
                    } else {
                        console.error('Error updating order:', data.error);
                        // Optionally reload page on error
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Optionally reload page on error
                    location.reload();
                });
        }
    });
</script>
