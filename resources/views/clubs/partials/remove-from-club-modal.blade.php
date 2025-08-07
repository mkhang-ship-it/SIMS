{{-- Modal xóa thành viên khỏi câu lạc bộ (include file) --}}
{{-- [2024-12-19] Created: Modal for removing members from club only (not deleting from system) --}}
@if (auth()->user()->hasPermissionForModuleCode('member_management', 'delete', 'branch') &&
        $user->club_role &&
        $user->club_role->is_admin == false)
    <div class="modal fade" id="removeFromClubModal-{{ $user->id }}" tabindex="-1"
        aria-labelledby="removeFromClubModalLabel-{{ $user->id }}" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="removeFromClubModalLabel-{{ $user->id }}">
                        Xác Nhận Xóa Khỏi Câu Lạc Bộ
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <p class="mb-2">Bạn có chắc chắn muốn xóa thành viên
                            <span class="fw-bold text-danger">{{ $user->name }}</span>
                            khỏi câu lạc bộ không?
                        </p>
                        <p class="text-muted small">Email: {{ $user->email }}</p>
                        <div class="alert alert-warning mt-3">
                            <i class="ri-information-line me-2"></i>
                            <strong>Lưu ý:</strong> Thành viên sẽ chỉ bị xóa khỏi câu lạc bộ này. Tài khoản của họ vẫn
                            tồn tại trong hệ thống.
                        </div>
                    </div>
                </div>
                <form action="{{ route('backend.members.destroy', $user->id) }}" method="POST"
                    id="removeFromClubForm{{ $user->id }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="ri-user-unfollow-line me-1"></i>Xóa Khỏi Câu Lạc Bộ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
