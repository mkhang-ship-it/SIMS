{{-- Refactored: Đồng bộ với SettingController mới, sử dụng session('club_id'), lấy dữ liệu club trực tiếp, word template và bank info từ Setting model. Đã xoá trường Swift Code khỏi phần thông tin ngân hàng. --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">

        <!-- Start::row-1 -->
        <div class="row">
            <!-- Sidebar Tabs -->
            <div class="col-xl-3">
                <div class="card custom-card">
                    <div class="card-header">
                        <h5 class="card-title">Danh Mục Cài Đặt</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush" id="settings-tab">
                            @if (auth()->user()->hasPermissionForModuleCode('setting_management', 'general', 'branch'))
                                <button class="list-group-item list-group-item-action active" id="tab-general-btn"
                                    data-bs-toggle="tab" data-bs-target="#tab-general" type="button">Thông Tin
                                    Chung</button>
                            @endif
                            @if (auth()->user()->hasPermissionForModuleCode('setting_management', 'word_template', 'branch'))
                                <button class="list-group-item list-group-item-action" id="tab-word-template-btn"
                                    data-bs-toggle="tab" data-bs-target="#tab-word-template" type="button">Mẫu
                                    Word</button>
                            @endif
                            @if (auth()->user()->hasPermissionForModuleCode('setting_management', 'payment', 'branch'))
                                <button class="list-group-item list-group-item-action" id="tab-payment-btn"
                                    data-bs-toggle="tab" data-bs-target="#tab-payment" type="button">Thông Tin Thanh
                                    Toán</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Section -->
            <div class="col-xl-9">
                <div class="card custom-card">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                        <div class="card-title fw-semibold fs-18 mb-2 mb-md-0" id="tab-title">
                            Thông Tin Chung
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            @if (auth()->user()->hasPermissionForModuleCode('setting_management', 'general', 'branch'))
                                <div class="tab-pane fade show active" id="tab-general">
                                    <form action="{{ route('backend.club.settings.general.update') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf

                                        <!-- Logo Upload (4 loại) -->
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Logo Câu Lạc Bộ</label>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Logo Ngang - Light</label>
                                                        <div class="mb-2">
                                                            @if ($club && $club->image_horizontal_light)
                                                                <img src="{{ Storage::url($club->image_horizontal_light) }}"
                                                                    alt="Logo Ngang Light" class="rounded-3"
                                                                    style="width: 100px; height: 100px; object-fit: contain;">
                                                            @else
                                                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center"
                                                                    style="width: 100px; height: 100px;"><i
                                                                        class="ti ti-image fs-24 text-muted"></i></div>
                                                            @endif
                                                        </div>
                                                        <input type="file" name="image_horizontal_light"
                                                            class="form-control" accept="image/*">
                                                        <small class="text-muted">Ngang - Light (JPG, PNG, GIF, max
                                                            2MB)</small>
                                                        @error('image_horizontal_light')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Logo Ngang - Dark</label>
                                                        <div class="mb-2">
                                                            @if ($club && $club->image_horizontal_dark)
                                                                <img src="{{ Storage::url($club->image_horizontal_dark) }}"
                                                                    alt="Logo Ngang Dark" class="rounded-3"
                                                                    style="width: 100px; height: 100px; object-fit: contain;">
                                                            @else
                                                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center"
                                                                    style="width: 100px; height: 100px;"><i
                                                                        class="ti ti-image fs-24 text-muted"></i></div>
                                                            @endif
                                                        </div>
                                                        <input type="file" name="image_horizontal_dark"
                                                            class="form-control" accept="image/*">
                                                        <small class="text-muted">Ngang - Dark (JPG, PNG, GIF, max
                                                            2MB)</small>
                                                        @error('image_horizontal_dark')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Logo Vuông - Light</label>
                                                        <div class="mb-2">
                                                            @if ($club && $club->image_square_light)
                                                                <img src="{{ Storage::url($club->image_square_light) }}"
                                                                    alt="Logo Vuông Light" class="rounded-3"
                                                                    style="width: 100px; height: 100px; object-fit: contain;">
                                                            @else
                                                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center"
                                                                    style="width: 100px; height: 100px;"><i
                                                                        class="ti ti-image fs-24 text-muted"></i></div>
                                                            @endif
                                                        </div>
                                                        <input type="file" name="image_square_light" class="form-control"
                                                            accept="image/*">
                                                        <small class="text-muted">Vuông - Light (JPG, PNG, GIF, max
                                                            2MB)</small>
                                                        @error('image_square_light')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Logo Vuông - Dark</label>
                                                        <div class="mb-2">
                                                            @if ($club && $club->image_square_dark)
                                                                <img src="{{ Storage::url($club->image_square_dark) }}"
                                                                    alt="Logo Vuông Dark" class="rounded-3"
                                                                    style="width: 100px; height: 100px; object-fit: contain;">
                                                            @else
                                                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center"
                                                                    style="width: 100px; height: 100px;"><i
                                                                        class="ti ti-image fs-24 text-muted"></i></div>
                                                            @endif
                                                        </div>
                                                        <input type="file" name="image_square_dark" class="form-control"
                                                            accept="image/*">
                                                        <small class="text-muted">Vuông - Dark (JPG, PNG, GIF, max
                                                            2MB)</small>
                                                        @error('image_square_dark')
                                                            <div class="text-danger mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Club Information -->
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Tên Câu Lạc Bộ <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control"
                                                    value="{{ old('name', $club->name ?? '') }}"
                                                    placeholder="Nhập tên câu lạc bộ">
                                                @error('name')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Email <span
                                                        class="text-danger">*</span></label>
                                                <input type="email" name="email" class="form-control"
                                                    value="{{ old('email', $club->email ?? '') }}"
                                                    placeholder="Nhập email">
                                                @error('email')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Số Điện Thoại <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="phone" class="form-control"
                                                    value="{{ old('phone', $club->phone ?? '') }}"
                                                    placeholder="Nhập số điện thoại">
                                                @error('phone')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Trạng Thái</label>
                                                <select class="form-select" disabled>
                                                    <option value="active"
                                                        {{ ($club->status ?? '') == 'active' ? 'selected' : '' }}>Hoạt động
                                                    </option>
                                                    <option value="inactive"
                                                        {{ ($club->status ?? '') == 'inactive' ? 'selected' : '' }}>Không
                                                        hoạt
                                                        động</option>
                                                </select>
                                                <small class="text-muted">Trạng thái chỉ có thể thay đổi bởi quản trị
                                                    viên</small>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12 mb-4">
                                                <label class="form-label fw-semibold">Địa Chỉ <span
                                                        class="text-danger">*</span></label>
                                                <textarea name="address" class="form-control" rows="3" placeholder="Nhập địa chỉ câu lạc bộ">{{ old('address', $club->address ?? '') }}</textarea>
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
                                </div>
                            @endif
                            @if (auth()->user()->hasPermissionForModuleCode('setting_management', 'word_template', 'branch'))
                                <div class="tab-pane fade" id="tab-word-template">
                                    <form action="{{ route('backend.club.settings.word-commitment-template.update') }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf

                                        <!-- Current Template Display -->
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Mẫu Bản Cam Kết Hiện Tại</label>
                                                <div class="d-flex align-items-center gap-3">
                                                    @if ($wordCommitmentTemplate)
                                                        <div
                                                            class="bg-light rounded-3 d-flex align-items-center justify-content-center p-3">
                                                            <i class="ti ti-file-text fs-24 text-primary me-2"></i>
                                                            <span class="fw-semibold">File mẫu đã được tải lên</span>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <small class="text-muted">File mẫu bản cam kết đã được cấu hình
                                                                cho câu lạc bộ</small>
                                                        </div>
                                                        <div>
                                                            <a href="{{ route('backend.club.settings.word-commitment-template.download') }}"
                                                                class="btn btn-outline-primary btn-sm">
                                                                <i class="ti ti-download me-1"></i>
                                                                Tải Xuống
                                                            </a>
                                                        </div>
                                                    @else
                                                        <div
                                                            class="bg-light rounded-3 d-flex align-items-center justify-content-center p-3">
                                                            <i class="ti ti-file-off fs-24 text-muted me-2"></i>
                                                            <span class="text-muted">Chưa có file mẫu</span>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <small class="text-muted">Vui lòng tải lên file mẫu bản cam
                                                                kết</small>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- File Upload -->
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Tải Lên File Mẫu Bản Cam Kết <span
                                                        class="text-danger">*</span></label>
                                                <input type="file" name="word_commitment_template"
                                                    class="form-control" accept=".docx" required>
                                                <small class="text-muted">Định dạng: DOCX. Kích thước tối đa: 5MB</small>
                                                @error('word_commitment_template')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Instructions -->
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <div class="alert alert-info">
                                                    <div class="d-flex align-items-start">
                                                        <i class="ti ti-info-circle fs-18 me-2"></i>
                                                        <div>
                                                            <h6 class="alert-heading mb-2">Hướng Dẫn Sử Dụng</h6>
                                                            <ul class="mb-0">
                                                                <li>File mẫu phải có định dạng DOCX (Microsoft Word)</li>
                                                                <li>Kích thước file không được vượt quá 5MB</li>
                                                                <li>File mẫu sẽ được sử dụng để tạo bản cam kết cho thành
                                                                    viên</li>
                                                                <li>Có thể sử dụng các placeholder như {member_name},
                                                                    {club_name}, {date} trong file mẫu</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="row">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="ti ti-upload me-1"></i>
                                                    Cập Nhật File Mẫu
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endif
                            @if (auth()->user()->hasPermissionForModuleCode('setting_management', 'payment', 'branch'))
                                <div class="tab-pane fade" id="tab-payment">
                                    <form action="{{ route('backend.club.settings.bank.update') }}" method="POST"
                                        id="bank-form">
                                        @csrf

                                        <!-- Bank Selection -->
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Chọn Ngân Hàng <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select" id="bank-select" name="bank_name" required>
                                                    <option value="">-- Chọn ngân hàng --</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Bank Information (Auto-filled) -->
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Tên Ngân Hàng <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="bank_name" id="bank_name"
                                                    class="form-control"
                                                    value="{{ old('bank_name', $bankSettings['bank_name'] ?? '') }}"
                                                    readonly required>
                                                @error('bank_name')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Mã Ngân Hàng <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="bank_code" id="bank_code"
                                                    class="form-control"
                                                    value="{{ old('bank_code', $bankSettings['bank_code'] ?? '') }}"
                                                    readonly required>
                                                @error('bank_code')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">BIN <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="bank_bin" id="bank_bin"
                                                    class="form-control"
                                                    value="{{ old('bank_bin', $bankSettings['bank_bin'] ?? '') }}"
                                                    readonly required>
                                                @error('bank_bin')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Tên Viết Tắt <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="bank_short_name" id="bank_short_name"
                                                    class="form-control"
                                                    value="{{ old('bank_short_name', $bankSettings['bank_short_name'] ?? '') }}"
                                                    readonly required>
                                                @error('bank_short_name')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <hr>

                                        <!-- Account Information -->
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Tên Chủ Tài Khoản <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="bank_account_name" id="bank_account_name"
                                                    class="form-control"
                                                    value="{{ old('bank_account_name', $bankSettings['bank_account_name'] ?? '') }}"
                                                    placeholder="Nhập tên chủ tài khoản" required>
                                                @error('bank_account_name')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">Số Tài Khoản <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="bank_account_number" id="bank_account_number"
                                                    class="form-control"
                                                    value="{{ old('bank_account_number', $bankSettings['bank_account_number'] ?? '') }}"
                                                    placeholder="Nhập số tài khoản" required>
                                                @error('bank_account_number')
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
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End::row-1 -->
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabKey = 'settingsActiveTab';
            const tabTriggerList = [].slice.call(document.querySelectorAll(
                '#settings-tab button[data-bs-toggle="tab"]'));

            // Tab titles mapping
            const tabTitles = {
                'tab-general-btn': 'Thông Tin Chung',
                'tab-word-template-btn': 'Mẫu Word',
                'tab-payment-btn': 'Thông Tin Thanh Toán'
            };

            // Function to update tab title
            function updateTabTitle(tabId) {
                const titleElement = document.getElementById('tab-title');
                if (titleElement && tabTitles[tabId]) {
                    titleElement.textContent = tabTitles[tabId];
                }
            }

            const savedTabId = localStorage.getItem(tabKey);
            if (savedTabId) {
                const savedTab = document.querySelector(`#settings-tab button#${savedTabId}`);
                if (savedTab) {
                    // Remove active class from all tabs
                    tabTriggerList.forEach(tab => {
                        tab.classList.remove('active');
                    });

                    // Remove active class from all tab panes
                    document.querySelectorAll('.tab-pane').forEach(pane => {
                        pane.classList.remove('show', 'active');
                    });

                    // Add active class to saved tab
                    savedTab.classList.add('active');

                    // Show saved tab pane
                    const targetPane = document.querySelector(savedTab.getAttribute('data-bs-target'));
                    if (targetPane) {
                        targetPane.classList.add('show', 'active');
                    }

                    // Update title
                    updateTabTitle(savedTabId);
                }
            }

            tabTriggerList.forEach(function(tabTriggerEl) {
                tabTriggerEl.addEventListener('shown.bs.tab', function(event) {
                    localStorage.setItem(tabKey, event.target.id);
                    updateTabTitle(event.target.id);
                });
            });


        });
    </script>

    <!-- Bank Settings JavaScript -->
    <script src="{{ asset('assets/js/bank-settings.js') }}"></script>
@endsection
