{{-- Modal chi tiết thành viên (include file) --}}
<div class="modal fade" id="memberDetailsModal-{{ $user->id }}" tabindex="-1"
    aria-labelledby="memberDetailsModalLabel-{{ $user->id }}" aria-hidden="true" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="memberDetailsModalLabel-{{ $user->id }}">Thông Tin Thành Viên -
                    {{ $user->name }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- Updated: Changed all col-12 fields to col-6 for a two-column layout except the avatar --}}
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 text-center mb-3">
                        <img src="{{ !empty($user->avatar) ? asset('storage/' . $user->avatar) : asset('assets/images/avatar_example.png') }}"
                            alt="Avatar" class="avatar avatar-xxl avatar-rounded mb-2"
                            style="max-width:500px;max-height:500px;object-fit:cover;">
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label">Tên Thành Viên *</label>
                        <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label">Email *</label>
                        <input type="text" class="form-control" value="{{ $user->email }}" readonly>
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label">Giới Tính</label>
                        <input type="text" class="form-control"
                            value="{{ $user->gender === 'male' ? 'Nam' : ($user->gender === 'female' ? 'Nữ' : '') }}"
                            readonly>
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label">Ngày Sinh</label>
                        <input type="text" class="form-control" value="{{ $user->birthday }}" readonly>
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label">CCCD/CMND</label>
                        <input type="text" class="form-control" value="{{ $user->identity_card_code }}" readonly>
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label">Số Điện Thoại</label>
                        <input type="text" class="form-control" value="{{ $user->phone }}" readonly>
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label">Mã Sinh Viên</label>
                        <input type="text" class="form-control" value="{{ $user->student_code }}" readonly>
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label">Chuyên Ngành</label>
                        <input type="text" class="form-control" value="{{ $user->major ? $user->major->name : '' }}"
                            readonly>
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label">Khóa Học</label>
                        <input type="text" class="form-control"
                            value="{{ $user->course ? $user->course->name : '' }}" readonly>
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label">Vai Trò *</label>
                        <input type="text" class="form-control" value="{{ $user->role ? $user->role->name : '' }}"
                            readonly>
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label">Trạng Thái *</label>
                        <input type="text" class="form-control"
                            value="{{ $user->status === 'active' ? 'Hoạt động' : ($user->status === 'inactive' ? 'Không hoạt động' : '') }}"
                            readonly>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
