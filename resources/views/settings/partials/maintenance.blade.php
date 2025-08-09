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
                            <small class="text-muted">Khi bật, tất cả người dùng sẽ thấy trang bảo trì trừ khi có token
                                bypass hoặc là admin.</small>
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
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="bypass_token_display"
                                            value="{{ $maintenanceSettings['maintenance_bypass_token'] ?? '' }}"
                                            readonly>
                                        <button class="btn btn-outline-secondary" type="button" id="copy_token">
                                            <i class="ri-file-copy-line"></i> Sao Chép
                                        </button>
                                    </div>
                                    <input type="hidden" name="maintenance_bypass_token" id="bypass_token_input"
                                        value="{{ $maintenanceSettings['maintenance_bypass_token'] ?? '' }}">
                                </div>
                            </div>
                            <small class="text-muted">
                                Token này được tạo tự động mỗi khi bật/tắt chế độ bảo trì và cho phép truy cập vào hệ
                                thống khi đang bảo trì. Token sẽ hết hạn sau 10 phút không hoạt động.
                                Sử dụng: <code>{{ url('/') }}/bypass/TOKEN</code>
                            </small>
                        </div>
                    </div>

                    <!-- Maintenance Message -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <label for="maintenance_message" class="form-label fw-semibold">Thông Báo Bảo Trì</label>
                            <textarea class="form-control" id="maintenance_message" name="maintenance_message" rows="3"
                                placeholder="Nhập thông báo bảo trì tùy chỉnh...">{{ $maintenanceSettings['maintenance_message'] ?? '' }}</textarea>
                            <small class="text-muted">Để trống để sử dụng thông báo mặc định.</small>
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
    });
</script>
