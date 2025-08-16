{{-- Event Plan Show View - Single Event Plan Display with Navigation Tabs
Displays individual event plan in detailed format for specific event
Updated: Changed from index view to show single event plan details
Updated: Split into 6 nav tabs with separate files for better organization:
- Tab 1: General information (show-general.blade.php) - Thông tin chung, Nội dung tổ chức, Hình thức tổ chức, Thành phần tổ chức
- Tab 2: Objectives (show-objectives.blade.php) - Mục tiêu và các chỉ số đo lường display only
- Tab 3: Action Plans (show-action-plans.blade.php) - Kế hoạch hành động chi tiết với timeline và quản lý rủi ro
- Tab 4: Budget Items (show-budget-items.blade.php) - Quản lý ngân sách chi tiết với tính toán chi phí
- Tab 5: Equipment (show-equipment.blade.php) - Quản lý vật dụng với thông tin tên, số lượng, loại (club/school)
- Tab 6: Timeline (show-timeline.blade.php) - Quản lý timeline sự kiện với khả năng kéo thả để sắp xếp thứ tự
Updated: ALL modals placed in main file after card to fix footer layout issues:
- Edit Event Plan Modal, Add/Edit/Delete Objective Modals, Add/Edit/Delete Action Plan Modals, Add/Edit/Delete Budget Item Modals, Add/Edit/Delete Equipment Modals
Updated: Added mobile-responsive nav tabs with horizontal scrolling capability
Updated: Added Excel export button in header to export comprehensive event information to Excel format --}}

@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <div>
                <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Kế Hoạch Sự Kiện</h1>
            </div>
            <div class="d-flex gap-2">

                <a href="{{ route('backend.club.events.index') }}" class="btn btn-primary">
                    <i class="ti ti-arrow-left me-1"></i>Quay Lại
                </a>
            </div>
        </div>

        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="card-title">
                            <h5 class="fw-semibold mb-0">{{ $eventPlan->event->name ?? 'Chưa có tên' }}</h5>
                            <small class="text-muted">{{ $eventPlan->event->event_code ?? 'N/A' }}</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div>
                                <span class="badge bg-{{ $eventPlan->event->status_color ?? 'secondary' }} fs-12">
                                    {{ $eventPlan->event->status_label ?? 'Chưa xác định' }}
                                </span>
                            </div>
                            @if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'view', 'branch'))
                                <a href="{{ route('backend.club.events.export.excel', $eventPlan->event->id) }}"
                                    class="btn btn-primary-light">
                                    <i class="ti ti-download me-1"></i>Xuất Excel
                                </a>
                            @endif
                            @if (auth()->user()->hasPermissionForModuleCode('event_plan_management', 'edit', 'branch'))
                                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                    data-bs-target="#editEventPlanModal">
                                    <i class="ti ti-edit me-1"></i>Cập Nhật Kế Hoạch
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">

                        <!-- Navigation Tabs with Mobile Scroll -->
                        <div class="nav-tabs-wrapper">
                            <ul class="nav nav-tabs nav-justified mb-4 mobile-scroll-tabs" id="eventPlanTabs"
                                role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="general-tab" data-bs-toggle="tab"
                                        data-bs-target="#general-content" type="button" role="tab"
                                        aria-controls="general-content" aria-selected="true">
                                        <i class="ti ti-info-circle me-1"></i>Thông Tin Chung
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="objectives-tab" data-bs-toggle="tab"
                                        data-bs-target="#objectives-content" type="button" role="tab"
                                        aria-controls="objectives-content" aria-selected="false">
                                        <i class="ti ti-target me-1"></i>Mục Tiêu Và Chỉ Số
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="action-plans-tab" data-bs-toggle="tab"
                                        data-bs-target="#action-plans-content" type="button" role="tab"
                                        aria-controls="action-plans-content" aria-selected="false">
                                        <i class="ti ti-checklist me-1"></i>Kế Hoạch Hành Động
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="budget-items-tab" data-bs-toggle="tab"
                                        data-bs-target="#budget-items-content" type="button" role="tab"
                                        aria-controls="budget-items-content" aria-selected="false">
                                        <i class="ti ti-coin me-1"></i>Dự Toán Chi Phí
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="equipment-tab" data-bs-toggle="tab"
                                        data-bs-target="#equipment-content" type="button" role="tab"
                                        aria-controls="equipment-content" aria-selected="false">
                                        <i class="ti ti-package me-1"></i>Quản Lý Vật Dụng
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="timeline-tab" data-bs-toggle="tab"
                                        data-bs-target="#timeline-content" type="button" role="tab"
                                        aria-controls="timeline-content" aria-selected="false">
                                        <i class="ti ti-timeline me-1"></i>Timeline Sự Kiện
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <!-- Tab Content -->
                        <div class="tab-content" id="eventPlanTabsContent">
                            <!-- General Information Tab -->
                            <div class="tab-pane fade show active" id="general-content" role="tabpanel"
                                aria-labelledby="general-tab">
                                @include('clubs.event-plans.show-general')
                            </div>

                            <!-- Objectives Tab -->
                            <div class="tab-pane fade" id="objectives-content" role="tabpanel"
                                aria-labelledby="objectives-tab">
                                @include('clubs.event-plans.show-objectives')
                            </div>

                            <!-- Action Plans Tab -->
                            <div class="tab-pane fade" id="action-plans-content" role="tabpanel"
                                aria-labelledby="action-plans-tab">
                                @include('clubs.event-plans.show-action-plans')
                            </div>

                            <!-- Budget Items Tab -->
                            <div class="tab-pane fade" id="budget-items-content" role="tabpanel"
                                aria-labelledby="budget-items-tab">
                                @include('clubs.event-plans.show-budget-items')
                            </div>

                            <!-- Equipment Tab -->
                            <div class="tab-pane fade" id="equipment-content" role="tabpanel"
                                aria-labelledby="equipment-tab">
                                @include('clubs.event-plans.show-equipment')
                            </div>

                            <!-- Timeline Tab -->
                            <div class="tab-pane fade" id="timeline-content" role="tabpanel"
                                aria-labelledby="timeline-tab">
                                @include('clubs.event-plans.show-timeline')
                            </div>
                        </div>

                    </div>
                    <div class="card-footer bg-light">
                        <div class="d-flex align-items-center justify-content-between">
                            <small class="text-muted">
                                <i class="ti ti-calendar me-1"></i>
                                Tạo lúc: {{ $eventPlan->created_at->format('d/m/Y H:i') }}
                            </small>
                            <small class="text-muted">
                                <i class="ti ti-clock me-1"></i>
                                Cập nhật: {{ $eventPlan->updated_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Include Modals - Outside main content to prevent footer layout issues --}}
@include('clubs.event-plans.show-modals')

{{-- Mobile Navigation Tabs Scroll Styles --}}
<style>
    /* Mobile responsive nav tabs with horizontal scroll */
    @media (max-width: 768px) {
        .nav-tabs-wrapper {
            position: relative;
            margin: 0 -15px;
            /* Extend to container edges */
            padding: 0 15px;
        }

        .mobile-scroll-tabs {
            flex-wrap: nowrap !important;
            overflow-x: auto;
            overflow-y: hidden;
            /* -webkit-overflow-scrolling: touch; */
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;
            /* IE/Edge */
            border-bottom: 1px solid #dee2e6;
        }

        .mobile-scroll-tabs::-webkit-scrollbar {
            display: none;
            /* Chrome/Safari */
        }

        .mobile-scroll-tabs .nav-item {
            flex: 0 0 auto;
            min-width: 160px;
            /* Minimum width for each tab */
        }

        .mobile-scroll-tabs .nav-link {
            white-space: nowrap;
            text-align: center;
            padding: 12px 16px;
            font-size: 13px;
            border-radius: 0;
        }

        .mobile-scroll-tabs .nav-link i {
            display: block;
            margin-bottom: 4px;
            font-size: 16px;
        }

        /* Scroll indicator for mobile */
        .nav-tabs-wrapper::after {
            content: '';
            position: absolute;
            top: 50%;
            right: 0;
            transform: translateY(-50%);
            width: 20px;
            height: 100%;
            background: linear-gradient(to left, rgba(255, 255, 255, 1), rgba(255, 255, 255, 0));
            pointer-events: none;
            z-index: 2;
        }
    }

    /* Tablet adjustments */
    @media (min-width: 769px) and (max-width: 1024px) {
        .mobile-scroll-tabs .nav-link {
            font-size: 14px;
            padding: 10px 12px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabKey = 'eventPlanActiveTab';
        const tabTriggerList = [].slice.call(document.querySelectorAll(
            '#eventPlanTabs button[data-bs-toggle="tab"]'));

        const savedTabId = localStorage.getItem(tabKey);
        if (savedTabId) {
            const savedTab = document.querySelector(`#eventPlanTabs button#${savedTabId}`);
            if (savedTab) {
                const tab = new bootstrap.Tab(savedTab);
                tab.show();
            }
        }

        tabTriggerList.forEach(function(tabTriggerEl) {
            tabTriggerEl.addEventListener('shown.bs.tab', function(event) {
                localStorage.setItem(tabKey, event.target.id);
            });
        });
    });
</script>
