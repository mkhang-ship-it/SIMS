{{-- Created event show view for displaying detailed event information
Features: event details, QR code display, image gallery, check-in functionality
Updated: Single slot timing display instead of separate start/end slots
Updated: Added participants list section with statistics and attendee details
Updated: Removed export participants functionality per user request --}}

@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <div>
                <h1 class="page-title fw-semibold fs-18 mb-2">Chi Tiết Sự Kiện</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('backend.club.events.index') }}">Quản Lý Sự Kiện</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $event->name }}</li>
                    </ol>
                </nav>
            </div>
            <div>
                @if(auth()->user()->hasPermissionForModuleCode('event_management', 'view', 'branch'))
                    <a href="{{ route('backend.club.events.images.index', $event) }}" class="btn btn-info me-2">
                        <i class="ti ti-photo"></i>
                        Quản Lý Ảnh
                    </a>
                @endif
                <a href="{{ route('backend.club.events.index') }}" class="btn btn-light">
                    <i class="ti ti-arrow-left"></i>
                    Quay Lại
                </a>
            </div>
        </div>

        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="row">
            <div class="col-xl-8">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            Thông Tin Sự Kiện
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Mã Sự Kiện:</label>
                                    <p class="mb-0">{{ $event->event_code }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Trạng Thái:</label>
                                    <p class="mb-0">
                                        <span class="badge bg-{{ $event->status_color }}">
                                            {{ $event->status_label }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Thời Gian:</label>
                                    <p class="mb-0">
                                        <i class="ti ti-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($event->event_date)->format('d/m/Y') }} | {{ $event->time_range }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Địa Điểm:</label>
                                    <p class="mb-0">
                                        <i class="ti ti-map-pin me-1"></i>
                                        {{ $event->classRoom->name }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Học Kỳ:</label>
                                    <p class="mb-0">{{ $event->semester->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Câu Lạc Bộ:</label>
                                    <p class="mb-0">{{ $event->club->name }}</p>
                                </div>
                            </div>
                        </div>

                        @if ($event->description)
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Mô Tả Chi Tiết:</label>
                                <div class="border rounded p-3 bg-light">
                                    {!! nl2br(e($event->description)) !!}
                                </div>
                            </div>
                        @endif

                        @if ($event->image)
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Hình Ảnh:</label>
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->name }}"
                                        class="img-fluid rounded" style="max-height: 400px; cursor: pointer;"
                                        data-bs-toggle="modal" data-bs-target="#imageModal">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Participants List -->
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            Danh Sách Người Tham Gia
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Statistics -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h4 class="mb-1 text-primary">{{ $participantStats['total_participants'] }}</h4>
                                    <p class="mb-0 text-muted">Tổng số người tham gia</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h6 class="mb-1 text-success">
                                        {{ $participantStats['earliest_checkin'] ? $participantStats['earliest_checkin']->format('H:i') : '--:--' }}
                                    </h6>
                                    <p class="mb-0 text-muted">Check-in đầu tiên</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h6 class="mb-1 text-warning">
                                        {{ $participantStats['latest_checkin'] ? $participantStats['latest_checkin']->format('H:i') : '--:--' }}
                                    </h6>
                                    <p class="mb-0 text-muted">Check-in cuối cùng</p>
                                </div>
                            </div>
                        </div>

                        @if ($participants->count() > 0)
                            <!-- Participants Table -->
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Họ và Tên</th>
                                            <th>Email</th>
                                            <th>Thời gian Check-in</th>
                                            <th>Thiết bị</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($participants as $index => $participant)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img src="{{ $participant->user->avatar ?? asset('assets/images/avatar_example.png') }}"
                                                                alt="{{ $participant->user->name }}"
                                                                class="rounded-circle">
                                                        </div>
                                                        <div>
                                                            <span class="fw-semibold">{{ $participant->user->name }}</span>
                                                            @if ($participant->user->student_code)
                                                                <br><small
                                                                    class="text-muted">{{ $participant->user->student_code }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $participant->user->email }}</td>
                                                <td>
                                                    <span
                                                        class="fw-semibold">{{ $participant->checkin_time->format('H:i:s') }}</span>
                                                    <br><small
                                                        class="text-muted">{{ $participant->checkin_time->format('d/m/Y') }}</small>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-light text-dark">{{ $participant->browser_name }}</span>
                                                    @if ($participant->distance)
                                                        <br><small
                                                            class="text-muted">{{ number_format($participant->distance, 2) }}
                                                            km</small>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="ti ti-users-off fs-1 text-muted mb-3"></i>
                                <p class="text-muted">Chưa có ai tham gia sự kiện này</p>
                                @if ($event->status === 'pending')
                                    <small class="text-warning">Sự kiện đang chờ duyệt</small>
                                @elseif($event->status === 'cancelled')
                                    <small class="text-danger">Sự kiện đã bị hủy</small>
                                @elseif($event->status === 'completed')
                                    <small class="text-info">Sự kiện đã hoàn thành</small>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
        <!--End::row-1 -->

    </div>


@endsection
