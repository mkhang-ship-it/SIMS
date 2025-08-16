@if (auth()->user()->hasPermissionForModuleCode('setting_management', 'registration', 'branch'))
    <form action="{{ route('backend.club.settings.registration.update', $club->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <!-- Trạng thái đăng ký -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Trạng Thái Trang Đăng Ký</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="registration_enabled"
                                        name="registration_enabled" value="1"
                                        {{ $club->registration_enabled ? 'checked' : '' }}>
                                    <label class="form-check-label" for="registration_enabled">
                                        Bật Trang Đăng Ký
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                @if ($club->registration_token)
                                    <label class="form-label">Token Đăng Ký</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control"
                                            value="{{ $club->registration_token }}" readonly>
                                        <button class="btn btn-outline-secondary" type="button" onclick="copyToken()">
                                            <i class="ti ti-copy"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Thời gian mở đăng ký -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Thời Gian Mở Đăng Ký</label>
                                <input type="datetime-local" class="form-control" name="registration_opened_at"
                                    value="{{ $club->registration_opened_at ? $club->registration_opened_at->format('Y-m-d\TH:i') : '' }}">
                                <small class="text-muted">Để trống nếu muốn mở ngay lập tức.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Thời Gian Đóng Đăng Ký</label>
                                <input type="datetime-local" class="form-control" name="registration_closed_at"
                                    value="{{ $club->registration_closed_at ? $club->registration_closed_at->format('Y-m-d\TH:i') : '' }}">
                                <small class="text-muted">Để trống nếu không muốn giới hạn thời gian.</small>
                            </div>
                        </div>

                        <!-- Role mặc định -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Vai Trò Mặc Định</label>
                                <select class="form-select" name="default_role_id">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ $club->default_role_id == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <!-- Link đăng ký -->
                        @if ($club->registration_token)
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label">Link Đăng Ký</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control"
                                            value="{{ route('register', $club->registration_token) }}" readonly>
                                        <button class="btn btn-outline-secondary" type="button" onclick="copyLink()">
                                            <i class="ti ti-copy"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">Chia sẻ link này để thành viên có thể đăng ký tham gia câu
                                        lạc bộ.</small>
                                </div>
                            </div>
                        @endif

                        <!-- Nút lưu -->
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-1"></i>
                                    Lưu cài đặt
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endif
