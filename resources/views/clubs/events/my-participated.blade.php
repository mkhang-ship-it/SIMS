{{-- Updated my-participated events view - Enhanced with consistent patterns from other index pages --}}
{{-- Features: DataTables integration, responsive design, consistent styling, improved statistics section, auto-submit semester filtering --}}
{{-- Follows project patterns: badges, buttons, table structure, responsive header design, and auto-submit filter forms --}}
{{-- Fixed: Statistics calculation now uses accurate data from controller instead of paginated collection --}}

@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Sự Kiện Đã Tham Gia</h1>

            <!-- Desktop Actions -->
            <div class="d-none d-md-flex gap-2 align-items-center">
                <a href="{{ route('backend.club.events.checkin.scanner') }}" class="btn btn-primary">
                    <i class="bi bi-qr-code-scan"></i> Quét QR Check-in
                </a>
            </div>

            <!-- Mobile Dropdown -->
            <div class="dropdown d-md-none">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Hành động
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('backend.club.events.checkin.scanner') }}">
                            <i class="bi bi-qr-code-scan"></i> Quét QR Check-in
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Header Close -->

        <!-- Statistics Card -->
        <div class="row mb-4">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">Thống Kê Tham Gia</div>
                        <div class="card-options">
                            <form method="GET" action="{{ route('backend.club.events.my-participated') }}" id="filterForm"
                                class="d-flex">
                                <select class="form-select" id="semester_id" name="semester_id"
                                    onchange="this.form.submit()" style="min-width: 200px;">
                                    <option value="all"
                                        {{ request('semester_id') == 'all' || !request('semester_id') ? 'selected' : '' }}>
                                        Tất cả học kỳ
                                    </option>
                                    @foreach ($semesters as $semester)
                                        <option value="{{ $semester->id }}"
                                            {{ request('semester_id') == $semester->id ? 'selected' : '' }}>
                                            {{ $semester->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3 col-6">
                                <div class="border-end border-bottom border-md-bottom-0 py-3">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <i class="ti ti-calendar-stats text-primary fs-24 me-2"></i>
                                        <h4 class="fw-semibold text-primary mb-0">{{ $statistics['total'] }}</h4>
                                    </div>
                                    <p class="text-muted mb-0 fw-medium">Tổng Sự Kiện</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="border-end border-bottom border-md-bottom-0 py-3">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <i class="ti ti-circle-check text-success fs-24 me-2"></i>
                                        <h4 class="fw-semibold text-success mb-0">{{ $statistics['completed'] }}</h4>
                                    </div>
                                    <p class="text-muted mb-0 fw-medium">Đã Hoàn Thành</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="border-end border-bottom border-sm-bottom-0 py-3">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <i class="ti ti-clock text-info fs-24 me-2"></i>
                                        <h4 class="fw-semibold text-info mb-0">{{ $statistics['active'] }}</h4>
                                    </div>
                                    <p class="text-muted mb-0 fw-medium">Đang Diễn Ra</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="py-3">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <i class="ti ti-hourglass text-warning fs-24 me-2"></i>
                                        <h4 class="fw-semibold text-warning mb-0">{{ $statistics['pending'] }}</h4>
                                    </div>
                                    <p class="text-muted mb-0 fw-medium">Chờ Duyệt</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Start::row-1 -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            Danh Sách Sự Kiện Đã Tham Gia
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($participatedEvents->count() > 0)
                            <div class="table-responsive">
                                <table class="table text-nowrap table-bordered" id="data-table">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center !important;">STT</th>
                                            <th>Tên Sự Kiện</th>
                                            <th style="text-align: center !important;">Học Kỳ</th>
                                            <th style="text-align: center !important;">Ngày Diễn Ra</th>
                                            <th style="text-align: center !important;">Thời Gian Check-in</th>
                                            <th style="text-align: center !important;">Địa Điểm</th>
                                            <th style="text-align: center !important;">Khoảng Cách</th>
                                            <th style="text-align: center !important;">Trạng Thái</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($participatedEvents as $index => $checkin)
                                            @php
                                                $event = $checkin->event;
                                            @endphp
                                            <tr>
                                                <td class="text-center">{{ $participatedEvents->firstItem() + $index }}
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-start">
                                                        <div class="me-2">
                                                            <i class="ti ti-calendar-event text-primary fs-18"></i>
                                                        </div>
                                                        <div>
                                                            <div class="fw-semibold">{{ $event->name }}</div>
                                                            <small class="text-muted">
                                                                <i class="ti ti-hash"></i> {{ $event->event_code }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-secondary-transparent">
                                                        {{ $event->semester->name ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="fw-medium">{{ $event->formatted_event_date }}</div>
                                                    <small class="text-muted">
                                                        <i class="ti ti-clock"></i> {{ $event->time_range }}
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <div class="fw-medium text-primary">
                                                        {{ $checkin->formatted_checkin_time }}
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="ti ti-device-desktop"></i> {{ $checkin->browser_name }}
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <i class="ti ti-map-pin text-secondary me-1"></i>
                                                        <span>{{ $event->classRoom->name ?? 'N/A' }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @if ($checkin->distance)
                                                        <span class="badge bg-success-transparent">
                                                            <i class="ti ti-route"></i> {{ round($checkin->distance, 2) }}
                                                            km
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary-transparent">
                                                            <i class="ti ti-question-mark"></i> N/A
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-{{ $event->status_color }}">
                                                        @switch($event->status)
                                                            @case('completed')
                                                                <i class="ti ti-circle-check me-1"></i>
                                                            @break

                                                            @case('active')
                                                                <i class="ti ti-clock me-1"></i>
                                                            @break

                                                            @case('pending')
                                                                <i class="ti ti-hourglass me-1"></i>
                                                            @break

                                                            @default
                                                                <i class="ti ti-circle me-1"></i>
                                                        @endswitch
                                                        {{ $event->status_label }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        @else
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="ti ti-calendar-x text-muted" style="font-size: 64px;"></i>
                                </div>
                                <h5 class="text-muted mb-2">Chưa có sự kiện nào</h5>
                                <p class="text-muted">
                                    @if (request('semester_id') && request('semester_id') != 'all')
                                        Bạn chưa tham gia sự kiện nào trong học kỳ này.
                                    @else
                                        Bạn chưa tham gia sự kiện nào.
                                    @endif
                                </p>
                                <a href="{{ route('backend.club.events.checkin.scanner') }}" class="btn btn-primary">
                                    <i class="bi bi-qr-code-scan"></i> Quét QR Check-in
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- End::row-1 -->
    </div>
@endsection
