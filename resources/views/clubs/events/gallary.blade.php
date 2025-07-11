{{-- Updated: Added checkbox selection for bulk operations in header (desktop and mobile) with proper order: Select All → Bulk Delete → Add New, download functionality for individual images, added delete confirmation modal with image preview for both single and bulk delete, hidden Add New and Bulk Delete buttons on mobile (only in dropdown), added bulk delete functionality with clean AJAX responses (no server messages), improved UI with hover effects and transitions, fixed modal z-index issues for proper display, implemented multiple image upload with preview grid and dynamic button text, fixed bulk delete route conflict by using separate endpoint /bulk-delete --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Quản Lý Ảnh Sự Kiện: {{ $event->name }}</h1>

            <!-- Nút trên desktop -->
            <div class="d-flex gap-2 align-items-center">
                @if (count($images) > 0)
                    <div class="form-check me-3">
                        <input class="form-check-input" type="checkbox" id="selectAllImages">
                        <label class="form-check-label" for="selectAllImages">
                            Chọn tất cả ảnh
                        </label>
                    </div>
                @endif
                <div id="bulkActions" style="display: none;">
                    <button type="button" id="bulkDeleteBtn" class="btn btn-danger me-2 d-none d-md-inline-block">
                        <i class="ti ti-trash"></i>
                        Xóa đã chọn (<span id="selectedCount">0</span>)
                    </button>
                </div>
                @if (auth()->user()->hasPermissionForModuleCode('event_image_management', 'create', 'branch'))
                    <button type="button" class="btn btn-primary d-none d-md-block" data-bs-toggle="modal"
                        data-bs-target="#uploadModal">
                        <i class="ti ti-plus"></i>
                        Thêm Mới
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
                    @if (count($images) > 0)
                        <li>
                            <div class="dropdown-item-text">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAllImagesMobile">
                                    <label class="form-check-label" for="selectAllImagesMobile">
                                        Chọn tất cả ảnh
                                    </label>
                                </div>
                            </div>
                        </li>
                        <li id="bulkActionsMobile" style="display: none;">
                            <button type="button" class="dropdown-item text-danger"
                                onclick="document.getElementById('bulkDeleteBtn').click()">
                                <i class="ti ti-trash"></i> Xóa đã chọn (<span id="selectedCountMobile">0</span>)
                            </button>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                    @endif
                    @if (auth()->user()->hasPermissionForModuleCode('event_image_management', 'create', 'branch'))
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                <i class="ti ti-plus"></i> Thêm Mới
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>



        <!-- Gallery Grid -->
        <div class="row" id="galleryContainer">
            @forelse($images as $image)
                <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3" data-image-id="{{ $image->id }}">
                    <div class="card position-relative gallery-image-card">
                        <!-- Selection checkbox -->
                        <div class="position-absolute top-0 start-0 p-2">
                            <div class="form-check">
                                <input class="form-check-input image-checkbox" type="checkbox" value="{{ $image->id }}"
                                    id="image_{{ $image->id }}">
                            </div>
                        </div>

                        <a href="{{ asset('storage/' . $image->image_path) }}" class="glightbox" data-gallery="gallery1">
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="Event Image" class="card-img-top"
                                style="height: 200px; object-fit: cover;">
                        </a>
                        <div class="card-body p-2">
                            <div class="btn-group btn-group-sm w-100">
                                <button type="button" class="btn btn-outline-primary download-image-btn"
                                    data-image-id="{{ $image->id }}">
                                    <i class="fe fe-download"></i> Tải
                                </button>
                                <button type="button" class="btn btn-outline-danger delete-image-btn"
                                    data-image-id="{{ $image->id }}">
                                    <i class="fe fe-trash-2"></i> Xóa
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fe fe-image fs-24 mb-2"></i>
                        <p class="mb-0">Chưa có hình ảnh nào. Hãy thêm hình ảnh đầu tiên!</p>
                    </div>
                </div>
            @endforelse
        </div>

    </div>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Thêm Hình Ảnh</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="uploadForm" action="{{ route('backend.club.events.images.store', $event) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="imageUpload" class="form-label">Chọn Hình Ảnh</label>
                            <input type="file" class="form-control" id="imageUpload" name="images[]"
                                accept="image/*" multiple required>
                            <div class="form-text">Định dạng được hỗ trợ: JPEG, PNG, JPG, GIF. Kích thước tối đa: 5MB mỗi
                                ảnh. Có thể chọn nhiều ảnh cùng lúc. Tối đa 30 ảnh mỗi lần.</div>
                        </div>
                        <div id="uploadPreview" class="mb-3" style="display: none;">
                            <div class="row" id="previewContainer"></div>
                            <div class="mt-2">
                                <small class="text-muted">Đã chọn: <span id="fileCount">0</span> ảnh</small>
                            </div>
                        </div>
                        <div id="uploadErrors" class="alert alert-danger" style="display: none;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary" id="uploadSubmitBtn">
                            <span class="upload-btn-text">Tải Lên</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">Xác Nhận Xóa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="fe fe-alert-triangle text-warning fs-48 mb-3"></i>
                        <h6 id="deleteConfirmMessage">Bạn có chắc chắn muốn xóa hình ảnh này?</h6>
                        <p class="text-muted">Hành động này không thể hoàn tác.</p>
                        <div id="deletePreviewContainer" class="mb-3" style="display: none;">
                            <img id="deletePreviewImage" src="" alt="Image to delete" class="img-thumbnail"
                                style="max-height: 150px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" id="confirmDeleteButton" class="btn btn-danger">
                        <span class="delete-btn-text">Xóa</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .image-checkbox {
            transform: scale(1.2);
        }

        .position-absolute.top-0.start-0 {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 0 0 8px 0;
        }

        .card:hover .position-absolute.top-0.start-0 {
            background: rgba(255, 255, 255, 1);
        }

        #bulkActions {
            transition: all 0.3s ease;
        }

        .gallery-image-card {
            transition: transform 0.2s ease;
        }

        .gallery-image-card:hover {
            transform: translateY(-2px);
        }

        .alert {
            z-index: 1040;
        }

        .form-check-label {
            font-weight: 500;
            color: #495057;
        }

        .page-header-breadcrumb .form-check {
            margin-bottom: 0;
        }

        #deleteConfirmModal .modal-body {
            padding: 2rem 1rem;
        }

        #deleteConfirmModal .fe-alert-triangle {
            color: #f39c12;
        }

        #deletePreviewImage {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Modal z-index fixes */
        .modal {
            z-index: 1055 !important;
        }

        .modal-backdrop {
            z-index: 1050 !important;
        }

        #uploadModal,
        #deleteConfirmModal {
            z-index: 1060 !important;
        }

        #uploadModal .modal-dialog,
        #deleteConfirmModal .modal-dialog {
            z-index: 1065 !important;
        }

        /* Ensure modal content is properly positioned */
        .modal-content {
            position: relative;
            z-index: 1070;
        }

        /* Fix any potential header/navbar conflicts */
        .page-header-breadcrumb {
            z-index: 1030;
        }

        /* Bootstrap overrides for modal positioning */
        .modal.show {
            display: block !important;
        }

        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out;
            transform: translate(0, -50px);
        }

        .modal.show .modal-dialog {
            transform: none;
        }

        /* Upload preview styling */
        #previewContainer .img-thumbnail {
            border: 2px solid #e9ecef;
            transition: border-color 0.3s ease;
        }

        #previewContainer .img-thumbnail:hover {
            border-color: #007bff;
        }

        #previewContainer small {
            font-size: 0.75rem;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const eventId = "{{ $event->id }}";
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Select all functionality
            const selectAllCheckbox = document.getElementById('selectAllImages');
            const selectAllCheckboxMobile = document.getElementById('selectAllImagesMobile');
            const imageCheckboxes = document.querySelectorAll('.image-checkbox');
            const bulkActions = document.getElementById('bulkActions');
            const bulkActionsMobile = document.getElementById('bulkActionsMobile');
            const selectedCount = document.getElementById('selectedCount');
            const selectedCountMobile = document.getElementById('selectedCountMobile');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

            // Delete modal elements
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            const deleteConfirmMessage = document.getElementById('deleteConfirmMessage');
            const deletePreviewContainer = document.getElementById('deletePreviewContainer');
            const deletePreviewImage = document.getElementById('deletePreviewImage');
            const confirmDeleteButton = document.getElementById('confirmDeleteButton');

            // Track current delete operation
            let currentDeleteType = null; // 'single' or 'bulk'
            let currentDeleteData = null;

            function updateBulkActions() {
                const checkedBoxes = document.querySelectorAll('.image-checkbox:checked');
                const count = checkedBoxes.length;

                if (count > 0) {
                    bulkActions.style.display = 'block';
                    selectedCount.textContent = count;
                    if (bulkActionsMobile) {
                        bulkActionsMobile.style.display = 'block';
                        selectedCountMobile.textContent = count;
                    }
                } else {
                    bulkActions.style.display = 'none';
                    if (bulkActionsMobile) {
                        bulkActionsMobile.style.display = 'none';
                    }
                }
            }

            function updateSelectAllState() {
                const allChecked = Array.from(imageCheckboxes).every(cb => cb.checked);
                const someChecked = Array.from(imageCheckboxes).some(cb => cb.checked);

                // Update desktop checkbox
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.indeterminate = someChecked && !allChecked;
                }

                // Update mobile checkbox
                if (selectAllCheckboxMobile) {
                    selectAllCheckboxMobile.checked = allChecked;
                    selectAllCheckboxMobile.indeterminate = someChecked && !allChecked;
                }
            }

            function toggleAllImages(checked) {
                imageCheckboxes.forEach(checkbox => {
                    checkbox.checked = checked;
                });
                updateBulkActions();
                updateSelectAllState();
            }

            // Desktop select all checkbox
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    toggleAllImages(this.checked);
                });
            }

            // Mobile select all checkbox
            if (selectAllCheckboxMobile) {
                selectAllCheckboxMobile.addEventListener('change', function() {
                    toggleAllImages(this.checked);
                });
            }

            imageCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateBulkActions();
                    updateSelectAllState();
                });
            });

            // Download functionality
            document.addEventListener('click', function(e) {
                if (e.target.closest('.download-image-btn')) {
                    const imageId = e.target.closest('.download-image-btn').dataset.imageId;
                    const downloadUrl = `/club/events/${eventId}/images/${imageId}/download`;
                    window.location.href = downloadUrl;
                }
            });

            // Single delete functionality
            document.addEventListener('click', function(e) {
                if (e.target.closest('.delete-image-btn')) {
                    const imageId = e.target.closest('.delete-image-btn').dataset.imageId;
                    const imageCard = e.target.closest('[data-image-id]');
                    const imageElement = imageCard.querySelector('img');

                    // Setup modal for single delete
                    currentDeleteType = 'single';
                    currentDeleteData = imageId;
                    deleteConfirmMessage.textContent = 'Bạn có chắc chắn muốn xóa hình ảnh này?';

                    // Show image preview
                    if (imageElement) {
                        deletePreviewImage.src = imageElement.src;
                        deletePreviewContainer.style.display = 'block';
                    } else {
                        deletePreviewContainer.style.display = 'none';
                    }

                    deleteModal.show();
                }
            });

            // Bulk delete functionality
            if (bulkDeleteBtn) {
                bulkDeleteBtn.addEventListener('click', function() {
                    const checkedBoxes = document.querySelectorAll('.image-checkbox:checked');
                    const imageIds = Array.from(checkedBoxes).map(cb => cb.value);

                    if (imageIds.length === 0) {
                        return;
                    }

                    // Setup modal for bulk delete
                    currentDeleteType = 'bulk';
                    currentDeleteData = imageIds;
                    deleteConfirmMessage.textContent =
                        `Bạn có chắc chắn muốn xóa ${imageIds.length} hình ảnh đã chọn?`;
                    deletePreviewContainer.style.display = 'none';

                    deleteModal.show();
                });
            }

            // Handle confirm delete button in modal
            confirmDeleteButton.addEventListener('click', function() {
                if (currentDeleteType === 'single' && currentDeleteData) {
                    deleteImage(currentDeleteData);
                } else if (currentDeleteType === 'bulk' && currentDeleteData) {
                    bulkDeleteImages(currentDeleteData);
                }
                deleteModal.hide();
            });

            function deleteImage(imageId) {
                fetch(`/club/events/${eventId}/images/${imageId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the image card from DOM
                            const imageCard = document.querySelector(`[data-image-id="${imageId}"]`);
                            if (imageCard) {
                                imageCard.remove();
                            }

                            // Update bulk actions
                            updateBulkActions();

                            // Check if no images left
                            checkEmptyGallery();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }

            function bulkDeleteImages(imageIds) {
                fetch(`/club/events/${eventId}/images/bulk-delete`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            image_ids: imageIds
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the image cards from DOM
                            imageIds.forEach(imageId => {
                                const imageCard = document.querySelector(
                                    `[data-image-id="${imageId}"]`);
                                if (imageCard) {
                                    imageCard.remove();
                                }
                            });

                            // Update bulk actions
                            updateBulkActions();

                            // Update select all checkboxes and bulk actions
                            if (selectAllCheckbox) {
                                selectAllCheckbox.checked = false;
                                selectAllCheckbox.indeterminate = false;
                            }
                            if (selectAllCheckboxMobile) {
                                selectAllCheckboxMobile.checked = false;
                                selectAllCheckboxMobile.indeterminate = false;
                            }
                            if (bulkActionsMobile) {
                                bulkActionsMobile.style.display = 'none';
                            }

                            // Check if no images left
                            checkEmptyGallery();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }



            function checkEmptyGallery() {
                const remainingImages = document.querySelectorAll('[data-image-id]');
                if (remainingImages.length === 0) {
                    const galleryContainer = document.getElementById('galleryContainer');
                    galleryContainer.innerHTML = `
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <i class="fe fe-image fs-24 mb-2"></i>
                                <p class="mb-0">Chưa có hình ảnh nào. Hãy thêm hình ảnh đầu tiên!</p>
                            </div>
                        </div>
                    `;

                    // Hide select all controls
                    if (selectAllCheckbox) {
                        selectAllCheckbox.closest('.form-check').style.display = 'none';
                    }
                    if (selectAllCheckboxMobile) {
                        selectAllCheckboxMobile.closest('li').style.display = 'none';
                    }
                    if (bulkActionsMobile) {
                        bulkActionsMobile.style.display = 'none';
                    }
                }
            }

            // Upload preview functionality
            const imageUpload = document.getElementById('imageUpload');
            const uploadPreview = document.getElementById('uploadPreview');
            const previewContainer = document.getElementById('previewContainer');
            const fileCount = document.getElementById('fileCount');
            const uploadSubmitBtn = document.getElementById('uploadSubmitBtn');
            const uploadBtnText = uploadSubmitBtn.querySelector('.upload-btn-text');

            if (imageUpload) {
                imageUpload.addEventListener('change', function(e) {
                    const files = Array.from(e.target.files);

                    if (files.length > 0) {
                        previewContainer.innerHTML = '';
                        fileCount.textContent = files.length;

                        files.forEach((file, index) => {
                            if (file.type.startsWith('image/')) {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    const colDiv = document.createElement('div');
                                    colDiv.className = 'col-md-3 col-sm-4 col-6 mb-2';

                                    colDiv.innerHTML = `
                                        <div class="position-relative">
                                            <img src="${e.target.result}" alt="Preview ${index + 1}"
                                                 class="img-thumbnail w-100" style="height: 100px; object-fit: cover;">
                                            <small class="text-muted d-block text-center mt-1">${file.name}</small>
                                        </div>
                                    `;

                                    previewContainer.appendChild(colDiv);
                                };
                                reader.readAsDataURL(file);
                            }
                        });

                        uploadPreview.style.display = 'block';
                        uploadBtnText.textContent = files.length === 1 ? 'Tải Lên 1 Ảnh' :
                            `Tải Lên ${files.length} Ảnh`;
                    } else {
                        uploadPreview.style.display = 'none';
                        uploadBtnText.textContent = 'Tải Lên';
                    }
                });
            }
        });
    </script>
@endsection
