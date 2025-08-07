{{-- Admin General Settings Partial - Site Information, Logo Management and Address --}}
<form action="{{ route('backend.settings.general.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <!-- Logo Upload (4 loại) -->
    <div class="row mb-4">
        <div class="col-12">
            <label class="form-label fw-semibold">Logo Hệ Thống</label>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Logo Ngang - Light</label>
                    <div class="mb-2">
                        @if (isset($generalSettings['image_horizontal_light']) && $generalSettings['image_horizontal_light'])
                            <img src="{{ Storage::url($generalSettings['image_horizontal_light']) }}"
                                alt="Logo Ngang Light" class="rounded-3"
                                style="width: 100px; height: 100px; object-fit: contain;">
                        @else
                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center"
                                style="width: 100px; height: 100px;"><i class="ti ti-image fs-24 text-muted"></i></div>
                        @endif
                    </div>
                    <input type="file" name="image_horizontal_light" class="form-control" accept="image/*">
                    <small class="text-muted">Ngang - Light (JPG, PNG, GIF, max 2MB)</small>
                    @error('image_horizontal_light')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Logo Ngang - Dark</label>
                    <div class="mb-2">
                        @if (isset($generalSettings['image_horizontal_dark']) && $generalSettings['image_horizontal_dark'])
                            <img src="{{ Storage::url($generalSettings['image_horizontal_dark']) }}"
                                alt="Logo Ngang Dark" class="rounded-3"
                                style="width: 100px; height: 100px; object-fit: contain;">
                        @else
                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center"
                                style="width: 100px; height: 100px;"><i class="ti ti-image fs-24 text-muted"></i></div>
                        @endif
                    </div>
                    <input type="file" name="image_horizontal_dark" class="form-control" accept="image/*">
                    <small class="text-muted">Ngang - Dark (JPG, PNG, GIF, max 2MB)</small>
                    @error('image_horizontal_dark')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Logo Vuông - Light</label>
                    <div class="mb-2">
                        @if (isset($generalSettings['image_square_light']) && $generalSettings['image_square_light'])
                            <img src="{{ Storage::url($generalSettings['image_square_light']) }}" alt="Logo Vuông Light"
                                class="rounded-3" style="width: 100px; height: 100px; object-fit: contain;">
                        @else
                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center"
                                style="width: 100px; height: 100px;"><i class="ti ti-image fs-24 text-muted"></i></div>
                        @endif
                    </div>
                    <input type="file" name="image_square_light" class="form-control" accept="image/*">
                    <small class="text-muted">Vuông - Light (JPG, PNG, GIF, max 2MB)</small>
                    @error('image_square_light')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Logo Vuông - Dark</label>
                    <div class="mb-2">
                        @if (isset($generalSettings['image_square_dark']) && $generalSettings['image_square_dark'])
                            <img src="{{ Storage::url($generalSettings['image_square_dark']) }}" alt="Logo Vuông Dark"
                                class="rounded-3" style="width: 100px; height: 100px; object-fit: contain;">
                        @else
                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center"
                                style="width: 100px; height: 100px;"><i class="ti ti-image fs-24 text-muted"></i></div>
                        @endif
                    </div>
                    <input type="file" name="image_square_dark" class="form-control" accept="image/*">
                    <small class="text-muted">Vuông - Dark (JPG, PNG, GIF, max 2MB)</small>
                    @error('image_square_dark')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
    <!-- Site Information -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Tên Trang Web <span class="text-danger">*</span></label>
            <input type="text" name="site_name" class="form-control"
                value="{{ old('site_name', $generalSettings['site_name'] ?? '') }}" placeholder="Nhập tên trang web">
            @error('site_name')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Email Liên Hệ <span class="text-danger">*</span></label>
            <input type="email" name="site_email" class="form-control"
                value="{{ old('site_email', $generalSettings['site_email'] ?? '') }}" placeholder="Nhập email liên hệ">
            @error('site_email')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Số Điện Thoại <span class="text-danger">*</span></label>
            <input type="text" name="site_phone" class="form-control"
                value="{{ old('site_phone', $generalSettings['site_phone'] ?? '') }}" placeholder="Nhập số điện thoại">
            @error('site_phone')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <!-- Address Selection -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Tỉnh/Thành</label>
            <select class="form-select" id="province_id" name="province_id">
                <option value="">-- Chọn tỉnh/thành --</option>
                @foreach ($provinces as $province)
                    <option value="{{ $province->id }}" @if ($adminAddress && $adminAddress->province_id == $province->id) selected @endif>
                        {{ $province->name }} ({{ $province->code }})</option>
                @endforeach
            </select>
            @error('province_id')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Xã/Phường</label>
            <select class="form-select" id="commune_id" name="commune_id">
                <option value="">-- Chọn xã/phường --</option>
                @if ($adminAddress && $adminAddress->commune)
                    <option value="{{ $adminAddress->commune->id }}" selected>
                        {{ $adminAddress->commune->name }} ({{ $adminAddress->commune->code }})</option>
                @endif
            </select>
            @error('commune_id')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="row">
        <div class="col-12 mb-4">
            <label class="form-label fw-semibold">Địa Chỉ Chi Tiết</label>
            <textarea name="address" class="form-control" rows="3"
                placeholder="Nhập địa chỉ chi tiết (số nhà, đường, phố...)">{{ old('address', $adminAddress ? $adminAddress->address : '') }}</textarea>
            @error('address')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <!-- Submit Button -->
    <div class="row">
        <div class="col-12">
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-device-floppy me-1"></i>
                Cập Nhật Thông Tin
            </button>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý chọn tỉnh và load xã
        const provinceSelect = document.getElementById('province_id');
        const communeSelect = document.getElementById('commune_id');

        if (provinceSelect) {
            provinceSelect.addEventListener('change', function() {
                const provinceId = this.value;

                // Reset commune select
                communeSelect.innerHTML = '<option value="">-- Chọn xã/phường --</option>';

                if (provinceId) {
                    // Load communes via AJAX
                    fetch(`{{ route('backend.settings.get-communes') }}?province_id=${provinceId}`)
                        .then(response => response.json())
                        .then(communes => {
                            communes.forEach(commune => {
                                const option = document.createElement('option');
                                option.value = commune.id;
                                option.textContent = `${commune.name} (${commune.code})`;
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
</script>
