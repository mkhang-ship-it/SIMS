{{-- Admin events index view - Shows all events from all clubs system-wide
Features: View all events, approve pending events only
Admin can only approve events, no other actions allowed --}}

@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Sự Kiện</h1>

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
                                        <th>Mã Sự Kiện</th>
                                        <th>Tên Sự Kiện</th>
                                        <th>Câu Lạc Bộ</th>
                                        <th style="text-align: center !important;">Ngày Diễn Ra</th>
                                        <th style="text-align: center !important;">Thời Gian</th>
                                        <th style="text-align: center !important;">Địa Điểm</th>
                                        <th style="text-align: center !important;">Trạng Thái</th>
                                        <th style="text-align: center !important;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($events as $index => $event)
                                        <tr>
                                            <td class="text-center">
                                                {{ ($events->currentPage() - 1) * $events->perPage() + $index + 1 }}</td>
                                            <td>
                                                {{ $event->event_code }}
                                            </td>
                                            <td>
                                                {{ $event->name }}
                                                @if ($event->description)
                                                    <br><small
                                                        class="text-muted">{{ Str::limit($event->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="fw-medium">{{ $event->club->name ?? '-' }}</div>
                                                @if ($event->club->email)
                                                    <small class="text-muted">{{ $event->club->email }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="fw-medium">{{ $event->formatted_event_date }}</div>
                                                <small class="text-muted">{{ $event->semester->name ?? '-' }}</small>
                                            </td>
                                            <td class="text-center">
                                                <div>{{ $event->slot->name ?? '-' }}</div>
                                                <small class="text-muted">{{ $event->time_range }}</small>
                                            </td>
                                            <td class="text-center">{{ $event->classRoom->name ?? '-' }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $event->status_color }}">
                                                    {{ $event->status_label }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="gap-2 fs-15">
                                                    @if (auth()->user()->hasPermissionForModuleCode('event_management', 'approve', 'central') &&
                                                            $event->status === 'pending')
                                                        <button type="button" class="btn btn-icon btn-sm btn-success-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#approveEventModal{{ $event->id }}"
                                                            title="Phê duyệt sự kiện">
                                                            <i class="ri-check-line"></i>
                                                        </button>
                                                    @else
                                                        <span class="text-muted">
                                                            @if ($event->status === 'active')
                                                                <i class="ri-check-fill text-success"
                                                                    title="Đã phê duyệt"></i>
                                                            @elseif($event->status === 'cancelled')
                                                                <i class="ri-close-fill text-danger" title="Đã hủy"></i>
                                                            @elseif($event->status === 'completed')
                                                                <i class="ri-flag-fill text-info" title="Đã hoàn thành"></i>
                                                            @endif
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Approve Event Modal -->
                                        @if (auth()->user()->hasPermissionForModuleCode('event_management', 'approve', 'central') &&
                                                $event->status === 'pending')
                                            <div class="modal fade" id="approveEventModal{{ $event->id }}"
                                                tabindex="-1" aria-labelledby="approveEventModalLabel{{ $event->id }}"
                                                aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="approveEventModalLabel{{ $event->id }}">
                                                                Phê Duyệt Sự Kiện
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <form
                                                            action="{{ route('backend.events.approve', $event->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-body">
                                                                <div class="text-center mb-3">
                                                                    <i class="ri-question-mark-fill fs-50 text-warning"></i>
                                                                </div>
                                                                <p class="text-center">
                                                                    Bạn có chắc chắn muốn phê duyệt sự kiện
                                                                    <strong>"{{ $event->name }}"</strong>
                                                                    của câu lạc bộ
                                                                    <strong>"{{ $event->club->name }}"</strong> không?
                                                                </p>
                                                                <div class="alert alert-info">
                                                                    <strong>Thông tin sự kiện:</strong><br>
                                                                    <div class="row">
                                                                        <div class="col-6">
                                                                            <small>
                                                                                <strong>Ngày:</strong>
                                                                                {{ $event->formatted_event_date }}<br>
                                                                                <strong>Thời gian:</strong>
                                                                                {{ $event->time_range }}<br>
                                                                            </small>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <small>
                                                                                <strong>Địa điểm:</strong>
                                                                                {{ $event->classRoom->name ?? '-' }}<br>
                                                                                <strong>Học kỳ:</strong>
                                                                                {{ $event->semester->name ?? '-' }}
                                                                            </small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Hủy</button>
                                                                <button type="submit" class="btn btn-success">
                                                                    <i class="ri-check-line"></i> Phê Duyệt
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
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
