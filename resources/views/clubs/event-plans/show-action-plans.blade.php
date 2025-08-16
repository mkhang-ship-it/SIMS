<div class="mb-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h6 class="fw-bold text-success mb-0">
            <i class="ti ti-checklist me-1"></i>Kế Hoạch Hành Động
        </h6>
        @if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'create', 'branch'))
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addActionPlanModal">
                <i class="ti ti-plus me-1"></i>Thêm Kế Hoạch Hành Động
            </button>
        @endif
    </div>

    @if ($eventPlan->actionPlans && $eventPlan->actionPlans->count() > 0)
        <div class="table-responsive">
            <table id="action-plans-table" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="text-align: center !important;">STT</th>
                        <th>
                            <i class="ti ti-list-check me-1"></i>Hạng mục hoạt động
                        </th>
                        <th>
                            <i class="ti ti-target me-1"></i>Kết quả cần đạt
                        </th>
                        <th>
                            <i class="ti ti-clock me-1"></i>Thời gian thực hiện
                        </th>
                        <th>
                            <i class="ti ti-user-check me-1"></i>Chịu trách nhiệm
                        </th>
                        <th>
                            <i class="ti ti-users me-1"></i>Thành viên phối hợp
                        </th>
                        <th>
                            <i class="ti ti-alert-triangle me-1"></i>Rủi ro phát sinh
                        </th>
                        <th>
                            <i class="ti ti-shield-check me-1"></i>Phòng ngừa rủi ro
                        </th>
                        <th style="text-align: center !important;">Trạng thái</th>
                        <th style="text-align: center !important;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($eventPlan->actionPlans as $index => $actionPlan)
                        <tr>
                            <td class="text-center fw-bold">{{ $index + 1 }}</td>
                            <td>
                                <div class="lh-base">{{ $actionPlan->activity_item }}</div>
                            </td>
                            <td>
                                <div class="lh-base">{{ $actionPlan->expected_result }}</div>
                            </td>
                            <td>
                                <div class="lh-base">
                                    <div><strong>Bắt đầu:</strong> {{ $actionPlan->start_time->format('d/m/Y H:i') }}
                                    </div>
                                    <div><strong>Kết thúc:</strong> {{ $actionPlan->end_time->format('d/m/Y H:i') }}
                                    </div>
                                    @if ($actionPlan->duration)
                                        <div><strong>Thời lượng:</strong> {{ $actionPlan->duration }}</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="lh-base">{{ $actionPlan->primaryResponsible->name ?? 'Chưa chỉ định' }}
                                </div>
                                @if ($actionPlan->primaryResponsible && $actionPlan->primaryResponsible->role)
                                    <div class="lh-base"><small
                                            class="text-muted">{{ $actionPlan->primaryResponsible->role->name }}</small>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="lh-base">
                                    @if ($actionPlan->support_members && count($actionPlan->support_members) > 0)
                                        @foreach ($actionPlan->supportMembersUsers() as $member)
                                            <div>{{ $member->name }}</div>
                                        @endforeach
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="lh-base">
                                    @if ($actionPlan->potential_risks)
                                        {{ $actionPlan->potential_risks }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="lh-base">
                                    @if ($actionPlan->risk_prevention_actions)
                                        {{ $actionPlan->risk_prevention_actions }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="lh-base">
                                    <span class="badge bg-{{ $actionPlan->status_color }}">
                                        {{ $actionPlan->status_label }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="gap-2 fs-15">
                                    @if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'edit', 'branch'))
                                        <button type="button" class="btn btn-icon btn-sm btn-info-light"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editActionPlanModal{{ $actionPlan->id }}"
                                            title="Chỉnh sửa kế hoạch hành động">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                    @endif
                                    @if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'delete', 'branch'))
                                        <button type="button" class="btn btn-icon btn-sm btn-danger-light"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteActionPlanModal{{ $actionPlan->id }}"
                                            title="Xóa kế hoạch hành động">
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
            <i class="ti ti-checklist" style="font-size: 3rem; color: #6c757d; opacity: 0.5;"></i>
            <h6 class="text-muted mt-3 mb-1">Chưa có kế hoạch hành động nào</h6>
            <p class="text-muted small mb-0">Hãy thêm kế hoạch hành động đầu tiên cho sự kiện này</p>
        </div>
    @endif
</div>
{{-- End of Kế hoạch hành động --}}
