{{--
Updated: Thay đổi thống kê từ đếm thành viên sang tổng điểm và thêm xuất Excel
- Thay "Có Điểm Trừ" thành "Tổng Điểm Trừ" với tổng số điểm âm
- Thay "Điểm Dương/Bằng 0" thành "Tổng Điểm Cộng" với tổng số điểm dương
- Cập nhật icon: minus cho điểm trừ, plus cho điểm cộng
- Hiển thị tổng giá trị điểm thay vì số lượng thành viên
- Thêm chức năng xuất Excel với format: STT, Họ tên, MSSV, Chức vụ, Email, Mô tả, Điểm CLB, Điểm trường
--}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Tổng Hợp Điểm Thành Viên</h1>
            <!-- Nút trên desktop -->
            <div class="d-none d-md-block">
                <a href="{{ route('backend.club.discipline-records.index') }}" class="btn btn-info me-2">
                    <i class="ti ti-list"></i>
                    Danh Sách Điểm Thành Viên
                </a>
                <a href="{{ route('backend.club.discipline-records.summary.export', ['semester_id' => $selectedSemester->id]) }}"
                    class="btn btn-success">
                    <i class="ti ti-file-excel"></i>
                    Xuất Excel
                </a>
            </div>
            <!-- Dropdown trên mobile -->
            <div class="dropdown d-md-none">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Hành động
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('backend.club.discipline-records.index') }}">
                            <i class="ti ti-list"></i> Danh Sách Điểm Thành Viên
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item"
                            href="{{ route('backend.club.discipline-records.summary.export', ['semester_id' => $selectedSemester->id]) }}">
                            <i class="ti ti-file-excel"></i> Xuất Excel
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Semester Filter -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card mb-3">
                    <div class="card-body">
                        <form method="GET" action="{{ route('backend.club.discipline-records.summary') }}">
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Chọn Học Kỳ</label>
                                        <select class="form-select" name="semester_id" onchange="this.form.submit()">
                                            @foreach ($semesters as $semester)
                                                <option value="{{ $semester->id }}"
                                                    {{ $selectedSemester->id === $semester->id ? 'selected' : '' }}>
                                                    {{ $semester->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-3 text-end">
                                        <span class="badge bg-info fs-6">
                                            <i class="ti ti-calendar"></i>
                                            Học kỳ hiện tại: {{ $selectedSemester->name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="row">
            <div class="col-md-3">
                <div class="card custom-card text-center">
                    <div class="card-body">
                        <div class="text-primary mb-2">
                            <i class="ti ti-users fs-1"></i>
                        </div>
                        <h4 class="fw-semibold mb-2">{{ count($membersSummary) }}</h4>
                        <p class="text-muted mb-0">Tổng Thành Viên</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card custom-card text-center">
                    <div class="card-body">
                        <div class="text-danger mb-2">
                            <i class="ti ti-minus fs-1"></i>
                        </div>
                        <h4 class="fw-semibold mb-2">
                            {{ $totalPenaltyPoints ?? 0 }}
                        </h4>
                        <p class="text-muted mb-0">Tổng Điểm Trừ</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card custom-card text-center">
                    <div class="card-body">
                        <div class="text-success mb-2">
                            <i class="ti ti-plus fs-1"></i>
                        </div>
                        <h4 class="fw-semibold mb-2">
                            {{ $totalBonusPoints ?? 0 }}
                        </h4>
                        <p class="text-muted mb-0">Tổng Điểm Cộng</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card custom-card text-center">
                    <div class="card-body">
                        <div class="text-warning mb-2">
                            <i class="ti ti-sum fs-1"></i>
                        </div>
                        <h4 class="fw-semibold mb-2">
                            {{ ($totalBonusPoints ?? 0) - ($totalPenaltyPoints ?? 0) }}
                        </h4>
                        <p class="text-muted mb-0">Tổng Điểm</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Members Summary -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">Tổng Hợp Điểm Theo Thành Viên</div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mb-4">
                            <table class="table text-nowrap table-bordered">
                                <thead>
                                    <tr>
                                        <th style="text-align: center !important;">STT</th>
                                        <th>Thành Viên</th>
                                        <th>Vai Trò</th>
                                        <th style="text-align: center !important;">Điểm Club</th>
                                        <th style="text-align: center !important;">Điểm Trường</th>
                                        <th style="text-align: center !important;">Thứ Hạng</th>
                                        <th>Điểm Gần Đây</th>
                                        <th style="text-align: center !important;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($membersSummary as $index => $memberData)
                                        @php
                                            $member = $memberData['member'];
                                            $totalPoints = $memberData['total_points'];
                                            $recentRecords = $memberData['recent_records'];

                                            // Calculate ranking (lower points = better rank)
                                            $currentRank = 1;
                                            foreach ($membersSummary as $idx => $data) {
                                                if ($idx >= $index) {
                                                    break;
                                                }
                                                if ($data['total_points'] < $totalPoints) {
                                                    $currentRank++;
                                                }
                                            }

                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <strong>{{ $member->user->name }}</strong><br>
                                                        <small class="text-muted">{{ $member->user->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $member->role->name ?? '-' }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-{{ $totalPoints < 0 ? 'danger' : ($totalPoints > 0 ? 'success' : 'secondary') }} fs-6">
                                                    {{ $totalPoints > 0 ? '+' . $totalPoints : $totalPoints }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info fs-6">
                                                    {{ number_format($memberData['school_points'], 0) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    // Calculate actual rank based on sorted data (higher points = better rank for combined system)
                                                    $actualRank = 1;
                                                    foreach ($membersSummary as $compareData) {
                                                        if ($compareData['total_points'] > $totalPoints) {
                                                            $actualRank++;
                                                        }
                                                    }

                                                    // Determine rank display color
                                                    $rankDisplayColor = 'primary';
                                                    if ($actualRank <= 3) {
                                                        $rankDisplayColor = 'warning'; // Top 3 - gold-like
                                                    } elseif ($actualRank <= 10) {
                                                        $rankDisplayColor = 'info'; // Top 10 - silver-like
                                                    }
                                                @endphp
                                                <span class="badge bg-{{ $rankDisplayColor }} fs-6">
                                                    #{{ $actualRank }}
                                                    @if ($actualRank == 1)
                                                        <i class="ti ti-trophy"></i>
                                                    @elseif($actualRank <= 3)
                                                        <i class="ti ti-medal"></i>
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                @if ($recentRecords->count() > 0)
                                                    @foreach ($recentRecords as $record)
                                                        <div class="mb-1">
                                                            <small
                                                                class="text-muted">{{ $record->applied_date->format('d/m') }}:</small>
                                                            <span
                                                                class="badge {{ $record->discipline->discipline_points > 0 ? 'bg-success-transparent' : 'bg-danger-transparent' }}">{{ $record->discipline->name }}</span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('backend.club.discipline-records.index', ['user_id' => $member->user->id, 'semester_id' => $selectedSemester->id]) }}"
                                                    class="btn btn-icon btn-sm btn-info-light" title="Xem chi tiết">
                                                    <i class="ri-eye-line"></i>
                                                </a>

                                                @if (auth()->user()->hasPermissionForModuleCode('discipline_record_management', 'create', 'branch'))
                                                    <button class="btn btn-icon btn-sm btn-primary-light"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#quickAddModal{{ $member->id }}"
                                                        title="Thêm điểm nhanh">
                                                        <i class="ri-add-line"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>

                                        <!-- Quick Add Modal -->
                                        @if (auth()->user()->hasPermissionForModuleCode('discipline_record_management', 'create', 'branch'))
                                            <div class="modal fade" id="quickAddModal{{ $member->id }}" tabindex="-1"
                                                aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h6 class="modal-title">Thêm Điểm -
                                                                {{ $member->user->name }}</h6>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form
                                                            action="{{ route('backend.club.discipline-records.store') }}"
                                                            method="POST">
                                                            @csrf
                                                            <input type="hidden" name="user_id"
                                                                value="{{ $member->user->id }}">
                                                            <input type="hidden" name="semester_id"
                                                                value="{{ $selectedSemester->id }}">
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Loại Điểm <span
                                                                            class="text-danger">*</span></label>
                                                                    <select class="form-select" name="discipline_id"
                                                                        required>
                                                                        <option value="">Chọn loại điểm</option>
                                                                        @foreach (\App\Models\Discipline::byClub(session('club_id'))->where('status', 'active')->get() as $discipline)
                                                                            <option value="{{ $discipline->id }}">
                                                                                {{ $discipline->name }}
                                                                                ({{ $discipline->formatted_points }})
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Ngày Áp Dụng <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="date" class="form-control"
                                                                        name="applied_date" value="{{ date('Y-m-d') }}"
                                                                        required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Ghi Chú</label>
                                                                    <textarea class="form-control" name="notes" rows="2"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-light"
                                                                    data-bs-dismiss="modal">Hủy</button>
                                                                <button type="submit" class="btn btn-primary">Thêm
                                                                    Điểm</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
