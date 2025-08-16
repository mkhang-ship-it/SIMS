@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Câu Lạc Bộ</h1>

            <!-- Nút trên desktop -->
            @if (auth()->user()->hasPermissionForModuleCode('club_management', 'create', 'central'))
                <div class="d-none d-md-block">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClubModal">
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
                    @if (auth()->user()->hasPermissionForModuleCode('club_management', 'create', 'central'))
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addClubModal">
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
                                        <th>Tên Câu Lạc Bộ</th>
                                        <th style="text-align: center !important;">Email</th>
                                        <th style="text-align: center !important;">Điện Thoại</th>
                                        <th style="text-align: center !important;">Địa Chỉ</th>
                                        <th style="text-align: center !important;">Trạng Thái</th>
                                        <th style="text-align: center !important;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($clubs as $index => $club)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="fw-semibold">{{ $club->name }}</td>
                                            <td class="text-center">{{ $club->email }}</td>
                                            <td class="text-center">{{ $club->phone }}</td>
                                            <td class="text-center">
                                                {{ $club->address ? $club->address->full_address : '-' }}</td>
                                            <td class="text-center">
                                                @switch($club->status)
                                                    @case('active')
                                                        <span class="badge bg-success">Hoạt Động</span>
                                                    @break

                                                    @case('inactive')
                                                        <span class="badge bg-warning">Tạm Ngưng</span>
                                                    @break

                                                    @default
                                                        <span class="badge bg-light text-dark">{{ $club->status }}</span>
                                                @endswitch
                                            </td>
                                            <td class="text-center">
                                                <div class=" gap-2 fs-15">
                                                    @if (auth()->user()->hasPermissionForModuleCode('club_management', 'view', 'central'))
                                                        <a href="{{ route('backend.clubs.show', $club->id) }}"
                                                            class="btn btn-icon btn-sm btn-primary-light" title="Xem">
                                                            <i class="ri-eye-line"></i>
                                                        </a>
                                                    @endif
                                                    @if (auth()->user()->hasPermissionForModuleCode('club_management', 'edit', 'central'))
                                                        <button class="btn btn-icon btn-sm btn-info-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editClubModal{{ $club->id }}"
                                                            title="Chỉnh sửa">
                                                            <i class="ri-edit-line"></i>
                                                        </button>
                                                    @endif
                                                    @if (auth()->user()->hasPermissionForModuleCode('club_management', 'delete', 'central'))
                                                        <button class="btn btn-icon btn-sm btn-danger-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteClubModal{{ $club->id }}"
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
                        @foreach ($clubs as $club)
                            <!-- Edit Club Modal -->
                            @if (auth()->user()->hasPermissionForModuleCode('club_management', 'edit', 'central'))
                                <div class="modal fade" id="editClubModal{{ $club->id }}" tabindex="-1"
                                    aria-labelledby="editClubModalLabel{{ $club->id }}" aria-hidden="true"
                                    data-bs-backdrop="static" data-bs-keyboard="false">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editClubModalLabel{{ $club->id }}">
                                                    Chỉnh Sửa Câu Lạc Bộ
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('backend.clubs.update', $club->id) }}" method="POST"
                                                id="editClubForm{{ $club->id }}" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="edit_name{{ $club->id }}" class="form-label">Tên
                                                            Câu Lạc Bộ <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control"
                                                            id="edit_name{{ $club->id }}" name="name"
                                                            value="{{ $club->name }}" required>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="edit_email{{ $club->id }}"
                                                                    class="form-label">Email <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="email" class="form-control"
                                                                    id="edit_email{{ $club->id }}" name="email"
                                                                    value="{{ $club->email }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="edit_phone{{ $club->id }}"
                                                                    class="form-label">Điện Thoại <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    id="edit_phone{{ $club->id }}" name="phone"
                                                                    value="{{ $club->phone }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Address Selection -->
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="edit_province_id{{ $club->id }}"
                                                                    class="form-label">Tỉnh/Thành <span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-select"
                                                                    id="edit_province_id{{ $club->id }}"
                                                                    name="province_id" required>
                                                                    <option value="">-- Chọn tỉnh/thành
                                                                        --</option>
                                                                    @foreach ($provinces as $province)
                                                                        <option value="{{ $province->id }}"
                                                                            @if ($club->address && $club->address->province_id == $province->id) selected @endif>
                                                                            {{ $province->name }}
                                                                            ({{ $province->code }})
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="edit_commune_id{{ $club->id }}"
                                                                    class="form-label">Xã/Phường <span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-select"
                                                                    id="edit_commune_id{{ $club->id }}"
                                                                    name="commune_id" required>
                                                                    <option value="">-- Chọn xã/phường --
                                                                    </option>
                                                                    @if ($club->address && $club->address->commune)
                                                                        <option value="{{ $club->address->commune->id }}"
                                                                            selected>
                                                                            {{ $club->address->commune->name }}
                                                                            ({{ $club->address->commune->code }})
                                                                        </option>
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="edit_address{{ $club->id }}"
                                                            class="form-label">Địa Chỉ Chi Tiết <span
                                                                class="text-danger">*</span></label>
                                                        <textarea class="form-control" id="edit_address{{ $club->id }}" name="address" rows="3"
                                                            placeholder="Nhập địa chỉ chi tiết (số nhà, đường, phố...)" required>{{ $club->address ? $club->address->address : '' }}</textarea>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="edit_status{{ $club->id }}"
                                                                    class="form-label">Trạng Thái <span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-select"
                                                                    id="edit_status{{ $club->id }}" name="status"
                                                                    required>
                                                                    <option value="">Chọn trạng thái
                                                                    </option>
                                                                    <option value="active"
                                                                        {{ $club->status == 'active' ? 'selected' : '' }}>
                                                                        Hoạt Động</option>
                                                                    <option value="inactive"
                                                                        {{ $club->status == 'inactive' ? 'selected' : '' }}>
                                                                        Tạm Ngưng</option>
                                                                </select>
                                                            </div>
                                                        </div>

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
                            <!-- Delete Club Modal -->
                            @if (auth()->user()->hasPermissionForModuleCode('club_management', 'delete', 'central'))
                                <div class="modal fade" id="deleteClubModal{{ $club->id }}" tabindex="-1"
                                    aria-labelledby="deleteClubModalLabel{{ $club->id }}" aria-hidden="true"
                                    data-bs-backdrop="static" data-bs-keyboard="false">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title text-danger"
                                                    id="deleteClubModalLabel{{ $club->id }}">
                                                    Xác Nhận Xóa</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="text-center">
                                                    <p class="mb-2">Bạn có chắc chắn muốn xóa câu lạc bộ
                                                        <span class="fw-bold text-danger">{{ $club->name }}</span>
                                                        không?
                                                    </p>
                                                    @if ($club->image)
                                                        <img src="{{ asset('storage/' . $club->image) }}"
                                                            alt="{{ $club->name }}" class="rounded mb-2"
                                                            style="width: 100px; height: 100px; object-fit: cover;">
                                                    @endif
                                                </div>
                                            </div>
                                            <form action="{{ route('backend.clubs.destroy', $club->id) }}" method="POST"
                                                id="deleteClubForm{{ $club->id }}">
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

    <!-- Add Club Modal -->
    @if (auth()->user()->hasPermissionForModuleCode('club_management', 'create', 'central'))
        <div class="modal fade" id="addClubModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="addClubModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addClubModalLabel">Thêm Câu Lạc Bộ Mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('backend.clubs.store') }}" method="POST" id="addClubForm"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="add_name" class="form-label">Tên Câu Lạc Bộ <span
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
                                        <label for="add_email" class="form-label">Email <span
                                                class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="add_email" name="email"
                                            required value="{{ old('email') }}">
                                        @error('email')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_phone" class="form-label">Điện Thoại <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="add_phone" name="phone"
                                            required value="{{ old('phone') }}">
                                        @error('phone')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- Address Selection -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_province_id" class="form-label">Tỉnh/Thành <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="add_province_id" name="province_id" required>
                                            <option value="">-- Chọn tỉnh/thành --</option>
                                            @foreach ($provinces as $province)
                                                <option value="{{ $province->id }}"
                                                    {{ old('province_id') == $province->id ? 'selected' : '' }}>
                                                    {{ $province->name }} ({{ $province->code }})</option>
                                            @endforeach
                                        </select>
                                        @error('province_id')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_commune_id" class="form-label">Xã/Phường <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="add_commune_id" name="commune_id" required>
                                            <option value="">-- Chọn xã/phường --</option>
                                        </select>
                                        @error('commune_id')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="add_address" class="form-label">Địa Chỉ Chi Tiết <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="add_address" name="address" rows="3"
                                    placeholder="Nhập địa chỉ chi tiết (số nhà, đường, phố...)" required>{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_status" class="form-label">Trạng Thái <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="add_status" name="status" required>
                                            <option value="">Chọn trạng thái</option>
                                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hoạt
                                                Động
                                            </option>
                                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                                Tạm
                                                Ngưng
                                            </option>
                                        </select>
                                        @error('status')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Xử lý cho modal Add Club
            const addProvinceSelect = document.getElementById('add_province_id');
            const addCommuneSelect = document.getElementById('add_commune_id');

            if (addProvinceSelect) {
                addProvinceSelect.addEventListener('change', function() {
                    const provinceId = this.value;

                    // Reset commune select
                    addCommuneSelect.innerHTML = '<option value="">-- Chọn xã/phường --</option>';

                    if (provinceId) {
                        // Load communes via AJAX
                        fetch(`{{ route('backend.clubs.get-communes') }}?province_id=${provinceId}`)
                            .then(response => response.json())
                            .then(communes => {
                                communes.forEach(commune => {
                                    const option = document.createElement('option');
                                    option.value = commune.id;
                                    option.textContent = `${commune.name} (${commune.code})`;
                                    addCommuneSelect.appendChild(option);
                                });
                            })
                            .catch(error => {
                                console.error('Error loading communes:', error);
                            });
                    }
                });
            }

            // Xử lý cho modal Edit Club
            const editProvinceSelects = document.querySelectorAll('[id^="edit_province_id"]');
            editProvinceSelects.forEach(provinceSelect => {
                const clubId = provinceSelect.id.replace('edit_province_id', '');
                const communeSelect = document.getElementById(`edit_commune_id${clubId}`);

                if (communeSelect) {
                    provinceSelect.addEventListener('change', function() {
                        const provinceId = this.value;

                        // Reset commune select
                        communeSelect.innerHTML = '<option value="">-- Chọn xã/phường --</option>';

                        if (provinceId) {
                            // Load communes via AJAX
                            fetch(
                                    `{{ route('backend.clubs.get-communes') }}?province_id=${provinceId}`
                                )
                                .then(response => response.json())
                                .then(communes => {
                                    communes.forEach(commune => {
                                        const option = document.createElement('option');
                                        option.value = commune.id;
                                        option.textContent =
                                            `${commune.name} (${commune.code})`;
                                        communeSelect.appendChild(option);
                                    });
                                })
                                .catch(error => {
                                    console.error('Error loading communes:', error);
                                });
                        }
                    });
                }
            });
        });
    </script>
@endsection
