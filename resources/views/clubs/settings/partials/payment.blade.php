{{-- Partial: Tab Thông Tin Thanh Toán (Payment) - Tách từ index.blade.php --}}
<form action="{{ route('backend.club.settings.bank.update') }}" method="POST" id="bank-form">
    @csrf
    <!-- Bank Selection -->
    <div class="row mb-4">
        <div class="col-12">
            <label class="form-label fw-semibold">Chọn Ngân Hàng <span class="text-danger">*</span></label>
            <select class="form-select" id="bank-select" name="bank_name">
                <option value="">-- Chọn ngân hàng --</option>
            </select>
        </div>
    </div>
    <!-- Bank Information (Auto-filled) -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Tên Ngân Hàng <span class="text-danger">*</span></label>
            <input type="text" name="bank_name" id="bank_name" class="form-control"
                value="{{ old('bank_name', $bankSettings['bank_name'] ?? '') }}" readonly required>
            @error('bank_name')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Mã Ngân Hàng <span class="text-danger">*</span></label>
            <input type="text" name="bank_code" id="bank_code" class="form-control"
                value="{{ old('bank_code', $bankSettings['bank_code'] ?? '') }}" readonly required>
            @error('bank_code')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">BIN <span class="text-danger">*</span></label>
            <input type="text" name="bank_bin" id="bank_bin" class="form-control"
                value="{{ old('bank_bin', $bankSettings['bank_bin'] ?? '') }}" readonly required>
            @error('bank_bin')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Tên Viết Tắt <span class="text-danger">*</span></label>
            <input type="text" name="bank_short_name" id="bank_short_name" class="form-control"
                value="{{ old('bank_short_name', $bankSettings['bank_short_name'] ?? '') }}" readonly required>
            @error('bank_short_name')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <hr>
    <!-- Account Information -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Tên Chủ Tài Khoản <span class="text-danger">*</span></label>
            <input type="text" name="bank_account_name" id="bank_account_name" class="form-control"
                value="{{ old('bank_account_name', $bankSettings['bank_account_name'] ?? '') }}"
                placeholder="Nhập tên chủ tài khoản" required>
            @error('bank_account_name')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Số Tài Khoản <span class="text-danger">*</span></label>
            <input type="text" name="bank_account_number" id="bank_account_number" class="form-control"
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
