@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Semester Filter -->
        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card custom-card mb-3">
                    <div class="card-body">
                        <form method="GET" action="{{ route('backend.club.discipline-records.my-points') }}">
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

        <!-- Personal Statistics -->
        <div class="row">
            <div class="col-md-2-4">
                <div class="card custom-card text-center">
                    <div class="card-body">
                        <div class="text-success mb-2">
                            <i class="ti ti-plus fs-1"></i>
                        </div>
                        <h4 class="fw-semibold mb-2 text-success">
                            +{{ $bonusPoints ?? 0 }}
                        </h4>
                        <p class="text-muted mb-0">Điểm Cộng</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2-4">
                <div class="card custom-card text-center">
                    <div class="card-body">
                        <div class="text-danger mb-2">
                            <i class="ti ti-minus fs-1"></i>
                        </div>
                        <h4 class="fw-semibold mb-2 text-danger">
                            {{ $penaltyPoints ?? 0 }}
                        </h4>
                        <p class="text-muted mb-0">Điểm Trừ</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2-4">
                <div class="card custom-card text-center">
                    <div class="card-body">
                        <div class="text-{{ $totalClubPoints >= 0 ? 'success' : 'danger' }} mb-2">
                            <i class="ti ti-{{ $totalClubPoints >= 0 ? 'plus' : 'minus' }} fs-1"></i>
                        </div>
                        <h4 class="fw-semibold mb-2 text-{{ $totalClubPoints >= 0 ? 'success' : 'danger' }}">
                            {{ $totalClubPoints >= 0 ? '+' : '' }}{{ $totalClubPoints }}
                        </h4>
                        <p class="text-muted mb-0">Điểm Club</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2-4">
                <div class="card custom-card text-center">
                    <div class="card-body">
                        <div class="text-info mb-2">
                            <i class="ti ti-school fs-1"></i>
                        </div>
                        <h4 class="fw-semibold mb-2 text-info">
                            {{ number_format($schoolPoint ? $schoolPoint->school_points : 0, 0) }}
                        </h4>
                        <p class="text-muted mb-0">Điểm Trường</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2-4">
                <div class="card custom-card text-center">
                    <div class="card-body">
                        <div class="text-warning mb-2">
                            <i class="ti ti-trophy fs-1"></i>
                        </div>
                        <h4 class="fw-semibold mb-2 text-warning">
                            {{ $ranking ?? '-' }}
                        </h4>
                        <p class="text-muted mb-0">Thứ Hạng</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Discipline Records List -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="ti ti-list me-2"></i>
                            Lịch Sử Điểm
                        </div>
                        <div class="card-toolbar">
                            <span class="badge bg-info">
                                {{ $disciplineRecords->count() }} bản ghi
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($disciplineRecords->count() > 0)
                            <div class="table-responsive">
                                <table class="table text-nowrap table-bordered table-hover" id="data-table">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center !important;">STT</th>
                                            <th>Ngày</th>
                                            <th>Loại Điểm</th>
                                            <th style="text-align: center !important;">Điểm</th>
                                            <th>Ghi Chú</th>
                                            <th>Ghi Nhận Bởi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($disciplineRecords as $index => $record)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($record->applied_date)->format('d/m/Y') }}
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $record->discipline->discipline_points >= 0 ? 'success' : 'danger' }}">
                                                        {{ $record->discipline->name }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="text-{{ $record->discipline->discipline_points >= 0 ? 'success' : 'danger' }} fs-6 fw-bold">
                                                        {{ $record->discipline->discipline_points >= 0 ? '+' : '' }}{{ $record->discipline->discipline_points }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($record->notes)
                                                        <span class="text-muted" title="{{ $record->notes }}">
                                                            {{ Str::limit($record->notes, 50) }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="text-muted">
                                                        {{ $record->appliedBy->name ?? 'Hệ thống' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="text-muted mb-3">
                                    <i class="ti ti-check-circle fs-1"></i>
                                </div>
                                <h5 class="text-muted">Không có bản ghi điểm nào</h5>
                                <p class="text-muted">Bạn chưa có điểm số nào trong học kỳ này!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Cách Tính Điểm và Xếp Hạng -->
        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="ti ti-calculator me-2"></i>
                            Cách Tính Điểm và Xếp Hạng
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="fw-semibold text-primary mb-3">
                                    <i class="ti ti-plus-circle me-2"></i>
                                    Điểm Câu Lạc Bộ
                                </h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="ti ti-arrow-right text-success me-2"></i>
                                        <strong>Điểm Cộng:</strong> Tổng tất cả điểm dương từ các hoạt động tích cực.
                                    </li>
                                    <li class="mb-2">
                                        <i class="ti ti-arrow-right text-danger me-2"></i>
                                        <strong>Điểm Trừ:</strong> Tổng tất cả điểm âm từ các vi phạm.
                                    </li>
                                    <li class="mb-2">
                                        <i class="ti ti-arrow-right text-info me-2"></i>
                                        <strong>Điểm Câu Lạc Bộ:</strong> Điểm cộng - Điểm trừ = Tổng điểm thực tế.
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-semibold text-info mb-3">
                                    <i class="ti ti-school me-2"></i>
                                    Điểm Trường
                                </h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="ti ti-arrow-right text-primary me-2"></i>
                                        <strong>Công thức:</strong> (Điểm Club / Điểm cao nhất) × 5.
                                    </li>
                                    <li class="mb-2">
                                        <i class="ti ti-arrow-right text-warning me-2"></i>
                                        <strong>Bonus Leader:</strong> +5 điểm nếu là ban chủ nhiệm.
                                    </li>
                                    <li class="mb-2">
                                        <i class="ti ti-arrow-right text-success me-2"></i>
                                        <strong>Thang điểm:</strong> Tối đa 10 điểm (5 điểm trường + 5 điểm ban chủ
                                        nhiệm).
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="fw-semibold text-warning mb-3">
                                    <i class="ti ti-trophy me-2"></i>
                                    Cách Xếp Hạng
                                </h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="ti ti-arrow-right text-warning me-2"></i>
                                        <strong>Xếp hạng theo:</strong> Điểm Club (từ cao xuống thấp).
                                    </li>
                                    <li class="mb-2">
                                        <i class="ti ti-arrow-right text-info me-2"></i>
                                        <strong>Điểm cao nhất:</strong> Xếp hạng 1.
                                    </li>
                                    <li class="mb-2">
                                        <i class="ti ti-arrow-right text-muted me-2"></i>
                                        <strong>Điểm âm:</strong> Vẫn được xếp hạng (điểm âm cao nhất xếp cuối).
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-semibold text-success mb-3">
                                    <i class="ti ti-info-circle me-2"></i>
                                    Lưu Ý Quan Trọng
                                </h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="ti ti-check text-success me-2"></i>
                                        Điểm được tính tự động mỗi khi có thay đổi.
                                    </li>
                                    <li class="mb-2">
                                        <i class="ti ti-check text-success me-2"></i>
                                        Xếp hạng cập nhật real-time theo học kỳ.
                                    </li>
                                    <li class="mb-2">
                                        <i class="ti ti-check text-success me-2"></i>
                                        Điểm trường được quy đổi từ điểm câu lạc bộ.
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-bulb fs-4 me-3"></i>
                                <div>
                                    <strong>Mẹo:</strong> Để cải thiện xếp hạng, hãy tham gia tích cực các hoạt động của
                                    club và tuân thủ nội quy để tránh bị trừ điểm. Điểm leader (+5) chỉ áp dụng cho các vị
                                    trí ban chủ nhiệm trong câu lạc bộ.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .col-md-2-4 {
            flex: 0 0 20%;
            max-width: 20%;
        }

        @media (max-width: 767.98px) {
            .col-md-2-4 {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: 1rem;
            }
        }
    </style>

    <script>
        // Auto-submit form when semester changes
        document.addEventListener('DOMContentLoaded', function() {
            const semesterSelect = document.querySelector('select[name="semester_id"]');
            if (semesterSelect) {
                semesterSelect.addEventListener('change', function() {
                    this.form.submit();
                });
            }
        });
    </script>
@endsection
