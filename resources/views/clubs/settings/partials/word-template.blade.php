{{-- Partial: Tab Mẫu Word (Word Template) - Tách từ index.blade.php --}}
<form action="{{ route('backend.club.settings.word-commitment-template.update') }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    <!-- Current Template Display -->
    <div class="row mb-4">
        <div class="col-12">
            <label class="form-label fw-semibold">Mẫu Bản Cam Kết Hiện Tại</label>
            <div class="d-flex align-items-center gap-3">
                @if ($wordCommitmentTemplate)
                    <div class="bg-light rounded-3 d-flex align-items-center justify-content-center p-3">
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
                    <div class="bg-light rounded-3 d-flex align-items-center justify-content-center p-3">
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
            <input type="file" name="word_commitment_template" class="form-control" accept=".docx" required>
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
