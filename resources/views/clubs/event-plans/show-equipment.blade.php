<div class="mb-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h6 class="fw-bold text-info mb-0">
            <i class="ti ti-package me-1"></i>Quản Lý Vật Dụng
        </h6>
        @if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'create', 'branch'))
            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
                <i class="ti ti-plus me-1"></i>Thêm Vật Dụng
            </button>
        @endif
    </div>

    @if ($eventPlan->equipment && $eventPlan->equipment->count() > 0)
        <div class="table-responsive">
            <table id="equipment-table" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="text-align: center !important;">STT</th>
                        <th>
                            <i class="ti ti-package me-1"></i>Tên vật dụng
                        </th>
                        <th style="text-align: center !important;">
                            <i class="ti ti-hash me-1"></i>Số lượng
                        </th>
                        <th style="text-align: center !important;">
                            <i class="ti ti-building me-1"></i>Loại
                        </th>
                        <th style="text-align: center !important;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($eventPlan->equipment as $index => $equipment)
                        <tr>
                            <td class="text-center fw-bold">{{ $index + 1 }}</td>
                            <td>
                                <div class="lh-base">
                                    {{ $equipment->name }}
                                    @if ($equipment->notes)
                                        <br>
                                        <small class="text-muted">{{ $equipment->notes }}</small>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="lh-base">{{ number_format($equipment->quantity) }}</div>
                            </td>
                            <td class="text-center">
                                <div class="lh-base">
                                    <span class="badge bg-{{ $equipment->type_color }}">
                                        {{ $equipment->type_label }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="gap-2 fs-15">
                                    @if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'edit', 'branch'))
                                        <button type="button" class="btn btn-icon btn-sm btn-info-light"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editEquipmentModal{{ $equipment->id }}"
                                            title="Chỉnh sửa vật dụng">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                    @endif
                                    @if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'delete', 'branch'))
                                        <button type="button" class="btn btn-icon btn-sm btn-danger-light"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteEquipmentModal{{ $equipment->id }}"
                                            title="Xóa vật dụng">
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
    @else
        <div class="text-center py-5">
            <i class="ti ti-package" style="font-size: 3rem; color: #6c757d; opacity: 0.5;"></i>
            <h6 class="text-muted mt-3 mb-1">Chưa có vật dụng nào</h6>
            <p class="text-muted small mb-0">Hãy thêm vật dụng đầu tiên cho kế hoạch sự kiện này</p>
        </div>
    @endif
</div>
