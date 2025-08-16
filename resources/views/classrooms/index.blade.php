{{-- Updated ClassRoom view - Added longitude and latitude fields for location tracking with current location feature --}}
{{-- Adapted for ClassRoom model with name, code, status, longitude, and latitude fields --}}
{{-- Code field is display-only since it's auto-generated --}}
{{-- Longitude and latitude are optional string fields for flexible classroom location with geolocation support --}}

@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Phòng Học</h1>

            <!-- Nút trên desktop -->
            @if (auth()->user()->hasPermissionForModuleCode('classroom_management', 'create', 'central'))
                <div class="d-none d-md-block">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClassRoomModal">
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
                    @if (auth()->user()->hasPermissionForModuleCode('classroom_management', 'create', 'central'))
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                data-bs-target="#addClassRoomModal">
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
                                        <th>Tên Phòng Học</th>
                                        <th style="text-align: center !important;">Mã Phòng</th>
                                        <th style="text-align: center !important;">Kinh Độ</th>
                                        <th style="text-align: center !important;">Vĩ Độ</th>
                                        <th style="text-align: center !important;">Trạng Thái</th>
                                        <th style="text-align: center !important;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($classrooms as $index => $classroom)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="fw-semibold">{{ $classroom->name }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ $classroom->code }}</span>
                                            </td>
                                            <td class="text-center">
                                                {{ $classroom->longitude ?: '-' }}
                                            </td>
                                            <td class="text-center">
                                                {{ $classroom->latitude ?: '-' }}
                                            </td>
                                            <td class="text-center">
                                                @switch($classroom->status)
                                                    @case('active')
                                                        <span class="badge bg-success">Hoạt Động</span>
                                                    @break

                                                    @case('inactive')
                                                        <span class="badge bg-warning">Tạm Ngưng</span>
                                                    @break

                                                    @default
                                                        <span class="badge bg-light text-dark">{{ $classroom->status }}</span>
                                                @endswitch
                                            </td>
                                            <td class="text-center">
                                                <div class=" gap-2 fs-15">
                                                    @if (auth()->user()->hasPermissionForModuleCode('classroom_management', 'edit', 'central'))
                                                        <button class="btn btn-icon btn-sm btn-info-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editClassRoomModal{{ $classroom->id }}"
                                                            title="Chỉnh sửa">
                                                            <i class="ri-edit-line"></i>
                                                        </button>
                                                    @endif
                                                    @if (auth()->user()->hasPermissionForModuleCode('classroom_management', 'delete', 'central'))
                                                        <button class="btn btn-icon btn-sm btn-danger-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteClassRoomModal{{ $classroom->id }}"
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
                        @foreach ($classrooms as $classroom)
                            <!-- Edit ClassRoom Modal -->
                            @if (auth()->user()->hasPermissionForModuleCode('classroom_management', 'edit', 'central'))
                                <div class="modal fade" id="editClassRoomModal{{ $classroom->id }}" tabindex="-1"
                                    aria-labelledby="editClassRoomModalLabel{{ $classroom->id }}" aria-hidden="true"
                                    data-bs-backdrop="static" data-bs-keyboard="false">
                                    <div class="modal-dialog modal-dialog-centered ">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editClassRoomModalLabel{{ $classroom->id }}">
                                                    Chỉnh Sửa
                                                    Phòng
                                                    Học
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('backend.classrooms.update', $classroom->id) }}"
                                                method="POST" id="editClassRoomForm{{ $classroom->id }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="edit_name{{ $classroom->id }}" class="form-label">Tên
                                                            Phòng Học <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control"
                                                            id="edit_name{{ $classroom->id }}" name="name"
                                                            value="{{ $classroom->name }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="edit_code{{ $classroom->id }}" class="form-label">Mã
                                                            Phòng</label>
                                                        <input type="text" class="form-control"
                                                            id="edit_code{{ $classroom->id }}"
                                                            value="{{ $classroom->code }}" disabled>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="edit_longitude{{ $classroom->id }}"
                                                                    class="form-label">Kinh Độ <span
                                                                        class="text-danger">*</span></label>
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control"
                                                                        id="edit_longitude{{ $classroom->id }}"
                                                                        name="longitude"
                                                                        value="{{ $classroom->longitude }}"
                                                                        placeholder="Ví dụ: 105.851067" required>
                                                                    <button class="btn btn-outline-success" type="button"
                                                                        onclick="getCurrentLocation('edit', '{{ $classroom->id }}')"
                                                                        title="Lấy vị trí hiện tại">
                                                                        <i class="ri-map-pin-line"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="edit_latitude{{ $classroom->id }}"
                                                                    class="form-label">Vĩ Độ <span
                                                                        class="text-danger">*</span></label>
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control"
                                                                        id="edit_latitude{{ $classroom->id }}"
                                                                        name="latitude"
                                                                        value="{{ $classroom->latitude }}"
                                                                        placeholder="Ví dụ: 21.034888" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="edit_status{{ $classroom->id }}"
                                                            class="form-label">Trạng Thái <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-select" id="edit_status{{ $classroom->id }}"
                                                            name="status" required>
                                                            <option value="">Chọn trạng thái</option>
                                                            <option value="active"
                                                                {{ $classroom->status == 'active' ? 'selected' : '' }}>
                                                                Hoạt Động</option>
                                                            <option value="inactive"
                                                                {{ $classroom->status == 'inactive' ? 'selected' : '' }}>
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
                            <!-- Delete ClassRoom Modal -->
                            @if (auth()->user()->hasPermissionForModuleCode('classroom_management', 'delete', 'central'))
                                <div class="modal fade" id="deleteClassRoomModal{{ $classroom->id }}" tabindex="-1"
                                    aria-labelledby="deleteClassRoomModalLabel{{ $classroom->id }}" aria-hidden="true"
                                    data-bs-backdrop="static" data-bs-keyboard="false">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title text-danger"
                                                    id="deleteClassRoomModalLabel{{ $classroom->id }}">
                                                    Xác Nhận Xóa</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="text-center">
                                                    <p class="mb-2">Bạn có chắc chắn muốn xóa phòng học <span
                                                            class="fw-bold text-danger">{{ $classroom->name }}</span>
                                                        không?
                                                    </p>
                                                </div>
                                            </div>
                                            <form action="{{ route('backend.classrooms.destroy', $classroom->id) }}"
                                                method="POST" id="deleteClassRoomForm{{ $classroom->id }}">
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

    <!-- Add ClassRoom Modal -->
    @if (auth()->user()->hasPermissionForModuleCode('classroom_management', 'create', 'central'))
        <div class="modal fade" id="addClassRoomModal" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="addClassRoomModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addClassRoomModalLabel">Thêm Phòng Học Mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('backend.classrooms.store') }}" method="POST" id="addClassRoomForm">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="add_name" class="form-label">Tên Phòng Học <span
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
                                        <label for="add_longitude" class="form-label">Kinh Độ <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="add_longitude"
                                                name="longitude" value="{{ old('longitude') }}"
                                                placeholder="Ví dụ: 105.851067" required>
                                            <button class="btn btn-outline-success" type="button"
                                                onclick="getCurrentLocation('add')" title="Lấy vị trí hiện tại">
                                                <i class="ri-map-pin-line"></i>
                                            </button>
                                        </div>
                                        @error('longitude')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_latitude" class="form-label">Vĩ Độ <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="add_latitude" name="latitude"
                                            value="{{ old('latitude') }}" required placeholder="Ví dụ: 21.034888">
                                        @error('latitude')
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

    <script>
        function getCurrentLocation(mode, classroomId = null) {
            if (!navigator.geolocation) {
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const longitude = position.coords.longitude;
                    const latitude = position.coords.latitude;

                    // Điền vào các trường input
                    if (mode === 'add') {
                        document.getElementById('add_longitude').value = longitude.toFixed(6);
                        document.getElementById('add_latitude').value = latitude.toFixed(6);
                    } else {
                        document.getElementById(`edit_longitude${classroomId}`).value = longitude.toFixed(6);
                        document.getElementById(`edit_latitude${classroomId}`).value = latitude.toFixed(6);
                    }
                },
                function(error) {
                    // Không hiển thị thông báo lỗi
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 60000
                }
            );
        }
    </script>
@endsection
