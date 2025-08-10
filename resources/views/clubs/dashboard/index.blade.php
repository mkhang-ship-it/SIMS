{{-- Updated: Created comprehensive club dashboard with visual reports and statistics
- Added overview statistics cards for members, events, finances
- Added visual charts for growth tracking and trend analysis
- Added recent activities section with events, invoices, expenses
- Added financial summary cards with revenue/expense tracking
- All data filtered by current user's club membership
--}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Overview Statistics Cards -->
        <div class="row mt-4">
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="avatar avatar-md bg-primary">
                                    <i class="bx bx-group fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <h6 class="fw-semibold mb-0">Thành Viên</h6>
                                <div class="d-flex align-items-center">
                                    <span class="text-lg fw-semibold">{{ number_format($stats['total_members']) }}</span>
                                    <span class="badge bg-success-transparent ms-2">
                                        Hoạt động
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="avatar avatar-md bg-warning">
                                    <i class="bx bx-calendar-event fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <h6 class="fw-semibold mb-0">Sự Kiện</h6>
                                <div class="d-flex align-items-center">
                                    <span class="text-lg fw-semibold">{{ number_format($stats['total_events']) }}</span>
                                    @if($stats['pending_events'] > 0)
                                        <span class="badge bg-warning-transparent ms-2">
                                            {{ number_format($stats['pending_events']) }} chờ duyệt
                                        </span>
                                    @else
                                        <span class="badge bg-success-transparent ms-2">
                                            {{ number_format($stats['active_events']) }} hoạt động
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="avatar avatar-md bg-success">
                                    <i class="bx bx-money fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <h6 class="fw-semibold mb-0">Thu Nhập</h6>
                                <div class="d-flex align-items-center">
                                    <span class="text-lg fw-semibold">{{ number_format($stats['total_revenue']) }}đ</span>
                                    @if($stats['pending_revenue'] > 0)
                                        <span class="badge bg-info-transparent ms-2">
                                            +{{ number_format($stats['pending_revenue']) }}đ chờ
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="avatar avatar-md bg-danger">
                                    <i class="bx bx-credit-card fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <h6 class="fw-semibold mb-0">Chi Phí</h6>
                                <div class="d-flex align-items-center">
                                    <span class="text-lg fw-semibold">{{ number_format($stats['total_expenses']) }}đ</span>
                                    @if($stats['pending_expenses'] > 0)
                                        <span class="badge bg-warning-transparent ms-2">
                                            +{{ number_format($stats['pending_expenses']) }}đ chờ
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Summary Row -->
        <div class="row">
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="avatar avatar-md bg-info">
                                    <i class="bx bx-trending-up fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <h6 class="fw-semibold mb-0">Lợi Nhuận</h6>
                                <span class="text-lg fw-semibold {{ $stats['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($stats['net_profit']) }}đ
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="avatar avatar-md bg-secondary">
                                    <i class="bx bx-receipt fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <h6 class="fw-semibold mb-0">Hóa Đơn</h6>
                                <div class="d-flex align-items-center">
                                    <span class="text-lg fw-semibold">{{ number_format($stats['total_invoices']) }}</span>
                                    <span class="badge bg-success-transparent ms-2">
                                        {{ number_format($stats['paid_invoices']) }} đã thanh toán
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="avatar avatar-md bg-purple">
                                    <i class="bx bx-trophy fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <h6 class="fw-semibold mb-0">Giải Thưởng</h6>
                                <span class="text-lg fw-semibold">{{ number_format($stats['total_awards']) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="avatar avatar-md bg-teal">
                                    <i class="bx bx-gift fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <h6 class="fw-semibold mb-0">Lucky Draw</h6>
                                <div class="d-flex align-items-center">
                                    <span class="text-lg fw-semibold">{{ number_format($stats['total_lucky_draws']) }}</span>
                                    <span class="badge bg-success-transparent ms-2">
                                        {{ number_format($stats['active_lucky_draws']) }} hoạt động
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Statistics Row -->
        <div class="row">
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <div class="card custom-card border-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="avatar avatar-md bg-primary-gradient">
                                    <i class="bx bx-check-circle fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <h6 class="fw-semibold mb-0 text-primary">Tôi Đã Tham Gia</h6>
                                <div class="d-flex align-items-center">
                                    <span class="text-lg fw-semibold text-primary">{{ number_format($stats['participated_events']) }}</span>
                                    <span class="badge bg-primary-transparent ms-2">
                                        sự kiện
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-9 col-lg-8 col-md-6 col-sm-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="avatar avatar-md bg-info">
                                    <i class="bx bx-info-circle fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <h6 class="fw-semibold mb-0">Thông Tin Cá Nhân</h6>
                                <p class="text-muted mb-0">
                                    Bạn đã tham gia <strong>{{ number_format($stats['participated_events']) }} sự kiện</strong> của club này.
                                    Cảm ơn bạn đã tích cực tham gia các hoạt động!
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <!-- My Participation Chart -->
            <div class="col-xl-4">
                <div class="card custom-card border-primary">
                    <div class="card-header bg-primary-transparent">
                        <div class="card-title text-primary">
                            <i class="bx bx-user-check me-2"></i>Tham Gia Của Tôi ({{ date('Y') }})
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="myParticipationChart"></div>
                    </div>
                </div>
            </div>

            <!-- Event Growth Chart -->
            <div class="col-xl-4">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="bx bx-trending-up me-2"></i>Tăng Trưởng Sự Kiện (12 tháng)
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="eventGrowthChart"></div>
                    </div>
                </div>
            </div>

            <!-- Financial Chart -->
            <div class="col-xl-4">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="bx bx-bar-chart me-2"></i>Thu Chi Theo Tháng ({{ date('Y') }})
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="revenueExpenseChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Distribution Charts -->
        <div class="row">
            <!-- Event Status -->
            <div class="col-xl-6">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="bx bx-pie-chart-alt me-2"></i>Phân Bố Trạng Thái Sự Kiện
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="eventStatusChart"></div>
                    </div>
                </div>
            </div>

            <!-- Member Roles Distribution -->
            <div class="col-xl-6">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="bx bx-doughnut-chart me-2"></i>Phân Bố Vai Trò Thành Viên
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="memberRolesChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="row">
            <!-- My Participated Events -->
            <div class="col-xl-4">
                <div class="card custom-card border-primary">
                    <div class="card-header bg-primary-transparent">
                        <div class="card-title text-primary">
                            <i class="bx bx-user-check me-2"></i>Sự Kiện Tôi Đã Tham Gia
                        </div>
                    </div>
                    <div class="card-body">
                        @if($recentParticipatedEvents->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($recentParticipatedEvents as $event)
                                    <div class="list-group-item border-0 p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <span class="avatar avatar-sm bg-primary">
                                                    <i class="bx bx-check fs-12"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <h6 class="mb-1 fw-semibold">{{ $event->name }}</h6>
                                                <p class="text-muted mb-1 fs-12">
                                                    <i class="bx bx-calendar me-1"></i>{{ \Carbon\Carbon::parse($event->event_date)->format('d/m/Y') }}
                                                </p>
                                                <p class="text-muted mb-0 fs-11">
                                                    <i class="bx bx-time me-1"></i>Check-in: {{ \Carbon\Carbon::parse($event->checkin_time)->format('H:i d/m/Y') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="bx bx-user-check fs-48"></i>
                                <p class="mt-3">Chưa tham gia sự kiện nào</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Events -->
            <div class="col-xl-4">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="bx bx-calendar me-2"></i>Sự Kiện Gần Đây
                        </div>
                    </div>
                    <div class="card-body">
                        @if($recentEvents->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($recentEvents as $event)
                                    <div class="list-group-item border-0 p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @php
                                                    $statusClass = match($event->status) {
                                                        'active' => 'bg-success',
                                                        'pending' => 'bg-warning',
                                                        'completed' => 'bg-info',
                                                        'cancelled' => 'bg-danger',
                                                        default => 'bg-secondary'
                                                    };
                                                @endphp
                                                <span class="avatar avatar-sm {{ $statusClass }}">
                                                    <i class="bx bx-calendar-event fs-12"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <h6 class="mb-1 fw-semibold">{{ $event->name }}</h6>
                                                <p class="text-muted mb-1 fs-12">
                                                    <i class="bx bx-calendar me-1"></i>{{ $event->event_date->format('d/m/Y') }}
                                                </p>
                                                <span class="badge bg-{{ $statusClass == 'bg-success' ? 'success' : ($statusClass == 'bg-warning' ? 'warning' : ($statusClass == 'bg-info' ? 'info' : 'danger')) }}-transparent">
                                                    {{ $event->status }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="bx bx-calendar fs-48"></i>
                                <p class="mt-3">Chưa có sự kiện gần đây</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Financial Activities -->
            <div class="col-xl-4">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="bx bx-money me-2"></i>Hoạt Động Tài Chính Gần Đây
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @if($recentInvoices->count() > 0 || $recentExpenses->count() > 0)
                                @foreach($recentInvoices->take(3) as $invoice)
                                    <div class="list-group-item border-0 p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <span class="avatar avatar-sm bg-success">
                                                    <i class="bx bx-plus fs-12"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <h6 class="mb-1 fw-semibold">Thu: {{ $invoice->name }}</h6>
                                                <p class="text-muted mb-1 fs-12">
                                                    {{ $invoice->user->name ?? 'N/A' }} - {{ number_format($invoice->total_amount) }}đ
                                                </p>
                                                <span class="badge bg-{{ $invoice->status == 'paid' ? 'success' : 'warning' }}-transparent">
                                                    {{ $invoice->status }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                @foreach($recentExpenses->take(3) as $expense)
                                    <div class="list-group-item border-0 p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <span class="avatar avatar-sm bg-danger">
                                                    <i class="bx bx-minus fs-12"></i>
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <h6 class="mb-1 fw-semibold">Chi: {{ $expense->name }}</h6>
                                                <p class="text-muted mb-1 fs-12">
                                                    {{ $expense->requestedBy->name ?? 'N/A' }} - {{ number_format($expense->amount) }}đ
                                                </p>
                                                <span class="badge bg-{{ $expense->status == 'approved' ? 'success' : ($expense->status == 'pending' ? 'warning' : 'danger') }}-transparent">
                                                    {{ $expense->status }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="bx bx-money fs-48"></i>
                                    <p class="mt-3">Chưa có hoạt động tài chính gần đây</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // My Participation Chart
        if (document.querySelector('#myParticipationChart')) {
            const myEventData = Array.from({length: 12}, (_, i) => {
                const monthData = {!! json_encode($myEventStats) !!}.find(item => item.month == (i + 1));
                return monthData ? parseInt(monthData.count) : 0;
            });

            const myParticipationOptions = {
                series: [{
                    name: 'Sự kiện đã tham gia',
                    data: myEventData
                }],
                chart: {
                    type: 'line',
                    height: 350,
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#845adf'],
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                markers: {
                    size: 6,
                    colors: ['#845adf'],
                    strokeColors: '#fff',
                    strokeWidth: 2,
                    hover: {
                        sizeOffset: 3
                    }
                },
                xaxis: {
                    categories: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12']
                },
                yaxis: {
                    title: {
                        text: 'Số sự kiện'
                    }
                },
                grid: {
                    borderColor: '#f2f6f7'
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.9,
                        stops: [0, 90, 100]
                    }
                }
            };

            const myParticipationChart = new ApexCharts(document.querySelector('#myParticipationChart'), myParticipationOptions);
            myParticipationChart.render();
        }

        // Event Growth Chart
        if (document.querySelector('#eventGrowthChart')) {
            const eventGrowthData = {!! json_encode($eventGrowth->map(function($item) {
                return [
                    'x' => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT),
                    'y' => $item->count
                ];
            })) !!};

            const eventGrowthOptions = {
                series: [{
                    name: 'Số sự kiện',
                    data: eventGrowthData
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#845adf'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.1,
                    }
                },
                xaxis: {
                    type: 'category',
                    title: {
                        text: 'Tháng'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Số sự kiện'
                    }
                },
                grid: {
                    borderColor: '#f2f6f7'
                }
            };

            const eventGrowthChart = new ApexCharts(document.querySelector('#eventGrowthChart'), eventGrowthOptions);
            eventGrowthChart.render();
        }

        // Revenue vs Expense Chart
        if (document.querySelector('#revenueExpenseChart')) {
            const revenueData = Array.from({length: 12}, (_, i) => {
                const monthData = {!! json_encode($monthlyRevenue) !!}.find(item => item.month == (i + 1));
                return monthData ? parseFloat(monthData.total) : 0;
            });

            const expenseData = Array.from({length: 12}, (_, i) => {
                const monthData = {!! json_encode($monthlyExpense) !!}.find(item => item.month == (i + 1));
                return monthData ? parseFloat(monthData.total) : 0;
            });

            const revenueExpenseOptions = {
                series: [{
                    name: 'Thu nhập',
                    data: revenueData
                }, {
                    name: 'Chi phí',
                    data: expenseData
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#23b7e5', '#f5b849'],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: ['Th1', 'Th2', 'Th3', 'Th4', 'Th5', 'Th6', 'Th7', 'Th8', 'Th9', 'Th10', 'Th11', 'Th12']
                },
                yaxis: {
                    title: {
                        text: 'Số tiền (VNĐ)'
                    },
                    labels: {
                        formatter: function(val) {
                            return val.toLocaleString('vi-VN');
                        }
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val.toLocaleString('vi-VN') + ' VNĐ';
                        }
                    }
                },
                grid: {
                    borderColor: '#f2f6f7'
                }
            };

            const revenueExpenseChart = new ApexCharts(document.querySelector('#revenueExpenseChart'), revenueExpenseOptions);
            revenueExpenseChart.render();
        }

        // Event Status Chart
        if (document.querySelector('#eventStatusChart')) {
            const eventStatusData = {!! json_encode($eventsByStatus) !!};
            const statusLabels = eventStatusData.map(item => item.status);
            const statusCounts = eventStatusData.map(item => item.count);

            const eventStatusOptions = {
                series: statusCounts,
                chart: {
                    type: 'pie',
                    height: 350
                },
                labels: statusLabels,
                colors: ['#23b7e5', '#f5b849', '#49b6f5', '#e85347'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 300
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                legend: {
                    position: 'bottom'
                }
            };

            const eventStatusChart = new ApexCharts(document.querySelector('#eventStatusChart'), eventStatusOptions);
            eventStatusChart.render();
        }

        // Member Roles Chart
        if (document.querySelector('#memberRolesChart')) {
            const memberRoleData = {!! json_encode($membersByRole) !!};
            const roleLabels = memberRoleData.map(item => item.name);
            const roleCounts = memberRoleData.map(item => item.count);

            const memberRolesOptions = {
                series: roleCounts,
                chart: {
                    type: 'donut',
                    height: 350
                },
                labels: roleLabels,
                colors: ['#845adf', '#23b7e5', '#f5b849', '#49b6f5'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 300
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                legend: {
                    position: 'bottom'
                }
            };

            const memberRolesChart = new ApexCharts(document.querySelector('#memberRolesChart'), memberRolesOptions);
            memberRolesChart.render();
        }
    });
</script>
@endsection
