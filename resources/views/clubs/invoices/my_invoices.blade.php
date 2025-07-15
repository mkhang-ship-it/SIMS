{{-- My Invoice Page - Member view of their own invoices --}}
{{-- Updated: Fixed layout issues - improved responsive design, moved modals outside table, fixed table structure, added proper chart spacing --}}
{{-- Features: Read-only view, statistics, payment history, filter functionality --}}
{{-- Layout: Responsive 3-9 column structure with statistics sidebar and ApexCharts --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Hóa Đơn Của Tôi</h1>
        </div>
        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="row">
            <div class="col-xl-3">
                <div class="card custom-card">
                    <div class="card-header">
                        <h5 class="card-title">Thống Kê Hóa Đơn</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="p-4 border-bottom border-block-end-dashed d-flex align-items-top">
                            <div class="svg-icon-background bg-primary-transparent me-4">
                                <svg xmlns="http://www.w3.org/2000/svg" data-name="Layer 1" viewBox="0 0 24 24"
                                    class="svg-primary">
                                    <path
                                        d="M13,16H7a1,1,0,0,0,0,2h6a1,1,0,0,0,0-2ZM9,10h2a1,1,0,0,0,0-2H9a1,1,0,0,0,0,2Zm12,2H18V3a1,1,0,0,0-.5-.87,1,1,0,0,0-1,0l-3,1.72-3-1.72a1,1,0,0,0-1,0l-3,1.72-3-1.72a1,1,0,0,0-1,0A1,1,0,0,0,2,3V19a3,3,0,0,0,3,3H19a3,3,0,0,0,3-3V13A1,1,0,0,0,21,12ZM5,20a1,1,0,0,1-1-1V4.73L6,5.87a1.08,1.08,0,0,0,1,0l3-1.72,3,1.72a1.08,1.08,0,0,0,1,0l2-1.14V19a3,3,0,0,0,.18,1Zm15-1a1,1,0,0,1-2,0V14h2Zm-7-7H7a1,1,0,0,0,0,2h6a1,1,0,0,0,0-2Z" />
                                </svg>
                            </div>
                            <div class="flex-fill">
                                <h6 class="mb-2 fs-12">Tổng Hóa Đơn
                                    <span class="badge bg-primary fw-semibold float-end">
                                        {{ $statistics['total'] }}
                                    </span>
                                </h6>
                                <div class="pb-0 mt-0">
                                    <div>
                                        <h4 class="fs-18 fw-semibold mb-2">
                                            {{ number_format($statistics['total_amount'], 0, ',', '.') }} VNĐ
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 border-bottom border-block-end-dashed d-flex align-items-top">
                            <div class="svg-icon-background bg-success-transparent me-4">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="svg-success">
                                    <path
                                        d="M11.5,20h-6a1,1,0,0,1-1-1V5a1,1,0,0,1,1-1h5V7a3,3,0,0,0,3,3h3v5a1,1,0,0,0,2,0V9s0,0,0-.06a1.31,1.31,0,0,0-.06-.27l0-.09a1.07,1.07,0,0,0-.19-.28h0l-6-6h0a1.07,1.07,0,0,0-.28-.19.29.29,0,0,0-.1,0A1.1,1.1,0,0,0,11.56,2H5.5a3,3,0,0,0-3,3V19a3,3,0,0,0,3,3h6a1,1,0,0,0,0-2Zm1-14.59L15.09,8H13.5a1,1,0,0,1-1-1ZM7.5,14h6a1,1,0,0,0,0-2h-6a1,1,0,0,0,0,2Zm4,2h-4a1,1,0,0,0,0,2h4a1,1,0,0,0,0-2Zm-4-6h1a1,1,0,0,0,0-2h-1a1,1,0,0,0,0,2Zm13.71,6.29a1,1,0,0,0-1.42,0l-3.29,3.3-1.29-1.3a1,1,0,0,0-1.42,1.42l2,2a1,1,0,0,0,1.42,0l4-4A1,1,0,0,0,21.21,16.29Z" />
                                </svg>
                            </div>
                            <div class="flex-fill">
                                <h6 class="mb-2 fs-12">Đã Thanh Toán
                                    <span class="badge bg-success fw-semibold float-end">
                                        {{ $statistics['paid'] }}
                                    </span>
                                </h6>
                                <div>
                                    <h4 class="fs-18 fw-semibold mb-2">
                                        {{ number_format($statistics['paid_amount'], 0, ',', '.') }} VNĐ
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-top p-4 border-bottom border-block-end-dashed">
                            <div class="svg-icon-background bg-warning-transparent me-4">
                                <svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24"
                                    viewBox="0 0 24 24" class="svg-warning">
                                    <path
                                        d="M19,12h-7V5c0-0.6-0.4-1-1-1c-5,0-9,4-9,9s4,9,9,9s9-4,9-9C20,12.4,19.6,12,19,12z M12,19.9c-3.8,0.6-7.4-2.1-7.9-5.9C3.5,10.2,6.2,6.6,10,6.1V13c0,0.6,0.4,1,1,1h6.9C17.5,17.1,15.1,19.5,12,19.9z M15,2c-0.6,0-1,0.4-1,1v6c0,0.6,0.4,1,1,1h6c0.6,0,1-0.4,1-1C22,5.1,18.9,2,15,2z M16,8V4.1C18,4.5,19.5,6,19.9,8H16z" />
                                </svg>
                            </div>
                            <div class="flex-fill">
                                <h6 class="mb-2 fs-12">Chờ Thanh Toán
                                    <span class="badge bg-warning fw-semibold float-end">
                                        {{ $statistics['pending'] }}
                                    </span>
                                </h6>
                                <div>
                                    <h4 class="fs-18 fw-semibold mb-2">
                                        {{ number_format($statistics['total_amount'] - $statistics['paid_amount'], 0, ',', '.') }}
                                        VNĐ
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-top p-4 border-bottom border-block-end-dashed">
                            <div class="svg-icon-background bg-danger-transparent me-4">
                                <svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24"
                                    viewBox="0 0 24 24" class="svg-danger">
                                    <path
                                        d="M12,2A10,10,0,1,0,22,12,10.01146,10.01146,0,0,0,12,2Zm0,18a8,8,0,1,1,8-8A8.00917,8.00917,0,0,1,12,20ZM12.92676,8.5a1,1,0,0,0-1.85352,0L9.92676,13.5a1,1,0,0,0,1.85352,0ZM12,15.99963a.99943.99943,0,0,0-1,1v.00037a1,1,0,0,0,2,0v-.00037A1.00018,1.00018,0,0,0,12,15.99963Z" />
                                </svg>
                            </div>
                            <div class="flex-fill">
                                <h6 class="mb-2 fs-12">Quá Hạn Thanh Toán
                                    <span class="badge bg-danger fw-semibold float-end">
                                        {{ $statistics['overdue'] }}
                                    </span>
                                </h6>
                                <div>
                                    <h4 class="fs-18 fw-semibold mb-2">
                                        @php
                                            $overdueAmount = $invoices
                                                ->where('status', 'overdue')
                                                ->sum('remaining_amount');
                                        @endphp
                                        {{ number_format($overdueAmount, 0, ',', '.') }} VNĐ
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="p-4">
                            <div id="my-invoice-stats"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-9">
                <div class="card custom-card">
                    <!-- Header with filter form -->
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                        <div class="card-title fw-semibold fs-18 mb-2 mb-md-0">
                            Danh Sách Hóa Đơn
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <form method="GET" action="{{ route('backend.club.invoices.my-invoices') }}"
                                class="d-flex flex-wrap gap-2 align-items-end">
                                <div class="d-flex flex-column">
                                    <label class="form-label mb-1">Học Kỳ</label>
                                    <select name="semester_id" class="form-select">
                                        <option value="">Tất cả học kỳ</option>
                                        @foreach ($semesters as $semester)
                                            <option value="{{ $semester->id }}"
                                                {{ $selectedSemester == $semester->id ? 'selected' : '' }}>
                                                {{ $semester->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="d-flex flex-column">
                                    <label class="form-label mb-1">Tháng</label>
                                    <select name="month" class="form-select">
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}"
                                                {{ $selectedMonth == $i ? 'selected' : '' }}>
                                                Tháng {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="d-flex flex-column">
                                    <label class="form-label mb-1">Năm</label>
                                    <select name="year" class="form-select">
                                        @for ($year = date('Y'); $year >= date('Y') - 2; $year--)
                                            <option value="{{ $year }}"
                                                {{ $selectedYear == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-filter"></i> Lọc
                                    </button>
                                    <a href="{{ route('backend.club.invoices.index') }}" class="btn btn-secondary">
                                        <i class="ti ti-refresh"></i> Làm Mới
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="data-table" class="table text-nowrap table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 60px;">STT</th>
                                        <th style="width: 120px;">Mã Hóa Đơn</th>
                                        <th>Tên Hóa Đơn</th>
                                        <th style="width: 100px;">Học Kỳ</th>
                                        <th class="text-center" style="width: 120px;">Số Tiền</th>
                                        <th class="text-center" style="width: 120px;">Đã Thanh Toán</th>
                                        <th class="text-center" style="width: 140px;">Ngày Tạo<br>Hạn Thanh Toán</th>
                                        <th class="text-center" style="width: 100px;">Trạng Thái</th>
                                        <th class="text-center" style="width: 120px;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoices as $index => $invoice)
                                        <tr class="invoice-list">
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <a href="{{ route('backend.club.invoices.show', $invoice) }}"
                                                    class="fw-semibold text-primary">
                                                    {{ $invoice->invoice_code }}
                                                </a>
                                            </td>
                                            <td>{{ $invoice->name }}</td>
                                            <td>
                                                <span class="badge bg-info-transparent">
                                                    {{ $invoice->semester->name ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-semibold">
                                                    {{ number_format($invoice->total_amount, 0, ',', '.') }} VNĐ
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-semibold text-success">
                                                    {{ number_format($invoice->total_paid, 0, ',', '.') }} VNĐ
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="fs-12">{{ $invoice->created_at->format('d/m/Y') }}</div>
                                                <div class="fs-12 text-muted">
                                                    {{ $invoice->due_date->format('d/m/Y H:i') }}
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'pending' ? 'warning' : 'danger') }}-transparent">
                                                    {{ $invoice->status_label }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex gap-1 justify-content-center">
                                                    <a href="{{ route('backend.club.invoices.export.pdf', $invoice->id) }}"
                                                        class="btn btn-primary-light btn-icon btn-sm"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        data-bs-title="Xuất PDF">
                                                        <i class="ri-file-pdf-line"></i>
                                                    </a>
                                                    <a href="{{ route('backend.club.invoices.show', $invoice) }}"
                                                        class="btn btn-primary-light btn-icon btn-sm"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        data-bs-title="Xem chi tiết">
                                                        <i class="ri-eye-line"></i>
                                                    </a>

                                                    @if ($invoice->transactions->count() > 0)
                                                        <button class="btn btn-info-light btn-icon btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#paymentHistoryModal{{ $invoice->id }}"
                                                            data-bs-placement="top" data-bs-title="Lịch sử thanh toán">
                                                            <i class="ri-history-line"></i>
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
        <!--End::row-1 -->
    </div>

    <!-- Payment History Modals - Moved outside table for better layout -->
    @foreach ($invoices as $invoice)
        @if ($invoice->transactions->count() > 0)
            <div class="modal fade" id="paymentHistoryModal{{ $invoice->id }}" tabindex="-1"
                aria-labelledby="paymentHistoryModalLabel{{ $invoice->id }}" aria-hidden="true"
                data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="paymentHistoryModalLabel{{ $invoice->id }}">
                                Lịch Sử Thanh Toán - {{ $invoice->invoice_code }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Ngày Thanh Toán</th>
                                            <th>Loại</th>
                                            <th>Số Tiền</th>
                                            <th>Phương Thức</th>
                                            <th>Ghi Chú</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($invoice->transactions as $transaction)
                                            <tr>
                                                <td>{{ $transaction->payment_date->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $transaction->type_color }}-transparent">
                                                        {{ $transaction->type_label }}
                                                    </span>
                                                </td>
                                                <td>{{ number_format($transaction->paid_amount, 0, ',', '.') }} VNĐ</td>
                                                <td>{{ $transaction->payment_method_label }}</td>
                                                <td>{{ $transaction->note ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection

@section('scripts')
    <script>
        (function() {
            "use strict"

            // for my invoice stats
            var options = {
                series: [{
                    name: 'Tổng',
                    data: [
                        {{ $statistics['monthly_stats'][1]['total'] }},
                        {{ $statistics['monthly_stats'][2]['total'] }},
                        {{ $statistics['monthly_stats'][3]['total'] }},
                        {{ $statistics['monthly_stats'][4]['total'] }},
                        {{ $statistics['monthly_stats'][5]['total'] }},
                        {{ $statistics['monthly_stats'][6]['total'] }},
                        {{ $statistics['monthly_stats'][7]['total'] }},
                        {{ $statistics['monthly_stats'][8]['total'] }},
                        {{ $statistics['monthly_stats'][9]['total'] }},
                        {{ $statistics['monthly_stats'][10]['total'] }},
                        {{ $statistics['monthly_stats'][11]['total'] }},
                        {{ $statistics['monthly_stats'][12]['total'] }}
                    ]
                }, {
                    name: 'Đã Thanh Toán',
                    data: [
                        {{ $statistics['monthly_stats'][1]['paid'] }},
                        {{ $statistics['monthly_stats'][2]['paid'] }},
                        {{ $statistics['monthly_stats'][3]['paid'] }},
                        {{ $statistics['monthly_stats'][4]['paid'] }},
                        {{ $statistics['monthly_stats'][5]['paid'] }},
                        {{ $statistics['monthly_stats'][6]['paid'] }},
                        {{ $statistics['monthly_stats'][7]['paid'] }},
                        {{ $statistics['monthly_stats'][8]['paid'] }},
                        {{ $statistics['monthly_stats'][9]['paid'] }},
                        {{ $statistics['monthly_stats'][10]['paid'] }},
                        {{ $statistics['monthly_stats'][11]['paid'] }},
                        {{ $statistics['monthly_stats'][12]['paid'] }}
                    ]
                }, {
                    name: 'Chờ Thanh Toán',
                    data: [
                        {{ $statistics['monthly_stats'][1]['pending'] }},
                        {{ $statistics['monthly_stats'][2]['pending'] }},
                        {{ $statistics['monthly_stats'][3]['pending'] }},
                        {{ $statistics['monthly_stats'][4]['pending'] }},
                        {{ $statistics['monthly_stats'][5]['pending'] }},
                        {{ $statistics['monthly_stats'][6]['pending'] }},
                        {{ $statistics['monthly_stats'][7]['pending'] }},
                        {{ $statistics['monthly_stats'][8]['pending'] }},
                        {{ $statistics['monthly_stats'][9]['pending'] }},
                        {{ $statistics['monthly_stats'][10]['pending'] }},
                        {{ $statistics['monthly_stats'][11]['pending'] }},
                        {{ $statistics['monthly_stats'][12]['pending'] }}
                    ]
                }, {
                    name: 'Quá Hạn',
                    data: [
                        {{ $statistics['monthly_stats'][1]['overdue'] }},
                        {{ $statistics['monthly_stats'][2]['overdue'] }},
                        {{ $statistics['monthly_stats'][3]['overdue'] }},
                        {{ $statistics['monthly_stats'][4]['overdue'] }},
                        {{ $statistics['monthly_stats'][5]['overdue'] }},
                        {{ $statistics['monthly_stats'][6]['overdue'] }},
                        {{ $statistics['monthly_stats'][7]['overdue'] }},
                        {{ $statistics['monthly_stats'][8]['overdue'] }},
                        {{ $statistics['monthly_stats'][9]['overdue'] }},
                        {{ $statistics['monthly_stats'][10]['overdue'] }},
                        {{ $statistics['monthly_stats'][11]['overdue'] }},
                        {{ $statistics['monthly_stats'][12]['overdue'] }}
                    ]
                }],
                chart: {
                    type: 'bar',
                    height: 210,
                    stacked: true
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '25%',
                        endingShape: 'rounded',
                    },
                },
                grid: {
                    borderColor: '#f2f5f7',
                },
                dataLabels: {
                    enabled: false
                },
                colors: ["#4b9bfa", "#28d193", "#ffbe14", "#f5574a"],
                stroke: {
                    show: true,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
                    labels: {
                        show: true,
                        style: {
                            colors: "#8c9097",
                            fontSize: '11px',
                            fontWeight: 600,
                            cssClass: 'apexcharts-xaxis-label',
                        },
                    }
                },
                yaxis: {
                    title: {
                        style: {
                            color: "#8c9097",
                        }
                    },
                    labels: {
                        show: true,
                        style: {
                            colors: "#8c9097",
                            fontSize: '11px',
                            fontWeight: 600,
                            cssClass: 'apexcharts-xaxis-label',
                        },
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + " hóa đơn"
                        }
                    }
                }
            };
            var chart = new ApexCharts(document.querySelector("#my-invoice-stats"), options);
            chart.render();
        })();
    </script>
@endsection
