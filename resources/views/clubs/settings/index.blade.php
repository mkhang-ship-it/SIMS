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
                            @if (auth()->user()->hasPermissionForModuleCode('setting_management', 'general', 'branch'))
                                <button class="list-group-item list-group-item-action active" id="tab-general-btn"
                                    data-bs-toggle="tab" data-bs-target="#tab-general" type="button">Thông Tin
                                    Chung</button>
                            @endif
                            @if (auth()->user()->hasPermissionForModuleCode('setting_management', 'word_template', 'branch'))
                                <button class="list-group-item list-group-item-action" id="tab-word-template-btn"
                                    data-bs-toggle="tab" data-bs-target="#tab-word-template" type="button">Mẫu
                                    Word</button>
                            @endif
                            @if (auth()->user()->hasPermissionForModuleCode('setting_management', 'payment', 'branch'))
                                <button class="list-group-item list-group-item-action" id="tab-payment-btn"
                                    data-bs-toggle="tab" data-bs-target="#tab-payment" type="button">Thông Tin Thanh
                                    Toán</button>
                            @endif
                            @if (auth()->user()->hasPermissionForModuleCode('setting_management', 'mail', 'branch'))
                                <button class="list-group-item list-group-item-action" id="tab-mail-btn"
                                    data-bs-toggle="tab" data-bs-target="#tab-mail" type="button">Cấu Hình Email</button>
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
                            @if (auth()->user()->hasPermissionForModuleCode('setting_management', 'general', 'branch'))
                                <div class="tab-pane fade show active" id="tab-general">
                                    @include('clubs.settings.partials.general')
                                </div>
                            @endif
                            @if (auth()->user()->hasPermissionForModuleCode('setting_management', 'word_template', 'branch'))
                                <div class="tab-pane fade" id="tab-word-template">
                                    @include('clubs.settings.partials.word-template')
                                </div>
                            @endif
                            @if (auth()->user()->hasPermissionForModuleCode('setting_management', 'payment', 'branch'))
                                <div class="tab-pane fade" id="tab-payment">
                                    @include('clubs.settings.partials.payment')
                                </div>
                            @endif
                            @if (auth()->user()->hasPermissionForModuleCode('setting_management', 'mail', 'branch'))
                                <div class="tab-pane fade" id="tab-mail">
                                    @include('clubs.settings.partials.mail')
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
            const tabKey = 'settingsActiveTab';
            const tabTriggerList = [].slice.call(document.querySelectorAll(
                '#settings-tab button[data-bs-toggle="tab"]'));

            // Tab titles mapping
            const tabTitles = {
                'tab-general-btn': 'Thông Tin Chung',
                'tab-word-template-btn': 'Mẫu Word',
                'tab-payment-btn': 'Thông Tin Thanh Toán',
                'tab-mail-btn': 'Cấu Hình Email',
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
                const savedTab = document.querySelector(`#settings-tab button#${savedTabId}`);
                if (savedTab) {
                    // Remove active class from all tabs
                    tabTriggerList.forEach(tab => {
                        tab.classList.remove('active');
                    });

                    // Remove active class from all tab panes
                    document.querySelectorAll('.tab-pane').forEach(pane => {
                        pane.classList.remove('show', 'active');
                    });

                    // Add active class to saved tab
                    savedTab.classList.add('active');

                    // Show saved tab pane
                    const targetPane = document.querySelector(savedTab.getAttribute('data-bs-target'));
                    if (targetPane) {
                        targetPane.classList.add('show', 'active');
                    }

                    // Update title
                    updateTabTitle(savedTabId);
                }
            }

            tabTriggerList.forEach(function(tabTriggerEl) {
                tabTriggerEl.addEventListener('shown.bs.tab', function(event) {
                    localStorage.setItem(tabKey, event.target.id);
                    updateTabTitle(event.target.id);
                });
            });

            // Hiển thị các trường SMTP/Outlook theo mailer
            function toggleMailFields() {
                const mailer = document.getElementById('mail_mailer_select').value;
                const smtpFields = document.getElementById('smtp-fields');
                const outlookFields = document.getElementById('outlook-fields');
                smtpFields.style.display = (mailer === 'smtp') ? '' : 'none';
                outlookFields.style.display = (mailer === 'outlook') ? '' : 'none';

                // Fix: Remove 'required' from SMTP fields when hidden, add when shown
                const smtpRequiredFields = smtpFields.querySelectorAll(
                    '[name="mail_host"], [name="mail_port"], [name="mail_encryption"], [name="mail_username"]');
                smtpRequiredFields.forEach(function(input) {
                    if (mailer === 'smtp') {
                        input.setAttribute('required', 'required');
                    } else {
                        input.removeAttribute('required');
                    }
                });
            }
            document.getElementById('mail_mailer_select').addEventListener('change', toggleMailFields);
            // Gọi khi load trang
            toggleMailFields();

        });
    </script>

    <!-- Bank Settings JavaScript -->
    <script src="{{ asset('assets/js/bank-settings.js') }}"></script>
@endsection
