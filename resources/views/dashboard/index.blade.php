{{-- Updated: Fixed chart display issues in admin dashboard
- Fixed JavaScript DOM ready function structure for proper chart initialization
- Added element existence checks before creating ApexCharts instances
- Moved chart scripts to @section('scripts') for proper loading order
- Changed from jQuery to vanilla JavaScript DOMContentLoaded to avoid $ is not defined error
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
                                <i class="bx bx-user fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <h6 class="fw-semibold mb-0">Người Dùng</h6>
                            <div class="d-flex align-items-center">
                                <span class="text-lg fw-semibold">{{ number_format($stats['total_users']) }}</span>
                                <span class="badge bg-success-transparent ms-2">
                                    {{ number_format($stats['active_users']) }} hoạt động
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
                            <span class="avatar avatar-md bg-secondary">
                                <i class="bx bx-group fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <h6 class="fw-semibold mb-0">Câu Lạc Bộ</h6>
                            <div class="d-flex align-items-center">
                                <span class="text-lg fw-semibold">{{ number_format($stats['total_clubs']) }}</span>
                                <span class="badge bg-success-transparent ms-2">
                                    {{ number_format($stats['active_clubs']) }} hoạt động
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
                                <span class="badge bg-warning-transparent ms-2">
                                    {{ number_format($stats['pending_events']) }} chờ duyệt
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
                            <span class="avatar avatar-md bg-info">
                                <i class="bx bx-book fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <h6 class="fw-semibold mb-0">Khóa Học</h6>
                            <div class="d-flex align-items-center">
                                <span class="text-lg fw-semibold">{{ number_format($stats['total_courses']) }}</span>
                                <span class="badge bg-info-transparent ms-2">
                                    {{ number_format($stats['total_semesters']) }} học kỳ
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Statistics Row -->
    <div class="row">
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="avatar avatar-md bg-success">
                                <i class="bx bx-building fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <h6 class="fw-semibold mb-0">Phòng Học</h6>
                            <span class="text-lg fw-semibold">{{ number_format($stats['total_classrooms']) }}</span>
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
                                <i class="bx bx-book fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <h6 class="fw-semibold mb-0">Chuyên Ngành</h6>
                            <span class="text-lg fw-semibold">{{ number_format($stats['total_majors']) }}</span>
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
                                <i class="bx bx-shield fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <h6 class="fw-semibold mb-0">Vai Trò</h6>
                            <span class="text-lg fw-semibold">{{ number_format($stats['total_roles']) }}</span>
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
                                <i class="bx bx-chat fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <h6 class="fw-semibold mb-0">Yêu Cầu Tính Năng</h6>
                            <div class="d-flex align-items-center">
                                <span class="text-lg fw-semibold">{{ number_format($stats['feature_requests']) }}</span>
                                @if($stats['pending_feature_requests'] > 0)
                                <span class="badge bg-warning-transparent ms-2">
                                    {{ number_format($stats['pending_feature_requests']) }} chờ xử lý
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- User Growth Chart -->
        <div class="col-xl-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bx bx-trending-up me-2"></i>Tăng Trưởng Người Dùng (12 tháng qua)
                    </div>
                </div>
                <div class="card-body">
                    <div id="userGrowthChart"></div>
                </div>
            </div>
        </div>

        <!-- Event Status Distribution -->
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
    </div>

    <!-- More Charts Row -->
    <div class="row">
        <!-- Users by Major -->
        <div class="col-xl-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bx bx-bar-chart me-2"></i>Top 10 Chuyên Ngành Theo Số Người Dùng
                    </div>
                </div>
                <div class="card-body">
                    <div id="usersByMajorChart"></div>
                </div>
            </div>
        </div>

        <!-- Monthly Events -->
        <div class="col-xl-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bx bx-area-chart me-2"></i>Sự Kiện Theo Tháng ({{ date('Y') }})
                    </div>
                </div>
                <div class="card-body">
                    <div id="monthlyEventsChart"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities and Top Clubs -->
    <div class="row">
        <!-- Recent Activities -->
        <div class="col-xl-8">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bx bx-time me-2"></i>Hoạt Động Gần Đây (7 ngày qua)
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Recent Users -->
                        <div class="col-md-4">
                            <h6 class="fw-semibold mb-3 text-primary">
                                <i class="bx bx-user me-2"></i>Người Dùng Mới
                            </h6>
                            @if($recentUsers->count() > 0)
                            @foreach($recentUsers as $user)
                            <div class="d-flex align-items-center mb-3 p-2 bg-light rounded">
                                <div class="me-2">
                                    @if($user->avatar)
                                    <img src="{{ Storage::url($user->avatar) }}" alt="avatar"
                                        class="avatar avatar-xs rounded-circle">
                                    @else
                                    <span class="avatar avatar-xs bg-primary rounded-circle">
                                        {{ substr($user->name, 0, 1) }}
                                    </span>
                                    @endif
                                </div>
                                <div class="flex-fill">
                                    <div class="fw-semibold fs-12">{{ $user->name }}</div>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                            </div>
                            @endforeach
                            @else
                            <p class="text-muted">Không có người dùng mới trong 7 ngày qua.</p>
                            @endif
                        </div>

                        <!-- Recent Clubs -->
                        <div class="col-md-4">
                            <h6 class="fw-semibold mb-3 text-secondary">
                                <i class="bx bx-group me-2"></i>Câu Lạc Bộ Mới
                            </h6>
                            @if($recentClubs->count() > 0)
                            @foreach($recentClubs as $club)
                            <div class="d-flex align-items-center mb-3 p-2 bg-light rounded">
                                <div class="me-2">
                                    <span class="avatar avatar-xs bg-secondary rounded-circle">
                                        <i class="bx bx-group"></i>
                                    </span>
                                </div>
                                <div class="flex-fill">
                                    <div class="fw-semibold fs-12">{{ $club->name }}</div>
                                    <small class="text-muted">{{ $club->email }}</small>
                                </div>
                            </div>
                            @endforeach
                            @else
                            <p class="text-muted">Không có câu lạc bộ mới trong 7 ngày qua.</p>
                            @endif
                        </div>

                        <!-- Recent Events -->
                        <div class="col-md-4">
                            <h6 class="fw-semibold mb-3 text-warning">
                                <i class="bx bx-calendar-event me-2"></i>Sự Kiện Mới
                            </h6>
                            @if($recentEvents->count() > 0)
                            @foreach($recentEvents as $event)
                            <div class="d-flex align-items-center mb-3 p-2 bg-light rounded">
                                <div class="me-2">
                                    <span class="avatar avatar-xs bg-warning rounded-circle">
                                        <i class="bx bx-calendar-event"></i>
                                    </span>
                                </div>
                                <div class="flex-fill">
                                    <div class="fw-semibold fs-12">{{ $event->name }}</div>
                                    <small class="text-muted">
                                        CLB: {{ $event->club->name ?? 'N/A' }}
                                    </small>
                                </div>
                            </div>
                            @endforeach
                            @else
                            <p class="text-muted">Không có sự kiện mới trong 7 ngày qua.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Active Clubs -->
        <div class="col-xl-4">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bx bx-trophy me-2"></i>Top CLB Hoạt Động
                    </div>
                </div>
                <div class="card-body">
                    @if($mostActiveClubs->count() > 0)
                    @foreach($mostActiveClubs as $index => $club)
                    <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                        <div class="me-2">
                            <span
                                class="badge bg-{{ $index === 0 ? 'success' : ($index === 1 ? 'warning' : ($index === 2 ? 'info' : 'secondary')) }} rounded-circle">
                                {{ $index + 1 }}
                            </span>
                        </div>
                        <div class="flex-fill">
                            <div class="fw-semibold">{{ $club->name }}</div>
                            <small class="text-muted">{{ $club->events_count }} sự kiện</small>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p class="text-muted">Không có dữ liệu câu lạc bộ.</p>
                    @endif
                </div>
            </div>

            <!-- System Statistics -->
            <div class="card custom-card mt-4">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bx bx-chart me-2"></i>Thống Kê Hệ Thống
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-3 bg-primary-transparent rounded">
                                <span class="text-lg fw-bold text-primary">{{ number_format($systemStats['total_logs'])
                                    }}</span>
                                <p class="text-muted mb-0 fs-12">Tổng Log</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-success-transparent rounded">
                                <span class="text-lg fw-bold text-success">{{ number_format($systemStats['logs_today'])
                                    }}</span>
                                <p class="text-muted mb-0 fs-12">Log Hôm Nay</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="text-center p-3 bg-warning-transparent rounded">
                                <span class="text-lg fw-bold text-warning">{{
                                    number_format($systemStats['logs_this_week']) }}</span>
                                <p class="text-muted mb-0 fs-12">Log Tuần Này</p>
                            </div>
                        </div>
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
    // Bootstrap theme colors for charts
    const themeColors = {
        primary: 'rgba(13, 110, 253, 1)',
        secondary: 'rgba(108, 117, 125, 1)',
        success: 'rgba(25, 135, 84, 1)',
        danger: 'rgba(220, 53, 69, 1)',
        warning: 'rgba(255, 193, 7, 1)',
        info: 'rgba(13, 202, 240, 1)',
        light: 'rgba(248, 249, 250, 1)',
        dark: 'rgba(33, 37, 41, 1)'
    };

    // User Growth Chart
    const userGrowthData = @json($userGrowth);
    if (document.querySelector("#userGrowthChart")) {
        const userGrowthChart = new ApexCharts(document.querySelector("#userGrowthChart"), {
            series: [{
                name: 'Người dùng mới',
                data: userGrowthData.map(item => item.count)
            }],
            chart: {
                height: 350,
                type: 'line',
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3,
                colors: [themeColors.primary]
            },
            colors: [themeColors.primary],
            grid: {
                borderColor: '#e9ecef',
            },
            xaxis: {
                categories: userGrowthData.map(item => `${item.month}/${item.year}`),
                labels: {
                    style: {
                        colors: '#6c757d'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#6c757d'
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " người dùng"
                    }
                }
            }
        });
        userGrowthChart.render();
    }

    // Event Status Chart
    const eventStatusData = @json($eventsByStatus);
    if (document.querySelector("#eventStatusChart")) {
        const eventStatusChart = new ApexCharts(document.querySelector("#eventStatusChart"), {
            series: eventStatusData.map(item => item.count),
            chart: {
                type: 'pie',
                height: 350
            },
            labels: eventStatusData.map(item => {
                const labels = {
                    'active': 'Hoạt động',
                    'pending': 'Chờ duyệt',
                    'inactive': 'Tạm ngưng',
                    'rejected': 'Bị từ chối'
                };
                return labels[item.status] || item.status;
            }),
            colors: [themeColors.success, themeColors.warning, themeColors.secondary, themeColors.danger],
            legend: {
                position: 'bottom'
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " sự kiện"
                    }
                }
            },
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
            }]
        });
        eventStatusChart.render();
    }

    // Users by Major Chart
    const usersByMajorData = @json($usersByMajor);
    if (document.querySelector("#usersByMajorChart")) {
        const usersByMajorChart = new ApexCharts(document.querySelector("#usersByMajorChart"), {
            series: [{
                name: 'Số người dùng',
                data: usersByMajorData.map(item => item.count)
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: true,
                }
            },
            dataLabels: {
                enabled: false
            },
            colors: [themeColors.info],
            grid: {
                borderColor: '#e9ecef',
            },
            xaxis: {
                categories: usersByMajorData.map(item => item.name.length > 20 ? item.name.substring(0, 20) + '...' : item.name),
                labels: {
                    style: {
                        colors: '#6c757d'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#6c757d'
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " sinh viên"
                    }
                }
            }
        });
        usersByMajorChart.render();
    }

    // Monthly Events Chart
    const monthlyEventsData = @json($monthlyEvents);
    if (document.querySelector("#monthlyEventsChart")) {
        const monthNames = ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
                           'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];

        // Create array for all 12 months with 0 default
        const monthlyEventCounts = Array(12).fill(0);
        monthlyEventsData.forEach(item => {
            monthlyEventCounts[item.month - 1] = item.count;
        });

        const monthlyEventsChart = new ApexCharts(document.querySelector("#monthlyEventsChart"), {
            series: [{
                name: 'Số sự kiện',
                data: monthlyEventCounts
            }],
            chart: {
                height: 350,
                type: 'area',
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3,
                colors: [themeColors.warning]
            },
            colors: [themeColors.warning],
            fill: {
                type: 'solid',
                opacity: 0.3
            },
            grid: {
                borderColor: '#e9ecef',
            },
            xaxis: {
                categories: monthNames,
                labels: {
                    style: {
                        colors: '#6c757d'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#6c757d'
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " sự kiện"
                    }
                }
            }
        });
        monthlyEventsChart.render();
    }
});
</script>