{{-- OAuth Settings Partial - GitHub authentication configuration --}}
{{-- Added GitHub OAuth settings form with enable/disable toggle, client ID, and client secret --}}
{{-- Includes input validation, secure configuration management, and preserves saved credentials when disabled --}}

<form action="{{ route('backend.settings.update-oauth-settings') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-xl-12">
            <div class="mb-3">
                <label class="form-label">Cấu Hình OAuth GitHub</label>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="github_oauth_enabled"
                        name="github_oauth_enabled" value="1"
                        {{ ($oauthSettings['github_oauth_enabled'] ?? '0') === '1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="github_oauth_enabled">
                        Bật đăng nhập với GitHub
                    </label>
                </div>
                <small class="form-text text-muted">
                    Cho phép người dùng đăng nhập bằng tài khoản GitHub của họ.
                </small>
            </div>
        </div>
    </div>

    <div id="github-oauth-fields"
        style="{{ ($oauthSettings['github_oauth_enabled'] ?? '0') === '1' ? 'display: block;' : 'display: none;' }}">
        <div class="row">
            <div class="col-xl-12">
                <div class="mb-3">
                    <label for="github_client_id" class="form-label">
                        GitHub Client ID <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('github_client_id') is-invalid @enderror"
                        id="github_client_id" name="github_client_id"
                        value="{{ old('github_client_id', $oauthSettings['github_client_id'] ?? '') }}"
                        placeholder="Nhập GitHub Client ID">
                    @error('github_client_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Client ID từ GitHub OAuth App
                    </small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="mb-3">
                    <label for="github_client_secret" class="form-label">
                        GitHub Client Secret <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input type="password" class="form-control @error('github_client_secret') is-invalid @enderror"
                            id="github_client_secret" name="github_client_secret"
                            value="{{ old('github_client_secret', $oauthSettings['github_client_secret'] ?? '') }}"
                            placeholder="Nhập GitHub Client Secret">
                        <button class="btn btn-light" type="button"
                            onclick="togglePasswordVisibility('github_client_secret', this)">
                            <i class="ri-eye-off-line align-middle"></i>
                        </button>
                    </div>
                    @error('github_client_secret')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Client Secret từ GitHub OAuth App
                    </small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="alert alert-info">
                    <h6 class="alert-heading"><i class="ri-information-line me-2"></i>Hướng dẫn cấu hình GitHub OAuth
                    </h6>
                    <p class="mb-2">1. Truy cập <a href="https://github.com/settings/applications/new"
                            target="_blank">GitHub Developer Settings</a></p>
                    <p class="mb-2">2. Tạo một OAuth App mới với thông tin sau:</p>
                    <ul class="mb-2">
                        <li><strong>Application name:</strong> {{ $generalSettings['site_name'] ?? 'Your App Name' }}
                        </li>
                        <li><strong>Homepage URL:</strong> {{ url('/') }}</li>
                        <li><strong>Authorization callback URL:</strong> {{ url('/auth/github/callback') }}</li>
                    </ul>
                    <p class="mb-0">3. Sau khi tạo xong, copy Client ID và Client Secret vào form trên</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-12">
            <button type="submit" class="btn btn-primary">
                <i class="ri-save-line me-1"></i>Lưu Cấu Hình OAuth
            </button>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const githubOAuthEnabled = document.getElementById('github_oauth_enabled');
        const githubOAuthFields = document.getElementById('github-oauth-fields');

        function toggleGitHubFields() {
            if (githubOAuthEnabled.checked) {
                githubOAuthFields.style.display = 'block';
            } else {
                githubOAuthFields.style.display = 'none';
            }
        }

        // Initial toggle
        toggleGitHubFields();

        // Toggle on change
        githubOAuthEnabled.addEventListener('change', toggleGitHubFields);
    });

    function togglePasswordVisibility(fieldId, button) {
        const field = document.getElementById(fieldId);
        const icon = button.querySelector('i');

        if (field.type === 'password') {
            field.type = 'text';
            icon.className = 'ri-eye-line align-middle';
        } else {
            field.type = 'password';
            icon.className = 'ri-eye-off-line align-middle';
        }
    }
</script>
