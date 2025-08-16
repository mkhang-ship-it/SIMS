@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Chi Tiết Hóa Đơn</h1>

            <!-- Nút trên desktop -->
            <div class="d-none d-md-block">
                <a href="{{ route('backend.club.invoices.index') }}" class="btn btn-secondary me-2">
                    <i class="ri-arrow-left-line me-1"></i>Quay Lại
                </a>
                @if ($invoice->canBeEdited() && auth()->user()->hasPermissionForModuleCode('invoice_management', 'edit', 'branch'))
                    <button class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#editInvoiceModal">
                        <i class="ri-edit-line me-1"></i>Chỉnh Sửa
                    </button>
                @endif
                @if (
                    $invoice->status === 'pending' ||
                        ($invoice->status === 'overdue' &&
                            auth()->user()->hasPermissionForModuleCode('invoice_management', 'mark_as_paid', 'branch')))
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                        <i class="ri-money-dollar-circle-line me-1"></i>Ghi Nhận Thanh Toán
                    </button>
                @endif
            </div>

            <!-- Dropdown trên mobile -->
            <div class="dropdown d-md-none">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Hành động
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('backend.club.invoices.index') }}">
                            <i class="ri-arrow-left-line me-1"></i>Quay Lại
                        </a>
                    </li>
                    @if ($invoice->canBeEdited() && auth()->user()->hasPermissionForModuleCode('invoice_management', 'edit', 'branch'))
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                data-bs-target="#editInvoiceModal">
                                <i class="ri-edit-line me-1"></i>Chỉnh Sửa
                            </a>
                        </li>
                    @endif
                    @if (
                        $invoice->status === 'pending' ||
                            ($invoice->status === 'overdue' &&
                                auth()->user()->hasPermissionForModuleCode('invoice_management', 'mark_as_paid', 'branch')))
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                data-bs-target="#addPaymentModal">
                                <i class="ri-money-dollar-circle-line me-1"></i>Ghi Nhận Thanh Toán
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="row">
            <div class="col-xl-9">
                <div class="card custom-card">
                    <div class="card-header d-md-flex d-block">
                        <div class="h5 mb-0 d-sm-flex d-bllock align-items-center">
                            <div class="">
                                <div class="h6 fw-semibold mb-0">THÔNG TIN HÓA ĐƠN: <span
                                        class="text-primary">{{ $invoice->invoice_code }}</span></div>
                            </div>
                        </div>
                        <div class="ms-auto mt-md-0 mt-2">
                            <button class="btn btn-sm btn-secondary me-1" onclick="javascript:window.print();">In Hóa Đơn<i
                                    class="ri-printer-line ms-1 align-middle d-inline-block"></i></button>
                            <a class="btn btn-sm btn-primary"
                                href="{{ route('backend.club.invoices.export.pdf', $invoice) }}" target="">Xuất PDF<i
                                    class="ri-file-pdf-line ms-1 align-middle d-inline-block"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-xl-12">
                                <div class="row">
                                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6">
                                        <p class="text-muted mb-2">
                                            Thông Tin Câu Lạc Bộ :
                                        </p>
                                        <p class="fw-bold mb-1">
                                            {{ $invoice->club->name ?? 'N/A' }}
                                        </p>
                                        <p class="mb-1 text-muted">
                                            {{ $invoice->club->address ? $invoice->club->address->address : 'Chưa có địa chỉ' }}
                                        </p>
                                        <p class="mb-1 text-muted">
                                            {{ $invoice->club->email ?? 'Chưa có email' }}
                                        </p>
                                        <p class="mb-1 text-muted">
                                            {{ $invoice->club->phone ?? 'Chưa có số điện thoại' }}
                                        </p>
                                        <p class="text-muted">Học kỳ: <span
                                                class="text-primary fw-semibold">{{ $invoice->semester->name ?? 'N/A' }}</span>
                                        </p>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 ms-auto mt-sm-0 mt-3">
                                        <p class="text-muted mb-2">
                                            Thông Tin Thành Viên :
                                        </p>
                                        <p class="fw-bold mb-1">
                                            {{ $invoice->user->name ?? 'N/A' }}
                                        </p>
                                        <p class="text-muted mb-1">
                                            {{ $invoice->user->email ?? 'N/A' }}
                                        </p>
                                        <p class="text-muted mb-1">
                                            {{ $invoice->user->phone ?? 'Chưa có số điện thoại' }}
                                        </p>
                                        <p class="text-muted mb-1">
                                            Mã thành viên: {{ $invoice->user->member_code ?? 'N/A' }}
                                        </p>
                                        <p class="text-muted">
                                            Ngày tham gia:
                                            {{ $invoice->user->created_at ? $invoice->user->created_at->format('d/m/Y') : 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3">
                                <p class="fw-semibold text-muted mb-1">Mã Hóa Đơn:</p>
                                <p class="fs-15 mb-1">{{ $invoice->invoice_code }}</p>
                            </div>
                            <div class="col-xl-3">
                                <p class="fw-semibold text-muted mb-1">Ngày Tạo:</p>
                                <p class="fs-15 mb-1">{{ $invoice->created_at->format('d/m/Y') }} - <span
                                        class="text-muted fs-12">{{ $invoice->created_at->format('H:i') }}</span></p>
                            </div>
                            <div class="col-xl-3">
                                <p class="fw-semibold text-muted mb-1">Hạn Thanh Toán:</p>
                                <p class="fs-15 mb-1">{{ $invoice->due_date->format('d/m/Y') }} - <span
                                        class="text-muted fs-12">{{ $invoice->due_date->format('H:i') }}</span></p>
                            </div>
                            <div class="col-xl-3">
                                <p class="fw-semibold text-muted mb-1">Tổng Tiền:</p>
                                <p class="fs-16 mb-1 fw-semibold">{{ number_format($invoice->total_amount, 0, ',', '.') }}
                                    VNĐ</p>
                            </div>
                            <div class="col-xl-12">
                                <div class="table-responsive">
                                    <table class="table nowrap text-nowrap border mt-4">
                                        <thead>
                                            <tr>
                                                <th class="text-center">TÊN HÓA ĐƠN</th>
                                                <th class="text-center">SỐ LƯỢNG</th>
                                                <th class="text-center">ĐƠN GIÁ</th>
                                                <th class="text-center">TỔNG TIỀN</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center">
                                                    <div class="fw-semibold">
                                                        {{ $invoice->name }}
                                                    </div>
                                                </td>
                                                <td class="product-quantity-container text-center">
                                                    1
                                                </td>
                                                <td class="text-center">
                                                    {{ number_format($invoice->total_amount, 0, ',', '.') }} VNĐ
                                                </td>
                                                <td class="text-center">
                                                    {{ number_format($invoice->total_amount, 0, ',', '.') }} VNĐ
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3"></td>
                                                <td colspan="2">
                                                    <table class="table table-sm text-nowrap mb-0 table-borderless">
                                                        <tbody>
                                                            <tr>
                                                                <th scope="row">
                                                                    <p class="mb-0">Tổng Tiền :</p>
                                                                </th>
                                                                <td>
                                                                    <p class="mb-0 fw-semibold fs-15">
                                                                        {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                                                        VNĐ</p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">
                                                                    <p class="mb-0">Đã Thanh Toán :</p>
                                                                </th>
                                                                <td>
                                                                    <p class="mb-0 fw-semibold fs-15 text-success">
                                                                        {{ number_format($invoice->total_paid, 0, ',', '.') }}
                                                                        VNĐ</p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">
                                                                    <p class="mb-0">Còn Lại :</p>
                                                                </th>
                                                                <td>
                                                                    <p
                                                                        class="mb-0 fw-semibold fs-15 text-{{ $invoice->remaining_amount > 0 ? 'danger' : 'success' }}">
                                                                        {{ number_format($invoice->remaining_amount, 0, ',', '.') }}
                                                                        VNĐ</p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">
                                                                    <p class="mb-0 fs-14">Trạng Thái :</p>
                                                                </th>
                                                                <td>
                                                                    <p class="mb-0 fw-semibold fs-16">
                                                                        <span
                                                                            class="badge bg-{{ $invoice->status_color }}">{{ $invoice->status_label }}</span>
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div>
                                    <label for="invoice-note" class="form-label">Ghi Chú:</label>
                                    <textarea class="form-control form-control-light" id="invoice-note" rows="3" readonly>{{ $invoice->note ?? 'Không có ghi chú.' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-xl-3">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            Thông Tin Thanh Toán
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-xl-12">
                                <p class="fs-14 fw-semibold">
                                    Thông Tin Hóa Đơn
                                </p>
                                <p>
                                    <span class="fw-semibold text-muted fs-12">Mã Hóa Đơn :</span>
                                    {{ $invoice->invoice_code }}
                                </p>
                                <p>
                                    <span class="fw-semibold text-muted fs-12">Thành Viên :</span>
                                    {{ $invoice->user->name ?? 'N/A' }}
                                </p>
                                <p>
                                    <span class="fw-semibold text-muted fs-12">Tổng Tiền :</span> <span
                                        class="text-primary fw-semibold fs-14">{{ number_format($invoice->total_amount, 0, ',', '.') }}
                                        VNĐ</span>
                                </p>
                                <p>
                                    <span class="fw-semibold text-muted fs-12">Đã Thanh Toán :</span> <span
                                        class="text-success fw-semibold fs-14">{{ number_format($invoice->total_paid, 0, ',', '.') }}
                                        VNĐ</span>
                                </p>
                                <p>
                                    <span class="fw-semibold text-muted fs-12">Còn Lại :</span> <span
                                        class="text-{{ $invoice->remaining_amount > 0 ? 'danger' : 'success' }} fw-semibold fs-14">{{ number_format($invoice->remaining_amount, 0, ',', '.') }}
                                        VNĐ</span>
                                </p>
                                <p>
                                    <span class="fw-semibold text-muted fs-12">Hạn Thanh Toán :</span>
                                    {{ $invoice->due_date->format('d/m/Y') }}
                                    @if ($invoice->isOverdue())
                                        - <span
                                            class="text-danger fs-12 fw-semibold">{{ $invoice->due_date->diffForHumans() }}</span>
                                    @endif
                                </p>
                                <p>
                                    <span class="fw-semibold text-muted fs-12">Trạng Thái :
                                        <span
                                            class="badge bg-{{ $invoice->status_color }}">{{ $invoice->status_label }}</span>
                                    </span>
                                </p>
                                @if ($invoice->remaining_amount > 0)
                                    <div class="alert alert-{{ $invoice->isOverdue() ? 'danger' : 'warning' }}"
                                        role="alert">
                                        @if ($invoice->isOverdue())
                                            <i class="ti ti-alert-triangle"></i>
                                            Hóa đơn đã quá hạn thanh toán. Vui lòng liên hệ để được hỗ trợ.
                                        @else
                                            <i class="ti ti-info-circle"></i>
                                            Vui lòng thanh toán hóa đơn trước ngày
                                            {{ $invoice->due_date->format('d/m/Y H:i') }}.
                                        @endif
                                    </div>
                                @else
                                    <div class="alert alert-success" role="alert">
                                        <i class="ti ti-check"></i>
                                        Hóa đơn đã được thanh toán.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transaction History -->
                @if ($invoice->transactions->count() > 0)
                    <div class="card custom-card mt-3">
                        <div class="card-header">
                            <div class="card-title">
                                Lịch Sử Thanh Toán
                            </div>
                        </div>
                        <div class="card-body">
                            @foreach ($invoice->transactions->sortByDesc('payment_date') as $transaction)
                                <div class="d-flex align-items-start mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                                    <div class="avatar avatar-sm bg-success-transparent me-3">
                                        <i class="ri-money-dollar-circle-line text-success"></i>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1 fw-semibold">
                                                    {{ number_format($transaction->paid_amount, 0, ',', '.') }} VNĐ</h6>
                                                <p class="mb-1 text-muted fs-12">
                                                    {{ $transaction->payment_date->format('d/m/Y H:i') }}</p>
                                                <div class="d-flex gap-1">
                                                    <span
                                                        class="badge bg-{{ $transaction->type_color }}-transparent">{{ $transaction->type_label }}</span>
                                                    <span
                                                        class="badge bg-info-transparent">{{ $transaction->payment_method }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        @if ($transaction->note)
                                            <p class="mb-0 text-muted fs-12 mt-2">{{ $transaction->note }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <!--End::row-1 -->

        <!-- Edit Invoice Modal -->
        @if ($invoice->canBeEdited() && auth()->user()->hasPermissionForModuleCode('invoice_management', 'edit', 'branch'))
            <div class="modal fade" id="editInvoiceModal" tabindex="-1" aria-labelledby="editInvoiceModalLabel"
                aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editInvoiceModalLabel">
                                Chỉnh Sửa Hóa Đơn - {{ $invoice->invoice_code }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form action="{{ route('backend.club.invoices.update', $invoice) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tên Hóa Đơn <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name"
                                                value="{{ $invoice->name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Số Tiền <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="total_amount"
                                                value="{{ $invoice->total_amount }}" min="0" step="0.01"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Hạn Thanh Toán <span
                                                    class="text-danger">*</span></label>
                                            <input type="datetime-local" class="form-control" name="due_date"
                                                value="{{ $invoice->due_date->format('Y-m-d\TH:i') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Thành Viên</label>
                                            <input type="text" class="form-control"
                                                value="{{ $invoice->user->name ?? 'N/A' }}" readonly>
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
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-primary">Cập Nhật</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Add Payment Modal -->
        @if (
            $invoice->status === 'pending' ||
                ($invoice->status === 'overdue' &&
                    auth()->user()->hasPermissionForModuleCode('invoice_management', 'mark_as_paid', 'branch')))
            <div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel"
                aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addPaymentModalLabel">Ghi Nhận Thanh Toán</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form action="{{ route('backend.club.invoices.transactions.mark-as-paid', $invoice) }}"
                            method="POST">
                            @csrf
                            <div class="modal-body">

                                <div class="mb-3">
                                    <label class="form-label">Số Tiền Thanh Toán <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="paid_amount"
                                        value="{{ $invoice->remaining_amount }}" readonly required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Ngày Thanh Toán <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" name="payment_date"
                                        value="{{ date('Y-m-d\TH:i') }}"
                                        min="{{ $invoice->created_at->format('Y-m-d\TH:i') }}"
                                        max="{{ $invoice->due_date->format('Y-m-d\TH:i') }}" required>
                                    <small class="text-muted">Từ {{ $invoice->created_at->format('d/m/Y H:i') }} đến
                                        {{ $invoice->due_date->format('d/m/Y H:i') }}</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Phương Thức Thanh Toán <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="Tiền Mặt" readonly>
                                    <input type="hidden" name="payment_method" value="Cash" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Ghi Chú</label>
                                    <textarea class="form-control" name="note" rows="3" maxlength="500"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-success">Ghi Nhận</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection
