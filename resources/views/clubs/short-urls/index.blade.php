@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý URL Shortener</h1>

            <!-- Nút trên desktop -->
            @if (auth()->user()->hasPermissionForModuleCode('short_url_management', 'create', 'branch'))
                <div class="d-none d-md-block">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addShortUrlModal">
                        <i class="ti ti-plus"></i>
                        Tạo URL Shortener
                    </button>
                </div>
            @endif
            <!-- Dropdown trên mobile -->
            <div class="dropdown d-md-none">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Hành động
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @if (auth()->user()->hasPermissionForModuleCode('short_url_management', 'create', 'branch'))
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                data-bs-target="#addShortUrlModal">
                                <i class="ti ti-plus"></i> Tạo URL Shortener
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
                                        <th>Tiêu Đề</th>
                                        <th>URL Gốc</th>
                                        <th style="text-align: center !important;">URL Rút Gọn</th>
                                        <th style="text-align: center !important;">Lượt Truy Cập</th>
                                        <th style="text-align: center !important;">Trạng Thái</th>
                                        <th style="text-align: center !important;">Ngày Tạo</th>
                                        <th style="text-align: center !important;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($shortUrls as $index => $shortUrl)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="fw-semibold">{{ $shortUrl->title ?: 'Không có tiêu đề' }}</td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 200px;"
                                                    title="{{ $shortUrl->original_url }}">
                                                    {{ $shortUrl->original_url }}
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center justify-content-center gap-2">
                                                    <code
                                                        class="bg-light px-2 py-1 rounded">{{ $shortUrl->short_code }}</code>
                                                    <button class="btn btn-sm btn-outline-primary"
                                                        onclick="copyToClipboard('{{ route('short-url.redirect', $shortUrl->short_code) }}')"
                                                        title="Sao chép URL">
                                                        <i class="ti ti-copy"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ $shortUrl->total_visits }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if ($shortUrl->isExpired())
                                                    <span class="badge bg-danger">Hết Hạn</span>
                                                @else
                                                    <span class="badge bg-success">Hoạt Động</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ $shortUrl->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex gap-2 justify-content-center">
                                                    <a href="{{ route('short-url.redirect', $shortUrl->short_code) }}"
                                                        target="_blank" class="btn btn-icon btn-sm btn-primary-light"
                                                        title="Mở URL">
                                                        <i class="ti ti-external-link"></i>
                                                    </a>
                                                    <button class="btn btn-icon btn-sm btn-warning-light"
                                                        data-bs-toggle="modal" data-bs-target="#qrModal{{ $shortUrl->id }}"
                                                        title="Mã QR">
                                                        <i class="ti ti-qrcode"></i>
                                                    </button>
                                                    <button class="btn btn-icon btn-sm btn-success-light"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#statsModal{{ $shortUrl->id }}" title="Thống kê">
                                                        <i class="ti ti-chart-bar"></i>
                                                    </button>
                                                    @if (auth()->user()->hasPermissionForModuleCode('short_url_management', 'edit', 'branch'))
                                                        <button class="btn btn-icon btn-sm btn-info-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editShortUrlModal{{ $shortUrl->id }}"
                                                            title="Chỉnh sửa">
                                                            <i class="ti ti-edit"></i>
                                                        </button>
                                                    @endif
                                                    @if (auth()->user()->hasPermissionForModuleCode('short_url_management', 'delete', 'branch'))
                                                        <button class="btn btn-icon btn-sm btn-danger-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteShortUrlModal{{ $shortUrl->id }}"
                                                            title="Xóa">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    @endif
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

        <!-- Add Short URL Modal -->
        <div class="modal fade" id="addShortUrlModal" tabindex="-1" aria-labelledby="addShortUrlModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addShortUrlModalLabel">Tạo URL Shortener Mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('backend.club.short-urls.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="title" class="form-label">Tiêu Đề</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        placeholder="Mô tả ngắn cho URL">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="original_url" class="form-label">URL Gốc <span
                                            class="text-danger">*</span></label>
                                    <input type="url" class="form-control" id="original_url" name="original_url"
                                        placeholder="https://example.com" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="expires_at" class="form-label">Thời Gian Hết Hạn</label>
                                    <input type="datetime-local" class="form-control" id="expires_at" name="expires_at">
                                    <div class="form-text">Để trống nếu không muốn đặt thời gian hết hạn.</div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary">Tạo URL</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- QR Code Modals -->
        @foreach ($shortUrls as $shortUrl)
            <div class="modal fade" id="qrModal{{ $shortUrl->id }}" tabindex="-1" aria-hidden="true"
                data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Mã QR - {{ $shortUrl->title ?: 'URL Shortener' }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">

                            <!-- QR Code -->
                            <div class="mb-4">
                                <div class="qr-code-container p-3 ">
                                    {!! QrCode::size(300)->generate(route('short-url.redirect', $shortUrl->short_code)) !!}
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <a href="{{ route('backend.club.short-urls.qr-download', $shortUrl->id) }}"
                                class="btn btn-primary" download="qr-{{ $shortUrl->short_code }}.svg">
                                <i class="ti ti-download"></i> Tải Mã QR
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Stats Modals -->
        @foreach ($shortUrls as $shortUrl)
            <div class="modal fade" id="statsModal{{ $shortUrl->id }}" tabindex="-1" aria-hidden="true"
                data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Thống Kê URL Shortener</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Thông tin URL -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">Thông Tin URL</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>Tiêu đề:</strong>
                                                        {{ $shortUrl->title ?: 'Không có tiêu đề' }}</p>
                                                    <p><strong>URL gốc:</strong> <a href="{{ $shortUrl->original_url }}"
                                                            target="_blank">{{ $shortUrl->original_url }}</a></p>
                                                    <p><strong>Short code:</strong>
                                                        <code>{{ $shortUrl->short_code }}</code>
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Ngày tạo:</strong>
                                                        {{ $shortUrl->created_at->format('d/m/Y H:i') }}</p>
                                                    <p><strong>Thời gian hết hạn:</strong>
                                                        {{ $shortUrl->expires_at ? $shortUrl->expires_at->format('d/m/Y H:i') : 'Không có' }}
                                                    </p>
                                                    <p><strong>Trạng thái:</strong>
                                                        @if ($shortUrl->isExpired())
                                                            <span class="badge bg-danger">Hết Hạn</span>
                                                        @else
                                                            <span class="badge bg-success">Hoạt Động</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Thống kê tổng quan -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h4>{{ $shortUrl->total_visits }}</h4>
                                            <p class="mb-0">Tổng lượt truy cập</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h4>{{ $shortUrl->unique_visits }}</h4>
                                            <p class="mb-0">Lượt truy cập unique</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h4>{{ $shortUrl->visits()->today()->count() }}</h4>
                                            <p class="mb-0">Hôm nay</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body text-center">
                                            <h4>{{ $shortUrl->visits()->yesterday()->count() }}</h4>
                                            <p class="mb-0">Hôm qua</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Thống kê theo trình duyệt và thiết bị -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            @php
                                                $browserStats = $shortUrl
                                                    ->visits()
                                                    ->get()
                                                    ->groupBy('browser')
                                                    ->map(function ($group) {
                                                        return $group->count();
                                                    })
                                                    ->sortDesc();
                                            @endphp
                                            @if ($browserStats->count() > 0)
                                                <div class="mb-3">
                                                    <canvas id="browserChart{{ $shortUrl->id }}" width="400"
                                                        height="200"></canvas>
                                                </div>
                                                <div class="mt-3">
                                                    @foreach ($browserStats as $browser => $count)
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-2">
                                                            <span>{{ $browser }}</span>
                                                            <span class="badge bg-primary">{{ $count }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-muted">Chưa có dữ liệu</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            @php
                                                $deviceStats = $shortUrl
                                                    ->visits()
                                                    ->get()
                                                    ->groupBy('device_type')
                                                    ->map(function ($group) {
                                                        return $group->count();
                                                    })
                                                    ->sortDesc();
                                            @endphp
                                            @if ($deviceStats->count() > 0)
                                                <div class="mb-3">
                                                    <canvas id="deviceChart{{ $shortUrl->id }}" width="400"
                                                        height="200"></canvas>
                                                </div>
                                                <div class="mt-3">
                                                    @foreach ($deviceStats as $device => $count)
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-2">
                                                            <span>{{ $device }}</span>
                                                            <span class="badge bg-success">{{ $count }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-muted">Chưa có dữ liệu</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Lịch sử truy cập gần đây -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0">Lịch Sử Truy Cập Gần Đây</h6>
                                        </div>
                                        <div class="card-body">
                                            @php
                                                $recentVisits = $shortUrl
                                                    ->visits()
                                                    ->orderBy('visited_at', 'desc')
                                                    ->limit(10)
                                                    ->get();
                                            @endphp
                                            @if ($recentVisits->count() > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Thời gian</th>
                                                                <th>IP</th>
                                                                <th>Trình duyệt</th>
                                                                <th>Thiết bị</th>
                                                                <th>Referrer</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($recentVisits as $visit)
                                                                <tr>
                                                                    <td>{{ $visit->visited_at->format('d/m/Y H:i:s') }}
                                                                    </td>
                                                                    <td><code>{{ $visit->ip }}</code></td>
                                                                    <td>{{ $visit->browser }}</td>
                                                                    <td>{{ $visit->device_type }}</td>
                                                                    <td>
                                                                        @if ($visit->referrer)
                                                                            <a href="{{ $visit->referrer }}"
                                                                                target="_blank"
                                                                                class="text-truncate d-inline-block"
                                                                                style="max-width: 150px;"
                                                                                title="{{ $visit->referrer }}">
                                                                                {{ $visit->referrer }}
                                                                            </a>
                                                                        @else
                                                                            <span class="text-muted">Trực tiếp</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <p class="text-muted">Chưa có lượt truy cập nào</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                </div>
            </div>

            @if (auth()->user()->hasPermissionForModuleCode('short_url_management', 'edit', 'branch'))
                <div class="modal fade" id="editShortUrlModal{{ $shortUrl->id }}" tabindex="-1" aria-hidden="true"
                    data-bs-backdrop="static" data-bs-keyboard="false">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Chỉnh Sửa URL Shortener</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form action="{{ route('backend.club.short-urls.update', $shortUrl->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="title_{{ $shortUrl->id }}" class="form-label">Tiêu Đề</label>
                                            <input type="text" class="form-control" id="title_{{ $shortUrl->id }}"
                                                name="title" value="{{ $shortUrl->title }}"
                                                placeholder="Mô tả ngắn cho URL">
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="original_url_{{ $shortUrl->id }}" class="form-label">URL Gốc
                                                <span class="text-danger">*</span></label>
                                            <input type="url" class="form-control"
                                                id="original_url_{{ $shortUrl->id }}" name="original_url"
                                                value="{{ $shortUrl->original_url }}" placeholder="https://example.com"
                                                required>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="expires_at_{{ $shortUrl->id }}" class="form-label">Ngày Hết
                                                Hạn</label>
                                            <input type="datetime-local" class="form-control"
                                                id="expires_at_{{ $shortUrl->id }}" name="expires_at"
                                                value="{{ $shortUrl->expires_at ? $shortUrl->expires_at->format('Y-m-d\TH:i') : '' }}">
                                            <div class="form-text">Để trống nếu không muốn đặt thời gian hết hạn.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Đóng</button>
                                    <button type="submit" class="btn btn-primary">Cập Nhật</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
            @if (auth()->user()->hasPermissionForModuleCode('short_url_management', 'delete', 'branch'))
                <div class="modal fade" id="deleteShortUrlModal{{ $shortUrl->id }}" tabindex="-1" aria-hidden="true"
                    data-bs-backdrop="static" data-bs-keyboard="false">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Xác Nhận Xóa</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Bạn có chắc chắn muốn xóa URL rút gọn này?</p>
                                <p><strong>Tiêu đề:</strong> {{ $shortUrl->title ?: 'Không có tiêu đề' }}</p>
                                <p><strong>URL gốc:</strong> {{ $shortUrl->original_url }}</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                <form action="{{ route('backend.club.short-urls.destroy', $shortUrl->id) }}"
                                    method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Xóa</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Hiển thị thông báo thành công
                const toast = document.createElement('div');
                toast.className = 'position-fixed top-0 end-0 p-3';
                toast.style.zIndex = '9999';
                toast.innerHTML = `
                    <div class="toast show" role="alert">
                        <div class="toast-header">
                            <strong class="me-auto">Thành công</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                        </div>
                        <div class="toast-body">
                            Đã sao chép URL vào clipboard!
                        </div>
                    </div>
                `;
                document.body.appendChild(toast);

                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 3000);
            });
        }

        // Khởi tạo biểu đồ khi modal được mở
        document.addEventListener('DOMContentLoaded', function() {
            // Lắng nghe sự kiện mở modal
            const statsModals = document.querySelectorAll('[id^="statsModal"]');

            statsModals.forEach(modal => {
                modal.addEventListener('shown.bs.modal', function() {
                    const shortUrlId = this.id.replace('statsModal', '');
                    initializeCharts(shortUrlId);
                });
            });
        });

        function initializeCharts(shortUrlId) {
            // Lấy dữ liệu từ DOM
            const browserChartCanvas = document.getElementById(`browserChart${shortUrlId}`);
            const deviceChartCanvas = document.getElementById(`deviceChart${shortUrlId}`);

            if (browserChartCanvas) {
                const browserData = getChartDataFromDOM(shortUrlId, 'browser');
                createChart(browserChartCanvas, browserData, 'Trình Duyệt', [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                ]);
            }

            if (deviceChartCanvas) {
                const deviceData = getChartDataFromDOM(shortUrlId, 'device');
                createChart(deviceChartCanvas, deviceData, 'Thiết Bị', [
                    '#4BC0C0', '#FF6384', '#36A2EB'
                ]);
            }
        }

        function getChartDataFromDOM(shortUrlId, type) {
            const modal = document.getElementById(`statsModal${shortUrlId}`);
            const labels = [];
            const data = [];

            if (type === 'browser') {
                const browserItems = modal.querySelectorAll('.card-body .d-flex.justify-content-between');
                browserItems.forEach(item => {
                    const label = item.querySelector('span:first-child').textContent.trim();
                    const count = parseInt(item.querySelector('.badge').textContent);
                    labels.push(label);
                    data.push(count);
                });
            } else if (type === 'device') {
                const deviceItems = modal.querySelectorAll('.card-body .d-flex.justify-content-between');
                deviceItems.forEach(item => {
                    const label = item.querySelector('span:first-child').textContent.trim();
                    const count = parseInt(item.querySelector('.badge').textContent);
                    labels.push(label);
                    data.push(count);
                });
            }

            return {
                labels,
                data
            };
        }

        function createChart(canvas, chartData, title, colors) {
            // Xóa chart cũ nếu có
            if (canvas.chart) {
                canvas.chart.destroy();
            }

            const ctx = canvas.getContext('2d');
            canvas.chart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        data: chartData.data,
                        backgroundColor: colors,
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: `Thống Kê ${title}`,
                            font: {
                                size: 16,
                                weight: 'bold'
                            },
                            padding: {
                                top: 10,
                                bottom: 20
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    animation: {
                        animateRotate: true,
                        animateScale: true
                    }
                }
            });
        }
    </script>
@endsection
