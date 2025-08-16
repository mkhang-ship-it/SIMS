{{--
Latest Changes: Bổ sung modal thu hồi và xóa
- Thay thế confirm dialog đơn giản bằng modal có thông tin chi tiết
- Modal thu hồi: yêu cầu lý do thu hồi, hiển thị thông tin bản ghi, chỉ cho records có status 'active'
- Modal xóa: yêu cầu lý do xóa, checkbox xác nhận, cảnh báo không thể hoàn tác
- Cải thiện UX với thông tin rõ ràng và xác nhận an toàn
- Buttons trong action column được chuyển từ form submission sang modal trigger
--}}

@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Điểm Thành Viên</h1>

            <!-- Nút trên desktop -->
            @if (auth()->user()->hasPermissionForModuleCode('discipline_record_management', 'create', 'branch') ||
                    auth()->user()->hasPermissionForModuleCode('discipline_record_management', 'view_summary', 'branch'))
                <div class="d-none d-md-block">
                    @if (auth()->user()->hasPermissionForModuleCode('discipline_record_management', 'create', 'branch'))
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRecordModal">
                            <i class="ti ti-plus"></i>
                            Thêm Điểm
                        </button>
                    @endif
                    @if (auth()->user()->hasPermissionForModuleCode('discipline_record_management', 'view_summary', 'branch'))
                        <a href="{{ route('backend.club.discipline-records.summary') }}" class="btn btn-info">
                            <i class="ti ti-chart-bar"></i>
                            Tổng Hợp Điểm
                        </a>
                    @endif
                </div>
            @endif
            <!-- Dropdown trên mobile -->
            <div class="dropdown d-md-none">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Hành động
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @if (auth()->user()->hasPermissionForModuleCode('discipline_record_management', 'create', 'branch'))
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addRecordModal">
                                <i class="ti ti-plus"></i> Thêm Điểm
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->hasPermissionForModuleCode('discipline_record_management', 'view_summary', 'branch'))
                        <li>
                            <a class="dropdown-item" href="{{ route('backend.club.discipline-records.summary') }}">
                                <i class="ti ti-chart-bar"></i> Tổng Hợp Điểm
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- Filters -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card mb-3">
                    <div class="card-body">
                        <form method="GET" action="{{ route('backend.club.discipline-records.index') }}">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Học Kỳ</label>
                                        <select class="form-select" name="semester_id">
                                            <option value="">Tất cả học kỳ</option>
                                            @foreach ($semesters as $semester)
                                                <option value="{{ $semester->id }}"
                                                    {{ request('semester_id') == $semester->id ? 'selected' : '' }}>
                                                    {{ $semester->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Thành Viên</label>
                                        <select class="form-select" name="user_id">
                                            <option value="">Tất cả thành viên</option>
                                            @foreach ($clubMembers as $member)
                                                <option value="{{ $member->id }}"
                                                    {{ request('user_id') == $member->id ? 'selected' : '' }}>
                                                    {{ $member->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label class="form-label">Loại Điểm</label>
                                        <select class="form-select" name="discipline_id">
                                            <option value="">Tất cả loại</option>
                                            @foreach ($disciplines as $discipline)
                                                <option value="{{ $discipline->id }}"
                                                    {{ request('discipline_id') == $discipline->id ? 'selected' : '' }}>
                                                    {{ $discipline->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label class="form-label">Trạng Thái</label>
                                        <select class="form-select" name="status">
                                            <option value="">Tất cả trạng thái</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                                Hoạt động</option>
                                            <option value="revoked" {{ request('status') == 'revoked' ? 'selected' : '' }}>
                                                Đã thu hồi</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ti ti-search"></i>
                                                Lọc
                                            </button>
                                            <a href="{{ route('backend.club.discipline-records.index') }}"
                                                class="btn btn-light">
                                                <i class="ti ti-refresh"></i>
                                                Reset
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Records Table -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="table-responsive mb-4">
                            <table class="table text-nowrap table-bordered" id="data-table">
                                <thead>
                                    <tr>
                                        <th style="text-align: center !important;">STT</th>
                                        <th>Thành Viên</th>
                                        <th>Loại Điểm</th>
                                        <th style="text-align: center !important;">Điểm</th>
                                        <th style="text-align: center !important;">Ngày Áp Dụng</th>
                                        <th>Học Kỳ</th>
                                        <th style="text-align: center !important;">Trạng Thái</th>
                                        <th style="text-align: center !important;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($disciplineRecords as $record)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <strong>{{ $record->user->name }}</strong><br>
                                                        <small class="text-muted">{{ $record->user->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-semibold">{{ $record->discipline->name }}</span><br>
                                                <small class="text-muted">{{ $record->discipline->code }}</small>
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge {{ $record->discipline->discipline_points > 0 ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $record->discipline->discipline_points > 0 ? '+' : '' }}{{ $record->formatted_points }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                {{ $record->applied_date->format('d/m/Y') }}
                                                <br>
                                                <small class="text-muted">{{ $record->appliedBy->name }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $record->semester->name }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-{{ $record->status_color }}">{{ $record->status_label }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="gap-2 fs-15">
                                                    @if (auth()->user()->hasPermissionForModuleCode('discipline_record_management', 'edit', 'branch'))
                                                        <button class="btn btn-icon btn-sm btn-info-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editRecordModal{{ $record->id }}"
                                                            title="Chỉnh sửa">
                                                            <i class="ri-edit-line"></i>
                                                        </button>
                                                    @endif

                                                    @if (
                                                        $record->status === 'active' &&
                                                            auth()->user()->hasPermissionForModuleCode('discipline_record_management', 'revoke', 'branch'))
                                                        <button class="btn btn-icon btn-sm btn-warning-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#revokeRecordModal{{ $record->id }}"
                                                            title="Thu hồi">
                                                            <i class="ri-close-circle-line"></i>
                                                        </button>
                                                    @endif

                                                    @if (auth()->user()->hasPermissionForModuleCode('discipline_record_management', 'delete', 'branch'))
                                                        <button class="btn btn-icon btn-sm btn-danger-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteRecordModal{{ $record->id }}"
                                                            title="Xóa">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    @endif
                                                </div>
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
    </div>

    @foreach ($disciplineRecords as $record)
        <!-- Edit Record Modal -->
        @if (auth()->user()->hasPermissionForModuleCode('discipline_record_management', 'edit', 'branch'))
            <div class="modal fade" id="editRecordModal{{ $record->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Chỉnh Sửa Điểm</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('backend.club.discipline-records.update', $record->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Loại Điểm <span class="text-danger">*</span></label>
                                            <select class="form-select" name="discipline_id" required>
                                                @foreach ($disciplines as $discipline)
                                                    <option value="{{ $discipline->id }}"
                                                        {{ $record->discipline_id === $discipline->id ? 'selected' : '' }}>
                                                        {{ $discipline->name }}
                                                        ({{ $discipline->formatted_points }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Học Kỳ <span class="text-danger">*</span></label>
                                            <select class="form-select" name="semester_id" required>
                                                @foreach ($semesters as $semester)
                                                    <option value="{{ $semester->id }}"
                                                        {{ $record->semester_id === $semester->id ? 'selected' : '' }}>
                                                        {{ $semester->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Ngày Áp Dụng <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="applied_date"
                                        value="{{ $record->applied_date->format('Y-m-d') }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Ghi Chú</label>
                                    <textarea class="form-control" name="notes" rows="2">{{ $record->notes }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-primary">Cập
                                    Nhật</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Revoke Record Modal -->
        @if (
            $record->status === 'active' &&
                auth()->user()->hasPermissionForModuleCode('discipline_record_management', 'revoke', 'branch'))
            <div class="modal fade" id="revokeRecordModal{{ $record->id }}" tabindex="-1" aria-hidden="true"
                data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-warning">
                                <i class="ri-warning-line"></i>
                                Thu Hồi Điểm
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                <strong>Cảnh báo!</strong> Bạn sắp thu hồi điểm này. Hành
                                động này sẽ thay đổi trạng thái của điểm từ "Hoạt động" sang
                                "Đã thu hồi".
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Thành viên:</strong><br>
                                    <span class="text-muted">{{ $record->user->name }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Loại điểm:</strong><br>
                                    <span class="text-muted">{{ $record->discipline->name }}</span>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Điểm:</strong><br>
                                    <span
                                        class="badge {{ $record->discipline->discipline_points > 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $record->discipline->discipline_points > 0 ? '+' : '' }}{{ $record->formatted_points }}
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Ngày áp dụng:</strong><br>
                                    <span class="text-muted">{{ $record->applied_date->format('d/m/Y') }}</span>
                                </div>
                            </div>

                            <form id="revokeForm{{ $record->id }}"
                                action="{{ route('backend.club.discipline-records.revoke', $record->id) }}"
                                method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="mt-3">
                                    <label class="form-label">Lý do thu hồi <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="revoke_reason" rows="3" placeholder="Nhập lý do thu hồi điểm này..."
                                        required></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" form="revokeForm{{ $record->id }}" class="btn btn-warning">
                                <i class="ri-close-circle-line"></i>
                                Thu Hồi Điểm
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Delete Record Modal -->
        @if (auth()->user()->hasPermissionForModuleCode('discipline_record_management', 'delete', 'branch'))
            <div class="modal fade" id="deleteRecordModal{{ $record->id }}" tabindex="-1" aria-hidden="true"
                data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-danger">
                                <i class="ri-delete-bin-line"></i>
                                Xóa Điểm
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-danger">
                                <strong>Nguy hiểm!</strong> Bạn sắp xóa vĩnh viễn điểm này.
                                Hành động này không thể hoàn tác!
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Thành viên:</strong><br>
                                    <span class="text-muted">{{ $record->user->name }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Loại điểm:</strong><br>
                                    <span class="text-muted">{{ $record->discipline->name }}</span>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Điểm:</strong><br>
                                    <span
                                        class="badge {{ $record->discipline->discipline_points > 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $record->discipline->discipline_points > 0 ? '+' : '' }}{{ $record->formatted_points }}
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Ngày áp dụng:</strong><br>
                                    <span class="text-muted">{{ $record->applied_date->format('d/m/Y') }}</span>
                                </div>
                            </div>

                            <div class="mt-3">
                                <p class="text-muted">
                                    <strong>Lưu ý:</strong> Sau khi xóa, dữ liệu sẽ không
                                    thể khôi phục.
                                    Nếu bạn chỉ muốn tạm thời vô hiệu hóa điểm này, hãy sử
                                    dụng tính năng "Thu hồi" thay vì xóa.
                                </p>
                            </div>

                            <form id="deleteForm{{ $record->id }}"
                                action="{{ route('backend.club.discipline-records.destroy', $record->id) }}"
                                method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="mt-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            id="confirmDelete{{ $record->id }}" required>
                                        <label class="form-check-label text-danger"
                                            for="confirmDelete{{ $record->id }}">
                                            Tôi hiểu rằng hành động này không thể hoàn tác.
                                        </label>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" form="deleteForm{{ $record->id }}" class="btn btn-danger">
                                <i class="ri-delete-bin-line"></i>
                                Xóa Vĩnh Viễn
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <!-- Add Record Modal -->
    @if (auth()->user()->hasPermissionForModuleCode('discipline_record_management', 'create', 'branch'))
        <div class="modal fade" id="addRecordModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm Điểm Mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('backend.club.discipline-records.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Thành Viên <span class="text-danger">*</span></label>
                                        <select class="form-select" name="user_id" required>
                                            <option value="">Chọn thành viên</option>
                                            @foreach ($clubMembers as $member)
                                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Loại Điểm <span class="text-danger">*</span></label>
                                        <select class="form-select" name="discipline_id" required>
                                            <option value="">Chọn loại điểm</option>
                                            @foreach ($disciplines as $discipline)
                                                <option value="{{ $discipline->id }}">
                                                    {{ $discipline->name }} ({{ $discipline->formatted_points }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Học Kỳ <span class="text-danger">*</span></label>
                                <select class="form-select" name="semester_id" required>
                                    <option value="">Chọn học kỳ</option>
                                    @foreach ($semesters as $semester)
                                        <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Ngày Áp Dụng <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="applied_date"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Ghi Chú</label>
                                <textarea class="form-control" name="notes" rows="2" placeholder="Ghi chú thêm (nếu có)..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary">Thêm Điểm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
@endsection
