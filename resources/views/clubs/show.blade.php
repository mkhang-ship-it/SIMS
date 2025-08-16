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
                                    {{ $club->address ? $club->address->full_address : '-' }}
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
                                                                                class="badge bg-primary-transparent rounded-pill me-2">{{ $leader->club_role->name }}</span>
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
                                                                    @if (auth()->user()->hasPermissionForModuleCode('member_management', 'delete', 'branch') &&
                                                                            $leader->club_role &&
                                                                            $leader->club_role->is_admin == false)
                                                                        <button class="btn btn-sm btn-danger btn-wave"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#removeFromClubModal-{{ $leader->id }}">
                                                                            <i class="ri-user-unfollow-line me-1"></i>Xóa
                                                                        </button>
                                                                    @endif
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
                                                                                class="badge bg-success-transparent rounded-pill me-2">{{ $member->club_role->name }}</span>
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
                                                                    @if (auth()->user()->hasPermissionForModuleCode('member_management', 'delete', 'branch') &&
                                                                            $member->club_role &&
                                                                            $member->club_role->is_admin == false)
                                                                        <button class="btn btn-sm btn-danger btn-wave"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#removeFromClubModal-{{ $member->id }}">
                                                                            <i class="ri-user-unfollow-line me-1"></i>Xóa
                                                                        </button>
                                                                    @endif
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
    </div>
@endsection

<!-- Include Member Details Modals -->
@foreach ($leaders as $leader)
    @include('clubs.partials.member-details-modal', ['user' => $leader])
@endforeach

@foreach ($members as $member)
    @include('clubs.partials.member-details-modal', ['user' => $member])
@endforeach

<!-- Include Remove From Club Modals -->
@foreach ($leaders as $leader)
    @include('clubs.partials.remove-from-club-modal', ['user' => $leader])
@endforeach

@foreach ($members as $member)
    @include('clubs.partials.remove-from-club-modal', ['user' => $member])
@endforeach
