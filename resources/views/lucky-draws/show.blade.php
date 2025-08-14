{{-- Lucky Draw show view - Updated: Added security_code field display for lucky draw management --}}

@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">
                <i class="fas fa-dice text-primary me-2"></i>
                Chi Tiết Lucky Draw
            </h1>

            <!-- Action Buttons -->
            <div class="d-flex gap-2">
                <a href="{{ route('backend.lucky-draws.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Quay Lại
                </a>
            </div>
        </div>

        <!-- Start::row-1 -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <!-- Lucky Draw Information -->
                        <div class="row mb-4">
                            <div class="col-lg-8">
                                <div class="mb-4">
                                    <h3 class="fw-semibold text-primary mb-2">{{ $luckyDraw->title }}</h3>
                                </div>

                                <!-- Time Information -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-3 border rounded">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-calendar-start text-success fs-4"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-1 fw-semibold">Thời Gian Bắt Đầu</h6>
                                                <p class="mb-0 text-muted">
                                                    {{ $luckyDraw->start_date->format('d/m/Y H:i:s') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-3 border rounded">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-calendar-end text-danger fs-4"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-1 fw-semibold">Thời Gian Kết Thúc</h6>
                                                <p class="mb-0 text-muted">{{ $luckyDraw->end_date->format('d/m/Y H:i:s') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Public Link, Security Code and Creator Info -->
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-link text-info me-1"></i>
                                                Link Công Khai
                                            </label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="publicLink"
                                                    value="{{ route('lucky-draw.show', $luckyDraw->slug) }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-shield-alt text-warning me-1"></i>
                                                Mã Bảo Mật
                                            </label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="securityCode"
                                                    value="{{ $luckyDraw->security_code ?? '-' }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center p-3 border rounded">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-user text-secondary fs-4"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-1 fw-semibold">Người Tạo</h6>
                                                <p class="mb-1">{{ $luckyDraw->creator->name ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status and Statistics -->
                            <div class="col-lg-4">
                                <!-- Status Card -->
                                <div class="card bg-light mb-3">
                                    <div class="card-body text-center">
                                        <h6 class="fw-semibold mb-3">Trạng Thái</h6>
                                        @if ($luckyDraw->isExpired())
                                            <span class="badge bg-danger fs-6 px-3 py-2">Đã Hết Hạn</span>
                                        @elseif($luckyDraw->isActive())
                                            <span class="badge bg-success fs-6 px-3 py-2">Đang Hoạt Động</span>
                                        @elseif(!$luckyDraw->is_active)
                                            <span class="badge bg-secondary fs-6 px-3 py-2">Tạm Dừng</span>
                                        @else
                                            <span class="badge bg-warning fs-6 px-3 py-2">Chưa Bắt Đầu</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Statistics Card -->
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="fw-semibold mb-3">
                                            <i class="fas fa-chart-bar text-primary me-1"></i>
                                            Thống Kê
                                        </h6>
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <h4 class="text-info fw-bold">{{ count($luckyDraw->participants ?? []) }}
                                                </h4>
                                                <small class="text-muted">Người Tham Gia</small>
                                            </div>
                                            <div class="col-6">
                                                <h4 class="text-warning fw-bold">{{ count($luckyDraw->winners ?? []) }}
                                                </h4>
                                                <small class="text-muted">Người Trúng Thưởng</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Prizes and Participants -->
                        <div class="row g-4">
                            <!-- Prizes Section -->
                            <div class="col-lg-6">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-semibold">
                                        <i class="fas fa-gift text-warning me-2"></i>
                                        Danh Sách Giải Thưởng
                                    </h5>
                                    <a href="{{ route('backend.lucky-draws.prizes.index', $luckyDraw->slug) }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="fas fa-cog me-1"></i>
                                        Quản Lý Giải Thưởng
                                    </a>
                                </div>

                                @if ($luckyDraw->prizes && count($luckyDraw->prizes) > 0)
                                    <div class="table-responsive">
                                        <table class="table text-nowrap table-bordered">
                                            <thead class="table-warning">
                                                <tr>
                                                    <th style="text-align: center !important;">STT</th>
                                                    <th style="text-align: center !important;">Hình Ảnh</th>
                                                    <th>Tên Giải Thưởng</th>
                                                    <th style="text-align: center !important;">Số Lượng</th>
                                                    <th style="text-align: center !important;">Còn Lại</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($luckyDraw->prizes as $index => $prize)
                                                    <tr>
                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                        <td class="text-center">
                                                            @if (isset($prize->image_url))
                                                                <img src="{{ $prize->image_url }}"
                                                                    alt="{{ $prize->name }}" class="img-thumbnail rounded"
                                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                                            @else
                                                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                                    style="width: 50px; height: 50px;">
                                                                    <i class="fas fa-image text-muted"></i>
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="fw-semibold">{{ $prize->name }}</span>
                                                            @if (isset($prize->isAvailable) && !$prize->isAvailable())
                                                                <span class="badge bg-danger ms-2">Hết Giải</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-info">{{ $prize->quantity ?? 1 }}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            @php
                                                                $remaining = isset($prize->remaining_quantity)
                                                                    ? $prize->remaining_quantity
                                                                    : $prize->quantity ?? 1;
                                                            @endphp
                                                            <span
                                                                class="badge bg-{{ $remaining > 0 ? 'success' : 'danger' }}">
                                                                {{ $remaining }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-gift text-muted fs-1 mb-3"></i>
                                        <p class="text-muted">Chưa có giải thưởng nào.</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Participants Section -->
                            <div class="col-lg-6">
                                <h5 class="fw-semibold mb-3">
                                    <i class="fas fa-users text-success me-2"></i>
                                    Danh Sách Người Tham Gia
                                </h5>

                                @if ($luckyDraw->participants && count($luckyDraw->participants) > 0)
                                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table text-nowrap table-bordered">
                                            <thead class="table-success">
                                                <tr>
                                                    <th style="text-align: center !important;">STT</th>
                                                    <th>Tên</th>
                                                    <th style="text-align: center !important;">Trạng Thái</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($luckyDraw->participants as $index => $participant)
                                                    <tr>
                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                        <td class="fw-semibold">{{ $participant }}</td>
                                                        <td class="text-center">
                                                            @php
                                                                $isWinner = collect(
                                                                    $luckyDraw->winners ?? [],
                                                                )->contains('name', $participant);
                                                            @endphp
                                                            @if ($isWinner)
                                                                <span class="badge bg-warning">Đã Trúng</span>
                                                            @else
                                                                <span class="badge bg-light text-dark">Chưa Trúng</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-users text-muted fs-1 mb-3"></i>
                                        <p class="text-muted">Chưa có người tham gia nào.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Winners Section -->
                        @if ($luckyDraw->winners && count($luckyDraw->winners) > 0)
                            <hr class="my-4">
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="fw-semibold mb-3">
                                        <i class="fas fa-trophy text-warning me-2"></i>
                                        Danh Sách Người Trúng Thưởng
                                    </h5>
                                    <div class="table-responsive">
                                        <table class="table text-nowrap table-bordered table-hover">
                                            <thead class="table-warning">
                                                <tr>
                                                    <th style="text-align: center !important;">STT</th>
                                                    <th>Người Trúng Thưởng</th>
                                                    <th style="text-align: center !important;">Giải Thưởng</th>
                                                    <th style="text-align: center !important;">Thời Gian Trúng</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($luckyDraw->winners as $index => $winner)
                                                    <tr>
                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                        <td class="fw-semibold">{{ $winner['name'] ?? 'N/A' }}</td>
                                                        <td class="text-center">
                                                            <span class="badge bg-warning text-dark">
                                                                {{ $winner['prize'] ?? 'Không có giải' }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <small class="text-muted">
                                                                {{ isset($winner['won_at']) ? \Carbon\Carbon::parse($winner['won_at'])->format('d/m/Y H:i:s') : 'N/A' }}
                                                            </small>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!--End::row-1 -->

    </div>
@endsection
