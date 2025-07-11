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
    </div>
    <div class="mb-3">
        <small class="text-muted">
            <i class="ti ti-info-circle me-1"></i>
            Kéo thả để thay đổi thứ tự timeline trong cùng ngày. Tổng cộng có {{ $eventPlan->timeline->count() }} mục
            timeline.
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
