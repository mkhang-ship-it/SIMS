{{-- Budget Items Tab
Contains: Dự toán chi phí (Budget Items) với chức năng CRUD, trình bày dạng bảng
Updated: Đổi sang trình bày dạng bảng giống mục tiêu, đơn giản hóa theo format objectives, thêm cột tổng tiền và tổng tất cả --}}

<div class="mb-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h6 class="fw-bold text-purple mb-0">
            <i class="ti ti-coin me-1"></i>Dự Toán Chi Phí
        </h6>
        @if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'create', 'branch'))
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBudgetItemModal">
                <i class="ti ti-plus me-1"></i>Thêm Chi Phí
            </button>
        @endif
    </div>

    @if ($eventPlan->budgetItems && $eventPlan->budgetItems->count() > 0)
        <div class="table-responsive">
            <table id="budget-items-table" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="text-align: center !important;">STT</th>
                        <th>
                            <i class="ti ti-clipboard-list me-1"></i>Nội dung
                        </th>
                        <th>
                            <i class="ti ti-hash me-1"></i>Số lượng & Đơn vị
                        </th>
                        <th>
                            <i class="ti ti-currency-dollar me-1"></i>Đơn giá
                        </th>
                        <th>
                            <i class="ti ti-calculator me-1"></i>Tổng tiền
                        </th>
                        <th>
                            <i class="ti ti-user-check me-1"></i>Phụ trách
                        </th>
                        <th style="text-align: center !important;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalBudget = 0; @endphp
                    @foreach ($eventPlan->budgetItems as $index => $budgetItem)
                        @php
                            $itemTotal = $budgetItem->quantity * $budgetItem->unit_price;
                            $totalBudget += $itemTotal;
                        @endphp
                        <tr>
                            <td class="text-center fw-bold">{{ $index + 1 }}</td>
                            <td>
                                <div class="lh-base">
                                    {{ $budgetItem->content }}
                                    @if ($budgetItem->notes)
                                        <br>
                                        <small class="text-muted">{{ $budgetItem->notes }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="lh-base">{{ number_format($budgetItem->quantity) }} {{ $budgetItem->unit }}
                                </div>
                            </td>
                            <td>
                                <div class="lh-base">{{ number_format($budgetItem->unit_price, 0, ',', '.') }} VNĐ</div>
                            </td>
                            <td>
                                <div class="lh-base">{{ number_format($itemTotal, 0, ',', '.') }} VNĐ</div>
                            </td>
                            <td>
                                <div class="lh-base">{{ $budgetItem->responsible_person }}</div>
                            </td>
                            <td class="text-center">
                                <div class="gap-2 fs-15">
                                    @if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'edit', 'branch'))
                                        <button type="button" class="btn btn-icon btn-sm btn-info-light"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editBudgetItemModal{{ $budgetItem->id }}"
                                            title="Chỉnh sửa chi phí">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                    @endif
                                    @if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'delete', 'branch'))
                                        <button type="button" class="btn btn-icon btn-sm btn-danger-light"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteBudgetItemModal{{ $budgetItem->id }}"
                                            title="Xóa chi phí">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-info">
                        <td colspan="4" class="text-end fw-bold">
                            <strong>Tổng ngân sách:</strong>
                        </td>
                        <td class="fw-bold text-danger">
                            <strong>{{ number_format($totalBudget, 0, ',', '.') }} VNĐ</strong>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @else
        <div class="text-center py-5">
            <i class="ti ti-coin" style="font-size: 3rem; color: #6c757d; opacity: 0.5;"></i>
            <h6 class="text-muted mt-3 mb-1">Chưa có chi phí nào</h6>
            <p class="text-muted small mb-0">Hãy thêm chi phí đầu tiên cho kế hoạch sự kiện này</p>
        </div>
    @endif
</div>
