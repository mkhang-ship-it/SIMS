<div class="row">
    <div class="col-12">
        <div class="alert alert-info">
            <h6 class="alert-heading"><i class="ti ti-info-circle me-2"></i>Thông Tin</h6>
            <p class="mb-0">Chức năng này cho phép bạn chạy lại phân quyền từ seeders mà không làm mất các phân quyền
                đã được gán cho user. Hệ thống sẽ tự động backup và restore lại các phân quyền hiện tại.</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card custom-card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="ti ti-shield-lock me-2"></i>
                    Phân Quyền Admin
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('backend.settings.refresh-permissions') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="type" value="admin">
                    <button type="button" class="btn btn-primary"
                        onclick="showConfirmModal('admin', 'Bạn có chắc chắn muốn chạy lại phân quyền cho admin?')">
                        <i class="ti ti-refresh me-1"></i>
                        Chạy Lại Phân Quyền Admin
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card custom-card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="ti ti-users me-2"></i>
                    Phân Quyền Câu Lạc Bộ
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('backend.settings.refresh-permissions') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="type" value="club">
                    <button type="button" class="btn btn-success"
                        onclick="showConfirmModal('club', 'Bạn có chắc chắn muốn chạy lại phân quyền cho câu lạc bộ?')">
                        <i class="ti ti-refresh me-1"></i>
                        Chạy Lại Phân Quyền Club
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card custom-card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="ti ti-settings me-2"></i>
                    Tất Cả Phân Quyền
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('backend.settings.refresh-permissions') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="type" value="all">
                    <button type="button" class="btn btn-warning"
                        onclick="showConfirmModal('all', 'Bạn có chắc chắn muốn chạy lại tất cả phân quyền? Quá trình này có thể mất vài phút.')">
                        <i class="ti ti-refresh me-1"></i>
                        Chạy Lại Tất Cả Phân Quyền
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">
                    <i class="ti ti-alert-triangle me-2 text-warning"></i>
                    Xác Nhận Hành Động
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>
                    Hủy
                </button>
                <form id="confirmForm" action="{{ route('backend.settings.refresh-permissions') }}" method="POST"
                    class="d-inline">
                    @csrf
                    <input type="hidden" name="type" id="confirmType" value="">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check me-1"></i>
                        Xác Nhận
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showConfirmModal(type, message) {
        document.getElementById('confirmMessage').textContent = message;
        document.getElementById('confirmType').value = type;

        const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        modal.show();
    }
</script>
