@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Yêu Cầu Nộp Văn Bản</h1>

        <!-- Nút trên desktop -->
        <div class="d-none d-md-block">
            <a href="{{ route('backend.club.documents.index') }}" class="btn btn-info me-2">
                <i class="ti ti-file-text"></i>
                Văn Bản Đã Nộp
            </a>
            @if (auth()->user()->hasPermissionForModuleCode('document_request_management', 'create', 'branch'))
            <a href="{{ route('backend.club.document-requests.create') }}" class="btn btn-primary">
                <i class="ti ti-plus"></i>
                Tạo Yêu Cầu Nộp Mới
            </a>
            @endif
        </div>

        <!-- Dropdown trên mobile -->
        <div class="dropdown d-md-none">
            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                Hành động
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('backend.club.documents.index') }}">
                        <i class="ti ti-file-text"></i> Văn Bản Đã Nộp
                    </a>
                </li>
                @if (auth()->user()->hasPermissionForModuleCode('document_request_management', 'create', 'branch'))
                <li>
                    <a class="dropdown-item" href="{{ route('backend.club.document-requests.create') }}">
                        <i class="ti ti-plus"></i> Tạo Yêu Cầu Nộp Mới
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="row">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-body">
                    <form method="GET" action="{{ route('backend.club.document-requests.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <select name="document_type_id" class="form-select">
                                <option value="">Tất Cả Loại Văn Bản</option>
                                @foreach ($documentTypes as $type)
                                <option value="{{ $type->id }}" {{ request('document_type_id')==$type->id ? 'selected' :
                                    '' }}>
                                    {{ $type->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="status" class="form-select">
                                <option value="">Tất Cả Trạng Thái</option>
                                <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Đang hoạt
                                    động</option>
                                <option value="expired" {{ request('status')=='expired' ? 'selected' : '' }}>Đã hết
                                    hạn</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-search"></i>
                                Tìm kiếm
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="card custom-card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="ti ti-file-text fs-1"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">Tổng Yêu Cầu Nộp</h6>
                            <h4 class="mb-0">{{ $documentRequests->total() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card custom-card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="ti ti-clock fs-1"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">Đang Hoạt Động</h6>
                            <h4 class="mb-0">{{ $documentRequests->where('deadline', '>=', now())->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card custom-card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="ti ti-clock-x fs-1"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">Đã Hết Hạn</h6>
                            <h4 class="mb-0">{{ $documentRequests->where('deadline', '<', now())->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card custom-card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="ti ti-users fs-1"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">Tuần Này</h6>
                            <h4 class="mb-0">
                                {{ $documentRequests->where('created_at', '>=', now()->startOfWeek())->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Requests Table -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="table-responsive mb-4">
                        <table class="table text-nowrap table-bordered" id="data-table">
                            <thead>
                                <tr>
                                    <th style="text-align: center !important;">STT</th>
                                    <th>Loại Văn Bản</th>
                                    <th>Người Yêu Cầu</th>
                                    <th style="text-align: center !important;">Hạn Nộp</th>
                                    <th style="text-align: center !important;">Tiến Độ</th>
                                    <th style="text-align: center !important;">Trạng Thái</th>
                                    <th style="text-align: center !important;">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($documentRequests as $index => $request)
                                @php
                                $totalUsers = $request->documentRequestUsers->count();
                                $completedUsers = $request->documentRequestUsers
                                ->whereNotNull('document_id')
                                ->count();
                                $progressPercent =
                                $totalUsers > 0 ? ($completedUsers / $totalUsers) * 100 : 0;
                                $isExpired = $request->deadline < now(); @endphp <tr>
                                    <td class="text-center">
                                        {{ ($documentRequests->currentPage() - 1) * $documentRequests->perPage() +
                                        $loop->iteration }}
                                    </td>
                                    <td>{{ $request->documentType->name }}</td>
                                    <td>{{ $request->requestedByUser->name }}</td>
                                    <td class="text-center">
                                        <div class="mb-1">
                                            {{ $request->deadline->format('d/m/Y') }}
                                        </div>
                                        <small class="text-muted">{{ $request->deadline->format('H:i') }}</small>
                                        @if ($isExpired)
                                        <br><span class="badge bg-danger mt-1">Đã hết hạn</span>
                                        @else
                                        <br><span class="badge bg-success mt-1">Còn
                                            {{ $request->deadline->diffForHumans() }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="mb-2">
                                            <span class="fw-bold">{{ $completedUsers }}/{{ $totalUsers }}</span>
                                        </div>
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar
                                                {{ $progressPercent == 100 ? 'bg-success' : ($progressPercent > 50 ? 'bg-info' : 'bg-warning') }}"
                                                role="progressbar" style="width: {{ $progressPercent }}%">
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ number_format($progressPercent, 1) }}%</small>
                                    </td>
                                    <td class="text-center">
                                        @if ($isExpired)
                                        <span class="badge bg-danger">Hết hạn</span>
                                        @else
                                        <span class="badge bg-success">Hoạt động</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="gap-2 fs-15">
                                            @if(auth()->user()->hasPermissionForModuleCode('document_request_management',
                                            'view', 'branch'))
                                            <a href="{{ route('backend.club.document-requests.show', $request) }}"
                                                class="btn btn-icon btn-sm btn-primary-light" title="Xem chi tiết">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            @endif

                                            @if(auth()->user()->hasPermissionForModuleCode('document_request_management',
                                            'edit', 'branch') && !$isExpired)
                                            <a href="{{ route('backend.club.document-requests.edit', $request) }}"
                                                class="btn btn-icon btn-sm btn-info-light" title="Chỉnh sửa">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            @endif



                                            @if(auth()->user()->hasPermissionForModuleCode('document_request_management',
                                            'delete', 'branch'))
                                            <button class="btn btn-icon btn-sm btn-danger-light" data-bs-toggle="modal"
                                                data-bs-target="#deleteRequestModal{{ $request->id }}" title="Xóa">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                    </tr>
                                    @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $documentRequests->appends(request()->query())->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@foreach ($documentRequests as $request)
<!-- Delete Request Modal -->
@if (auth()->user()->hasPermissionForModuleCode('document_request_management', 'delete', 'branch'))
<div class="modal fade" id="deleteRequestModal{{ $request->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác Nhận Xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mb-2">Bạn có chắc chắn muốn xóa yêu cầu
                    <strong class="text-danger">{{ $request->title }}</strong>?
                </p>
                @if ($request->documentRequestUsers->whereNotNull('document_id')->count() > 0)
                <div class="alert alert-warning">
                    <i class="ri-alert-line"></i>
                    <strong>Cảnh báo:</strong> Yêu cầu này đã có người nộp tài liệu và không thể xóa.
                </div>
                @else
                <p class="text-danger">
                    <i class="ri-alert-line"></i>
                    Thao tác này không thể hoàn tác.
                </p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                @if ($request->documentRequestUsers->whereNotNull('document_id')->count() == 0)
                <form action="{{ route('backend.club.document-requests.destroy', $request) }}" method="POST"
                    style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

<script>
    function sendReminder(requestId) {
            if (confirm('Bạn có chắc chắn muốn gửi nhắc nhở đến những người chưa nộp tài liệu?')) {
                // Create a form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/club/document-requests/${requestId}/send-reminder`;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                document.body.appendChild(form);
                form.submit();
            }
        }
</script>
@endsection
