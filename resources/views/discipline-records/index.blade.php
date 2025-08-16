{{--
Central Discipline Records Index - Xem điểm thành viên toàn hệ thống
- Cho phép central admin xem tất cả bản ghi điểm của tất cả câu lạc bộ
- Có filters để lọc theo club, semester, user, status, discipline
- Hiển thị thông tin club trong bảng để phân biệt
--}}

@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Điểm Thành Viên</h1>

            <!-- Nút trên desktop -->
            @if (auth()->user()->hasPermissionForModuleCode('discipline_record_management', 'view_summary', 'central'))
                <div class="d-none d-md-block">
                    <a href="{{ route('backend.discipline-records.summary') }}" class="btn btn-info">
                        <i class="ti ti-chart-bar"></i>
                        Tổng Hợp Điểm
                    </a>
                </div>
            @endif
            <!-- Dropdown trên mobile -->
            <div class="dropdown d-md-none">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Hành động
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @if (auth()->user()->hasPermissionForModuleCode('discipline_record_management', 'view_summary', 'central'))
                        <li>
                            <a class="dropdown-item" href="{{ route('backend.discipline-records.summary') }}">
                                <i class="ti ti-chart-bar"></i> Tổng Hợp Điểm
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card mb-3">
                    <div class="card-header">
                        <div class="card-title">Bộ Lọc</div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('backend.discipline-records.index') }}">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Câu Lạc Bộ</label>
                                        <select class="form-select" name="club_id" id="clubFilter">
                                            <option value="">Tất cả câu lạc bộ</option>
                                            @foreach ($clubs as $club)
                                                <option value="{{ $club->id }}"
                                                    {{ request('club_id') == $club->id ? 'selected' : '' }}>
                                                    {{ $club->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
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
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label class="form-label">Trạng Thái</label>
                                        <select class="form-select" name="status">
                                            <option value="">Tất cả</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                                Hoạt động</option>
                                            <option value="revoked" {{ request('status') == 'revoked' ? 'selected' : '' }}>
                                                Đã thu hồi</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label class="form-label">Loại Điểm</label>
                                        <select class="form-select" name="discipline_id" id="disciplineFilter">
                                            <option value="">Tất cả</option>
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
                                        <label class="form-label">&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ti ti-filter"></i>
                                                Lọc
                                            </button>
                                            <a href="{{ route('backend.discipline-records.index') }}"
                                                class="btn btn-light">
                                                <i class="ti ti-refresh"></i>
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
                    <div class="card-header">
                        <div class="card-title">Danh Sách Điểm Thành Viên
                            @if ($disciplineRecords->count() > 0)
                                <span class="badge bg-primary ms-2">{{ $disciplineRecords->count() }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($disciplineRecords->count() > 0)
                            <div class="table-responsive">
                                <table class="table text-nowrap table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center !important;">STT</th>
                                            <th>Thành Viên</th>
                                            <th>Câu Lạc Bộ</th>
                                            <th>Loại Điểm</th>
                                            <th style="text-align: center !important;">Điểm</th>
                                            <th>Học Kỳ</th>
                                            <th>Ngày Áp Dụng</th>
                                            <th>Người Áp Dụng</th>
                                            <th style="text-align: center !important;">Trạng Thái</th>
                                            <th>Ghi Chú</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($disciplineRecords as $index => $record)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <strong>{{ $record->user->name }}</strong><br>
                                                            <small class="text-muted">{{ $record->user->email }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $record->club->name }}</span>
                                                </td>
                                                <td>
                                                    <span class="fw-semibold">{{ $record->discipline->name }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge bg-{{ $record->discipline->discipline_points >= 0 ? 'success' : 'danger' }} fs-6">
                                                        {{ $record->formatted_points }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $record->semester->name }}</span>
                                                </td>
                                                <td>
                                                    {{ $record->applied_date->format('d/m/Y') }}
                                                </td>
                                                <td>
                                                    <small>{{ $record->appliedBy->name ?? '-' }}</small>
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge bg-{{ $record->status_color }}">{{ $record->status_label }}</span>
                                                </td>
                                                <td>
                                                    @if ($record->notes)
                                                        <small>{{ Str::limit($record->notes, 50) }}</small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Auto-load disciplines when club is selected
        document.getElementById('clubFilter').addEventListener('change', function() {
            const clubId = this.value;
            const disciplineSelect = document.getElementById('disciplineFilter');

            // Clear current options
            disciplineSelect.innerHTML = '<option value="">Tất cả</option>';

            if (clubId) {
                // This would need an AJAX endpoint to fetch disciplines by club
                // For now, we'll submit the form to reload disciplines
                document.querySelector('form').submit();
            }
        });
    </script>
@endsection
