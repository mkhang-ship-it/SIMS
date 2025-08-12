{{-- OAuth Settings Partial - GitHub and Microsoft authentication configuration split into 2 tabs --}}
{{-- Updated to use tabbed interface with separate GitHub and Microsoft OAuth tabs --}}
{{-- Includes input validation, secure configuration management, and preserves saved credentials when disabled --}}
{{-- Microsoft OAuth includes additional tenant ID configuration for Azure AD integration --}}

<form action="{{ route('backend.settings.update-oauth-settings') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- Tab Navigation -->
    <ul class="nav nav-tabs" id="oauthTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="github-tab" data-bs-toggle="tab" data-bs-target="#github-tab-pane"
                type="button" role="tab" aria-controls="github-tab-pane" aria-selected="true">
                <i class="ri-github-fill me-2"></i>GitHub OAuth
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="microsoft-tab" data-bs-toggle="tab" data-bs-target="#microsoft-tab-pane"
                type="button" role="tab" aria-controls="microsoft-tab-pane" aria-selected="false">
                <i class="ri-microsoft-fill me-2"></i>Microsoft OAuth
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content mt-3" id="oauthTabsContent">
        <!-- GitHub OAuth Tab -->
        <div class="tab-pane fade show active" id="github-tab-pane" role="tabpanel" aria-labelledby="github-tab"
            tabindex="0">
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
                                <input type="password"
                                    class="form-control @error('github_client_secret') is-invalid @enderror"
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
                            <h6 class="alert-heading"><i class="ri-information-line me-2"></i>Hướng dẫn cấu hình GitHub
                                OAuth
                            </h6>
                            <p class="mb-2">1. Truy cập <a href="https://github.com/settings/applications/new"
                                    target="_blank">GitHub Developer Settings</a></p>
                            <p class="mb-2">2. Tạo một OAuth App mới với thông tin sau:</p>
                            <ul class="mb-2">
                                <li><strong>Application name:</strong>
                                    {{ $generalSettings['site_name'] ?? 'Your App Name' }}
                                </li>
                                <li><strong>Homepage URL:</strong> {{ url('/') }}</li>
                                <li><strong>Authorization callback URL:</strong> {{ url('/auth/github/callback') }}</li>
                            </ul>
                            <p class="mb-0">3. Sau khi tạo xong, copy Client ID và Client Secret vào form trên</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Microsoft OAuth Tab -->
        <div class="tab-pane fade" id="microsoft-tab-pane" role="tabpanel" aria-labelledby="microsoft-tab"
            tabindex="0">
            <div class="row">
                <div class="col-xl-12">
                    <div class="mb-3">
                        <label class="form-label">Cấu Hình OAuth Microsoft</label>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch"
                                id="microsoft_oauth_enabled" name="microsoft_oauth_enabled" value="1"
                                {{ ($oauthSettings['microsoft_oauth_enabled'] ?? '0') === '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="microsoft_oauth_enabled">
                                Bật đăng nhập với Microsoft
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            Cho phép người dùng đăng nhập bằng tài khoản Microsoft/Azure AD của họ.
                        </small>
                    </div>
                </div>
            </div>

            <div id="microsoft-oauth-fields"
                style="{{ ($oauthSettings['microsoft_oauth_enabled'] ?? '0') === '1' ? 'display: block;' : 'display: none;' }}">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="mb-3">
                            <label for="microsoft_client_id" class="form-label">
                                Microsoft Client ID (Application ID) <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                class="form-control @error('microsoft_client_id') is-invalid @enderror"
                                id="microsoft_client_id" name="microsoft_client_id"
                                value="{{ old('microsoft_client_id', $oauthSettings['microsoft_client_id'] ?? '') }}"
                                placeholder="Nhập Microsoft Application ID">
                            @error('microsoft_client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Application ID từ Azure App Registration
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="mb-3">
                            <label for="microsoft_client_secret" class="form-label">
                                Microsoft Client Secret <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password"
                                    class="form-control @error('microsoft_client_secret') is-invalid @enderror"
                                    id="microsoft_client_secret" name="microsoft_client_secret"
                                    value="{{ old('microsoft_client_secret', $oauthSettings['microsoft_client_secret'] ?? '') }}"
                                    placeholder="Nhập Microsoft Client Secret">
                                <button class="btn btn-light" type="button"
                                    onclick="togglePasswordVisibility('microsoft_client_secret', this)">
                                    <i class="ri-eye-off-line align-middle"></i>
                                </button>
                            </div>
                            @error('microsoft_client_secret')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Client Secret từ Azure App Registration
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="mb-3">
                            <label for="microsoft_tenant_id" class="form-label">
                                Microsoft Tenant ID
                            </label>
                            <input type="text"
                                class="form-control @error('microsoft_tenant_id') is-invalid @enderror"
                                id="microsoft_tenant_id" name="microsoft_tenant_id"
                                value="{{ old('microsoft_tenant_id', $oauthSettings['microsoft_tenant_id'] ?? 'common') }}"
                                placeholder="Nhập Tenant ID hoặc để 'common' cho multi-tenant">
                            @error('microsoft_tenant_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Tenant ID từ Azure AD. Để 'common' cho multi-tenant hoặc nhập Tenant ID cụ thể
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="alert alert-info">
                            <h6 class="alert-heading"><i class="ri-information-line me-2"></i>Hướng dẫn cấu hình
                                Microsoft
                                OAuth
                            </h6>
                            <p class="mb-2">1. Truy cập <a
                                    href="https://portal.azure.com/#blade/Microsoft_AAD_RegisteredApps/ApplicationsListBlade"
                                    target="_blank">Azure Portal - App Registrations</a></p>
                            <p class="mb-2">2. Tạo một App Registration mới với thông tin sau:</p>
                            <ul class="mb-2">
                                <li><strong>Name:</strong> {{ $generalSettings['site_name'] ?? 'Your App Name' }}</li>
                                <li><strong>Supported account types:</strong> Accounts in any organizational directory
                                    (Any
                                    Azure AD directory - Multitenant)</li>
                                <li><strong>Redirect URI (Web):</strong> {{ url('/auth/microsoft/callback') }}</li>
                            </ul>
                            <p class="mb-2">3. Sau khi tạo xong:</p>
                            <ul class="mb-2">
                                <li>Copy <strong>Application (client) ID</strong> vào Client ID</li>
                                <li>Vào <strong>Certificates & secrets</strong> > <strong>Client secrets</strong> >
                                    <strong>New
                                        client secret</strong>
                                </li>
                                <li>Copy <strong>Value</strong> của secret vào Client Secret</li>
                                <li>Copy <strong>Directory (tenant) ID</strong> vào Tenant ID (hoặc để 'common')</li>
                            </ul>
                            <p class="mb-0">4. Trong <strong>API permissions</strong>, đảm bảo có quyền
                                <strong>User.Read</strong> của Microsoft Graph
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Save Button (outside tabs, applies to all) -->
    <div class="row mt-4">
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
        const microsoftOAuthEnabled = document.getElementById('microsoft_oauth_enabled');
        const microsoftOAuthFields = document.getElementById('microsoft-oauth-fields');

        function toggleGitHubFields() {
            if (githubOAuthEnabled.checked) {
                githubOAuthFields.style.display = 'block';
            } else {
                githubOAuthFields.style.display = 'none';
            }
        }

        function toggleMicrosoftFields() {
            if (microsoftOAuthEnabled.checked) {
                microsoftOAuthFields.style.display = 'block';
            } else {
                microsoftOAuthFields.style.display = 'none';
            }
        }

        // Initial toggle
        toggleGitHubFields();
        toggleMicrosoftFields();

        // Toggle on change
        githubOAuthEnabled.addEventListener('change', toggleGitHubFields);
        microsoftOAuthEnabled.addEventListener('change', toggleMicrosoftFields);
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
