{{-- Updated: Added prize availability tracking and winner validation for lucky draw system - includes remaining quantity display, availability badges, and winner count tracking --}}
@extends('layouts.app')

@section('styles')
    <style>
        .sortable-ghost {
            opacity: 0.5;
            background: #f8f9fa !important;
        }

        .sortable-chosen {
            background: #e3f2fd !important;
        }

        .sortable-drag {
            background: #fff !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
        }

        .reorder-handle:hover {
            color: #007bff !important;
        }

        .reorder-handle:active {
            cursor: grabbing !important;
        }

        #data-table tbody tr {
            transition: background-color 0.2s ease;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Giải Thưởng</h1>

            <!-- Nút trên desktop -->
            <div class="d-none d-md-block">
                @if (auth()->user()->hasPermissionForModuleCode('lucky_draw_management', 'create', 'branch'))
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPrizeModal">
                        <i class="ti ti-plus"></i>
                        Thêm Mới
                    </button>
                @endif
                <a href="{{ route('backend.club.lucky-draws.show', $luckyDraw) }}" class="btn btn-secondary">
                    <i class="ti ti-arrow-left"></i>
                    Quay Lại
                </a>
            </div>
            <!-- Dropdown trên mobile -->
            <div class="dropdown d-md-none">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Hành động
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @if (auth()->user()->hasPermissionForModuleCode('lucky_draw_management', 'create', 'branch'))
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addPrizeModal">
                                <i class="ti ti-plus"></i> Thêm Giải Thưởng
                            </a>
                        </li>
                    @endif
                    <li>
                        <a class="dropdown-item" href="{{ route('backend.club.lucky-draws.show', $luckyDraw) }}">
                            <i class="ti ti-arrow-left"></i> Quay Lại
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h5 class="mb-0">
                                <i class="ti ti-gift text-primary me-2"></i>
                                Danh sách giải thưởng - {{ $luckyDraw->title }}
                            </h5>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($prizes->count() > 0)
                            <div class="table-responsive">
                                <table class="table text-nowrap table-bordered" id="data-table">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center !important; width: 50px;">STT</th>
                                            <th style="text-align: center !important; width: 50px;">Sắp xếp</th>
                                            <th style="text-align: center !important;">Hình Ảnh</th>
                                            <th>Tên Giải Thưởng</th>
                                            <th style="text-align: center !important;">Loại</th>
                                            <th style="text-align: center !important;">Số Lượng</th>
                                            <th style="text-align: center !important;">Còn Lại</th>
                                            <th style="text-align: center !important;">Thao Tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($prizes as $index => $prize)
                                            <tr data-prize-id="{{ $prize->id }}">
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td class="text-center">
                                                    <div class="reorder-handle" style="cursor: grab; color: #6c757d;">
                                                        <i class="ti ti-grip-vertical fs-4"></i>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <img src="{{ $prize->image_url }}" alt="{{ $prize->name }}"
                                                        class="img-thumbnail" style="width: 50px; height: 50px;">
                                                </td>
                                                <td>
                                                    <div class="fw-semibold">{{ $prize->name }}</div>
                                                    @if (!$prize->isAvailable())
                                                        <span class="badge bg-danger">Hết giải</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($prize->special_gift)
                                                        <span class="badge bg-warning">
                                                            <i class="ti ti-star me-1"></i>Giải Đặc Biệt
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">Giải Thường</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-info">{{ $prize->quantity }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge bg-{{ $prize->remaining_quantity > 0 ? 'success' : 'danger' }}">
                                                        {{ $prize->remaining_quantity }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="gap-2 fs-15">
                                                        @if (auth()->user()->hasPermissionForModuleCode('lucky_draw_management', 'edit', 'branch'))
                                                            <button class="btn btn-icon btn-sm btn-info-light"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editPrizeModal{{ $prize->id }}"
                                                                title="Chỉnh sửa">
                                                                <i class="ri-edit-line"></i>
                                                            </button>
                                                        @endif
                                                        @if (auth()->user()->hasPermissionForModuleCode('lucky_draw_management', 'delete', 'branch'))
                                                            <button class="btn btn-icon btn-sm btn-danger-light"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#deletePrizeModal{{ $prize->id }}"
                                                                title="Xóa">
                                                                <i class="ri-delete-bin-line"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>

                                            @if (auth()->user()->hasPermissionForModuleCode('lucky_draw_management', 'edit', 'branch'))
                                                <!-- Edit Prize Modal for each prize -->
                                                <div class="modal fade" id="editPrizeModal{{ $prize->id }}"
                                                    tabindex="-1" aria-labelledby="editPrizeModalLabel{{ $prize->id }}"
                                                    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="editPrizeModalLabel{{ $prize->id }}">
                                                                    <i class="ti ti-edit text-primary me-2"></i> Chỉnh Sửa
                                                                    Giải
                                                                    Thưởng
                                                                </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form
                                                                action="{{ route('backend.club.lucky-draws.prizes.update', [$luckyDraw, $prize]) }}"
                                                                method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label for="edit_name{{ $prize->id }}"
                                                                            class="form-label">Tên Giải Thưởng <span
                                                                                class="text-danger">*</span></label>
                                                                        <input type="text" class="form-control"
                                                                            id="edit_name{{ $prize->id }}"
                                                                            name="name" value="{{ $prize->name }}"
                                                                            required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="edit_image{{ $prize->id }}"
                                                                            class="form-label">Hình Ảnh</label>
                                                                        <input type="file" class="form-control"
                                                                            id="edit_image{{ $prize->id }}"
                                                                            name="image" accept="image/*">
                                                                        <small class="form-text text-muted">Hỗ trợ: JPEG,
                                                                            PNG,
                                                                            JPG, GIF (tối đa 2MB)</small>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="edit_quantity{{ $prize->id }}"
                                                                            class="form-label">Số Lượng <span
                                                                                class="text-danger">*</span></label>
                                                                        <input type="number" class="form-control"
                                                                            id="edit_quantity{{ $prize->id }}"
                                                                            name="quantity" min="1"
                                                                            value="{{ $prize->quantity }}" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input"
                                                                                type="checkbox"
                                                                                id="edit_special_gift{{ $prize->id }}"
                                                                                name="special_gift" value="1"
                                                                                {{ $prize->special_gift ? 'checked' : '' }}>
                                                                            <label class="form-check-label"
                                                                                for="edit_special_gift{{ $prize->id }}">
                                                                                <i
                                                                                    class="ti ti-star text-warning me-1"></i>
                                                                                Giải Đặc Biệt
                                                                            </label>
                                                                        </div>
                                                                        <small class="form-text text-muted">Đánh dấu giải
                                                                            thưởng này là giải đặc biệt</small>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Hủy</button>
                                                                    <button type="submit" class="btn btn-primary">
                                                                        <i class="ti ti-device-floppy me-1"></i> Cập Nhật
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            @if (auth()->user()->hasPermissionForModuleCode('lucky_draw_management', 'delete', 'branch'))
                                                <!-- Delete Prize Modal for each prize -->
                                                <div class="modal fade" id="deletePrizeModal{{ $prize->id }}"
                                                    tabindex="-1"
                                                    aria-labelledby="deletePrizeModalLabel{{ $prize->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="deletePrizeModalLabel{{ $prize->id }}">
                                                                    <i class="ti ti-trash text-danger me-2"></i> Xóa Giải
                                                                    Thưởng
                                                                </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Bạn có chắc chắn muốn xóa giải thưởng
                                                                    <strong>"{{ $prize->name }}"</strong>?
                                                                </p>
                                                                <p class="text-muted mb-0">Hành động này không thể hoàn
                                                                    tác.
                                                                </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Hủy</button>
                                                                <form
                                                                    action="{{ route('backend.club.lucky-draws.prizes.destroy', [$luckyDraw, $prize]) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger">
                                                                        <i class="ti ti-trash me-1"></i> Xóa
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="ti ti-gift text-muted fs-1 mb-3"></i>
                                <p class="text-muted">Chưa có giải thưởng nào.</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#addPrizeModal">
                                    <i class="ti ti-plus"></i> Thêm giải thưởng đầu tiên
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!--End::row-1 -->

    </div>

    @if (auth()->user()->hasPermissionForModuleCode('lucky_draw_management', 'create', 'branch'))
        <!-- Add Prize Modal -->
        <div class="modal fade" id="addPrizeModal" tabindex="-1" aria-labelledby="addPrizeModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPrizeModalLabel">
                            <i class="ti ti-plus text-primary me-2"></i> Thêm Giải Thưởng
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('backend.club.lucky-draws.prizes.store', $luckyDraw) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Tên Giải Thưởng <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Hình Ảnh</label>
                                <input type="file" class="form-control" id="image" name="image"
                                    accept="image/*">
                                <small class="form-text text-muted">Hỗ trợ: JPEG, PNG, JPG, GIF (tối đa 2MB)</small>
                            </div>
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Số Lượng <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="quantity" name="quantity"
                                    min="1" value="1" required>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="special_gift"
                                        name="special_gift" value="1">
                                    <label class="form-check-label" for="special_gift">
                                        <i class="ti ti-star text-warning me-1"></i>
                                        Giải Đặc Biệt
                                    </label>
                                </div>
                                <small class="form-text text-muted">Đánh dấu giải thưởng này là giải đặc biệt</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i> Lưu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <!-- Sortable.js library for drag and drop reordering -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Sortable for prize reordering
            const tbody = document.querySelector('#data-table tbody');
            if (tbody) {
                new Sortable(tbody, {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    handle: '.reorder-handle',
                    onEnd: function(evt) {
                        const items = Array.from(tbody.querySelectorAll('tr')).map((tr, index) => {
                            const prizeId = tr.getAttribute('data-prize-id');
                            return {
                                id: prizeId,
                                order: index + 1
                            };
                        });

                        // Send reorder request to server
                        $.ajax({
                            url: '{{ route('backend.club.lucky-draws.prizes.reorder', $luckyDraw) }}',
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Content-Type': 'application/json'
                            },
                            data: JSON.stringify({
                                items: items
                            }),
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(
                                        'Đã cập nhật thứ tự giải thưởng thành công!');
                                    // Update order numbers in the table
                                    updateOrderNumbers();
                                } else {
                                    toastr.error('Có lỗi xảy ra khi cập nhật thứ tự!');
                                    location.reload(); // Reload to reset order
                                }
                            },
                            error: function() {
                                toastr.error('Có lỗi xảy ra khi cập nhật thứ tự!');
                                location.reload(); // Reload to reset order
                            }
                        });
                    }
                });
            }

            // Function to update order numbers in the table
            function updateOrderNumbers() {
                $('#data-table tbody tr').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });
            }
        });
    </script>
@endsection
