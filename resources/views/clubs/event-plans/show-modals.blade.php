{{-- Event Plan Modals - All modals separated to prevent footer layout issues
Contains: Edit Event Plan Modal, Add/Edit/Delete Objective Modals, Add/Edit/Delete Action Plan Modals, Add/Edit/Delete Budget Item Modals, Add/Edit/Delete Equipment Modals
Created: To fix footer layout by moving modals outside main content container
Updated: Added Action Plan modals with CRUD functionality and multi-select support
Updated: Removed view modal for Action Plans, keeping only Add/Edit/Delete modals
Updated: Added Budget Item modals with CRUD functionality and cost calculation support
Updated: Added Equipment modals with CRUD functionality for equipment management
Updated: Fixed budget items edit/delete modals to use dynamic system instead of JavaScript
Updated: Added Timeline modals with CRUD functionality for timeline management with drag/drop ordering support --}}

<!-- Edit Event Plan Modal -->
@if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'edit', 'branch'))
    <div class="modal fade" id="editEventPlanModal" tabindex="-1" aria-labelledby="editEventPlanModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-cro modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEventPlanModalLabel">
                        <i class="ti ti-edit me-1"></i>Cập Nhật Kế Hoạch Sự Kiện
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('backend.club.event-plans.update', $eventPlan->event->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">

                        <!-- 1. Thông tin chung -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">
                                <i class="ti ti-info-circle me-1"></i>1. Thông tin chung
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="prepared_by" class="form-label">Người lập</label>
                                        <select class="form-select @error('prepared_by') is-invalid @enderror"
                                            id="prepared_by" name="prepared_by">
                                            <option value="">-- Chọn người lập --</option>
                                            @foreach ($leaders as $leader)
                                                <option value="{{ $leader->id }}"
                                                    {{ old('prepared_by', $eventPlan->prepared_by) == $leader->id ? 'selected' : '' }}>
                                                    {{ $leader->name }} ({{ $leader->role->name }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('prepared_by')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="approved_by" class="form-label">Được phê duyệt bởi</label>
                                        <input type="text"
                                            class="form-control @error('approved_by') is-invalid @enderror"
                                            id="approved_by" name="approved_by"
                                            value="{{ old('approved_by', $eventPlan->approved_by) }}"
                                            placeholder="Nhập tên người phê duyệt">
                                        @error('approved_by')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="implementation_time" class="form-label">Thời gian triển khai</label>
                                        <input type="date"
                                            class="form-control @error('implementation_time') is-invalid @enderror"
                                            id="implementation_time" name="implementation_time"
                                            value="{{ old('implementation_time', $eventPlan->implementation_time ? $eventPlan->implementation_time->format('Y-m-d') : '') }}">
                                        @error('implementation_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Nội dung tổ chức -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-success mb-3 border-bottom pb-2">
                                <i class="ti ti-calendar-event me-1"></i>2. Nội dung tổ chức
                            </h6>
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="participants" class="form-label">Đối tượng tham gia</label>
                                        <input type="text"
                                            class="form-control @error('participants') is-invalid @enderror"
                                            id="participants" name="participants"
                                            value="{{ old('participants', $eventPlan->participants) }}"
                                            placeholder="Nhập đối tượng tham gia sự kiện">
                                        @error('participants')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Hình thức tổ chức -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-warning mb-3 border-bottom pb-2">
                                <i class="ti ti-file-description me-1"></i>3. Hình thức tổ chức
                            </h6>
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Mô tả hình thức tổ chức</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                            rows="4" placeholder="Mô tả chi tiết về hình thức tổ chức sự kiện">{{ old('description', $eventPlan->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Thành phần tổ chức -->
                        <div class="mb-0">
                            <h6 class="fw-bold text-info mb-3 border-bottom pb-2">
                                <i class="ti ti-users me-1"></i>4. Thành phần tổ chức
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="responsible_organizer" class="form-label">Chịu trách nhiệm tổ
                                            chức</label>
                                        <input type="text"
                                            class="form-control @error('responsible_organizer') is-invalid @enderror"
                                            id="responsible_organizer" name="responsible_organizer"
                                            value="{{ old('responsible_organizer', $eventPlan->responsible_organizer) }}"
                                            placeholder="Nhập người chịu trách nhiệm tổ chức">
                                        @error('responsible_organizer')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="advisor_coordinator" class="form-label">Cố vấn và phối hợp tổ
                                            chức</label>
                                        <input type="text"
                                            class="form-control @error('advisor_coordinator') is-invalid @enderror"
                                            id="advisor_coordinator" name="advisor_coordinator"
                                            value="{{ old('advisor_coordinator', $eventPlan->advisor_coordinator) }}"
                                            placeholder="Nhập cố vấn và người phối hợp">
                                        @error('advisor_coordinator')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ti ti-x me-1"></i>Hủy
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-check me-1"></i>Cập Nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Add Objective Modal -->
@if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'create', 'branch'))
    <div class="modal fade" id="addObjectiveModal" tabindex="-1" aria-labelledby="addObjectiveModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addObjectiveModalLabel">
                        <i class="ti ti-plus me-1"></i>Thêm mục tiêu mới
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('backend.club.event-plans.objectives.store', $eventPlan->event->id) }}"
                    method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="add_objective" class="form-label">
                                        <i class="ti ti-target me-1"></i>Mục tiêu <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('objective') is-invalid @enderror" id="add_objective" name="objective"
                                        rows="3" placeholder="Nhập mục tiêu cần đạt được cho sự kiện này...">{{ old('objective') }}</textarea>
                                    @error('objective')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="add_expected_result" class="form-label">
                                        <i class="ti ti-chart-line me-1"></i>Kết quả cần đạt <span
                                            class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('expected_result') is-invalid @enderror" id="add_expected_result"
                                        name="expected_result" rows="3" placeholder="Nhập kết quả cụ thể và có thể đo lường được...">{{ old('expected_result') }}</textarea>
                                    @error('expected_result')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="add_responsible_party" class="form-label">
                                        <i class="ti ti-user-check me-1"></i>Tổ chức/cá nhân chịu trách nhiệm <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control @error('responsible_party') is-invalid @enderror"
                                        id="add_responsible_party" name="responsible_party"
                                        value="{{ old('responsible_party') }}"
                                        placeholder="VD: Ban chủ nhiệm, Thành viên ABC, Phòng XYZ...">
                                    @error('responsible_party')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ti ti-x me-1"></i>Hủy
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i>Thêm mục tiêu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Edit & Delete Objective Modals -->
@foreach ($eventPlan->objectives as $objective)
    <!-- Edit Objective Modal -->
    @if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'edit', 'branch'))
        <div class="modal fade" id="editObjectiveModal{{ $objective->id }}" tabindex="-1"
            aria-labelledby="editObjectiveModalLabel{{ $objective->id }}" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editObjectiveModalLabel{{ $objective->id }}">
                            <i class="ti ti-edit me-1"></i>Cập nhật mục tiêu
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form
                        action="{{ route('backend.club.event-plans.objectives.update', [$eventPlan->event->id, $objective->id]) }}"
                        method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="edit_objective_{{ $objective->id }}" class="form-label">
                                            <i class="ti ti-target me-1"></i>Mục tiêu <span
                                                class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" id="edit_objective_{{ $objective->id }}" name="objective" rows="3"
                                            placeholder="Nhập mục tiêu cần đạt được cho sự kiện này...">{{ $objective->objective }}</textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="edit_expected_result_{{ $objective->id }}" class="form-label">
                                            <i class="ti ti-chart-line me-1"></i>Kết quả cần đạt <span
                                                class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" id="edit_expected_result_{{ $objective->id }}" name="expected_result" rows="3"
                                            placeholder="Nhập kết quả cụ thể và có thể đo lường được...">{{ $objective->expected_result }}</textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="edit_responsible_party_{{ $objective->id }}" class="form-label">
                                            <i class="ti ti-user-check me-1"></i>Tổ chức/cá nhân chịu trách nhiệm <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control"
                                            id="edit_responsible_party_{{ $objective->id }}" name="responsible_party"
                                            value="{{ $objective->responsible_party }}"
                                            placeholder="VD: Ban chủ nhiệm, Thành viên ABC, Phòng XYZ...">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                <i class="ti ti-x me-1"></i>Hủy
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="ti ti-check me-1"></i>Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Objective Modal -->
    @if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'delete', 'branch'))
        <div class="modal fade" id="deleteObjectiveModal{{ $objective->id }}" tabindex="-1"
            aria-labelledby="deleteObjectiveModalLabel{{ $objective->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger" id="deleteObjectiveModalLabel{{ $objective->id }}">
                            <i class="ti ti-alert-triangle me-1"></i>Xác nhận xóa mục tiêu
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <i class="ti ti-trash text-danger" style="font-size: 2.5rem;"></i>
                        </div>
                        <h6 class="text-center mb-3">Bạn có chắc chắn muốn xóa mục tiêu này?</h6>
                        <div class="alert alert-warning">
                            <div class="d-flex align-items-start">
                                <i class="ti ti-info-circle text-warning me-2 mt-1"></i>
                                <div>
                                    <strong>Mục tiêu:</strong><br>
                                    {{ $objective->objective }}
                                </div>
                            </div>
                        </div>
                        <p class="text-muted text-center small mb-0">
                            <i class="ti ti-alert-circle me-1"></i>
                            Hành động này không thể hoàn tác!
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ti ti-x me-1"></i>Hủy
                        </button>
                        <form
                            action="{{ route('backend.club.event-plans.objectives.destroy', [$eventPlan->event->id, $objective->id]) }}"
                            method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="ti ti-trash me-1"></i>Xóa mục tiêu
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

<!-- Action Plan Modals -->

<!-- Add Action Plan Modal -->
@if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'create', 'branch'))
    <div class="modal fade" id="addActionPlanModal" tabindex="-1" aria-labelledby="addActionPlanModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addActionPlanModalLabel">
                        <i class="ti ti-plus me-1"></i>Thêm Kế Hoạch Hành Động
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('backend.club.event-plans.action-plans.store', $eventPlan->event->id) }}"
                    method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <!-- Activity Item -->
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="add_activity_item" class="form-label">
                                        <i class="ti ti-list-check me-1"></i>Hạng mục hoạt động <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control @error('activity_item') is-invalid @enderror"
                                        id="add_activity_item" name="activity_item"
                                        value="{{ old('activity_item') }}"
                                        placeholder="Nhập hạng mục hoạt động cần thực hiện...">
                                    @error('activity_item')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Expected Result -->
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="add_expected_result_action" class="form-label">
                                        <i class="ti ti-target me-1"></i>Kết quả cần đạt <span
                                            class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('expected_result') is-invalid @enderror" id="add_expected_result_action"
                                        name="expected_result" rows="3" placeholder="Mô tả kết quả cụ thể cần đạt được...">{{ old('expected_result') }}</textarea>
                                    @error('expected_result')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Timeline -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add_start_time" class="form-label">
                                        <i class="ti ti-clock me-1"></i>Thời gian bắt đầu <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="datetime-local"
                                        class="form-control @error('start_time') is-invalid @enderror"
                                        id="add_start_time" name="start_time" value="{{ old('start_time') }}">
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add_end_time" class="form-label">
                                        <i class="ti ti-clock-stop me-1"></i>Thời gian kết thúc <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="datetime-local"
                                        class="form-control @error('end_time') is-invalid @enderror"
                                        id="add_end_time" name="end_time" value="{{ old('end_time') }}">
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Primary Responsible -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add_primary_responsible_id" class="form-label">
                                        <i class="ti ti-user-check me-1"></i>Chịu trách nhiệm chính <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('primary_responsible_id') is-invalid @enderror"
                                        id="add_primary_responsible_id" name="primary_responsible_id">
                                        <option value="">-- Chọn người chịu trách nhiệm chính --</option>
                                        @foreach ($clubMembers as $member)
                                            <option value="{{ $member->id }}"
                                                {{ old('primary_responsible_id') == $member->id ? 'selected' : '' }}>
                                                {{ $member->name }}
                                                @if ($member->role)
                                                    ({{ $member->role->name }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('primary_responsible_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Support Members -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add_support_members" class="form-label">
                                        <i class="ti ti-users me-1"></i>Thành viên phối hợp
                                    </label>
                                    <select class="form-select" id="add_support_members" name="support_members[]"
                                        multiple>
                                        @foreach ($clubMembers as $member)
                                            <option value="{{ $member->id }}"
                                                {{ in_array($member->id, old('support_members', [])) ? 'selected' : '' }}>
                                                {{ $member->name }}
                                                @if ($member->role)
                                                    ({{ $member->role->name }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Giữ Ctrl để chọn nhiều thành viên</div>
                                </div>
                            </div>

                            <!-- Risk Management -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add_potential_risks" class="form-label">
                                        <i class="ti ti-alert-triangle me-1"></i>Rủi ro phát sinh
                                    </label>
                                    <textarea class="form-control" id="add_potential_risks" name="potential_risks" rows="3"
                                        placeholder="Mô tả các rủi ro có thể xảy ra...">{{ old('potential_risks') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add_risk_prevention_actions" class="form-label">
                                        <i class="ti ti-shield-check me-1"></i>Hành động phòng ngừa rủi ro
                                    </label>
                                    <textarea class="form-control" id="add_risk_prevention_actions" name="risk_prevention_actions" rows="3"
                                        placeholder="Mô tả các biện pháp phòng ngừa...">{{ old('risk_prevention_actions') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ti ti-x me-1"></i>Hủy
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i>Thêm kế hoạch hành động
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Edit & Delete Action Plan Modals -->
@foreach ($eventPlan->actionPlans as $actionPlan)
    <!-- Edit Action Plan Modal -->
    @if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'edit', 'branch'))
        <div class="modal fade" id="editActionPlanModal{{ $actionPlan->id }}" tabindex="-1"
            aria-labelledby="editActionPlanModalLabel{{ $actionPlan->id }}" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editActionPlanModalLabel{{ $actionPlan->id }}">
                            <i class="ti ti-edit me-1"></i>Cập nhật kế hoạch hành động
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form
                        action="{{ route('backend.club.event-plans.action-plans.update', [$eventPlan->event->id, $actionPlan->id]) }}"
                        method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row">
                                <!-- Activity Item -->
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="edit_activity_item_{{ $actionPlan->id }}" class="form-label">
                                            <i class="ti ti-list-check me-1"></i>Hạng mục hoạt động <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control"
                                            id="edit_activity_item_{{ $actionPlan->id }}" name="activity_item"
                                            value="{{ $actionPlan->activity_item }}"
                                            placeholder="Nhập hạng mục hoạt động cần thực hiện...">
                                    </div>
                                </div>

                                <!-- Expected Result -->
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="edit_expected_result_action_{{ $actionPlan->id }}"
                                            class="form-label">
                                            <i class="ti ti-target me-1"></i>Kết quả cần đạt <span
                                                class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" id="edit_expected_result_action_{{ $actionPlan->id }}" name="expected_result"
                                            rows="3" placeholder="Mô tả kết quả cụ thể cần đạt được...">{{ $actionPlan->expected_result }}</textarea>
                                    </div>
                                </div>

                                <!-- Timeline -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_start_time_{{ $actionPlan->id }}" class="form-label">
                                            <i class="ti ti-clock me-1"></i>Thời gian bắt đầu <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="datetime-local" class="form-control"
                                            id="edit_start_time_{{ $actionPlan->id }}" name="start_time"
                                            value="{{ $actionPlan->start_time->format('Y-m-d\TH:i') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_end_time_{{ $actionPlan->id }}" class="form-label">
                                            <i class="ti ti-clock-stop me-1"></i>Thời gian kết thúc <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="datetime-local" class="form-control"
                                            id="edit_end_time_{{ $actionPlan->id }}" name="end_time"
                                            value="{{ $actionPlan->end_time->format('Y-m-d\TH:i') }}">
                                    </div>
                                </div>

                                <!-- Primary Responsible -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_primary_responsible_id_{{ $actionPlan->id }}"
                                            class="form-label">
                                            <i class="ti ti-user-check me-1"></i>Chịu trách nhiệm chính <span
                                                class="text-danger">*</span>
                                        </label>
                                        <select class="form-select"
                                            id="edit_primary_responsible_id_{{ $actionPlan->id }}"
                                            name="primary_responsible_id">
                                            <option value="">-- Chọn người chịu trách nhiệm chính --</option>
                                            @foreach ($clubMembers as $member)
                                                <option value="{{ $member->id }}"
                                                    {{ $actionPlan->primary_responsible_id == $member->id ? 'selected' : '' }}>
                                                    {{ $member->name }}
                                                    @if ($member->role)
                                                        ({{ $member->role->name }})
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Support Members -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_support_members_{{ $actionPlan->id }}" class="form-label">
                                            <i class="ti ti-users me-1"></i>Thành viên phối hợp
                                        </label>
                                        <select class="form-select" id="edit_support_members_{{ $actionPlan->id }}"
                                            name="support_members[]" multiple>
                                            @foreach ($clubMembers as $member)
                                                <option value="{{ $member->id }}"
                                                    {{ in_array($member->id, $actionPlan->support_members ?? []) ? 'selected' : '' }}>
                                                    {{ $member->name }}
                                                    @if ($member->role)
                                                        ({{ $member->role->name }})
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">Giữ Ctrl để chọn nhiều thành viên</div>
                                    </div>
                                </div>

                                <!-- Risk Management -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_potential_risks_{{ $actionPlan->id }}" class="form-label">
                                            <i class="ti ti-alert-triangle me-1"></i>Rủi ro phát sinh
                                        </label>
                                        <textarea class="form-control" id="edit_potential_risks_{{ $actionPlan->id }}" name="potential_risks"
                                            rows="3" placeholder="Mô tả các rủi ro có thể xảy ra...">{{ $actionPlan->potential_risks }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_risk_prevention_actions_{{ $actionPlan->id }}"
                                            class="form-label">
                                            <i class="ti ti-shield-check me-1"></i>Hành động phòng ngừa rủi ro
                                        </label>
                                        <textarea class="form-control" id="edit_risk_prevention_actions_{{ $actionPlan->id }}"
                                            name="risk_prevention_actions" rows="3" placeholder="Mô tả các biện pháp phòng ngừa...">{{ $actionPlan->risk_prevention_actions }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                <i class="ti ti-x me-1"></i>Hủy
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="ti ti-check me-1"></i>Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Action Plan Modal -->
    @if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'delete', 'branch'))
        <div class="modal fade" id="deleteActionPlanModal{{ $actionPlan->id }}" tabindex="-1"
            aria-labelledby="deleteActionPlanModalLabel{{ $actionPlan->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger" id="deleteActionPlanModalLabel{{ $actionPlan->id }}">
                            <i class="ti ti-alert-triangle me-1"></i>Xác nhận xóa kế hoạch hành động
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <i class="ti ti-trash text-danger" style="font-size: 2.5rem;"></i>
                        </div>
                        <h6 class="text-center mb-3">Bạn có chắc chắn muốn xóa kế hoạch hành động này?</h6>
                        <div class="alert alert-warning">
                            <div class="d-flex align-items-start">
                                <i class="ti ti-info-circle text-warning me-2 mt-1"></i>
                                <div>
                                    <strong>Hạng mục hoạt động:</strong><br>
                                    {{ $actionPlan->activity_item }}
                                    <br><br>
                                    <strong>Kết quả cần đạt:</strong><br>
                                    {{ $actionPlan->expected_result }}
                                    <br><br>
                                    <strong>Chịu trách nhiệm:</strong><br>
                                    {{ $actionPlan->primaryResponsible->name ?? 'Chưa chỉ định' }}
                                    <br><br>
                                    <strong>Thời gian:</strong><br>
                                    {{ $actionPlan->start_time->format('d/m/Y H:i') }} -
                                    {{ $actionPlan->end_time->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                        <p class="text-muted text-center small mb-0">
                            <i class="ti ti-alert-circle me-1"></i>
                            Hành động này không thể hoàn tác!
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ti ti-x me-1"></i>Hủy
                        </button>
                        <form
                            action="{{ route('backend.club.event-plans.action-plans.destroy', [$eventPlan->event->id, $actionPlan->id]) }}"
                            method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="ti ti-trash me-1"></i>Xóa kế hoạch hành động
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

{{-- Budget Items Modals --}}

<!-- Add Budget Item Modal -->
@if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'create', 'branch'))
    <div class="modal fade" id="addBudgetItemModal" tabindex="-1" aria-labelledby="addBudgetItemModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBudgetItemModalLabel">
                        <i class="ti ti-plus me-1"></i>Thêm mục ngân sách mới
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('backend.club.event-plans.budget-items.store', $eventPlan->event->id) }}"
                    method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <!-- Content -->
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="add_content" class="form-label">
                                        <i class="ti ti-file-text me-1"></i>Nội dung <span
                                            class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('content') is-invalid @enderror" id="add_content" name="content"
                                        rows="3" placeholder="Nhập nội dung mục ngân sách...">{{ old('content') }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Quantity and Unit -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add_quantity" class="form-label">
                                        <i class="ti ti-numbers me-1"></i>Số lượng <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                        class="form-control @error('quantity') is-invalid @enderror"
                                        id="add_quantity" name="quantity" value="{{ old('quantity') }}"
                                        placeholder="Nhập số lượng..." min="1" step="1">
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add_unit" class="form-label">
                                        <i class="ti ti-package me-1"></i>Đơn vị <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('unit') is-invalid @enderror"
                                        id="add_unit" name="unit" value="{{ old('unit') }}"
                                        placeholder="VD: cái, bộ, lít, kg...">
                                    @error('unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Unit Price -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add_unit_price" class="form-label">
                                        <i class="ti ti-currency-dong me-1"></i>Đơn giá (VNĐ) <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                        class="form-control @error('unit_price') is-invalid @enderror"
                                        id="add_unit_price" name="unit_price" value="{{ old('unit_price') }}"
                                        placeholder="Nhập đơn giá..." min="0" step="1000">
                                    @error('unit_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Responsible Person -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add_responsible_person" class="form-label">
                                        <i class="ti ti-user-check me-1"></i>Phụ trách <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control @error('responsible_person') is-invalid @enderror"
                                        id="add_responsible_person" name="responsible_person"
                                        value="{{ old('responsible_person') }}"
                                        placeholder="Nhập tên người phụ trách...">
                                    @error('responsible_person')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="add_notes" class="form-label">
                                        <i class="ti ti-note me-1"></i>Ghi chú
                                    </label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="add_notes" name="notes" rows="2"
                                        placeholder="Nhập ghi chú thêm (nếu có)...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ti ti-x me-1"></i>Hủy
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-check me-1"></i>Thêm mục ngân sách
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Edit Budget Item Modals (Dynamic) -->
@if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'edit', 'branch'))
    @foreach ($eventPlan->budgetItems as $budgetItem)
        <div class="modal fade" id="editBudgetItemModal{{ $budgetItem->id }}" tabindex="-1"
            aria-labelledby="editBudgetItemModalLabel{{ $budgetItem->id }}" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editBudgetItemModalLabel{{ $budgetItem->id }}">
                            <i class="ti ti-edit me-1"></i>Cập nhật mục ngân sách
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form
                        action="{{ route('backend.club.event-plans.budget-items.update', [$eventPlan->event->id, $budgetItem->id]) }}"
                        method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row">
                                <!-- Content -->
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="edit_content_{{ $budgetItem->id }}" class="form-label">
                                            <i class="ti ti-file-text me-1"></i>Nội dung <span
                                                class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" id="edit_content_{{ $budgetItem->id }}" name="content" rows="3"
                                            placeholder="Nhập nội dung mục ngân sách...">{{ $budgetItem->content }}</textarea>
                                    </div>
                                </div>

                                <!-- Quantity and Unit -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_quantity_{{ $budgetItem->id }}" class="form-label">
                                            <i class="ti ti-numbers me-1"></i>Số lượng <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control"
                                            id="edit_quantity_{{ $budgetItem->id }}" name="quantity"
                                            value="{{ $budgetItem->quantity }}" placeholder="Nhập số lượng..."
                                            min="1" step="1">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_unit_{{ $budgetItem->id }}" class="form-label">
                                            <i class="ti ti-package me-1"></i>Đơn vị <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control"
                                            id="edit_unit_{{ $budgetItem->id }}" name="unit"
                                            value="{{ $budgetItem->unit }}" placeholder="VD: cái, bộ, lít, kg...">
                                    </div>
                                </div>

                                <!-- Unit Price -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_unit_price_{{ $budgetItem->id }}" class="form-label">
                                            <i class="ti ti-currency-dong me-1"></i>Đơn giá (VNĐ) <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control"
                                            id="edit_unit_price_{{ $budgetItem->id }}" name="unit_price"
                                            value="{{ $budgetItem->unit_price }}" placeholder="Nhập đơn giá..."
                                            min="0" step="1000">
                                    </div>
                                </div>

                                <!-- Responsible Person -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_responsible_person_{{ $budgetItem->id }}"
                                            class="form-label">
                                            <i class="ti ti-user-check me-1"></i>Phụ trách <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control"
                                            id="edit_responsible_person_{{ $budgetItem->id }}"
                                            name="responsible_person" value="{{ $budgetItem->responsible_person }}"
                                            placeholder="Nhập tên người phụ trách...">
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="edit_notes_{{ $budgetItem->id }}" class="form-label">
                                            <i class="ti ti-note me-1"></i>Ghi chú
                                        </label>
                                        <textarea class="form-control" id="edit_notes_{{ $budgetItem->id }}" name="notes" rows="2"
                                            placeholder="Nhập ghi chú thêm (nếu có)...">{{ $budgetItem->notes }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                <i class="ti ti-x me-1"></i>Hủy
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="ti ti-check me-1"></i>Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif

<!-- Delete Budget Item Modals (Dynamic) -->
@if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'delete', 'branch'))
    @foreach ($eventPlan->budgetItems as $budgetItem)
        <div class="modal fade" id="deleteBudgetItemModal{{ $budgetItem->id }}" tabindex="-1"
            aria-labelledby="deleteBudgetItemModalLabel{{ $budgetItem->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger" id="deleteBudgetItemModalLabel{{ $budgetItem->id }}">
                            <i class="ti ti-alert-triangle me-1"></i>Xác nhận xóa mục ngân sách
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <i class="ti ti-trash text-danger" style="font-size: 2.5rem;"></i>
                        </div>
                        <h6 class="text-center mb-3">Bạn có chắc chắn muốn xóa mục ngân sách này?</h6>
                        <div class="alert alert-warning">
                            <div class="d-flex align-items-start">
                                <i class="ti ti-info-circle text-warning me-2 mt-1"></i>
                                <div>
                                    <strong>Nội dung:</strong><br>
                                    <span>{{ $budgetItem->content }}</span><br>
                                    <strong>Số lượng:</strong> {{ number_format($budgetItem->quantity) }}
                                    {{ $budgetItem->unit }}<br>
                                    <strong>Đơn giá:</strong>
                                    {{ number_format($budgetItem->unit_price, 0, ',', '.') }} VNĐ<br>
                                    <strong>Phụ trách:</strong> {{ $budgetItem->responsible_person }}
                                </div>
                            </div>
                        </div>
                        <p class="text-muted text-center small mb-0">
                            <i class="ti ti-alert-circle me-1"></i>
                            Hành động này không thể hoàn tác!
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ti ti-x me-1"></i>Hủy
                        </button>
                        <form
                            action="{{ route('backend.club.event-plans.budget-items.destroy', [$eventPlan->event->id, $budgetItem->id]) }}"
                            method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="ti ti-trash me-1"></i>Xóa mục ngân sách
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif

{{-- Equipment Modals --}}

<!-- Add Equipment Modal -->
@if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'create', 'branch'))
    <div class="modal fade" id="addEquipmentModal" tabindex="-1" aria-labelledby="addEquipmentModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEquipmentModalLabel">
                        <i class="ti ti-plus me-1"></i>Thêm vật dụng mới
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('backend.club.event-plans.equipment.store', $eventPlan->event->id) }}"
                    method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <!-- Name -->
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="add_name" class="form-label">
                                        <i class="ti ti-package me-1"></i>Tên vật dụng <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="add_name" name="name" value="{{ old('name') }}"
                                        placeholder="Nhập tên vật dụng...">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Quantity and Type -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add_quantity" class="form-label">
                                        <i class="ti ti-numbers me-1"></i>Số lượng <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                        class="form-control @error('quantity') is-invalid @enderror"
                                        id="add_quantity" name="quantity" value="{{ old('quantity') }}"
                                        placeholder="Nhập số lượng..." min="1" step="1">
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add_type" class="form-label">
                                        <i class="ti ti-building me-1"></i>Loại <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="add_type"
                                        name="type">
                                        <option value="">-- Chọn loại --</option>
                                        <option value="club" {{ old('type') == 'club' ? 'selected' : '' }}>Câu lạc
                                            bộ</option>
                                        <option value="school" {{ old('type') == 'school' ? 'selected' : '' }}>Trường
                                            học</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="add_notes" class="form-label">
                                        <i class="ti ti-note me-1"></i>Ghi chú
                                    </label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="add_notes" name="notes" rows="3"
                                        placeholder="Nhập ghi chú thêm (nếu có)...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ti ti-x me-1"></i>Hủy
                        </button>
                        <button type="submit" class="btn btn-info">
                            <i class="ti ti-check me-1"></i>Thêm vật dụng
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Edit Equipment Modals (Dynamic) -->
@if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'edit', 'branch'))
    @foreach ($eventPlan->equipment as $equipment)
        <div class="modal fade" id="editEquipmentModal{{ $equipment->id }}" tabindex="-1"
            aria-labelledby="editEquipmentModalLabel{{ $equipment->id }}" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editEquipmentModalLabel{{ $equipment->id }}">
                            <i class="ti ti-edit me-1"></i>Cập nhật vật dụng
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form
                        action="{{ route('backend.club.event-plans.equipment.update', [$eventPlan->event->id, $equipment->id]) }}"
                        method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row">
                                <!-- Name -->
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="edit_name_{{ $equipment->id }}" class="form-label">
                                            <i class="ti ti-package me-1"></i>Tên vật dụng <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control"
                                            id="edit_name_{{ $equipment->id }}" name="name"
                                            value="{{ $equipment->name }}" placeholder="Nhập tên vật dụng...">
                                    </div>
                                </div>

                                <!-- Quantity and Type -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_quantity_{{ $equipment->id }}" class="form-label">
                                            <i class="ti ti-numbers me-1"></i>Số lượng <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control"
                                            id="edit_quantity_{{ $equipment->id }}" name="quantity"
                                            value="{{ $equipment->quantity }}" placeholder="Nhập số lượng..."
                                            min="1" step="1">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_type_{{ $equipment->id }}" class="form-label">
                                            <i class="ti ti-building me-1"></i>Loại <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="edit_type_{{ $equipment->id }}"
                                            name="type">
                                            <option value="">-- Chọn loại --</option>
                                            <option value="club"
                                                {{ $equipment->type == 'club' ? 'selected' : '' }}>Câu lạc bộ</option>
                                            <option value="school"
                                                {{ $equipment->type == 'school' ? 'selected' : '' }}>Trường học
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="edit_notes_{{ $equipment->id }}" class="form-label">
                                            <i class="ti ti-note me-1"></i>Ghi chú
                                        </label>
                                        <textarea class="form-control" id="edit_notes_{{ $equipment->id }}" name="notes" rows="3"
                                            placeholder="Nhập ghi chú thêm (nếu có)...">{{ $equipment->notes }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                <i class="ti ti-x me-1"></i>Hủy
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="ti ti-check me-1"></i>Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif

<!-- Delete Equipment Modals (Dynamic) -->
@if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'delete', 'branch'))
    @foreach ($eventPlan->equipment as $equipment)
        <div class="modal fade" id="deleteEquipmentModal{{ $equipment->id }}" tabindex="-1"
            aria-labelledby="deleteEquipmentModalLabel{{ $equipment->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger" id="deleteEquipmentModalLabel{{ $equipment->id }}">
                            <i class="ti ti-alert-triangle me-1"></i>Xác nhận xóa vật dụng
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <i class="ti ti-trash text-danger" style="font-size: 2.5rem;"></i>
                        </div>
                        <h6 class="text-center mb-3">Bạn có chắc chắn muốn xóa vật dụng này?</h6>
                        <div class="alert alert-warning">
                            <div class="d-flex align-items-start">
                                <i class="ti ti-info-circle text-warning me-2 mt-1"></i>
                                <div>
                                    <strong>Tên vật dụng:</strong><br>
                                    <span>{{ $equipment->name }}</span><br>
                                    <strong>Số lượng:</strong> {{ number_format($equipment->quantity) }}<br>
                                    <strong>Loại:</strong> {{ $equipment->type_label }}
                                </div>
                            </div>
                        </div>
                        <p class="text-muted text-center small mb-0">
                            <i class="ti ti-alert-circle me-1"></i>
                            Hành động này không thể hoàn tác!
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ti ti-x me-1"></i>Hủy
                        </button>
                        <form
                            action="{{ route('backend.club.event-plans.equipment.destroy', [$eventPlan->event->id, $equipment->id]) }}"
                            method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="ti ti-trash me-1"></i>Xóa vật dụng
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif

{{-- Timeline Modals --}}

<!-- Add Timeline Modal -->
@if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'create', 'branch'))
    <div class="modal fade" id="addTimelineModal" tabindex="-1" aria-labelledby="addTimelineModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTimelineModalLabel">
                        <i class="ti ti-plus me-1"></i>Thêm timeline mới
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('backend.club.event-plans.timeline.store', $eventPlan->event->id) }}"
                    method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <!-- Date -->
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="add_date" class="form-label">
                                        <i class="ti ti-calendar me-1"></i>Ngày <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control @error('date') is-invalid @enderror"
                                        id="add_date" name="date" value="{{ old('date') }}">
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Start Time and End Time -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add_start_time" class="form-label">
                                        <i class="ti ti-clock me-1"></i>Thời gian bắt đầu <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="time"
                                        class="form-control @error('start_time') is-invalid @enderror"
                                        id="add_start_time" name="start_time" value="{{ old('start_time') }}">
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add_end_time" class="form-label">
                                        <i class="ti ti-clock-pause me-1"></i>Thời gian kết thúc <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="time"
                                        class="form-control @error('end_time') is-invalid @enderror"
                                        id="add_end_time" name="end_time" value="{{ old('end_time') }}">
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="add_content" class="form-label">
                                        <i class="ti ti-file-text me-1"></i>Nội dung <span
                                            class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('content') is-invalid @enderror" id="add_content" name="content"
                                        rows="3" placeholder="Nhập nội dung hoạt động...">{{ old('content') }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="add_notes" class="form-label">
                                        <i class="ti ti-note me-1"></i>Ghi chú
                                    </label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="add_notes" name="notes"
                                        rows="2" placeholder="Nhập ghi chú thêm (nếu có)...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ti ti-x me-1"></i>Hủy
                        </button>
                        <button type="submit" class="btn btn-info">
                            <i class="ti ti-check me-1"></i>Thêm timeline
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Edit Timeline Modals (Dynamic) -->
@if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'edit', 'branch'))
    @foreach ($eventPlan->timeline as $timelineItem)
        <div class="modal fade" id="editTimelineModal{{ $timelineItem->id }}" tabindex="-1"
            aria-labelledby="editTimelineModalLabel{{ $timelineItem->id }}" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editTimelineModalLabel{{ $timelineItem->id }}">
                            <i class="ti ti-edit me-1"></i>Cập nhật timeline
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form
                        action="{{ route('backend.club.event-plans.timeline.update', [$eventPlan->event->id, $timelineItem->id]) }}"
                        method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row">
                                <!-- Date -->
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="edit_date_{{ $timelineItem->id }}" class="form-label">
                                            <i class="ti ti-calendar me-1"></i>Ngày <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="date" class="form-control"
                                            id="edit_date_{{ $timelineItem->id }}" name="date"
                                            value="{{ $timelineItem->date->format('Y-m-d') }}">
                                    </div>
                                </div>

                                <!-- Start Time and End Time -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_start_time_{{ $timelineItem->id }}" class="form-label">
                                            <i class="ti ti-clock me-1"></i>Thời gian bắt đầu <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="time" class="form-control"
                                            id="edit_start_time_{{ $timelineItem->id }}" name="start_time"
                                            value="{{ $timelineItem->start_time->format('H:i') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_end_time_{{ $timelineItem->id }}" class="form-label">
                                            <i class="ti ti-clock-pause me-1"></i>Thời gian kết thúc <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="time" class="form-control"
                                            id="edit_end_time_{{ $timelineItem->id }}" name="end_time"
                                            value="{{ $timelineItem->end_time->format('H:i') }}">
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="edit_content_{{ $timelineItem->id }}" class="form-label">
                                            <i class="ti ti-file-text me-1"></i>Nội dung <span
                                                class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" id="edit_content_{{ $timelineItem->id }}" name="content" rows="3"
                                            placeholder="Nhập nội dung hoạt động...">{{ $timelineItem->content }}</textarea>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="edit_notes_{{ $timelineItem->id }}" class="form-label">
                                            <i class="ti ti-note me-1"></i>Ghi chú
                                        </label>
                                        <textarea class="form-control" id="edit_notes_{{ $timelineItem->id }}" name="notes" rows="2"
                                            placeholder="Nhập ghi chú thêm (nếu có)...">{{ $timelineItem->notes }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                <i class="ti ti-x me-1"></i>Hủy
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="ti ti-check me-1"></i>Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif

<!-- Delete Timeline Modals (Dynamic) -->
@if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'delete', 'branch'))
    @foreach ($eventPlan->timeline as $timelineItem)
        <div class="modal fade" id="deleteTimelineModal{{ $timelineItem->id }}" tabindex="-1"
            aria-labelledby="deleteTimelineModalLabel{{ $timelineItem->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger" id="deleteTimelineModalLabel{{ $timelineItem->id }}">
                            <i class="ti ti-alert-triangle me-1"></i>Xác nhận xóa timeline
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <i class="ti ti-trash text-danger" style="font-size: 2.5rem;"></i>
                        </div>
                        <h6 class="text-center mb-3">Bạn có chắc chắn muốn xóa timeline này?</h6>
                        <div class="alert alert-warning">
                            <div class="d-flex align-items-start">
                                <i class="ti ti-info-circle text-warning me-2 mt-1"></i>
                                <div>
                                    <strong>Ngày:</strong> {{ $timelineItem->formatted_date }}<br>
                                    <strong>Thời gian:</strong> {{ $timelineItem->time_range }}<br>
                                    <strong>Nội dung:</strong><br>
                                    <span>{{ $timelineItem->content }}</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted text-center small mb-0">
                            <i class="ti ti-alert-circle me-1"></i>
                            Hành động này không thể hoàn tác!
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ti ti-x me-1"></i>Hủy
                        </button>
                        <form
                            action="{{ route('backend.club.event-plans.timeline.destroy', [$eventPlan->event->id, $timelineItem->id]) }}"
                            method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="ti ti-trash me-1"></i>Xóa timeline
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif
