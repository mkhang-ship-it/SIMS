@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Chi Phí</h1>

            <div class="d-flex align-items-center gap-2">
                <!-- Nút trên desktop -->
                @if (auth()->user()->hasPermissionForModuleCode('expense_management', 'create', 'branch'))
                    <div class="d-none d-md-block">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                            <i class="ti ti-plus"></i>
                            Thêm Mới
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
                    @if (auth()->user()->hasPermissionForModuleCode('expense_management', 'create', 'branch'))
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                data-bs-target="#addExpenseModal">
                                <i class="ti ti-plus"></i> Tạo Yêu Cầu Chi Phí
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
                        <h5 class="card-title">Thống Kê Chi Phí</h5>
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
                                <h6 class="mb-2 fs-12">Tổng Chi Phí
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
                                <h6 class="mb-2 fs-12">Chờ Phê Duyệt
                                    <span class="badge bg-warning fw-semibold float-end">
                                        {{ $statistics['pending'] }}
                                    </span>
                                </h6>
                                <div>
                                    <h4 class="fs-18 fw-semibold mb-2">
                                        {{ number_format($statistics['pending_amount'], 0, ',', '.') }} VNĐ
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-top p-4 border-bottom border-block-end-dashed">
                            <div class="svg-icon-background bg-info-transparent me-4">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="svg-info">
                                    <path
                                        d="M12,2A10,10,0,1,0,22,12,10.01146,10.01146,0,0,0,12,2Zm0,18a8,8,0,1,1,8-8A8.00917,8.00917,0,0,1,12,20ZM12,6a1,1,0,0,0-1,1v5a1,1,0,0,0,2,0V7A1,1,0,0,0,12,6Zm0,10a1,1,0,1,0,1,1A1,1,0,0,0,12,16Z" />
                                </svg>
                            </div>
                            <div class="flex-fill">
                                <h6 class="mb-2 fs-12">Đã Phê Duyệt
                                    <span class="badge bg-info fw-semibold float-end">
                                        {{ $statistics['approved'] }}
                                    </span>
                                </h6>
                                <div>
                                    <h4 class="fs-18 fw-semibold mb-2">
                                        {{ number_format($statistics['approved_amount'], 0, ',', '.') }} VNĐ
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-top p-4">
                            <div class="svg-icon-background bg-danger-transparent me-4">
                                <svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24"
                                    viewBox="0 0 24 24" class="svg-danger">
                                    <path
                                        d="M12,2A10,10,0,1,0,22,12,10.01146,10.01146,0,0,0,12,2Zm0,18a8,8,0,1,1,8-8A8.00917,8.00917,0,0,1,12,20ZM12.92676,8.5a1,1,0,0,0-1.85352,0L9.92676,13.5a1,1,0,0,0,1.85352,0ZM12,15.99963a.99943.99943,0,0,0-1,1v.00037a1,1,0,0,0,2,0v-.00037A1.00018,1.00018,0,0,0,12,15.99963Z" />
                                </svg>
                            </div>
                            <div class="flex-fill">
                                <h6 class="mb-2 fs-12">Bị Từ Chối
                                    <span class="badge bg-danger fw-semibold float-end">
                                        {{ $statistics['rejected'] }}
                                    </span>
                                </h6>
                                <div>
                                    <h4 class="fs-18 fw-semibold mb-2">
                                        {{ number_format($statistics['rejected_amount'], 0, ',', '.') }} VNĐ
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div id="expense-list-stats"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-9">
                <div class="card custom-card">
                    <!-- Đã chỉnh sửa phần header theo mẫu clubs/index.blade.php: căn chỉnh, chia 2 bên, responsive -->
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                        <div class="card-title fw-semibold fs-18 mb-2 mb-md-0">
                            Danh Sách Chi Phí
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <form method="GET" action="{{ route('backend.club.expenses.index') }}"
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
                                    <a href="{{ route('backend.club.expenses.index') }}" class="btn btn-secondary">
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
                                        <th>Người Yêu Cầu</th>
                                        <th style="text-align: center !important;">Tên Chi Phí <br> Số Tiền</th>
                                        <th style="text-align: center !important;">Học Kỳ</th>
                                        <th style="text-align: center !important;">Ngày Yêu Cầu</th>
                                        <th style="text-align: center !important;">Người Phê Duyệt <br> Từ Chối</th>
                                        <th style="text-align: center !important;">Trạng Thái</th>
                                        <th style="text-align: center !important;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expenses as $index => $expense)
                                        <tr class="expense-list">
                                            <td class="text-center">
                                                @if ($expense->status === 'pending')
                                                    <input type="checkbox" class="form-check-input expense-checkbox"
                                                        value="{{ $expense->id }}">
                                                @else
                                                    <input type="checkbox" class="form-check-input expense-checkbox"
                                                        value="{{ $expense->id }}" disabled>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2 lh-1">
                                                        <span class="avatar avatar-sm avatar-rounded">
                                                            <img src="{{ asset('assets/images/avatar_example.png') }}"
                                                                alt="">
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 fw-semibold">
                                                            {{ $expense->requestedBy->name ?? 'N/A' }}
                                                        </p>
                                                        <p class="mb-0 fs-11 text-muted">
                                                            {{ $expense->requestedBy->email ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $expense->name }}</div>
                                                <div class="text-muted fs-11">
                                                    {{ number_format($expense->amount, 0, ',', '.') }} VNĐ</div>
                                            </td>
                                            <td class="text-center">
                                                {{ $expense->semester->name ?? 'N/A' }}
                                            </td>
                                            <td class="text-center">
                                                <div>{{ $expense->requested_at->format('d/m/Y H:i') }}</div>
                                                @if ($expense->status === 'paid' && $expense->transactions->isNotEmpty())
                                                    @php
                                                        $transaction = $expense->transactions->first();
                                                    @endphp
                                                    <div class="text-muted fs-11">Thanh toán:
                                                        {{ $transaction->payment_date->format('d/m/Y H:i') }}</div>
                                                    <div class="text-muted fs-11"> Phương thức:
                                                        {{ $transaction->payment_method_label }}
                                                    </div>
                                                @elseif ($expense->approved_at)
                                                    @if ($expense->status === 'approved')
                                                        <div class="text-muted fs-11">Phê duyệt:
                                                            {{ $expense->approved_at->format('d/m/Y H:i') }}</div>
                                                    @elseif ($expense->status === 'rejected')
                                                        <div class="text-muted fs-11">Từ chối:
                                                            {{ $expense->approved_at->format('d/m/Y H:i') }}</div>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($expense->status === 'approved' || $expense->status === 'rejected')
                                                    {{ $expense->approvedBy->name ?? 'N/A' }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-{{ $expense->status === 'paid' ? 'primary' : ($expense->status === 'approved' ? 'success' : ($expense->status === 'pending' ? 'warning' : 'danger')) }}-transparent">
                                                    {{ $expense->status_label }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if (
                                                    $expense->status === 'pending' &&
                                                        auth()->user()->hasPermissionForModuleCode('expense_management', 'approve', 'branch'))
                                                    <button class="btn btn-success-light btn-icon btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#approveExpenseModal{{ $expense->id }}"
                                                        data-bs-placement="top" data-bs-title="Phê duyệt">
                                                        <i class="ri-check-line"></i>
                                                    </button>
                                                @endif

                                                @if (
                                                    $expense->status === 'pending' &&
                                                        auth()->user()->hasPermissionForModuleCode('expense_management', 'reject', 'branch'))
                                                    <button class="btn btn-danger-light btn-icon btn-sm ms-1"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#rejectExpenseModal{{ $expense->id }}"
                                                        data-bs-placement="top" data-bs-title="Từ chối">
                                                        <i class="ri-close-line"></i>
                                                    </button>
                                                @endif

                                                @if (
                                                    $expense->status === 'approved' &&
                                                        auth()->user()->hasPermissionForModuleCode('expense_management', 'mark_as_paid', 'branch'))
                                                    <button class="btn btn-primary-light btn-icon btn-sm ms-1"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#markAsPaidModal{{ $expense->id }}"
                                                        data-bs-placement="top" data-bs-title="Đánh dấu đã thanh toán">
                                                        <i class="ri-money-dollar-circle-line"></i>
                                                    </button>
                                                @endif

                                                @if (
                                                    $expense->status === 'pending' &&
                                                        auth()->user()->hasPermissionForModuleCode('expense_management', 'edit', 'branch'))
                                                    <button class="btn btn-info-light btn-icon btn-sm ms-1"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editExpenseModal{{ $expense->id }}"
                                                        data-bs-placement="top" data-bs-title="Sửa">
                                                        <i class="ri-edit-line"></i>
                                                    </button>
                                                @endif

                                                @if (
                                                    $expense->status === 'pending' &&
                                                        auth()->user()->hasPermissionForModuleCode('expense_management', 'delete', 'branch'))
                                                    <button class="btn btn-danger-light btn-icon ms-1 btn-sm expense-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteExpenseModal{{ $expense->id }}"
                                                        data-bs-placement="top" data-bs-title="Xóa">
                                                        <i class="ri-delete-bin-5-line"></i>
                                                    </button>
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
        <!--End::row-1 -->

        @foreach ($expenses as $expense)
            <!-- Edit Expense Modal -->
            @if (
                $expense->status === 'pending' &&
                    auth()->user()->hasPermissionForModuleCode('expense_management', 'edit', 'branch'))
                <div class="modal fade" id="editExpenseModal{{ $expense->id }}" tabindex="-1"
                    aria-labelledby="editExpenseModalLabel{{ $expense->id }}" aria-hidden="true"
                    data-bs-backdrop="static" data-bs-keyboard="false">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editExpenseModalLabel{{ $expense->id }}">
                                    Chỉnh Sửa Yêu Cầu Chi Phí
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form action="{{ route('backend.club.expenses.update', $expense) }}" method="POST"
                                id="editExpenseForm{{ $expense->id }}">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Tên Chi Phí <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="name"
                                                    value="{{ $expense->name }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Số Tiền <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="amount"
                                                    value="{{ $expense->amount }}" min="0" step="0.01"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Học Kỳ <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select" name="semester_id" required>
                                                    @foreach ($semesters as $semester)
                                                        <option value="{{ $semester->id }}"
                                                            {{ $expense->semester_id == $semester->id ? 'selected' : '' }}>
                                                            {{ $semester->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Người Yêu Cầu</label>
                                                <input type="text" class="form-control"
                                                    value="{{ $expense->requestedBy->name ?? 'N/A' }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label">Mô Tả</label>
                                                <textarea class="form-control" name="description" rows="3" maxlength="1000">{{ $expense->description }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                    <button type="submit" class="btn btn-primary">Cập
                                        Nhật</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Delete Expense Modal -->
            @if (
                $expense->status === 'pending' &&
                    auth()->user()->hasPermissionForModuleCode('expense_management', 'delete', 'branch'))
                <div class="modal fade" id="deleteExpenseModal{{ $expense->id }}" tabindex="-1"
                    aria-labelledby="deleteExpenseModalLabel{{ $expense->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteExpenseModalLabel{{ $expense->id }}">
                                    Xác Nhận Xóa
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Bạn có chắc chắn muốn xóa yêu cầu chi phí
                                    <strong>{{ $expense->name }}</strong>?
                                </p>
                                <div class="alert alert-warning">
                                    <i class="ti ti-alert-triangle"></i>
                                    Hành động này không thể hoàn tác!
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <form action="{{ route('backend.club.expenses.destroy', $expense) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Xóa</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Approve Expense Modal -->
            @if (
                $expense->status === 'pending' &&
                    auth()->user()->hasPermissionForModuleCode('expense_management', 'approve', 'branch'))
                <div class="modal fade" id="approveExpenseModal{{ $expense->id }}" tabindex="-1"
                    aria-labelledby="approveExpenseModalLabel{{ $expense->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="approveExpenseModalLabel{{ $expense->id }}">
                                    Xác Nhận Phê Duyệt
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Bạn có chắc chắn muốn phê duyệt yêu cầu chi phí
                                    <strong>{{ $expense->name }}</strong>?
                                </p>
                                <div class="alert alert-info">
                                    <i class="ti ti-info-circle"></i>
                                    Yêu cầu sẽ được chuyển sang trạng thái "Đã phê duyệt"
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <form action="{{ route('backend.club.expenses.approve', $expense) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-success">
                                        Phê Duyệt
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Reject Expense Modal -->
            @if (
                $expense->status === 'pending' &&
                    auth()->user()->hasPermissionForModuleCode('expense_management', 'reject', 'branch'))
                <div class="modal fade" id="rejectExpenseModal{{ $expense->id }}" tabindex="-1"
                    aria-labelledby="rejectExpenseModalLabel{{ $expense->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="rejectExpenseModalLabel{{ $expense->id }}">
                                    Xác Nhận Từ Chối
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Bạn có chắc chắn muốn từ chối yêu cầu chi phí
                                    <strong>{{ $expense->name }}</strong>?
                                </p>
                                <div class="alert alert-warning">
                                    <i class="ti ti-alert-triangle"></i>
                                    Yêu cầu sẽ được chuyển sang trạng thái "Bị từ chối"
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <form action="{{ route('backend.club.expenses.reject', $expense) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-danger">
                                        Từ Chối
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Mark as Paid Modal -->
            @if (
                $expense->status === 'approved' &&
                    auth()->user()->hasPermissionForModuleCode('expense_management', 'mark_as_paid', 'branch'))
                <div class="modal fade" id="markAsPaidModal{{ $expense->id }}" tabindex="-1"
                    aria-labelledby="markAsPaidModalLabel{{ $expense->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="markAsPaidModalLabel{{ $expense->id }}">
                                    Xác Nhận Thanh Toán
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Bạn có chắc chắn muốn đánh dấu yêu cầu chi phí
                                    <strong>{{ $expense->name }}</strong> là đã thanh toán?
                                </p>
                                <div class="alert alert-success">
                                    <i class="ti ti-check-circle"></i>
                                    Yêu cầu sẽ được chuyển sang trạng thái "Đã thanh toán"
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <form action="{{ route('backend.club.expenses.mark-as-paid', $expense) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-primary">
                                        Đánh Dấu Đã Thanh Toán
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        <!-- Create Expense Modal -->
        @if (auth()->user()->hasPermissionForModuleCode('expense_management', 'create', 'branch'))
            <div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel"
                aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addExpenseModalLabel">Tạo Yêu Cầu Chi Phí Mới</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form action="{{ route('backend.club.expenses.store') }}" method="POST" id="addExpenseForm">
                            @csrf
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tên Chi Phí <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Số Tiền <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="amount" min="0"
                                                step="0.01" required>
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
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label">Mô Tả</label>
                                            <textarea class="form-control" name="description" rows="3" maxlength="1000"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-primary">Tạo Yêu Cầu</button>
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
                        <p>Bạn có chắc chắn muốn xóa <span id="selectedCount">0</span> yêu cầu chi phí đã chọn?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <form action="{{ route('backend.club.expenses.bulk-destroy') }}" method="POST"
                            id="bulkDeleteForm">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="expense_ids" id="bulkDeleteIds">
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

            // Select all functionality
            const selectAllCheckbox = document.getElementById('selectAll');
            const expenseCheckboxes = document.querySelectorAll('.expense-checkbox');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const bulkDeleteBtnMobile = document.getElementById('bulkDeleteBtnMobile');

            // Select all expenses
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    expenseCheckboxes.forEach(checkbox => {
                        if (!checkbox.disabled) {
                            checkbox.checked = this.checked;
                        }
                    });
                    updateBulkDeleteButton();
                });
            }

            // Individual expense selection
            expenseCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateSelectAllState();
                    updateBulkDeleteButton();
                });
            });

            // Bulk delete functionality
            if (bulkDeleteBtn) {
                bulkDeleteBtn.addEventListener('click', function() {
                    const selectedIds = [];
                    expenseCheckboxes.forEach(checkbox => {
                        if (checkbox.checked && !checkbox.disabled) {
                            selectedIds.push(checkbox.value);
                        }
                    });

                    if (selectedIds.length > 0) {
                        document.getElementById('selectedCount').textContent = selectedIds.length;
                        // Create hidden inputs for each selected ID
                        const bulkDeleteForm = document.getElementById('bulkDeleteForm');
                        const existingInputs = bulkDeleteForm.querySelectorAll('input[name="expense_ids[]"]');
                        existingInputs.forEach(input => input.remove());

                        selectedIds.forEach(id => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'expense_ids[]';
                            input.value = id;
                            bulkDeleteForm.appendChild(input);
                        });

                        new bootstrap.Modal(document.getElementById('bulkDeleteModal')).show();
                    }
                });
            }

            // Mobile bulk delete functionality
            if (bulkDeleteBtnMobile) {
                bulkDeleteBtnMobile.addEventListener('click', function(e) {
                    e.preventDefault();
                    const selectedIds = [];
                    expenseCheckboxes.forEach(checkbox => {
                        if (checkbox.checked && !checkbox.disabled) {
                            selectedIds.push(checkbox.value);
                        }
                    });

                    if (selectedIds.length > 0) {
                        document.getElementById('selectedCount').textContent = selectedIds.length;
                        // Create hidden inputs for each selected ID
                        const bulkDeleteForm = document.getElementById('bulkDeleteForm');
                        const existingInputs = bulkDeleteForm.querySelectorAll('input[name="expense_ids[]"]');
                        existingInputs.forEach(input => input.remove());

                        selectedIds.forEach(id => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'expense_ids[]';
                            input.value = id;
                            bulkDeleteForm.appendChild(input);
                        });

                        new bootstrap.Modal(document.getElementById('bulkDeleteModal')).show();
                    }
                });
            }

            // Remove the automatic deletion behavior - let the modal handle it
            // let expensebtn = document.querySelectorAll(".expense-btn");
            // expensebtn.forEach((eleBtn) => {
            //     eleBtn.onclick = () => {
            //         let expense = eleBtn.closest(".expense-list")
            //         expense.remove();
            //     }
            // })

            function updateSelectAllState() {
                if (!selectAllCheckbox) return;

                const enabledCheckboxes = document.querySelectorAll('.expense-checkbox:not(:disabled)');
                const checkedCheckboxes = document.querySelectorAll('.expense-checkbox:checked:not(:disabled)').length;

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
                const checkedCheckboxes = document.querySelectorAll('.expense-checkbox:checked:not(:disabled)').length;

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

            // Removed JavaScript functions for approve/reject/mark as paid - now using form submission
        })();
    </script>
@endsection
