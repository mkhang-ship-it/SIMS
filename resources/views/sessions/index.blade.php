@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Phiên Hệ Thống</h1>

            <!-- Nút trên desktop -->
            <div class="d-none d-md-block">
                @if (auth()->user()->hasPermissionForModuleCode('session_management', 'delete', 'central'))
                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#destroyAllModal">
                        <i class="ti ti-trash"></i>
                        Xóa Tất Cả Phiên
                    </button>
                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#destroyExpiredModal">
                        <i class="ti ti-clock"></i>
                        Xóa Phiên Hết Hạn
                    </button>
                @endif
            </div>

            <!-- Dropdown trên mobile -->
            <div class="dropdown d-md-none">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Hành động
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @if (auth()->user()->hasPermissionForModuleCode('session_management', 'delete', 'central'))
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                data-bs-target="#destroyAllModal">
                                <i class="ti ti-trash"></i> Xóa Tất Cả Phiên
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                data-bs-target="#destroyExpiredModal">
                                <i class="ti ti-clock"></i> Xóa Phiên Hết Hạn
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="row">
            <!-- Thống kê -->
            <div class="col-xl-12">
                <div class="row">
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="avatar avatar-md bg-primary">
                                            <i class="ti ti-users fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill">
                                        <h6 class="fw-semibold mb-1">Tổng Số Phiên</h6>
                                        <h4 class="fw-bold mb-0">{{ number_format($totalSessions) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="avatar avatar-md bg-success">
                                            <i class="ti ti-check fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill">
                                        <h6 class="fw-semibold mb-1">Phiên Hoạt Động</h6>
                                        <h4 class="fw-bold mb-0">{{ number_format($activeSessions) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="avatar avatar-md bg-warning">
                                            <i class="ti ti-clock fs-16"></i>
                                        </span>
                                    </div>
                                    <div class="flex-fill">
                                        <h6 class="fw-semibold mb-1">Phiên Hết Hạn</h6>
                                        <h4 class="fw-bold mb-0">{{ number_format($expiredSessions) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bảng phiên -->
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            Danh Sách Phiên Hệ Thống
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mb-4">
                            <table class="table text-nowrap table-bordered" id="data-table">
                                <thead>
                                    <tr>
                                        <th style="text-align: center !important;">STT</th>
                                        <th>Người Dùng</th>
                                        <th style="text-align: center !important;">IP Address</th>
                                        <th style="text-align: center !important;">User Agent</th>
                                        <th style="text-align: center !important;">Hoạt Động Cuối</th>
                                        <th style="text-align: center !important;">Trạng Thái</th>
                                        <th style="text-align: center !important;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sessions as $index => $session)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                @if ($session->user_id)
                                                    <div class="d-flex align-items-center">
                                                        <span class="avatar avatar-sm avatar-rounded me-2">
                                                            @if ($session->user_avatar)
                                                                <img src="{{ asset('storage/' . $session->user_avatar) }}"
                                                                    alt="">
                                                            @else
                                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($session->user_name) }}"
                                                                    alt="">
                                                            @endif
                                                        </span>
                                                        <div>
                                                            <h6 class="fw-semibold mb-0">{{ $session->user_name }}</h6>
                                                            <p class="mb-0 text-muted fs-11">{{ $session->user_email }}</p>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Khách</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-light text-dark">{{ $session->ip_address ?? 'N/A' }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="text-truncate" style="max-width: 200px;"
                                                    title="{{ $session->user_agent }}">
                                                    {{ $session->user_agent ?? 'N/A' }}
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s') }}
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $isActive =
                                                        $session->last_activity >=
                                                        time() - config('session.lifetime') * 60;
                                                @endphp
                                                @if ($isActive)
                                                    <span class="badge bg-success">Hoạt Động</span>
                                                @else
                                                    <span class="badge bg-warning">Hết Hạn</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="gap-2 fs-15">
                                                    @if ($session->user_id && auth()->user()->hasPermissionForModuleCode('session_management', 'delete', 'central'))
                                                        <button class="btn btn-icon btn-sm btn-warning-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#destroyUserSessionsModal"
                                                            data-user-id="{{ $session->user_id }}"
                                                            data-user-name="{{ $session->user_name }}"
                                                            title="Xóa phiên người dùng">
                                                            <i class="ti ti-user-x"></i>
                                                        </button>
                                                    @endif
                                                    @if (auth()->user()->hasPermissionForModuleCode('session_management', 'delete', 'central'))
                                                        <button class="btn btn-icon btn-sm btn-danger-light"
                                                            data-bs-toggle="modal" data-bs-target="#destroySessionModal"
                                                            data-session-id="{{ $session->id }}" title="Xóa phiên">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End::row-1 -->

    </div>

    <!-- Modal xóa phiên cụ thể -->
    @if (auth()->user()->hasPermissionForModuleCode('session_management', 'delete', 'central'))
        <div class="modal fade" id="destroySessionModal" tabindex="-1" aria-labelledby="destroySessionModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" id="destroySessionForm">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title" id="destroySessionModalLabel">Xác Nhận Xóa Phiên</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center">
                                <p>Bạn có chắc chắn muốn xóa phiên này?</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-danger">Xóa</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal xóa tất cả phiên của user -->
        <div class="modal fade" id="destroyUserSessionsModal" tabindex="-1"
            aria-labelledby="destroyUserSessionsModalLabel" aria-hidden="true" data-bs-backdrop="static"
            data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" id="destroyUserSessionsForm">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title" id="destroyUserSessionsModalLabel">Xác Nhận Xóa Phiên Người Dùng</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center">
                                <p>Bạn có chắc chắn muốn xóa tất cả phiên của người dùng <strong id="userName"></strong>?
                                </p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-warning">Xóa</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal xóa tất cả phiên -->
        <div class="modal fade" id="destroyAllModal" tabindex="-1" aria-labelledby="destroyAllModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="{{ route('backend.sessions.destroy-all') }}">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title" id="destroyAllModalLabel">Xác Nhận Xóa Tất Cả Phiên</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                <i class="ti ti-alert-triangle me-2"></i>
                                <strong>Cảnh báo!</strong> Hành động này sẽ đăng xuất tất cả người dùng khỏi hệ thống.
                            </div>
                            <p>Bạn có chắc chắn muốn xóa tất cả phiên?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-danger">Xóa Tất Cả</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal xóa phiên hết hạn -->
        <div class="modal fade" id="destroyExpiredModal" tabindex="-1" aria-labelledby="destroyExpiredModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="{{ route('backend.sessions.destroy-expired') }}">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title" id="destroyExpiredModalLabel">Xác Nhận Xóa Phiên Hết Hạn</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center">
                                <p>Bạn có chắc chắn muốn xóa tất cả phiên đã hết hạn?</p>
                                <p class="text-muted">Có <strong>{{ number_format($expiredSessions) }}</strong> phiên hết
                                    hạn sẽ
                                    bị xóa.</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-warning">Xóa Phiên Hết Hạn</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Xử lý modal xóa phiên cụ thể
            const destroySessionModal = document.getElementById('destroySessionModal');
            if (destroySessionModal) {
                destroySessionModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const sessionId = button.getAttribute('data-session-id');
                    const form = document.getElementById('destroySessionForm');
                    form.action = `{{ route('backend.sessions.index') }}/${sessionId}`;
                });
            }

            // Xử lý modal xóa phiên của user
            const destroyUserSessionsModal = document.getElementById('destroyUserSessionsModal');
            if (destroyUserSessionsModal) {
                destroyUserSessionsModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const userId = button.getAttribute('data-user-id');
                    const userName = button.getAttribute('data-user-name');
                    const form = document.getElementById('destroyUserSessionsForm');
                    const userNameElement = document.getElementById('userName');

                    form.action = `{{ route('backend.sessions.index') }}/user/${userId}`;
                    userNameElement.textContent = userName;
                });
            }
        });
    </script>
@endsection
