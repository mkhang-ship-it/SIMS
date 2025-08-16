@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Khóa Học</h1>

            <!-- Nút trên desktop -->
            @if (auth()->user()->hasPermissionForModuleCode('course_management', 'create', 'central'))
                <div class="d-none d-md-block">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
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
                    @if (auth()->user()->hasPermissionForModuleCode('course_management', 'create', 'central'))
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addCourseModal">
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
                                        <th>Tên Khóa Học</th>
                                        <th style="text-align: center !important;">Ngày Bắt Đầu</th>
                                        <th style="text-align: center !important;">Ngày Kết Thúc</th>
                                        <th style="text-align: center !important;">Trạng Thái</th>
                                        <th style="text-align: center !important;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($courses as $index => $course)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="fw-semibold">{{ $course->name }}</td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($course->start_date)->format('d/m/Y') }}</td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($course->end_date)->format('d/m/Y') }}</td>
                                            <td class="text-center">
                                                @switch($course->status)
                                                    @case('active')
                                                        <span class="badge bg-success">Hoạt Động</span>
                                                    @break

                                                    @case('inactive')
                                                        <span class="badge bg-warning">Tạm Ngưng</span>
                                                    @break

                                                    @default
                                                        <span class="badge bg-light text-dark">{{ $course->status }}</span>
                                                @endswitch
                                            </td>
                                            <td class="text-center">
                                                <div class=" gap-2 fs-15">
                                                    @if (auth()->user()->hasPermissionForModuleCode('course_management', 'edit', 'central'))
                                                        <button class="btn btn-icon btn-sm btn-info-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editCourseModal{{ $course->id }}"
                                                            title="Chỉnh sửa">
                                                            <i class="ri-edit-line"></i>
                                                        </button>
                                                    @endif
                                                    @if (auth()->user()->hasPermissionForModuleCode('course_management', 'delete', 'central'))
                                                        <button class="btn btn-icon btn-sm btn-danger-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteCourseModal{{ $course->id }}"
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
                        @foreach ($courses as $course)
                            <!-- Edit Course Modal -->
                            @if (auth()->user()->hasPermissionForModuleCode('course_management', 'edit', 'central'))
                                <div class="modal fade" id="editCourseModal{{ $course->id }}" tabindex="-1"
                                    aria-labelledby="editCourseModalLabel{{ $course->id }}" aria-hidden="true"
                                    data-bs-backdrop="static" data-bs-keyboard="false">
                                    <div class="modal-dialog modal-dialog-centered ">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editCourseModalLabel{{ $course->id }}">Chỉnh
                                                    Sửa Khóa
                                                    Học
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('backend.courses.update', $course->id) }}"
                                                method="POST" id="editCourseForm{{ $course->id }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="edit_name{{ $course->id }}" class="form-label">Tên
                                                            Khóa Học <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control"
                                                            id="edit_name{{ $course->id }}" name="name"
                                                            value="{{ $course->name }}" required>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="edit_start_date{{ $course->id }}"
                                                                    class="form-label">Ngày
                                                                    Bắt Đầu <span class="text-danger">*</span></label>
                                                                <input type="date" class="form-control"
                                                                    id="edit_start_date{{ $course->id }}"
                                                                    name="start_date" value="{{ $course->start_date }}"
                                                                    required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="edit_end_date{{ $course->id }}"
                                                                    class="form-label">Ngày
                                                                    Kết Thúc <span class="text-danger">*</span></label>
                                                                <input type="date" class="form-control"
                                                                    id="edit_end_date{{ $course->id }}" name="end_date"
                                                                    value="{{ $course->end_date }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="edit_status{{ $course->id }}"
                                                            class="form-label">Trạng Thái <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-select" id="edit_status{{ $course->id }}"
                                                            name="status" required>
                                                            <option value="">Chọn trạng thái</option>
                                                            <option value="active"
                                                                {{ $course->status == 'active' ? 'selected' : '' }}>
                                                                Hoạt Động</option>
                                                            <option value="inactive"
                                                                {{ $course->status == 'inactive' ? 'selected' : '' }}>
                                                                Tạm Ngưng</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Hủy</button>
                                                    <button type="submit" class="btn btn-primary">Cập
                                                        Nhật</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <!-- Delete Course Modal -->
                            @if (auth()->user()->hasPermissionForModuleCode('course_management', 'delete', 'central'))
                                <div class="modal fade" id="deleteCourseModal{{ $course->id }}" tabindex="-1"
                                    aria-labelledby="deleteCourseModalLabel{{ $course->id }}" aria-hidden="true"
                                    data-bs-backdrop="static" data-bs-keyboard="false">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title text-danger"
                                                    id="deleteCourseModalLabel{{ $course->id }}">
                                                    Xác Nhận Xóa</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="text-center">
                                                    <p class="mb-2">Bạn có chắc chắn muốn xóa khóa học <span
                                                            class="fw-bold text-danger">{{ $course->name }}</span>
                                                        không?
                                                    </p>
                                                </div>
                                            </div>
                                            <form action="{{ route('backend.courses.destroy', $course->id) }}"
                                                method="POST" id="deleteCourseForm{{ $course->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Hủy</button>
                                                    <button type="submit" class="btn btn-danger">Xóa</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <!--End::row-1 -->

    </div>

    <!-- Add Course Modal -->
    @if (auth()->user()->hasPermissionForModuleCode('course_management', 'create', 'central'))
        <div class="modal fade" id="addCourseModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="addCourseModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCourseModalLabel">Thêm Khóa Học Mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('backend.courses.store') }}" method="POST" id="addCourseForm">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="add_name" class="form-label">Tên Khóa Học <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_name" name="name" required
                                    value="{{ old('name') }}">
                                @error('name')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_start_date" class="form-label">Ngày Bắt Đầu <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="add_start_date" name="start_date"
                                            required value="{{ old('start_date') }}">
                                        @error('start_date')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_end_date" class="form-label">Ngày Kết Thúc <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="add_end_date" name="end_date"
                                            required value="{{ old('end_date') }}">
                                        @error('end_date')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="add_status" class="form-label">Trạng Thái <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="add_status" name="status" required>
                                    <option value="">Chọn trạng thái</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hoạt Động
                                    </option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tạm Ngưng
                                    </option>
                                </select>
                                @error('status')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary">Thêm Mới</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
