@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Châm Ngôn</h1>

            <!-- Nút trên desktop -->
            @if (auth()->user()->hasPermissionForModuleCode('quote_management', 'create', 'central'))
                <div class="d-none d-md-block">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuoteModal">
                        <i class="ti ti-plus"></i>
                        Thêm Mới
                    </button>
                </div>
            @endif
            <!-- Dropdown trên mobile -->
            <div class="dropdown d-md-none">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Hành động
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @if (auth()->user()->hasPermissionForModuleCode('quote_management', 'create', 'central'))
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addQuoteModal">
                                <i class="ti ti-plus"></i> Thêm Mới
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="table-responsive mb-4">
                            <table class="table text-nowrap table-bordered" id="data-table">
                                <thead>
                                    <tr>
                                        <th style="text-align: center !important;">STT</th>
                                        <th>Châm Ngôn</th>
                                        <th>Tác Giả</th>
                                        <th style="text-align: center !important;">Trạng Thái</th>
                                        <th style="text-align: center !important;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($quotes as $index => $quote)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="fw-semibold">{{ Str::limit($quote->quote, 100) }}</td>
                                            <td>{{ $quote->author ?? 'Chưa có thông tin' }}</td>
                                            <td class="text-center">
                                                @switch($quote->status)
                                                    @case('active')
                                                        <span class="badge bg-success">Hoạt Động</span>
                                                    @break

                                                    @case('inactive')
                                                        <span class="badge bg-warning">Tạm Ngưng</span>
                                                    @break

                                                    @default
                                                        <span class="badge bg-light text-dark">{{ $quote->status }}</span>
                                                @endswitch
                                            </td>
                                            <td class="text-center">
                                                <div class=" gap-2 fs-15">
                                                    @if (auth()->user()->hasPermissionForModuleCode('quote_management', 'edit', 'central'))
                                                        <button class="btn btn-icon btn-sm btn-info-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editQuoteModal{{ $quote->id }}"
                                                            title="Chỉnh sửa">
                                                            <i class="ri-edit-line"></i>
                                                        </button>
                                                    @endif
                                                    @if (auth()->user()->hasPermissionForModuleCode('quote_management', 'delete', 'central'))
                                                        <button class="btn btn-icon btn-sm btn-danger-light"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteQuoteModal{{ $quote->id }}"
                                                            title="Xóa">
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
                    </div>
                </div>
            </div>
        </div>
        <!--End::row-1 -->

    </div>

    @foreach ($quotes as $quote)
        <!-- Edit Quote Modal -->
        @if (auth()->user()->hasPermissionForModuleCode('quote_management', 'edit', 'central'))
            <div class="modal fade" id="editQuoteModal{{ $quote->id }}" tabindex="-1"
                aria-labelledby="editQuoteModalLabel{{ $quote->id }}" aria-hidden="true" data-bs-backdrop="static"
                data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editQuoteModalLabel{{ $quote->id }}">Chỉnh Sửa Châm Ngôn
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('backend.quotes.update', $quote->id) }}" method="POST"
                            id="editQuoteForm{{ $quote->id }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="edit_quote{{ $quote->id }}" class="form-label">Châm Ngôn <span
                                            class="text-danger">*</span></label>
                                    <textarea class="form-control" rows="4" id="edit_quote{{ $quote->id }}" name="quote" required>{{ $quote->quote }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_author{{ $quote->id }}" class="form-label">Tác Giả</label>
                                    <input type="text" class="form-control" id="edit_author{{ $quote->id }}"
                                        name="author" value="{{ $quote->author }}">
                                </div>
                                <div class="mb-3">
                                    <label for="edit_status{{ $quote->id }}" class="form-label">Trạng Thái <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="edit_status{{ $quote->id }}" name="status"
                                        required>
                                        <option value="">Chọn trạng thái</option>
                                        <option value="active" {{ $quote->status == 'active' ? 'selected' : '' }}>
                                            Hoạt Động</option>
                                        <option value="inactive" {{ $quote->status == 'inactive' ? 'selected' : '' }}>
                                            Tạm Ngưng</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-primary">Cập
                                    Nhật</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
        <!-- Delete Quote Modal -->
        @if (auth()->user()->hasPermissionForModuleCode('quote_management', 'delete', 'central'))
            <div class="modal fade" id="deleteQuoteModal{{ $quote->id }}" tabindex="-1"
                aria-labelledby="deleteQuoteModalLabel{{ $quote->id }}" aria-hidden="true" data-bs-backdrop="static"
                data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-danger" id="deleteQuoteModalLabel{{ $quote->id }}">
                                Xác Nhận Xóa</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center">
                                <p class="mb-2">Bạn có chắc chắn muốn xóa châm ngôn này
                                    không?
                                </p>
                            </div>
                        </div>
                        <form action="{{ route('backend.quotes.destroy', $quote->id) }}" method="POST"
                            id="deleteQuoteForm{{ $quote->id }}">
                            @csrf
                            @method('DELETE')
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-danger">Xóa</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <!-- Add Quote Modal -->
    @if (auth()->user()->hasPermissionForModuleCode('quote_management', 'create', 'central'))
        <div class="modal fade" id="addQuoteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="addQuoteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addQuoteModalLabel">Thêm Châm Ngôn Mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('backend.quotes.store') }}" method="POST" id="addQuoteForm">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="add_quote" class="form-label">Châm Ngôn <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" rows="4" id="add_quote" name="quote" required
                                    placeholder="Nhập nội dung quote...">{{ old('quote') }}</textarea>
                                @error('quote')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="add_author" class="form-label">Tác Giả</label>
                                <input type="text" class="form-control" id="add_author" name="author"
                                    value="{{ old('author') }}" placeholder="Nhập tên tác giả (không bắt buộc)">
                                @error('author')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="add_status" class="form-label">Trạng Thái <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="add_status" name="status" required>
                                    <option value="">Chọn trạng thái</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hoạt Động
                                    </option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tạm Ngưng
                                    </option>
                                </select>
                                @error('status')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary">Thêm Mới</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
