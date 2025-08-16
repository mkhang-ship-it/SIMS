@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Ca Học</h1>

            <!-- Nút trên desktop -->
            @if (auth()->user()->hasPermissionForModuleCode('slot_management', 'create', 'central'))
                <div class="d-none d-md-block">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSlotModal">
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
                    @if (auth()->user()->hasPermissionForModuleCode('slot_management', 'create', 'central'))
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addSlotModal">
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
                                        <th>Tên Ca Học</th>
                                        <th style="text-align: center !important;">Giờ Bắt Đầu</th>
                                        <th style="text-align: center !important;">Giờ Kết Thúc</th>
                                        <th style="text-align: center !important;">Trạng Thái</th>
                                        <th style="text-align: center !important;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($slots as $index => $slot)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="fw-semibold">{{ $slot->name }}</td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}</td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</td>
                                            <td class="text-center">
                                                @switch($slot->status)
                                                    @case('active')
                                                        <span class="badge bg-success">Hoạt Động</span>
                                                    @break

                                                    @case('inactive')
                                                        <span class="badge bg-warning">Tạm Ngưng</span>
                                                    @break

                                                    @default
                                                        <span class="badge bg-light text-dark">{{ $slot->status }}</span>
                                                @endswitch
                                            </td>
                                            <td class="text-center">
                                                <div class=" gap-2 fs-15">
                                                    @if (auth()->user()->hasPermissionForModuleCode('slot_management', 'edit', 'central'))
                                                        <button class="btn btn-icon btn-sm btn-info-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editSlotModal{{ $slot->id }}"
                                                            title="Chỉnh sửa">
                                                            <i class="ri-edit-line"></i>
                                                        </button>
                                                    @endif
                                                    @if (auth()->user()->hasPermissionForModuleCode('slot_management', 'delete', 'central'))
                                                        <button class="btn btn-icon btn-sm btn-danger-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteSlotModal{{ $slot->id }}"
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

    @foreach ($slots as $slot)
        <!-- Edit Slot Modal -->
        @if (auth()->user()->hasPermissionForModuleCode('slot_management', 'edit', 'central'))
            <div class="modal fade" id="editSlotModal{{ $slot->id }}" tabindex="-1"
                aria-labelledby="editSlotModalLabel{{ $slot->id }}" aria-hidden="true" data-bs-backdrop="static"
                data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editSlotModalLabel{{ $slot->id }}">
                                Chỉnh Sửa Ca Học
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('backend.slots.update', $slot->id) }}" method="POST"
                            id="editSlotForm{{ $slot->id }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="edit_name{{ $slot->id }}" class="form-label">Tên Ca Học <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_name{{ $slot->id }}"
                                        name="name" value="{{ $slot->name }}" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_start_time{{ $slot->id }}" class="form-label">Giờ
                                                Bắt Đầu <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control"
                                                id="edit_start_time{{ $slot->id }}" name="start_time"
                                                value="{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}"
                                                step="60" pattern="[0-9]{2}:[0-9]{2}" placeholder="HH:MM" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_end_time{{ $slot->id }}" class="form-label">Giờ
                                                Kết Thúc <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control"
                                                id="edit_end_time{{ $slot->id }}" name="end_time"
                                                value="{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}"
                                                step="60" pattern="[0-9]{2}:[0-9]{2}" placeholder="HH:MM" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_status{{ $slot->id }}" class="form-label">Trạng Thái <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="edit_status{{ $slot->id }}" name="status"
                                        required>
                                        <option value="">Chọn trạng thái</option>
                                        <option value="active" {{ $slot->status == 'active' ? 'selected' : '' }}>
                                            Hoạt Động</option>
                                        <option value="inactive" {{ $slot->status == 'inactive' ? 'selected' : '' }}>
                                            Tạm Ngưng</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-primary">Cập
                                    Nhật</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
        <!-- Delete Slot Modal -->
        @if (auth()->user()->hasPermissionForModuleCode('slot_management', 'delete', 'central'))
            <div class="modal fade" id="deleteSlotModal{{ $slot->id }}" tabindex="-1"
                aria-labelledby="deleteSlotModalLabel{{ $slot->id }}" aria-hidden="true" data-bs-backdrop="static"
                data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-danger" id="deleteSlotModalLabel{{ $slot->id }}">
                                Xác Nhận Xóa</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center">
                                <p class="mb-2">Bạn có chắc chắn muốn xóa ca học <span
                                        class="fw-bold text-danger">{{ $slot->name }}</span>
                                    không?
                                </p>
                            </div>
                        </div>
                        <form action="{{ route('backend.slots.destroy', $slot->id) }}" method="POST"
                            id="deleteSlotForm{{ $slot->id }}">
                            @csrf
                            @method('DELETE')
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-danger">Xóa</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <!-- Add Slot Modal -->
    @if (auth()->user()->hasPermissionForModuleCode('slot_management', 'create', 'central'))
        <div class="modal fade" id="addSlotModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="addSlotModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addSlotModalLabel">Thêm Ca Học Mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('backend.slots.store') }}" method="POST" id="addSlotForm">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="add_name" class="form-label">Tên Ca Học <span
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
                                        <label for="add_start_time" class="form-label">Giờ Bắt Đầu <span
                                                class="text-danger">*</span></label>
                                        <input type="time" class="form-control" id="add_start_time" name="start_time"
                                            step="60" pattern="[0-9]{2}:[0-9]{2}" placeholder="HH:MM" required
                                            value="{{ old('start_time') }}">
                                        @error('start_time')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_end_time" class="form-label">Giờ Kết Thúc <span
                                                class="text-danger">*</span></label>
                                        <input type="time" class="form-control" id="add_end_time" name="end_time"
                                            step="60" pattern="[0-9]{2}:[0-9]{2}" placeholder="HH:MM" required
                                            value="{{ old('end_time') }}">
                                        @error('end_time')
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
