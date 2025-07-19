{{-- Invoice Index Page - Updated for UserClubRole member selection (multi-club)
     Changed member selection in create modal to use UserClubRole (member_ids[]), display member name/email via $member->user --}}
{{-- Updated: Month/year filter now only shows months/years within the selected semester (dynamic JS filtering) --}}
{{-- Updated: New layout with 3-9 column structure, statistics sidebar, and ApexCharts --}}
{{-- Features: Modal-based CRUD operations, payment recording, bulk operations --}}
{{-- Fixed: Mobile bulk delete button functionality, select all checkbox state management, individual checkbox display, and bulk delete button visibility --}}
{{-- Fixed: Statistics bug for Đã thanh toán and Chờ thanh toán (now uses invoice status, not transaction table, for calculation; pending amount uses statistics['pending_amount']) --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Hóa Đơn</h1>

            <div class="d-flex align-items-center gap-2">
                <!-- Nút trên desktop -->
                @if (auth()->user()->hasPermissionForModuleCode('invoice_management', 'create', 'branch'))
                    <div class="d-none d-md-block">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addInvoiceModal">
                            <i class="ti ti-plus"></i>
                            Tạo Hóa Đơn
                        </button>
                    </div>
                @endif
                <div class="d-none d-md-block ms-2">
                    <button id="bulkDeleteBtn" class="btn btn-danger" style="display: none;">
                        <i class="ti ti-trash"></i>
                        Xóa Đã Chọn
                    </button>
                </div>
            </div>
            <!-- Dropdown trên mobile -->
            <div class="dropdown d-md-none">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Hành động
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @if (auth()->user()->hasPermissionForModuleCode('invoice_management', 'create', 'branch'))
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                data-bs-target="#addInvoiceModal">
                                <i class="ti ti-plus"></i> Tạo Hóa Đơn
                            </a>
                        </li>
                    @endif
                    <li>
                        <a class="dropdown-item" href="#" id="bulkDeleteBtnMobile" style="display: none;">
                            <i class="ti ti-trash"></i> Xóa Đã Chọn
                        </a>
                    </li>
                </ul>
            </div>
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
                                        {{ number_format($statistics['pending_amount'], 0, ',', '.') }}
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
                        <div id="invoice-list-stats"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-9">
                <div class="card custom-card">
                    <!-- Đã chỉnh sửa phần header theo mẫu clubs/index.blade.php: căn chỉnh, chia 2 bên, responsive -->
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                        <div class="card-title fw-semibold fs-18 mb-2 mb-md-0">
                            Danh Sách Hóa Đơn
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <form method="GET" action="{{ route('backend.club.invoices.index') }}"
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
                                        <th style="width: 50px; text-align: center !important;">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th style="text-align: center !important;">STT</th>
                                        <th>Mã Hóa Đơn</th>
                                        <th scope="col">Thành Viên</th>
                                        <th scope="col">Tên Hóa Đơn</th>
                                        <th style="text-align: center !important;">Số Tiền</th>
                                        <th style="text-align: center !important;">Ngày Tạo <br> Hạn Thanh Toán</th>
                                        <th style="text-align: center !important;">Trạng Thái</th>
                                        <th style="text-align: center !important;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoices as $index => $invoice)
                                        <tr class="invoice-list">
                                            <td class="text-center">
                                                @if ($invoice->canBeDeleted())
                                                    <input type="checkbox" class="form-check-input invoice-checkbox"
                                                        value="{{ $invoice->id }}">
                                                @else
                                                    <input type="checkbox" class="form-check-input invoice-checkbox"
                                                        value="{{ $invoice->id }}" disabled>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <a href="{{ route('backend.club.invoices.show', $invoice) }}"
                                                    class="fw-semibold text-primary">
                                                    {{ $invoice->invoice_code }}
                                                </a>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2 lh-1">
                                                        <span class="avatar avatar-sm avatar-rounded">
                                                            <img src="{{ asset('assets/images/avatar_example.png') }}"
                                                                alt="">
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 fw-semibold">{{ $invoice->user->name ?? 'N/A' }}
                                                        </p>
                                                        <p class="mb-0 fs-11 text-muted">
                                                            {{ $invoice->user->email ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $invoice->name }}</td>
                                            <td class="text-center">
                                                {{ number_format($invoice->total_amount, 0, ',', '.') }} VNĐ
                                            </td>
                                            <td class="text-center">
                                                <div>{{ $invoice->created_at->format('d/m/Y H:i') }}</div>
                                                <div>{{ $invoice->due_date->format('d/m/Y H:i') }}</div>
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'pending' ? 'warning' : 'danger') }}-transparent">
                                                    {{ $invoice->status_label }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('backend.club.invoices.export.pdf', $invoice->id) }}"
                                                    class="btn btn-primary-light btn-icon btn-sm" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-title="Xuất PDF">
                                                    <i class="ri-file-pdf-line"></i>
                                                </a>
                                                <a href="{{ route('backend.club.invoices.show', $invoice) }}"
                                                    class="btn btn-primary-light btn-icon btn-sm" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-title="Xem"><i
                                                        class="ri-eye-line"></i></a>

                                                @if (
                                                    ($invoice->status === 'pending' || $invoice->status === 'overdue') &&
                                                        auth()->user()->hasPermissionForModuleCode('invoice_management', 'mark_as_paid', 'branch'))
                                                    <button class="btn btn-success-light btn-icon btn-sm ms-1"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#addPaymentModal{{ $invoice->id }}"
                                                        data-bs-placement="top" data-bs-title="Thanh toán">
                                                        <i class="ri-money-dollar-circle-line"></i>
                                                    </button>
                                                @endif

                                                @if ($invoice->canBeEdited() && auth()->user()->hasPermissionForModuleCode('invoice_management', 'edit', 'branch'))
                                                    <button class="btn btn-info-light btn-icon btn-sm ms-1"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editInvoiceModal{{ $invoice->id }}"
                                                        data-bs-placement="top" data-bs-title="Sửa">
                                                        <i class="ri-edit-line"></i>
                                                    </button>
                                                @endif

                                                @if ($invoice->canBeDeleted() && auth()->user()->hasPermissionForModuleCode('invoice_management', 'delete', 'branch'))
                                                    <button class="btn btn-danger-light btn-icon ms-1 btn-sm invoice-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteInvoiceModal{{ $invoice->id }}"
                                                        data-bs-placement="top" data-bs-title="Delete">
                                                        <i class="ri-delete-bin-5-line"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>

                                        <!-- Edit Invoice Modal -->
                                        @if ($invoice->canBeEdited() && auth()->user()->hasPermissionForModuleCode('invoice_management', 'edit', 'branch'))
                                            <div class="modal fade" id="editInvoiceModal{{ $invoice->id }}"
                                                tabindex="-1" aria-labelledby="editInvoiceModalLabel{{ $invoice->id }}"
                                                aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="editInvoiceModalLabel{{ $invoice->id }}">
                                                                Chỉnh Sửa Hóa Đơn - {{ $invoice->invoice_code }}
                                                            </h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form
                                                            action="{{ route('backend.club.invoices.update', $invoice) }}"
                                                            method="POST" id="editInvoiceForm{{ $invoice->id }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Tên Hóa Đơn <span
                                                                                    class="text-danger">*</span></label>
                                                                            <input type="text" class="form-control"
                                                                                name="name"
                                                                                value="{{ $invoice->name }}" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Số Tiền <span
                                                                                    class="text-danger">*</span></label>
                                                                            <input type="number" class="form-control"
                                                                                name="total_amount"
                                                                                value="{{ $invoice->total_amount }}"
                                                                                min="0" step="0.01" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Hạn Thanh Toán <span
                                                                                    class="text-danger">*</span></label>
                                                                            <input type="datetime-local"
                                                                                class="form-control" name="due_date"
                                                                                value="{{ $invoice->due_date->format('Y-m-d\TH:i') }}"
                                                                                required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Thành Viên</label>
                                                                            <input type="text" class="form-control"
                                                                                value="{{ $invoice->user->name ?? 'N/A' }}"
                                                                                readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Ghi Chú</label>
                                                                            <textarea class="form-control" name="note" rows="3" maxlength="1000">{{ $invoice->note }}</textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Hủy</button>
                                                                <button type="submit" class="btn btn-primary">Cập
                                                                    Nhật</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Delete Invoice Modal -->
                                        @if ($invoice->canBeDeleted() && auth()->user()->hasPermissionForModuleCode('invoice_management', 'delete', 'branch'))
                                            <div class="modal fade" id="deleteInvoiceModal{{ $invoice->id }}"
                                                tabindex="-1"
                                                aria-labelledby="deleteInvoiceModalLabel{{ $invoice->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="deleteInvoiceModalLabel{{ $invoice->id }}">
                                                                Xác Nhận Xóa
                                                            </h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Bạn có chắc chắn muốn xóa hóa đơn
                                                                <strong>{{ $invoice->invoice_code }}</strong>?
                                                            </p>
                                                            <div class="alert alert-warning">
                                                                <i class="ti ti-alert-triangle"></i>
                                                                Hành động này không thể hoàn tác!
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Hủy</button>
                                                            <form
                                                                action="{{ route('backend.club.invoices.destroy', $invoice) }}"
                                                                method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Xóa</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Add Payment Modal -->
                                        @if (
                                            ($invoice->status === 'pending' || $invoice->status === 'overdue') &&
                                                auth()->user()->hasPermissionForModuleCode('invoice_management', 'mark_as_paid', 'branch'))
                                            <div class="modal fade" id="addPaymentModal{{ $invoice->id }}"
                                                tabindex="-1" aria-labelledby="addPaymentModalLabel{{ $invoice->id }}"
                                                aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="addPaymentModalLabel{{ $invoice->id }}">Ghi Nhận
                                                                Thanh Toán</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form
                                                            action="{{ route('backend.club.invoices.transactions.mark-as-paid', $invoice) }}"
                                                            method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Số Tiền Thanh Toán <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="number" class="form-control"
                                                                        name="paid_amount"
                                                                        value="{{ $invoice->remaining_amount }}" readonly
                                                                        required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Ngày Thanh Toán <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="datetime-local" class="form-control"
                                                                        name="payment_date"
                                                                        value="{{ date('Y-m-d\TH:i') }}"
                                                                        min="{{ $invoice->created_at->format('Y-m-d\TH:i') }}"
                                                                        max="{{ $invoice->due_date->format('Y-m-d\TH:i') }}"
                                                                        required>
                                                                    <small class="text-muted">Từ
                                                                        {{ $invoice->created_at->format('d/m/Y H:i') }} đến
                                                                        {{ $invoice->due_date->format('d/m/Y H:i') }}</small>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Phương Thức Thanh Toán <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control"
                                                                        value="Tiền Mặt" readonly>
                                                                    <input type="hidden" name="payment_method"
                                                                        value="Cash" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Ghi Chú</label>
                                                                    <textarea class="form-control" name="note" rows="3" maxlength="500"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Hủy</button>
                                                                <button type="submit" class="btn btn-success">Ghi
                                                                    Nhận</button>
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
        <!--End::row-1 -->

        <!-- Create Invoice Modal -->
        @if (auth()->user()->hasPermissionForModuleCode('invoice_management', 'create', 'branch'))
            <div class="modal fade" id="addInvoiceModal" tabindex="-1" aria-labelledby="addInvoiceModalLabel"
                aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addInvoiceModalLabel">Tạo Hóa Đơn Mới</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form action="{{ route('backend.club.invoices.store') }}" method="POST" id="addInvoiceForm">
                            @csrf
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tên Hóa Đơn <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Số Tiền <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="total_amount"
                                                min="0" step="0.01" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Học Kỳ <span class="text-danger">*</span></label>
                                            <select class="form-select" name="semester_id" required>
                                                <option value="">Chọn học kỳ</option>
                                                @foreach ($semesters as $semester)
                                                    <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Hạn Thanh Toán</label>
                                            <input type="datetime-local" class="form-control" name="due_date"
                                                value="{{ \Carbon\Carbon::now()->addDays(7)->format('Y-m-d\TH:i') }}">
                                            <small class="text-muted">Để trống sẽ tự động đặt 7 ngày từ hôm nay</small>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label">Chọn Thành Viên <span
                                                    class="text-danger">*</span></label>
                                            <div class="row">
                                                <div class="col-12 mb-2">
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                            id="selectAllMembers">
                                                            Chọn Tất Cả
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                                            id="deselectAllMembers">
                                                            Bỏ Chọn Tất Cả
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                                @if ($clubMembers->count() > 0)
                                                    @foreach ($clubMembers as $member)
                                                        <div class="form-check">
                                                            <input class="form-check-input member-checkbox"
                                                                type="checkbox" name="member_ids[]"
                                                                value="{{ $member->id }}"
                                                                id="member{{ $member->id }}">
                                                            <label class="form-check-label"
                                                                for="member{{ $member->id }}">
                                                                {{ $member->user->name }} - {{ $member->user->email }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <p class="text-muted mb-0">Không có thành viên nào trong câu lạc bộ.
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label">Ghi Chú</label>
                                            <textarea class="form-control" name="note" rows="3" maxlength="1000"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-primary">Tạo Hóa Đơn</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Bulk Delete Modal -->
        <div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-labelledby="bulkDeleteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bulkDeleteModalLabel">Xác Nhận Xóa Nhiều</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Bạn có chắc chắn muốn xóa <span id="selectedCount">0</span> hóa đơn đã chọn?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <form action="{{ route('backend.club.invoices.bulk-destroy') }}" method="POST"
                            id="bulkDeleteForm">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="invoice_ids" id="bulkDeleteIds">
                            <button type="submit" class="btn btn-danger">Xóa</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



@section('scripts')
   
    <script>
        (function() {
            "use strict"

            // for invoice stats
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
            var chart = new ApexCharts(document.querySelector("#invoice-list-stats"), options);
            chart.render();

            // Select all functionality
            const selectAllCheckbox = document.getElementById('selectAll');
            const invoiceCheckboxes = document.querySelectorAll('.invoice-checkbox');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const bulkDeleteBtnMobile = document.getElementById('bulkDeleteBtnMobile');

            // Member selection functionality
            const selectAllMembersBtn = document.getElementById('selectAllMembers');
            const deselectAllMembersBtn = document.getElementById('deselectAllMembers');
            const memberCheckboxes = document.querySelectorAll('.member-checkbox');

            // Select all invoices
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    invoiceCheckboxes.forEach(checkbox => {
                        if (!checkbox.disabled) {
                            checkbox.checked = this.checked;
                        }
                    });
                    updateBulkDeleteButton();
                });
            }

            // Individual invoice selection
            invoiceCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateSelectAllState();
                    updateBulkDeleteButton();
                });
            });

            // Select all members
            if (selectAllMembersBtn) {
                selectAllMembersBtn.addEventListener('click', function() {
                    memberCheckboxes.forEach(checkbox => {
                        checkbox.checked = true;
                    });
                });
            }

            // Deselect all members
            if (deselectAllMembersBtn) {
                deselectAllMembersBtn.addEventListener('click', function() {
                    memberCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                });
            }

            // Bulk delete functionality
            if (bulkDeleteBtn) {
                bulkDeleteBtn.addEventListener('click', function() {
                    const selectedIds = [];
                    invoiceCheckboxes.forEach(checkbox => {
                        if (checkbox.checked && !checkbox.disabled) {
                            selectedIds.push(checkbox.value);
                        }
                    });

                    if (selectedIds.length > 0) {
                        document.getElementById('selectedCount').textContent = selectedIds.length;
                        document.getElementById('bulkDeleteIds').value = JSON.stringify(selectedIds);
                        new bootstrap.Modal(document.getElementById('bulkDeleteModal')).show();
                    }
                });
            }

            // Mobile bulk delete functionality
            if (bulkDeleteBtnMobile) {
                bulkDeleteBtnMobile.addEventListener('click', function(e) {
                    e.preventDefault();
                    const selectedIds = [];
                    invoiceCheckboxes.forEach(checkbox => {
                        if (checkbox.checked && !checkbox.disabled) {
                            selectedIds.push(checkbox.value);
                        }
                    });

                    if (selectedIds.length > 0) {
                        document.getElementById('selectedCount').textContent = selectedIds.length;
                        document.getElementById('bulkDeleteIds').value = JSON.stringify(selectedIds);
                        new bootstrap.Modal(document.getElementById('bulkDeleteModal')).show();
                    }
                });
            }

            //delete Btn
            let invoicebtn = document.querySelectorAll(".invoice-btn");
            invoicebtn.forEach((eleBtn) => {
                eleBtn.onclick = () => {
                    let invoice = eleBtn.closest(".invoice-list")
                    invoice.remove();
                }
            })

            function updateSelectAllState() {
                if (!selectAllCheckbox) return;

                const enabledCheckboxes = document.querySelectorAll('.invoice-checkbox:not(:disabled)');
                const checkedCheckboxes = document.querySelectorAll('.invoice-checkbox:checked:not(:disabled)').length;

                if (checkedCheckboxes === 0) {
                    selectAllCheckbox.indeterminate = false;
                    selectAllCheckbox.checked = false;
                } else if (checkedCheckboxes === enabledCheckboxes.length) {
                    selectAllCheckbox.indeterminate = false;
                    selectAllCheckbox.checked = true;
                } else {
                    selectAllCheckbox.indeterminate = true;
                }
            }

            function updateBulkDeleteButton() {
                const checkedCheckboxes = document.querySelectorAll('.invoice-checkbox:checked:not(:disabled)').length;

                // Update desktop button
                if (bulkDeleteBtn) {
                    if (checkedCheckboxes === 0) {
                        bulkDeleteBtn.style.display = 'none';
                    } else {
                        bulkDeleteBtn.style.display = 'inline-block';
                    }
                }

                // Update mobile button
                if (bulkDeleteBtnMobile) {
                    if (checkedCheckboxes === 0) {
                        bulkDeleteBtnMobile.style.display = 'none';
                    } else {
                        bulkDeleteBtnMobile.style.display = 'block';
                    }
                }
            }
        })();
    </script>
@endsection
