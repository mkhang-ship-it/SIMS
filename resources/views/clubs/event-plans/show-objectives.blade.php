{{-- Event Plan Objectives Tab
Contains: Mục tiêu và các chỉ số đo lường with CRUD functionality in table format
Created: Split from main show view for better organization
Updated: Changed from card layout to table layout for better data presentation --}}

<!-- 5. Mục tiêu và các chỉ số đo lường -->
<div class="mb-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h6 class="fw-bold text-purple mb-0">
            <i class="ti ti-target me-1"></i>Mục Tiêu Và Chỉ Số
        </h6>
        @if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'create', 'branch'))
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addObjectiveModal">
                <i class="ti ti-plus me-1"></i>Thêm Mục Tiêu
            </button>
        @endif
    </div>

    @if ($eventPlan->objectives->count() > 0)
        <div class="table-responsive1">
            <table id="objectives-table" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="text-align: center !important;">STT</th>
                        <th>
                            <i class="ti ti-target me-1"></i>Mục tiêu
                        </th>
                        <th>
                            <i class="ti ti-chart-line me-1"></i>Kết quả cần đạt
                        </th>
                        <th>
                            <i class="ti ti-user-check me-1"></i>Chịu trách nhiệm
                        </th>
                        <th style="text-align: center !important;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($eventPlan->objectives as $index => $objective)
                        <tr>
                            <td class="text-center fw-bold">{{ $index + 1 }}</td>
                            <td>
                                <div class="lh-base">{{ $objective->objective }}</div>
                            </td>
                            <td>
                                <div class="lh-base">{{ $objective->expected_result }}</div>
                            </td>
                            <td>
                                <div class="lh-base">{{ $objective->responsible_party }}</div>
                            </td>
                            <td class="text-center">
                                <div class="gap-2 fs-15">
                                    @if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'edit', 'branch'))
                                        <button type="button" class="btn btn-icon btn-sm btn-info-light"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editObjectiveModal{{ $objective->id }}"
                                            title="Chỉnh sửa mục tiêu">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                    @endif
                                    @if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'delete', 'branch'))
                                        <button type="button" class="btn btn-icon btn-sm btn-danger-light"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteObjectiveModal{{ $objective->id }}"
                                            title="Xóa mục tiêu">
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
            <i class="ti ti-target" style="font-size: 3rem; color: #6c757d; opacity: 0.5;"></i>
            <h6 class="text-muted mt-3 mb-1">Chưa có mục tiêu nào</h6>
            <p class="text-muted small mb-0">Hãy thêm mục tiêu đầu tiên cho kế hoạch sự kiện này</p>
        </div>
    @endif
</div>
