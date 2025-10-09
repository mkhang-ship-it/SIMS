{{-- Updated: Fixed pagination elements property error and improved pagination
- Updated stats display to use array structure ($stats, $todayStats)
- Added pagination with per-page selector
- Improved STT numbering for paginated results
- Fixed Laravel pagination by replacing custom elements loop with built-in links() method
- Resolved "Undefined property: elements" error
--}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Log Hệ Thống</h1>

            <!-- Nút trên desktop -->
            <div class="d-none d-md-block">
                @if (auth()->user()->hasPermissionForModuleCode('log_management', 'delete', 'central'))
                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#destroyAllModal">
                        <i class="ti ti-trash"></i>
                        Xóa Tất Cả Log
                    </button>
                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#destroyOldModal">
                        <i class="ti ti-clock"></i>
                        Xóa Log Cũ
                    </button>
                @endif
                @if (auth()->user()->hasPermissionForModuleCode('log_management', 'export', 'central'))
                    <a href="{{ route('backend.logs.export', request()->query()) }}" class="btn btn-success">
                        <i class="ti ti-download"></i>
                        Xuất Excel
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
                    @if (auth()->user()->hasPermissionForModuleCode('log_management', 'delete', 'central'))
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                data-bs-target="#destroyAllModal">
                                <i class="ti ti-trash"></i> Xóa Tất Cả Log
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                data-bs-target="#destroyOldModal">
                                <i class="ti ti-clock"></i> Xóa Log Cũ
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasPermissionForModuleCode('log_management', 'export', 'central'))
                        <li>
                            <a class="dropdown-item" href="{{ route('backend.logs.export', request()->query()) }}">
                                <i class="ti ti-download"></i> Xuất Excel
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="row">
            <!-- Thống kê -->
            <div class="col-xl-12">
                <div class="row">
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="avatar avatar-md bg-primary">
                                            <i class="ti ti-file-text fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill">
                                        <h6 class="fw-semibold mb-1">Tổng Số Log</h6>
                                        <h4 class="fw-bold mb-0">{{ number_format($stats['totalLogs']) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="avatar avatar-md bg-success">
                                            <i class="ti ti-calendar fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill">
                                        <h6 class="fw-semibold mb-1">Log Hôm Nay</h6>
                                        <h4 class="fw-bold mb-0">{{ number_format($todayStats['todayLogs']) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="avatar avatar-md bg-warning">
                                            <i class="ti ti-users fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill">
                                        <h6 class="fw-semibold mb-1">Người Dùng</h6>
                                        <h4 class="fw-bold mb-0">{{ number_format($stats['uniqueUsers']) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="avatar avatar-md bg-info">
                                            <i class="ti ti-server fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill">
                                        <h6 class="fw-semibold mb-1">IP Khác Biệt</h6>
                                        <h4 class="fw-bold mb-0">{{ number_format($stats['uniqueIPs']) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Biểu đồ thống kê -->
            <div class="col-xl-6">
                <div class="card custom-card">
                    <div class="card-header">
                        <h5 class="card-title">Thống Kê Theo Method</h5>
                    </div>
                    <div class="card-body" style="height: 280px;">
                        <canvas id="methodChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card custom-card">
                    <div class="card-header">
                        <h5 class="card-title">Hoạt Động Hôm Nay (Theo Giờ)</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @for ($hour = 0; $hour < 24; $hour++)
                                @php
                                    $hourStat = $hourlyStats->firstWhere('hour', $hour);
                                    $count = $hourStat ? $hourStat->count : 0;
                                @endphp
                                <div class="col-3 mb-2">
                                    <div class="text-center">
                                        <div class="fw-semibold">{{ $hour }}:00</div>
                                        <div class="text-muted">{{ $count }}</div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <h5 class="card-title">Bộ Lọc</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('backend.logs.index') }}" class="row g-3">
                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <label class="form-label">Người Dùng</label>
                                <select name="user_id" class="form-select">
                                    <option value="">Tất cả</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <label class="form-label">Method</label>
                                <select name="method" class="form-select">
                                    <option value="">Tất cả</option>
                                    @foreach ($methods as $method)
                                        <option value="{{ $method }}"
                                            {{ request('method') == $method ? 'selected' : '' }}>
                                            {{ $method }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- <div class="col-lg-2 col-md-4 col-sm-6">
                                <label class="form-label">IP</label>
                                <input type="text" name="ip" class="form-control" value="{{ request('ip') }}"
                                    placeholder="Nhập IP...">
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <label class="form-label">URL</label>
                                <input type="text" name="url" class="form-control" value="{{ request('url') }}"
                                    placeholder="Nhập URL...">
                            </div> --}}
                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <label class="form-label">Từ Ngày</label>
                                <input type="date" name="date_from" class="form-control"
                                    value="{{ request('date_from') }}">
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <label class="form-label">Đến Ngày</label>
                                <input type="date" name="date_to" class="form-control"
                                    value="{{ request('date_to') }}">
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6">
                                <label class="form-label">Số bản ghi/trang</label>
                                <select name="per_page" class="form-select">
                                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10
                                    </option>
                                    <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25
                                    </option>
                                    <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50
                                    </option>
                                    <option value="100" {{ request('per_page', 100) == 100 ? 'selected' : '' }}>100
                                    </option>
                                    <option value="200" {{ request('per_page', 200) == 200 ? 'selected' : '' }}>200
                                    </option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search"></i> Lọc
                                </button>
                                <a href="{{ route('backend.logs.index') }}" class="btn btn-secondary">
                                    <i class="ti ti-refresh"></i> Làm Mới
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Bảng Logs -->
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="table-responsive mb-4">
                            <table class="table text-nowrap table-bordered" id="data-table-display">
                                <thead>
                                    <tr>
                                        <th style="text-align: center !important;">STT</th>
                                        <th style="text-align: center !important;">Thời Gian</th>
                                        <th style="text-align: center !important;">Người Dùng</th>
                                        <th style="text-align: center !important;">Method</th>
                                        <th style="text-align: center !important;">URL</th>
                                        <th style="text-align: center !important;">IP</th>
                                        <th style="text-align: center !important;">User Agent</th>
                                        <th style="text-align: center !important;">Request Data</th>
                                        <th style="text-align: center !important;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($logs as $index => $log)
                                        <tr>
                                            <td class="text-center">
                                                {{ ($logs->currentPage() - 1) * $logs->perPage() + $loop->iteration }}</td>
                                            <td class="text-center">
                                                <div class="d-flex flex-column">
                                                    <span
                                                        class="fw-semibold">{{ $log->created_at->format('d/m/Y') }}</span>
                                                    <small
                                                        class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if ($log->user)
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        @if ($log->user->avatar)
                                                            <span class="avatar avatar-xs me-2">
                                                                <img src="{{ Storage::url($log->user->avatar) }}"
                                                                    alt="Avatar">
                                                            </span>
                                                        @else
                                                            <span class="avatar avatar-xs bg-primary me-2">
                                                                {{ substr($log->user->name, 0, 1) }}
                                                            </span>
                                                        @endif
                                                        <span>{{ $log->user->name }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Guest</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $log->method_badge_class }}">
                                                    {{ $log->method }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="text-truncate" style="max-width: 300px;"
                                                    title="{{ $log->url }}">
                                                    {{ $log->truncated_url }}
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <code>{{ $log->ip }}</code>
                                            </td>
                                            <td class="text-center">
                                                <div class="text-truncate" style="max-width: 200px;"
                                                    title="{{ $log->user_agent }}">
                                                    {{ $log->truncated_user_agent }}
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if ($log->request_data)
                                                    <div class="request-data-preview">
                                                        @php
                                                            $requestData = is_string($log->request_data)
                                                                ? json_decode($log->request_data, true)
                                                                : $log->request_data;
                                                            if (is_array($requestData)) {
                                                                $displayData = [];
                                                                foreach ($requestData as $key => $value) {
                                                                    if (is_array($value)) {
                                                                        $displayData[] =
                                                                            '<strong>' .
                                                                            $key .
                                                                            '</strong>: [' .
                                                                            count($value) .
                                                                            ' items]';
                                                                    } else {
                                                                        $displayData[] =
                                                                            '<strong>' .
                                                                            $key .
                                                                            '</strong>: ' .
                                                                            (is_string($value) && strlen($value) > 40
                                                                                ? substr($value, 0, 40) . '...'
                                                                                : $value);
                                                                    }
                                                                }
                                                                echo implode('<br>', array_slice($displayData, 0, 4));
                                                                if (count($displayData) > 4) {
                                                                    echo '<br><small class="text-muted">... and ' .
                                                                        (count($displayData) - 4) .
                                                                        ' more</small>';
                                                                }
                                                            } else {
                                                                echo htmlspecialchars(
                                                                    substr($log->request_data, 0, 80),
                                                                ) . (strlen($log->request_data) > 80 ? '...' : '');
                                                            }
                                                        @endphp
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if (auth()->user()->hasPermissionForModuleCode('log_management', 'delete', 'central'))
                                                    <button type="button" class="btn btn-icon btn-sm btn-danger-light"
                                                        onclick="deleteLog('{{ $log->id }}')" title="Xóa">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Links -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Hiển thị {{ $logs->firstItem() ?? 0 }} đến {{ $logs->lastItem() ?? 0 }}
                                trong {{ number_format($logs->total()) }} bản ghi
                            </div>
                            <div class="d-flex justify-content-center">
                                {{ $logs->links('vendor.pagination.bootstrap-5') }}
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End::row-1 -->

    </div>

    <!-- Modal Xóa Tất Cả -->
    <div class="modal fade" id="destroyAllModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác Nhận Xóa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa tất cả log? Hành động này không thể hoàn tác!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <form action="{{ route('backend.logs.destroy-all') }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Xóa Tất Cả</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Xóa Log Cũ -->
    <div class="modal fade" id="destroyOldModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xóa Log Cũ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('backend.logs.destroy-old') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="mb-3">
                            <label class="form-label">Xóa log cũ hơn (ngày):</label>
                            <select name="days" class="form-select">
                                <option value="7">7 ngày</option>
                                <option value="30" selected>30 ngày</option>
                                <option value="90">90 ngày</option>
                                <option value="180">180 ngày</option>
                                <option value="365">1 năm</option>
                            </select>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-warning">Xóa Log Cũ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <style>
        #methodChart {
            max-height: 100%;
            width: 100% !important;
        }

        /* CSS cho modal log detail */
        .log-data {
            word-wrap: break-word;
            white-space: pre-wrap;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
        }

        .log-data-scrollable {
            max-height: 200px;
            overflow-y: auto;
        }

        /* CSS cho request data preview trong table */
        .request-data-preview {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            line-height: 1.4;
            text-align: left;
            max-height: 120px;
            overflow-y: auto;
            padding: 8px;
            background: #f8f9fa;
            border-radius: 4px;
            border: 1px solid #e9ecef;
        }

        .request-data-preview strong {
            color: #0d6efd;
            font-weight: 600;
        }

        .request-data-preview .text-muted {
            color: #6c757d !important;
            font-size: 10px;
        }

        /* CSS cho request data tree (nếu cần) */
        .request-data-tree {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.5;
        }

        .data-item {
            margin-bottom: 8px;
            padding: 4px 0;
        }

        .data-key {
            color: #0d6efd;
            font-weight: 600;
            margin-right: 8px;
        }

        .data-value {
            color: #333;
            word-break: break-all;
        }

        .array-preview {
            margin-top: 4px;
            padding-left: 16px;
        }

        .array-item {
            display: inline-block;
            background: #f8f9fa;
            padding: 2px 6px;
            margin: 2px;
            border-radius: 3px;
            font-size: 11px;
            border: 1px solid #dee2e6;
        }

        /* Đảm bảo modal không bị cuộn ngang */
        .modal-dialog {
            max-width: 90%;
        }

        .modal-body {
            overflow-x: hidden;
        }
    </style>

    <script>
        function deleteLog(logId) {
            if (confirm('Bạn có chắc chắn muốn xóa log này?')) {
                fetch(`/backend/logs/${logId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                }).then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        alert('Có lỗi xảy ra khi xóa log!');
                    }
                });
            }
        }

        // Chart.js cho Thống Kê Theo Method
        document.addEventListener('DOMContentLoaded', function() {
            const methodStats = @json($methodStats);

            const ctx = document.getElementById('methodChart').getContext('2d');
            const methodChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: methodStats.map(stat => stat.method),
                    datasets: [{
                        label: 'Số lượng',
                        data: methodStats.map(stat => stat.count),
                        backgroundColor: methodStats.map(stat => {
                            switch (stat.method) {
                                case 'GET':
                                    return '#198754'; // bg-success
                                case 'POST':
                                    return '#0d6efd'; // bg-primary
                                case 'PUT':
                                    return '#ffc107'; // bg-warning
                                case 'DELETE':
                                    return '#dc3545'; // bg-danger
                                default:
                                    return '#0dcaf0'; // bg-info
                            }
                        }),
                        borderColor: methodStats.map(stat => {
                            switch (stat.method) {
                                case 'GET':
                                    return '#146c43';
                                case 'POST':
                                    return '#0a58ca';
                                case 'PUT':
                                    return '#b45309';
                                case 'DELETE':
                                    return '#b02a37';
                                default:
                                    return '#0aa2c0';
                            }
                        }),
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#666',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y.toLocaleString() + ' requests';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        }
                    },
                    elements: {
                        bar: {
                            borderRadius: 6,
                            borderSkipped: false,
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        });
    </script>
@endsection
