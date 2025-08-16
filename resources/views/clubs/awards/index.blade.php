@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Bằng Khen / Giải Thưởng</h1>

            <!-- Nút trên desktop -->
            @if (auth()->user()->hasPermissionForModuleCode('award_management', 'create', 'branch'))
                <div class="d-none d-md-block">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAwardModal">
                        <i class="ti ti-plus"></i>
                        Thêm Mới
                    </button>
                </div>
            @endif
            <!-- Dropdown trên mobile -->
            <div class="dropdown d-md-none">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Hành động
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @if (auth()->user()->hasPermissionForModuleCode('award_management', 'create', 'branch'))
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addAwardModal">
                                <i class="ti ti-plus"></i> Thêm Mới
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="table-responsive mb-4">
                            <table class="table text-nowrap table-bordered" id="data-table">
                                <thead>
                                    <tr>
                                        <th style="text-align: center !important;">STT</th>
                                        <th>Mã Giải Thưởng</th>
                                        <th>Tên Giải Thưởng</th>
                                        <th style="text-align: center !important;">Số Người Nhận</th>
                                        <th style="text-align: center !important;">Học Kỳ</th>
                                        <th style="text-align: center !important;">Ngày Trao</th>
                                        <th style="text-align: center !important;">Trạng Thái</th>
                                        <th style="text-align: center !important;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($awards as $index => $award)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="fw-semibold">{{ $award->award_code }}</td>
                                            <td>{{ $award->title }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ $award->awardRecipients->count() }}
                                                    người</span>
                                            </td>
                                            <td class="text-center">
                                                @if ($award->semester)
                                                    <span class="badge bg-secondary">{{ $award->semester->name }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ $award->award_date->format('d/m/Y') }}
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-{{ $award->status_color }}">{{ $award->status_label }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="gap-2 fs-15">
                                                    @if (auth()->user()->hasPermissionForModuleCode('award_management', 'view', 'branch'))
                                                        <a href="{{ route('backend.club.awards.show', $award->id) }}"
                                                            class="btn btn-icon btn-sm btn-warning-light"
                                                            title="Quản lý người nhận">
                                                            <i class="ri-user-settings-line"></i>
                                                        </a>
                                                    @endif
                                                    @if (auth()->user()->hasPermissionForModuleCode('award_management', 'edit', 'branch'))
                                                        <button class="btn btn-icon btn-sm btn-info-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editAwardModal{{ $award->id }}"
                                                            title="Chỉnh sửa">
                                                            <i class="ri-edit-line"></i>
                                                        </button>
                                                    @endif
                                                    @if (auth()->user()->hasPermissionForModuleCode('award_management', 'delete', 'branch'))
                                                        <button class="btn btn-icon btn-sm btn-danger-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteAwardModal{{ $award->id }}"
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
        <!--End::row-1 -->

    </div>

    @foreach ($awards as $award)
        <!-- Edit Award Modal -->
        @if (auth()->user()->hasPermissionForModuleCode('award_management', 'edit', 'branch'))
            <div class="modal fade" id="editAwardModal{{ $award->id }}" tabindex="-1"
                aria-labelledby="editAwardModalLabel{{ $award->id }}" aria-hidden="true" data-bs-backdrop="static"
                data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editAwardModalLabel{{ $award->id }}">
                                Chỉnh Sửa Giải Thưởng
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('backend.club.awards.update', $award->id) }}" method="POST"
                            id="editAwardForm{{ $award->id }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_title{{ $award->id }}" class="form-label">
                                                Tên Giải Thưởng <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="edit_title{{ $award->id }}"
                                                name="title" value="{{ $award->title }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_semester_id{{ $award->id }}" class="form-label">
                                                Học Kỳ <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" id="edit_semester_id{{ $award->id }}"
                                                name="semester_id" required>
                                                @foreach ($semesters as $semester)
                                                    <option value="{{ $semester->id }}"
                                                        {{ $award->semester_id === $semester->id ? 'selected' : '' }}>
                                                        {{ $semester->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_award_date{{ $award->id }}" class="form-label">
                                                Ngày Trao <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" class="form-control"
                                                id="edit_award_date{{ $award->id }}" name="award_date"
                                                value="{{ $award->award_date->format('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_status{{ $award->id }}" class="form-label">
                                                Trạng Thái <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" id="edit_status{{ $award->id }}"
                                                name="status" required>
                                                <option value="pending"
                                                    {{ $award->status === 'pending' ? 'selected' : '' }}>
                                                    Đang xét duyệt
                                                </option>
                                                <option value="awarded"
                                                    {{ $award->status === 'awarded' ? 'selected' : '' }}>
                                                    Đã trao
                                                </option>
                                                <option value="revoked"
                                                    {{ $award->status === 'revoked' ? 'selected' : '' }}>
                                                    Đã thu hồi
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_notes{{ $award->id }}" class="form-label">Ghi Chú</label>
                                    <textarea class="form-control" id="edit_notes{{ $award->id }}" name="notes" rows="3">{{ $award->notes }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy"></i>
                                    Cập Nhật
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Delete Award Modal -->
        @if (auth()->user()->hasPermissionForModuleCode('award_management', 'delete', 'branch'))
            <div class="modal fade" id="deleteAwardModal{{ $award->id }}" tabindex="-1"
                aria-labelledby="deleteAwardModalLabel{{ $award->id }}" aria-hidden="true" data-bs-backdrop="static"
                data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteAwardModalLabel{{ $award->id }}">
                                Xác Nhận Xóa
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <p class="mb-2">Bạn có chắc chắn muốn xóa giải thưởng
                                <strong class="text-danger">
                                    {{ $award->title }}
                                </strong> này?
                            </p>
                            <p class="text-danger">
                                <i class="ri-alert-line"></i>
                                Thao tác này không thể hoàn tác và sẽ xóa tất cả thông tin
                                liên quan.
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                            <form action="{{ route('backend.club.awards.destroy', $award->id) }}" method="POST"
                                style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Xóa</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <!-- Add Award Modal -->
    @if (auth()->user()->hasPermissionForModuleCode('award_management', 'create', 'branch'))
        <div class="modal fade" id="addAwardModal" tabindex="-1" aria-labelledby="addAwardModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAwardModalLabel">Thêm Giải Thưởng Mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('backend.club.awards.store') }}" method="POST" id="addAwardForm">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_title" class="form-label">
                                            Tên Giải Thưởng <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="add_title" name="title"
                                            placeholder="VD: Thành viên xuất sắc tháng 3" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_semester_id" class="form-label">
                                            Học Kỳ <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="add_semester_id" name="semester_id" required>
                                            <option value="">Chọn học kỳ</option>
                                            @foreach ($semesters as $semester)
                                                <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_award_date" class="form-label">
                                            Ngày Trao <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" class="form-control" id="add_award_date" name="award_date"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_status" class="form-label">
                                            Trạng Thái <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="add_status" name="status" required>
                                            <option value="">Chọn trạng thái</option>
                                            <option value="pending">Đang xét duyệt</option>
                                            <option value="awarded">Đã trao</option>
                                            <option value="revoked">Đã thu hồi</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="add_notes" class="form-label">Ghi Chú</label>
                                <textarea class="form-control" id="add_notes" name="notes" rows="3" placeholder="Ghi chú nội bộ (nếu có)"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy"></i>
                                Thêm Mới
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
