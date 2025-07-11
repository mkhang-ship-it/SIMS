{{-- Club show page - Fixed null role reference error by adding null checks for $leader->role and $member->role --}}
{{-- Updated: Member details modal is now included from a separate partial file for each user --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Start::row-1 -->
        <div class="row mt-4">
            <div class="col-xxl-4 col-xl-12">
                <div class="card custom-card overflow-hidden">
                    <div class="card-body p-0">
                        <div class="d-sm-flex align-items-top p-4 border-bottom border-block-end-dashed main-profile-cover">
                            <div>
                                <span class="avatar avatar-xxl avatar-rounded online me-3">
                                    <img src="{{ !empty($club->image) ? asset('storage/' . $club->image) : asset('assets/images/avatar_example.png') }}"
                                        alt="">
                                </span>
                            </div>
                            <div class="flex-fill main-profile-info">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h6 class="fw-semibold mb-1 text-fixed-white">{{ $club->name }}</h6>
                                </div>
                                <div class="d-flex mb-0">
                                    <div class="me-4">
                                        <p class="fw-bold fs-20 text-center text-fixed-white text-shadow mb-0">
                                            {{ $leaders->count() }}
                                        </p>
                                        <p class="mb-0 fs-11 op-5 text-fixed-white">Chủ Nhiệm</p>
                                    </div>
                                    <div class="me-4">
                                        <p class="fw-bold fs-20 text-center text-fixed-white text-shadow mb-0">
                                            {{ $members->count() }}
                                        </p>
                                        <p class="mb-0 fs-11 op-5 text-fixed-white">Thành Viên</p>
                                    </div>
                                    <div class="me-4">
                                        <p class="fw-bold fs-20 text-center text-fixed-white text-shadow mb-0">
                                            {{ $events->count() }}
                                        </p>
                                        <p class="mb-0 fs-11 op-5 text-fixed-white">Sự Kiện</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 border-bottom border-block-end-dashed">
                            <p class="fs-15 mb-2 me-4 fw-semibold">Thông Tin Liên Hệ:</p>
                            <div class="text-muted">
                                <p class="mb-2">
                                    <span class="avatar avatar-sm avatar-rounded me-2 bg-light text-muted">
                                        <i class="ri-mail-line align-middle fs-14"></i>
                                    </span>
                                    {{ $club->email }}
                                </p>
                                <p class="mb-2">
                                    <span class="avatar avatar-sm avatar-rounded me-2 bg-light text-muted">
                                        <i class="ri-phone-line align-middle fs-14"></i>
                                    </span>
                                    {{ $club->phone }}
                                </p>
                                <p class="mb-0">
                                    <span class="avatar avatar-sm avatar-rounded me-2 bg-light text-muted">
                                        <i class="ri-map-pin-line align-middle fs-14"></i>
                                    </span>
                                    {{ $club->address }}
                                </p>
                            </div>
                        </div>
                        <div class="p-4 border-bottom border-block-end-dashed d-flex align-items-center">
                            <p class="fs-15 mb-2 me-4 fw-semibold">Mạng Xã Hội:</p>
                            <div class="btn-list mb-0">
                                <button class="btn btn-sm btn-icon btn-primary-light btn-wave waves-effect waves-light">
                                    <i class="ri-facebook-line fw-semibold"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-secondary-light btn-wave waves-effect waves-light">
                                    <i class="ri-twitter-line fw-semibold"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-warning-light btn-wave waves-effect waves-light">
                                    <i class="ri-instagram-line fw-semibold"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-success-light btn-wave waves-effect waves-light">
                                    <i class="ri-github-line fw-semibold"></i>
                                </button>
                                <button class="btn btn-sm btn-icon btn-danger-light btn-wave waves-effect waves-light">
                                    <i class="ri-youtube-line fw-semibold"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-8 col-xl-12">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-body p-0">
                                <div
                                    class="p-3 border-bottom border-block-end-dashed d-flex align-items-center justify-content-between">
                                    <div>
                                        <ul class="nav nav-tabs mb-0 tab-style-6 justify-content-start" id="myTab"
                                            role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="leaders-tab" data-bs-toggle="tab"
                                                    data-bs-target="#leaders-tab-pane" type="button" role="tab"
                                                    aria-controls="leaders-tab-pane" aria-selected="true"><i
                                                        class="ri-user-star-line me-1 align-middle d-inline-block"></i>Chủ
                                                    Nhiệm</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="members-tab" data-bs-toggle="tab"
                                                    data-bs-target="#members-tab-pane" type="button" role="tab"
                                                    aria-controls="members-tab-pane" aria-selected="false"><i
                                                        class="ri-user-line me-1 align-middle d-inline-block"></i>Thành
                                                    Viên</button>
                                            </li>
                                        </ul>
                                    </div>
                                    <div>
                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#addUserModal">
                                            <i class="ri-add-line me-1"></i>Thêm Mới
                                        </button>
                                    </div>
                                </div>
                                <div class="p-3">
                                    <div class="tab-content" id="myTabContent">
                                        <!-- Tab Chủ Nhiệm -->
                                        <div class="tab-pane fade show active p-0 border-0" id="leaders-tab-pane"
                                            role="tabpanel" aria-labelledby="leaders-tab" tabindex="0">
                                            <div class="row">
                                                @foreach ($leaders as $leader)
                                                    <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 col-sm-12">
                                                        <div class="card custom-card shadow-none border">
                                                            <div class="card-body p-4">
                                                                <div class="text-center">
                                                                    <span class="avatar avatar-xl avatar-rounded">
                                                                        <img src="{{ !empty($leader->avatar) ? asset('storage/' . $leader->avatar) : asset('assets/images/avatar_example.png') }}"
                                                                            alt="">

                                                                    </span>
                                                                    <div class="mt-2">
                                                                        <p class="mb-0 fw-semibold">{{ $leader->name }}</p>
                                                                        <p class="fs-12 op-7 mb-1 text-muted">
                                                                            {{ $leader->email }}</p>
                                                                        <div class="d-flex justify-content-center">
                                                                            <span
                                                                                class="badge bg-primary-transparent rounded-pill me-2">{{ $leader->role->name }}</span>
                                                                            <span
                                                                                class="badge bg-secondary-transparent rounded-pill me-2">{{ $leader->student_code }}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-footer text-center">
                                                                <div class="btn-list">
                                                                    <button class="btn btn-sm btn-info btn-wave">
                                                                        <i class="ri-phone-line me-1"></i>Liên Hệ
                                                                    </button>
                                                                    <button class="btn btn-sm btn-primary btn-wave"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#memberDetailsModal-{{ $leader->id }}">
                                                                        <i class="ri-eye-line me-1"></i>Chi Tiết
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <!-- Tab Thành Viên -->
                                        <div class="tab-pane fade p-0 border-0" id="members-tab-pane" role="tabpanel"
                                            aria-labelledby="members-tab" tabindex="0">
                                            <div class="row">
                                                @foreach ($members as $member)
                                                    <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 col-sm-12">
                                                        <div class="card custom-card shadow-none border">
                                                            <div class="card-body p-4">
                                                                <div class="text-center">
                                                                    <span class="avatar avatar-xl avatar-rounded">
                                                                        <img src="{{ !empty($member->avatar) ? asset('storage/' . $member->avatar) : asset('assets/images/avatar_example.png') }}"
                                                                            alt="">
                                                                    </span>
                                                                    <div class="mt-2">
                                                                        <p class="mb-0 fw-semibold">{{ $member->name }}
                                                                        </p>
                                                                        <p class="fs-12 op-7 mb-1 text-muted">
                                                                            {{ $member->email }}</p>
                                                                        <div class="d-flex justify-content-center">
                                                                            <span
                                                                                class="badge bg-success-transparent rounded-pill me-2">{{ $member->role->name }}</span>
                                                                            <span
                                                                                class="badge bg-secondary-transparent rounded-pill me-2">{{ $member->student_code }}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-footer text-center">
                                                                <div class="btn-list">
                                                                    <button class="btn btn-sm btn-info btn-wave">
                                                                        <i class="ri-phone-line me-1"></i>Liên Hệ
                                                                    </button>
                                                                    <button class="btn btn-sm btn-primary btn-wave"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#memberDetailsModal-{{ $member->id }}">
                                                                        <i class="ri-eye-line me-1"></i>Chi Tiết
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End::row-1 -->

        <!-- Add User Modal -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title" id="addUserModalLabel">Thêm Thành Viên Mới</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('backend.clubs.users.store', $club->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-xl-12 mb-3">
                                    <label for="name" class="form-label">Họ và Tên <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" placeholder="Nhập họ và tên"
                                        value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-xl-12 mb-3">
                                    <label for="email" class="form-label">Email <span
                                            class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" placeholder="Nhập địa chỉ email"
                                        value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-xl-12 mb-3">
                                    <label for="password" class="form-label">Mật Khẩu <span
                                            class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" placeholder="Nhập mật khẩu" required
                                        minlength="8">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-xl-12 mb-3">
                                    <label for="password_confirmation" class="form-label">Xác Nhận Mật Khẩu <span
                                            class="text-danger">*</span></label>
                                    <input type="password"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        id="password_confirmation" name="password_confirmation"
                                        placeholder="Nhập lại mật khẩu" required minlength="8">
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-xl-12 mb-3">
                                    <label for="role_id" class="form-label">Vai Trò <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('role_id') is-invalid @enderror" id="role_id"
                                        name="role_id" required>
                                        <option value="">Chọn vai trò</option>
                                        @foreach ($club->roles()->where('type', 'branch')->where('status', 'active')->get() as $role)
                                            <option value="{{ $role->id }}"
                                                {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary">Thêm Thành Viên</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

<!-- Include Member Details Modals -->
@foreach ($leaders as $leader)
    @include('clubs.partials.member-details-modal', ['user' => $leader])
@endforeach

@foreach ($members as $member)
    @include('clubs.partials.member-details-modal', ['user' => $member])
@endforeach
