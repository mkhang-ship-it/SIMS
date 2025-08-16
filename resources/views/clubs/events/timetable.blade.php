@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Xem Lịch Sự Kiện</h1>

            <!-- Back to Events List -->
            <div class="d-flex gap-2 align-items-center">
                @if (auth()->user()->hasPermissionForModuleCode('event_management', 'view', 'branch'))
                    <a href="{{ route('backend.club.events.index') }}" class="btn btn-primary-light">
                        <i class="ti ti-arrow-left"></i>
                        Danh Sách Sự Kiện
                    </a>
                @endif

                <!-- Week Selector -->
                <form method="GET" action="{{ route('backend.club.events.timetable') }}" class="d-flex gap-2">
                    <select name="week" class="form-select" style="min-width: 200px;" onchange="this.form.submit()">
                        @foreach ($weekOptions as $weekNum => $weekLabel)
                            <option value="{{ $weekNum }}" {{ $selectedWeek == $weekNum ? 'selected' : '' }}>
                                {{ $weekLabel }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="year" value="{{ $selectedYear }}">
                </form>
            </div>
        </div>

        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="card-title fw-semibold fs-18">
                                Tuần {{ $selectedWeek }} - Năm {{ $selectedYear }}
                            </div>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success">ACT</span>
                                    <span class="small">Đang Diễn Ra</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-warning">PEN</span>
                                    <span class="small">Chờ Duyệt</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-danger">CAN</span>
                                    <span class="small">Đã Hủy</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-info">COM</span>
                                    <span class="small">Đã Diễn Ra</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 120px;">Khung Giờ</th>
                                        @foreach ($dateHeaders as $dayIndex => $dayHeader)
                                            <th class="text-center" style="min-width: 180px;">
                                                {{ $dayHeader['name'] }}
                                                <br>
                                                <small class="text-muted">{{ $dayHeader['date'] }}</small>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($slots as $slot)
                                        <tr>
                                            <td class="text-center align-middle bg-light">
                                                <div class="fw-bold text-dark">{{ $slot->name }}</div>
                                                <small class="text-muted d-block mt-1">
                                                    {{ $slot->start_time }} - {{ $slot->end_time }}
                                                </small>
                                            </td>
                                            @for ($day = 0; $day < 7; $day++)
                                                <td class="text-center align-middle p-1"
                                                    style="height: 100px; vertical-align: top;">
                                                    @if (isset($timetable[$day][$slot->id]) && $timetable[$day][$slot->id])
                                                        @php
                                                            $event = $timetable[$day][$slot->id];
                                                            $statusColor = match ($event->status ?? 'active') {
                                                                'pending' => 'warning',
                                                                'active' => 'success',
                                                                'cancelled' => 'danger',
                                                                'completed' => 'info',
                                                                default => 'secondary',
                                                            };
                                                        @endphp
                                                        <div
                                                            class="event-card h-100 d-flex flex-column justify-content-between">
                                                            <!-- Event Header -->
                                                            <div
                                                                class="event-header p-2 bg-{{ $statusColor }} bg-opacity-10 border border-{{ $statusColor }} rounded-top">
                                                                @if (in_array($event->status, ['active', 'completed']))
                                                                    <a
                                                                        href="{{ route('backend.club.events.show', $event->id) }}">
                                                                        <div class="event-title fw-semibold text-{{ $statusColor }}"
                                                                            style="font-size: 0.8rem; line-height: 1.2; word-break: break-word;"
                                                                            title="{{ $event->name }}">
                                                                            {{ Str::limit($event->name, 25) }}
                                                                            <br>
                                                                            <span
                                                                                class="badge bg-{{ $statusColor }} badge-sm mt-1">
                                                                                {{ $event->classRoom->name }}
                                                                            </span>
                                                                        </div>
                                                                    </a>
                                                                @else
                                                                    <div class="event-title fw-semibold text-{{ $statusColor }}"
                                                                        style="font-size: 0.8rem; line-height: 1.2; word-break: break-word;"
                                                                        title="{{ $event->name }}">
                                                                        {{ Str::limit($event->name, 25) }}
                                                                        <br>
                                                                        <span
                                                                            class="badge bg-{{ $statusColor }} badge-sm mt-1">
                                                                            {{ $event->classRoom->name }}
                                                                        </span>
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            <!-- Event Details -->
                                                            <div class="event-details p-2 bg-light border-start border-end border-{{ $statusColor }}"
                                                                style="flex-grow: 1;">
                                                                <div class="d-flex justify-content-center align-items-center mb-1 text-center"
                                                                    style="font-size: 0.75rem;">
                                                                    @switch($event->status)
                                                                        @case('active')
                                                                            <i class="ti ti-play text-success me-1"></i>
                                                                            <span class="text-success">Đang Diễn Ra</span>
                                                                        @break

                                                                        @case('pending')
                                                                            <i class="ti ti-clock text-warning me-1"></i>
                                                                            <span class="text-warning">Chờ Duyệt</span>
                                                                        @break

                                                                        @case('cancelled')
                                                                            <i class="ti ti-x text-danger me-1"></i>
                                                                            <span class="text-danger">Đã Hủy</span>
                                                                        @break

                                                                        @case('completed')
                                                                            <i class="ti ti-check text-info me-1"></i>
                                                                            <span class="text-info">Đã Diễn Ra</span>
                                                                        @break

                                                                        @default
                                                                            <i class="ti ti-clock text-muted me-1"></i>
                                                                            <span
                                                                                class="text-muted">{{ ucfirst($event->status) }}</span>
                                                                    @endswitch
                                                                </div>
                                                            </div>

                                                            <!-- Event Footer -->
                                                            <div
                                                                class="event-footer p-1 bg-{{ $statusColor }} bg-opacity-5 border border-{{ $statusColor }} rounded-bottom">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-center flex-column">
                                                                    @if (isset($event->start_date))
                                                                        <small class="text-muted"
                                                                            style="font-size: 0.7rem;">
                                                                            <i class="ti ti-calendar-time me-1"></i>
                                                                            {{ \Carbon\Carbon::parse($event->start_date)->format('d/m') }}
                                                                        </small>
                                                                    @endif

                                                                    @if ($event->status === 'active' && !in_array($event->id, $userCheckins))
                                                                        <div class="mt-1">
                                                                            <a href="{{ route('backend.club.events.checkin.scanner') }}"
                                                                                class="btn btn-sm btn-success btn-outline px-2 py-1"
                                                                                style="font-size: 0.65rem; line-height: 1;"
                                                                                title="Check-in sự kiện">
                                                                                <i class="ti ti-qrcode me-1"></i>
                                                                                Check-in
                                                                            </a>
                                                                        </div>
                                                                    @elseif ($event->status === 'active' && in_array($event->id, $userCheckins))
                                                                        <div class="mt-1">
                                                                            <span class="badge bg-success px-2 py-1"
                                                                                style="font-size: 0.65rem; line-height: 1;"
                                                                                title="Đã check-in">
                                                                                <i class="ti ti-check me-1"></i>
                                                                                Đã tham gia
                                                                            </span>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            @endfor
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

    </div>
@endsection
