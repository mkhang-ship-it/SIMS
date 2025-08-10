{{-- Admin Maintenance Mode Settings Partial --}}
<form action="{{ route('backend.settings.maintenance.update') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h5 class="card-title">Cài Đặt Chế Độ Bảo Trì</h5>
                </div>
                <div class="card-body">
                    <!-- Maintenance Mode Toggle -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="maintenance_mode"
                                    name="maintenance_mode" value="1"
                                    {{ $maintenanceSettings['maintenance_mode'] ?? '0' === '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="maintenance_mode">
                                    Bật Chế Độ Bảo Trì
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Current Status -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert {{ ($maintenanceSettings['maintenance_mode'] ?? '0') === '1' ? 'alert-warning' : 'alert-success' }}"
                                role="alert">
                                <i class="ri-information-line me-2"></i>
                                <strong>Trạng thái hiện tại:</strong>
                                {{ ($maintenanceSettings['maintenance_mode'] ?? '0') === '1' ? 'Đang bảo trì' : 'Đang hoạt động bình thường' }}
                            </div>
                        </div>
                    </div>

                    <!-- Bypass Token Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="fw-semibold mb-3">Token Bypass</h6>
                            <div class="row">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="bypass_token_display"
                                            value="{{ $maintenanceSettings['maintenance_bypass_token'] ?? '' }}"
                                            readonly>
                                        <button class="btn btn-outline-secondary" type="button" id="copy_token">
                                            <i class="ri-file-copy-line"></i> Copy
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="bypass_link_display"
                                            value="{{ url('/') }}/bypass/{{ $maintenanceSettings['maintenance_bypass_token'] ?? '' }}"
                                            readonly>
                                        <button class="btn btn-outline-primary" type="button" id="copy_link">
                                            <i class="ri-link"></i> Copy
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="maintenance_bypass_token" id="bypass_token_input"
                                value="{{ $maintenanceSettings['maintenance_bypass_token'] ?? '' }}">
                        </div>
                        <span class="text-danger pt-2">
                            Token này được tạo tự động mỗi khi bật/tắt chế độ bảo trì và cho phép truy cập vào hệ
                            thống khi đang bảo trì. <br> Token sẽ hết hạn sau 10 phút không hoạt động.
                        </span>
                    </div>

                    <!-- Maintenance Message -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <label for="maintenance_message" class="form-label fw-semibold">Thông Báo Bảo Trì</label>
                            <textarea class="form-control" id="maintenance_message" name="maintenance_message" rows="3"
                                placeholder="Để trống để sử dụng thông báo mặc định.">{{ $maintenanceSettings['maintenance_message'] ?? '' }}</textarea>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-1"></i>Lưu Cài Đặt
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Copy token
        document.getElementById('copy_token').addEventListener('click', function() {
            const tokenDisplay = document.getElementById('bypass_token_display');
            tokenDisplay.select();
            tokenDisplay.setSelectionRange(0, 99999); // For mobile devices
            navigator.clipboard.writeText(tokenDisplay.value).then(function() {
                // Show success message
                const btn = document.getElementById('copy_token');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="ri-check-line"></i> Đã Sao Chép';
                btn.classList.remove('btn-outline-secondary');
                btn.classList.add('btn-success');

                setTimeout(function() {
                    btn.innerHTML = originalText;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-secondary');
                }, 2000);
            });
        });

        // Copy bypass link
        document.getElementById('copy_link').addEventListener('click', function() {
            const linkDisplay = document.getElementById('bypass_link_display');
            linkDisplay.select();
            linkDisplay.setSelectionRange(0, 99999); // For mobile devices
            navigator.clipboard.writeText(linkDisplay.value).then(function() {
                // Show success message
                const btn = document.getElementById('copy_link');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="ri-check-line"></i> Đã Sao Chép';
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-success');

                setTimeout(function() {
                    btn.innerHTML = originalText;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-primary');
                }, 2000);
            });
        });
    });
</script>
