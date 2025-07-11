
<div class="mb-4">
    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">
        <i class="ti ti-info-circle me-1"></i>1. Thông tin chung
    </h6>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="fw-medium text-muted mb-1">Câu lạc bộ:</label>
            <div class="p-2 bg-light rounded">
                {{ $eventPlan->club->name }}
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <label class="fw-medium text-muted mb-1">Người lập:</label>
            <div class="p-2 bg-light rounded">
                {{ $eventPlan->preparedBy->name ?? 'Chưa có thông tin' }}
                ({{ $eventPlan->preparedBy->role->name ?? 'Chưa có thông tin' }})
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <label class="fw-medium text-muted mb-1">Thời gian triển khai:</label>
            <div class="p-2 bg-light rounded">
                {{ $eventPlan->implementation_time ? $eventPlan->implementation_time->format('d/m/Y') : 'Chưa có thông tin' }}
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <label class="fw-medium text-muted mb-1">Được phê duyệt bởi:</label>
            <div class="p-2 bg-light rounded">
                {{ $eventPlan->approved_by ?? 'Chưa có thông tin' }}
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
        <div class="col-md-4 mb-3">
            <label class="fw-medium text-muted mb-1">Thời gian tổ chức:</label>
            <div class="p-2 bg-light rounded">
                <div>
                    {{ $eventPlan->event->formatted_event_date }}
                    ({{ $eventPlan->event->time_range }})
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <label class="fw-medium text-muted mb-1">Địa điểm tổ chức:</label>
            <div class="p-2 bg-light rounded">
                {{ $eventPlan->event->classRoom->name }}
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <label class="fw-medium text-muted mb-1">Học kỳ:</label>
            <div class="p-2 bg-light rounded">
                {{ $eventPlan->event->semester->name }}
            </div>
        </div>
        <div class="col-12 mb-3">
            <label class="fw-medium text-muted mb-1">Đối tượng tham gia:</label>
            <div class="p-2 bg-light rounded">
                {{ $eventPlan->participants ?? 'Chưa có thông tin' }}
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
            <div class="p-3 bg-light rounded">
                {{ $eventPlan->description ?? 'Chưa có thông tin' }}
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
        <div class="col-md-6 mb-3">
            <label class="fw-medium text-muted mb-1">Chịu trách nhiệm tổ chức:</label>
            <div class="p-2 bg-light rounded">
                {{ $eventPlan->responsible_organizer ?? 'Chưa có thông tin' }}
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <label class="fw-medium text-muted mb-1">Cố vấn và phối hợp tổ chức:</label>
            <div class="p-2 bg-light rounded">
                {{ $eventPlan->advisor_coordinator ?? 'Chưa có thông tin' }}
            </div>
        </div>
    </div>
</div>
