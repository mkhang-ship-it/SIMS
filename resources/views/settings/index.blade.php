{{-- Admin Settings Page - Email, General Configuration and Permission Management --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">

        <!-- Start::row-1 -->
        <div class="row">
            <!-- Sidebar Tabs -->
            <div class="col-xl-3">
                <div class="card custom-card">
                    <div class="card-header">
                        <h5 class="card-title">Danh Mục Cài Đặt</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush" id="settings-tab">
                            @if (auth()->user()->hasPermissionForModuleCode('setting_management', 'general', 'central'))
                                <button class="list-group-item list-group-item-action active" id="tab-general-btn"
                                    data-bs-toggle="tab" data-bs-target="#tab-general" type="button">Thông Tin
                                    Chung</button>
                            @endif
                            @if (auth()->user()->hasPermissionForModuleCode('setting_management', 'mail', 'central'))
                                <button class="list-group-item list-group-item-action" id="tab-mail-btn"
                                    data-bs-toggle="tab" data-bs-target="#tab-mail" type="button">Cấu Hình Email</button>
                            @endif
                            @if (auth()->user()->hasPermissionForModuleCode('setting_management', 'general', 'central'))
                                <button class="list-group-item list-group-item-action" id="tab-permissions-btn"
                                    data-bs-toggle="tab" data-bs-target="#tab-permissions" type="button">Quản Lý Phân
                                    Quyền</button>
                            @endif
                            @if (auth()->user()->hasPermissionForModuleCode('setting_management', 'maintenance', 'central'))
                                <button class="list-group-item list-group-item-action" id="tab-maintenance-btn"
                                    data-bs-toggle="tab" data-bs-target="#tab-maintenance" type="button">Chế Độ Bảo
                                    Trì</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Section -->
            <div class="col-xl-9">
                <div class="card custom-card">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                        <div class="card-title fw-semibold fs-18 mb-2 mb-md-0" id="tab-title">
                            Thông Tin Chung
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            @if (auth()->user()->hasPermissionForModuleCode('setting_management', 'general', 'central'))
                                <div class="tab-pane fade show active" id="tab-general">
                                    @include('settings.partials.general')
                                </div>
                            @endif
                            @if (auth()->user()->hasPermissionForModuleCode('setting_management', 'mail', 'central'))
                                <div class="tab-pane fade" id="tab-mail">
                                    @include('settings.partials.mail')
                                </div>
                            @endif
                            @if (auth()->user()->hasPermissionForModuleCode('setting_management', 'general', 'central'))
                                <div class="tab-pane fade" id="tab-permissions">
                                    @include('settings.partials.permissions')
                                </div>
                            @endif
                            @if (auth()->user()->hasPermissionForModuleCode('setting_management', 'maintenance', 'central'))
                                <div class="tab-pane fade" id="tab-maintenance">
                                    @include('settings.partials.maintenance')
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End::row-1 -->
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabKey = 'adminSettingsActiveTab';
            const tabTriggerList = [].slice.call(document.querySelectorAll(
                '#settings-tab button[data-bs-toggle="tab"]'));

            // Tab titles mapping
            const tabTitles = {
                'tab-general-btn': 'Thông Tin Chung',
                'tab-mail-btn': 'Cấu Hình Email',
                'tab-permissions-btn': 'Quản Lý Phân Quyền',
                'tab-maintenance-btn': 'Chế Độ Bảo Trì',
            };

            // Function to update tab title
            function updateTabTitle(tabId) {
                const titleElement = document.getElementById('tab-title');
                if (titleElement && tabTitles[tabId]) {
                    titleElement.textContent = tabTitles[tabId];
                }
            }

            const savedTabId = localStorage.getItem(tabKey);
            if (savedTabId) {
                const savedTab = document.getElementById(savedTabId);
                if (savedTab) {
                    const tab = new bootstrap.Tab(savedTab);
                    tab.show();
                    updateTabTitle(savedTabId);
                }
            }

            tabTriggerList.forEach(function(tabTriggerEl) {
                tabTriggerEl.addEventListener('shown.bs.tab', function(event) {
                    const targetId = event.target.id;
                    localStorage.setItem(tabKey, targetId);
                    updateTabTitle(targetId);
                });
            });

            // Mailer toggle functionality
            const mailerSelect = document.getElementById('mail_mailer_select');
            const smtpFields = document.getElementById('smtp-fields');
            const outlookFields = document.getElementById('outlook-fields');

            function toggleFields() {
                const selectedValue = mailerSelect.value;

                if (selectedValue === 'smtp') {
                    smtpFields.style.display = 'block';
                    outlookFields.style.display = 'none';
                } else if (selectedValue === 'outlook') {
                    smtpFields.style.display = 'none';
                    outlookFields.style.display = 'block';
                }
            }

            // Initial toggle
            if (mailerSelect) {
                toggleFields();
                // Toggle on change
                mailerSelect.addEventListener('change', toggleFields);
            }
        });
    </script>
@endsection
