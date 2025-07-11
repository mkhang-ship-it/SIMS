<div class="mb-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h6 class="fw-bold text-purple mb-0">
            <i class="ti ti-target me-1"></i>Mục Tiêu Và Chỉ Số
        </h6>
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
