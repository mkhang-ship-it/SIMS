@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Kế Hoạch Sự Kiện</h1>

            <!-- Nút trên desktop -->
            <div class="d-flex gap-2 align-items-center">
                @if (auth()->user()->hasPermissionForModuleCode('event_management', 'view', 'central'))
                    <a href="{{ route('backend.events.timetable') }}" class="btn btn-primary-light">
                        <i class="ti ti-calendar"></i> Xem Lịch Sự Kiện
                    </a>
                @endif
            </div>

            <!-- Dropdown trên mobile -->
            <div class="dropdown d-md-none">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Hành động
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @if (auth()->user()->hasPermissionForModuleCode('event_management', 'view', 'central'))
                        <li>
                            <a class="dropdown-item" href="{{ route('backend.events.timetable') }}">
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
                                        <th>Câu Lạc Bộ</th>
                                        <th>Tên Sự Kiện</th>
                                        <th>Người Lập</th>
                                        <th style="text-align: center !important;">Ngày Diễn Ra</th>
                                        <th style="text-align: center !important;">Thời Gian</th>
                                        <th style="text-align: center !important;">Địa Điểm</th>
                                        <th style="text-align: center !important;">Trạng Thái</th>
                                        <th style="text-align: center !important;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($eventPlans as $index => $eventPlan)
                                        <tr>
                                            <td class="text-center">
                                                {{ ($eventPlans->currentPage() - 1) * $eventPlans->perPage() + $index + 1 }}
                                            </td>
                                            <td>
                                                {{ $eventPlan->club->name }}
                                            </td>
                                            <td>
                                                {{ $eventPlan->event->name }}
                                                @if ($eventPlan->event->description)
                                                    <br><small
                                                        class="text-muted">{{ Str::limit($eventPlan->event->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="fw-medium">{{ $eventPlan->preparedBy->name ?? '-' }}</div>
                                                <small class="text-muted">{{ $eventPlan->preparedBy->email ?? '-' }}</small>
                                            </td>
                                            <td class="text-center">
                                                <div class="fw-medium">{{ $eventPlan->event->formatted_event_date }}</div>
                                                <small
                                                    class="text-muted">{{ $eventPlan->event->semester->name ?? '-' }}</small>
                                            </td>
                                            <td class="text-center">
                                                <div>{{ $eventPlan->event->slot->name ?? '-' }}</div>
                                                <small class="text-muted">{{ $eventPlan->event->time_range }}</small>
                                            </td>
                                            <td class="text-center">{{ $eventPlan->event->classRoom->name ?? '-' }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $eventPlan->event->status_color }}">
                                                    {{ $eventPlan->event->status_label }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="gap-2 fs-15">
                                                    <a href="{{ route('backend.events.event-plans.show', $eventPlan->event_id) }}"
                                                        class="btn btn-icon btn-sm btn-info-light"
                                                        title="Xem chi tiết kế hoạch">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                </div>
                                            </td>
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
