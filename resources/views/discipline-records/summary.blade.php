{{--
Central Discipline Records Summary - Tổng hợp điểm thành viên toàn hệ thống
- Cho phép central admin xem tổng hợp điểm của tất cả club hoặc club cụ thể
- Hiển thị thống kê tổng quan và bảng xếp hạng thành viên
- Có chức năng xuất Excel
- Tích hợp với school points system
--}}

@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">
                Tổng Hợp Điểm Thành Viên
                @if ($selectedClub)
                    - {{ $selectedClub->name }}
                @else
                    - Toàn Hệ Thống
                @endif
            </h1>

            <!-- Nút trên desktop -->
            <div class="d-none d-md-block">
                <a href="{{ route('backend.discipline-records.index') }}" class="btn btn-info me-2">
                    <i class="ti ti-list"></i>
                    Danh Sách Điểm Thành Viên
                </a>
                <a href="{{ route('backend.discipline-records.summary.export', array_filter(['semester_id' => $selectedSemester->id, 'club_id' => $selectedClub?->id])) }}"
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
                        <a class="dropdown-item" href="{{ route('backend.discipline-records.index') }}">
                            <i class="ti ti-list"></i> Danh Sách Điểm Thành Viên
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item"
                            href="{{ route('backend.discipline-records.summary.export', array_filter(['semester_id' => $selectedSemester->id, 'club_id' => $selectedClub?->id])) }}">
                            <i class="ti ti-file-excel"></i> Xuất Excel
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card mb-3">
                    <div class="card-body">
                        <form method="GET" action="{{ route('backend.discipline-records.summary') }}">
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Câu Lạc Bộ</label>
                                        <select class="form-select" name="club_id" onchange="this.form.submit()">
                                            <option value="">Tất cả câu lạc bộ</option>
                                            @foreach ($clubs as $club)
                                                <option value="{{ $club->id }}"
                                                    {{ $selectedClub && $selectedClub->id === $club->id ? 'selected' : '' }}>
                                                    {{ $club->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
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
                                <div class="col-md-4">
                                    <div class="mb-3 text-end">
                                        <span class="badge bg-info fs-6">
                                            <i class="ti ti-calendar"></i>
                                            Học kỳ hiện tại: {{ $selectedSemester->name }}
                                        </span>
                                        @if ($selectedClub)
                                            <span class="badge bg-primary fs-6 ms-2">
                                                <i class="ti ti-users"></i>
                                                {{ $selectedClub->name }}
                                            </span>
                                        @endif
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
                            {{ collect($membersSummary)->filter(fn($member) => $member['total_points'] < 0)->sum('total_points') }}
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
                            {{ collect($membersSummary)->filter(fn($member) => $member['total_points'] > 0)->sum('total_points') }}
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
                            {{ collect($membersSummary)->sum('total_points') }}
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
                        <div class="card-title">
                            Tổng Hợp Điểm Theo Thành Viên
                            @if (count($membersSummary) > 0)
                                <span class="badge bg-primary ms-2">{{ count($membersSummary) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mb-4">
                            <table class="table text-nowrap table-bordered">
                                <thead>
                                    <tr>
                                        <th style="text-align: center !important;">STT</th>
                                        <th>Thành Viên</th>
                                        @if (!$selectedClub)
                                            <th>Câu Lạc Bộ</th>
                                        @endif
                                        <th>Vai Trò</th>
                                        <th style="text-align: center !important;">Điểm Club</th>
                                        <th style="text-align: center !important;">Điểm Trường</th>
                                        <th style="text-align: center !important;">Thứ Hạng</th>
                                        <th>Điểm Gần Đây</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($membersSummary as $index => $memberData)
                                        @php
                                            $member = $memberData['member'];
                                            $totalPoints = $memberData['total_points'];
                                            $recentRecords = $memberData['recent_records'];

                                            // Calculate actual rank
                                            $actualRank = 1;
                                            foreach ($membersSummary as $compareData) {
                                                if ($compareData['total_points'] > $totalPoints) {
                                                    $actualRank++;
                                                }
                                            }

                                            // Determine rank color
                                            $rankDisplayColor = 'primary';
                                            if ($actualRank <= 3) {
                                                $rankDisplayColor = 'warning'; // Top 3 - gold-like
                                            } elseif ($actualRank <= 10) {
                                                $rankDisplayColor = 'info'; // Top 10 - silver-like
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
                                            @if (!$selectedClub)
                                                <td>
                                                    <span class="badge bg-info">{{ $member->club->name }}</span>
                                                </td>
                                            @endif
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
                                                    {{ number_format($memberData['school_points'], 1) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
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
                                                    @foreach ($recentRecords->take(2) as $record)
                                                        <div class="mb-1">
                                                            <small
                                                                class="text-muted">{{ $record->applied_date->format('d/m') }}:</small>
                                                            <span
                                                                class="badge {{ $record->discipline->discipline_points > 0 ? 'bg-success-transparent' : 'bg-danger-transparent' }}">{{ $record->discipline->name }}</span>
                                                        </div>
                                                    @endforeach
                                                    @if ($recentRecords->count() > 2)
                                                        <small class="text-muted">và {{ $recentRecords->count() - 2 }}
                                                            điểm khác...</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
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
@endsection
