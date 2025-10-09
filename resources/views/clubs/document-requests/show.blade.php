@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <div>
                <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Chi Tiết Yêu Cầu Nộp Văn Bản</h1>
            </div>

            <!-- Action buttons -->
            <div class="d-flex gap-2">
                <a href="{{ route('backend.club.document-requests.index') }}" class="btn btn-light">
                    <i class="ti ti-arrow-left"></i>
                    Quay Lại
                </a>

                @if (auth()->user()->hasPermissionForModuleCode('document_request_management', 'edit', 'branch') &&
                        $documentRequest->deadline >= now())
                    <a href="{{ route('backend.club.document-requests.edit', $documentRequest) }}" class="btn btn-info">
                        <i class="ti ti-edit"></i>
                        Chỉnh Sửa
                    </a>
                @endif
            </div>
        </div>

        <div class="row">
            <!-- Request Information -->
            <div class="col-xl-4">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">Thông Tin Yêu Cầu Nộp Văn Bản</div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Loại văn bản:</strong>
                            {{ $documentRequest->documentType->name }}
                        </div>

                        <div class="mb-3">
                            <strong>Người yêu cầu nộp:</strong>
                            {{ $documentRequest->requestedByUser->name }}
                            <br><small class="text-muted">{{ $documentRequest->created_at->format('d/m/Y H:i:s') }}</small>
                        </div>

                        <div class="mb-3">
                            <strong>Hạn nộp văn bản:</strong><br>
                            <span class="fs-5 {{ $documentRequest->deadline < now() ? 'text-danger' : 'text-success' }}">
                                {{ $documentRequest->deadline->format('d/m/Y H:i') }}
                            </span>
                            <br>
                            @if ($documentRequest->deadline < now())
                                <span class="badge bg-danger">Đã hết hạn</span>
                            @else
                                <span class="badge bg-success">Còn {{ $documentRequest->deadline->diffForHumans() }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Progress Statistics -->
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">Thống Kê Tiến Độ</div>
                    </div>
                    <div class="card-body">
                        @php
                            $totalUsers = $documentRequest->documentRequestUsers->count();
                            $completedUsers = $documentRequest->documentRequestUsers
                                ->whereNotNull('document_id')
                                ->count();
                            $pendingUsers = $totalUsers - $completedUsers;
                            $progressPercent = $totalUsers > 0 ? ($completedUsers / $totalUsers) * 100 : 0;
                        @endphp

                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <canvas id="progressChart" width="120" height="120"></canvas>
                                <div class="position-absolute top-50 start-50 translate-middle text-center">
                                    <h4 class="mb-0">{{ number_format($progressPercent, 1) }}%</h4>
                                </div>
                            </div>
                        </div>

                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h5 class="text-success mb-1">{{ $completedUsers }}</h5>
                                    <small class="text-muted">Đã nộp</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h5 class="text-warning mb-1">{{ $pendingUsers }}</h5>
                                <small class="text-muted">Chưa nộp</small>
                            </div>
                        </div>

                        <div class="progress mt-3" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progressPercent }}%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Member List and Status -->
            <div class="col-xl-8">
                <div class="card custom-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">Danh Sách Thành Viên ({{ $totalUsers }} người)</div>

                        <!-- Filter buttons -->
                        <div class="btn-group btn-group-sm" role="group">
                            <input type="radio" class="btn-check" name="statusFilter" id="filterAll" autocomplete="off"
                                checked>
                            <label class="btn btn-outline-primary" for="filterAll">Tất cả</label>

                            <input type="radio" class="btn-check" name="statusFilter" id="filterCompleted"
                                autocomplete="off">
                            <label class="btn btn-outline-success" for="filterCompleted">Đã nộp</label>

                            <input type="radio" class="btn-check" name="statusFilter" id="filterPending"
                                autocomplete="off">
                            <label class="btn btn-outline-warning" for="filterPending">Chưa nộp</label>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="data-table">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Thành viên</th>
                                        <th>Email</th>
                                        <th style="text-align: center;">Trạng thái</th>
                                        <th style="text-align: center;">Ngày nộp</th>
                                        <th style="text-align: center;">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($documentRequest->documentRequestUsers->sortBy('user.name') as $requestUser)
                                        <tr class="member-row"
                                            data-status="{{ $requestUser->document_id ? 'completed' : 'pending' }}">
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <p class="mb-0">{{ $requestUser->user->name }}</p>
                                                        @if ($requestUser->user->student_code)
                                                            <small
                                                                class="text-muted">{{ $requestUser->user->student_code }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $requestUser->user->email }}</td>
                                            <td class="text-center">
                                                @if ($requestUser->document_id)
                                                    <span class="badge bg-success">Đã nộp</span>
                                                @else
                                                    <span class="badge bg-warning">Chưa nộp</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($requestUser->submitted_at)
                                                    <span class="text-success">
                                                        {{ $requestUser->submitted_at->format('d/m/Y H:i') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($requestUser->document_id)
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <a href="{{ route('backend.club.documents.show', $requestUser->document) }}"
                                                            class="btn btn-sm btn-primary-light" title="Xem tài liệu">
                                                            <i class="ri-eye-line"></i>
                                                        </a>
                                                        <a href="{{ route('backend.club.documents.download', $requestUser->document) }}"
                                                            class="btn btn-sm btn-info-light" title="Tải xuống">
                                                            <i class="ri-download-line"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
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

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Progress Chart
            const ctx = document.getElementById('progressChart').getContext('2d');
            const progressPercent = {{ $progressPercent }};

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [progressPercent, 100 - progressPercent],
                        backgroundColor: [
                            '#28a745',
                            '#e9ecef'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: false,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // Filter functionality
            const filterButtons = document.querySelectorAll('input[name="statusFilter"]');
            const memberRows = document.querySelectorAll('.member-row');

            filterButtons.forEach(button => {
                button.addEventListener('change', function() {
                    const filter = this.id.replace('filter', '').toLowerCase();

                    memberRows.forEach(row => {
                        const status = row.getAttribute('data-status');

                        if (filter === 'all' || status === filter) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            });
        });
    </script>
@endsection
