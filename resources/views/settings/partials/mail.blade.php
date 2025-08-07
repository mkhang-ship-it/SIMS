{{-- Partial: Admin Email Configuration --}}
<form action="{{ route('backend.settings.mail.update') }}" method="POST" id="mail-form">
    @csrf
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Mailer <span class="text-danger">*</span></label>
            <select name="mail_mailer" class="form-select" id="mail_mailer_select" required>
                <option value="smtp"
                    {{ old('mail_mailer', $mailSettings['mail_mailer'] ?? '') == 'smtp' ? 'selected' : '' }}>
                    SMTP</option>
                <option value="outlook"
                    {{ old('mail_mailer', $mailSettings['mail_mailer'] ?? '') == 'outlook' ? 'selected' : '' }}>
                    Microsoft Graph API</option>
            </select>
        </div>
    </div>
    <div id="smtp-fields">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label fw-semibold">Host <span class="text-danger">*</span></label>
                <input type="text" name="mail_host" class="form-control"
                    value="{{ old('mail_host', $mailSettings['mail_host'] ?? '') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label fw-semibold">Port <span class="text-danger">*</span></label>
                <input type="number" name="mail_port" class="form-control"
                    value="{{ old('mail_port', $mailSettings['mail_port'] ?? '') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label fw-semibold">Encryption <span class="text-danger">*</span></label>
                <input type="text" name="mail_encryption" class="form-control"
                    value="{{ old('mail_encryption', $mailSettings['mail_encryption'] ?? '') }}" placeholder="tls/ssl"
                    required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                <input type="text" name="mail_username" class="form-control"
                    value="{{ old('mail_username', $mailSettings['mail_username'] ?? '') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                <input type="password" name="mail_password" class="form-control"
                    value="{{ old('mail_password', $mailSettings['mail_password'] ?? '') }}"
                    autocomplete="new-password">
            </div>
        </div>
    </div>
    <div id="outlook-fields" style="display:none;">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label fw-semibold">Client ID <span class="text-danger">*</span></label>
                <input type="text" name="mail_outlook_client_id" class="form-control"
                    value="{{ old('mail_outlook_client_id', $mailSettings['mail_outlook_client_id'] ?? '') }}"
                    placeholder="Microsoft App Client ID">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label fw-semibold">Secret ID <span class="text-danger">*</span></label>
                <input type="text" name="mail_outlook_secret_id" class="form-control"
                    value="{{ old('mail_outlook_secret_id', $mailSettings['mail_outlook_secret_id'] ?? '') }}"
                    placeholder="Microsoft App Secret">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label fw-semibold">Tenant ID <span class="text-danger">*</span></label>
                <input type="text" name="mail_outlook_tenant_id" class="form-control"
                    value="{{ old('mail_outlook_tenant_id', $mailSettings['mail_outlook_tenant_id'] ?? '') }}"
                    placeholder="Microsoft Tenant ID">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">From Address <span class="text-danger">*</span></label>
            <input type="email" name="mail_from_address" class="form-control"
                value="{{ old('mail_from_address', $mailSettings['mail_from_address'] ?? '') }}" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">From Name <span class="text-danger">*</span></label>
            <input type="text" name="mail_from_name" class="form-control"
                value="{{ old('mail_from_name', $mailSettings['mail_from_name'] ?? '') }}" required>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-device-floppy me-1"></i>
                Cập Nhật Cấu Hình Email
            </button>
        </div>
    </div>
</form>
<hr>
<!-- Form test gửi mail -->
<form action="{{ route('backend.settings.mail.test') }}" method="POST" class="mt-3">
    @csrf
    <div class="row align-items-end">
        <div class="col-md-6 mb-2">
            <label class="form-label fw-semibold">Test Cấu Hình Email</label>
            <input type="email" name="to" class="form-control" placeholder="Nhập email người nhận" required>
        </div>
        <div class="col-md-3 mb-2">
            <button type="submit" class="btn btn-success">
                <i class="ti ti-mail me-1"></i> Gửi Mail
            </button>
        </div>
    </div>
</form>
@if (session('success'))
    <div class="alert alert-success mt-2">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger mt-2">{{ session('error') }}</div>
@endif
