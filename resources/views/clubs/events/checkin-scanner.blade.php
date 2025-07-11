@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Quét QR Tự Động Check-in</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('backend.dashboard') }}">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="#">Sự kiện</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Quét QR Tự Động Check-in</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- Page Header Close -->

        <!-- Instructions Card -->
        <div class="row mb-4" id="instructions-section">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="ri-information-line me-2"></i>
                            Hướng Dẫn Sử Dụng
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="avatar avatar-sm bg-primary-transparent rounded-circle me-3 flex-shrink-0">
                                        <i class="ri-shield-check-line text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Cho phép truy cập camera</h6>
                                        <p class="text-muted mb-0 fs-12">Nhấn "Cho phép" khi trình duyệt yêu cầu quyền truy
                                            cập camera</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div
                                        class="avatar avatar-sm bg-secondary-transparent rounded-circle me-3 flex-shrink-0">
                                        <i class="ri-focus-2-line text-secondary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Hướng camera đúng vị trí</h6>
                                        <p class="text-muted mb-0 fs-12">Đưa mã QR vào khung quét trên màn hình</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="avatar avatar-sm bg-success-transparent rounded-circle me-3 flex-shrink-0">
                                        <i class="ri-hand-heart-line text-success"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Giữ thiết bị ổn định</h6>
                                        <p class="text-muted mb-0 fs-12">Không rung lắc để mã QR được quét chính xác</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="avatar avatar-sm bg-info-transparent rounded-circle me-3 flex-shrink-0">
                                        <i class="ri-check-double-line text-info"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Check-in tự động</h6>
                                        <p class="text-muted mb-0 fs-12">Sau khi quét thành công, hệ thống sẽ tự động
                                            check-in nếu vị trí hợp lệ</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scanner Section -->
        <div class="row" id="scanner-section">
            <div class="col-xl-12">
                <div class="card custom-card scanner-main-card">
                    <div class="card-header scanner-header">
                        <div class="row align-items-center w-100">
                            <div class="col">
                                <div class="d-flex align-items-center">
                                    <div class="scanner-icon-wrapper me-3">
                                        <i class="ri-qr-scan-2-line"></i>
                                    </div>
                                    <div>
                                        <h4 class="card-title mb-1">Quét QR Tự Động Check-in</h4>
                                        <p class="card-subtitle mb-0">Camera sẽ tự động khởi động và check-in ngay khi quét
                                            thành công</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body scanner-body">
                        <!-- Scanner Section -->
                        <div class="row justify-content-center">
                            <div class="col-xl-8 col-lg-10">

                                <!-- QR Reader Container -->
                                <div class="scanner-wrapper text-center mb-4">
                                    <div class="scanner-frame">
                                        <div class="scanner-corners">
                                            <div class="corner top-left"></div>
                                            <div class="corner top-right"></div>
                                            <div class="corner bottom-left"></div>
                                            <div class="corner bottom-right"></div>
                                        </div>
                                        <div id="qr-reader" class="qr-reader-container"></div>
                                    </div>

                                    <!-- Scanner Info -->
                                    <div class="scanner-info mt-3">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="ri-focus-2-line text-primary me-2"></i>
                                            <span class="text-muted">Hướng mã QR vào khung bên trên</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Results -->
                                <div id="qr-reader-results" class="text-center scanner-results"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </div>

    <style>
        /* Scanner Main Card */
        .scanner-main-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            box-shadow: 0 15px 35px rgba(50, 50, 93, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
            overflow: hidden;
        }

        .scanner-header {
            background: transparent;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.5rem 2rem;
        }

        .scanner-header .card-title {
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }

        .scanner-header .card-subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        /* Scanner Icon Wrapper */
        .scanner-icon-wrapper {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            animation: iconFloat 3s ease-in-out infinite;
        }

        .scanner-icon-wrapper i {
            font-size: 2rem;
            color: white;
        }

        @keyframes iconFloat {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        /* Status Indicator */
        .scanner-status {
            text-align: center;
        }

        .status-indicator {
            display: inline-block;
            position: relative;
            margin-bottom: 5px;
        }

        .pulse-dot {
            width: 12px;
            height: 12px;
            background: #00d4aa;
            border-radius: 50%;
            position: relative;
            animation: pulse 2s infinite;
        }

        .pulse-dot::before {
            content: '';
            position: absolute;
            top: -5px;
            left: -5px;
            width: 22px;
            height: 22px;
            border: 2px solid #00d4aa;
            border-radius: 50%;
            animation: pulseRing 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.8;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes pulseRing {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            100% {
                transform: scale(2);
                opacity: 0;
            }
        }

        /* Scanner Body */
        .scanner-body {
            background: white;
            padding: 3rem 2rem;
        }

        /* Scanner Wrapper */
        .scanner-wrapper {
            position: relative;
        }

        .scanner-frame {
            position: relative;
            display: inline-block;
            padding: 20px;
            background: linear-gradient(145deg, #f0f2f5, #ffffff);
            border-radius: 20px;
            box-shadow:
                inset 5px 5px 10px #d1d9e6,
                inset -5px -5px 10px #ffffff,
                0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .scanner-corners {
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            bottom: 10px;
            pointer-events: none;
            z-index: 2;
        }

        .corner {
            position: absolute;
            width: 30px;
            height: 30px;
            border: 3px solid #667eea;
            animation: cornerPulse 2s ease-in-out infinite;
        }

        .corner.top-left {
            top: 0;
            left: 0;
            border-right: none;
            border-bottom: none;
            border-radius: 12px 0 0 0;
        }

        .corner.top-right {
            top: 0;
            right: 0;
            border-left: none;
            border-bottom: none;
            border-radius: 0 12px 0 0;
        }

        .corner.bottom-left {
            bottom: 0;
            left: 0;
            border-right: none;
            border-top: none;
            border-radius: 0 0 0 12px;
        }

        .corner.bottom-right {
            bottom: 0;
            right: 0;
            border-left: none;
            border-top: none;
            border-radius: 0 0 12px 0;
        }

        @keyframes cornerPulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.6;
                transform: scale(1.1);
            }
        }

        /* QR Reader Container */
        .qr-reader-container {
            margin: 0 auto;
            max-width: 500px;
            position: relative;
            z-index: 1;
        }

        #qr-reader {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        #qr-reader video {
            border-radius: 16px;
        }

        #qr-reader__dashboard {
            background: linear-gradient(135deg, #f8fafc, #e2e8f0) !important;
            border-radius: 0 0 16px 16px !important;
            border-top: 1px solid #e2e8f0 !important;
        }

        #qr-reader__dashboard_section {
            padding: 24px !important;
        }

        #qr-reader__dashboard_section_csr button {
            background: linear-gradient(135deg, #667eea, #764ba2) !important;
            color: white !important;
            border: none !important;
            border-radius: 12px !important;
            padding: 12px 24px !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3) !important;
            transition: all 0.3s ease !important;
        }

        #qr-reader__dashboard_section_csr button:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4) !important;
        }

        /* Scanner Info */
        .scanner-info {
            background: rgba(102, 126, 234, 0.05);
            border: 1px solid rgba(102, 126, 234, 0.1);
            border-radius: 12px;
            padding: 12px 20px;
            margin-top: 1rem;
        }

        /* Scanner Results */
        .scanner-results .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.5rem;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            animation: slideInUp 0.3s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .scanner-results .alert-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .scanner-results .alert-info {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
        }

        .scanner-results .alert-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        /* Avatar styles for instructions */
        .avatar {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-primary-transparent {
            background-color: rgba(var(--bs-primary-rgb), 0.1);
        }

        .bg-secondary-transparent {
            background-color: rgba(var(--bs-secondary-rgb), 0.1);
        }

        .bg-success-transparent {
            background-color: rgba(var(--bs-success-rgb), 0.1);
        }

        .bg-info-transparent {
            background-color: rgba(var(--bs-info-rgb), 0.1);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .scanner-header {
                padding: 1rem 1.5rem;
            }

            .scanner-header .card-title {
                font-size: 1.25rem;
            }

            .scanner-icon-wrapper {
                width: 50px;
                height: 50px;
            }

            .scanner-icon-wrapper i {
                font-size: 1.5rem;
            }

            .scanner-body {
                padding: 2rem 1.5rem;
            }

            .scanner-frame {
                padding: 15px;
            }

            .corner {
                width: 25px;
                height: 25px;
            }
        }
    </style>

    <script src="https://unpkg.com/html5-qrcode"></script>

    <script>
        let userLocation = null;
        let currentQrHash = null;
        let html5QrcodeScanner = null;

        function docReady(fn) {
            if (document.readyState === "complete" || document.readyState === "interactive") {
                setTimeout(fn, 1);
            } else {
                document.addEventListener("DOMContentLoaded", fn);
            }
        }

        // Reset scanner to initial state
        function resetScanner() {
            // Reset scanner header title
            document.querySelector('.scanner-header .card-title').textContent = 'Quét QR Tự Động Check-in';
            document.querySelector('.scanner-header .card-subtitle').textContent =
                'Camera sẽ tự động khởi động và check-in ngay khi quét thành công';

            // Reset scanner body to original scanner content
            const scannerBody = document.querySelector('.scanner-body');
            scannerBody.innerHTML = `
                <!-- Scanner Section -->
                <div class="row justify-content-center">
                    <div class="col-xl-8 col-lg-10">
                        <!-- QR Reader Container -->
                        <div class="scanner-wrapper text-center mb-4">
                            <div class="scanner-frame">
                                <div class="scanner-corners">
                                    <div class="corner top-left"></div>
                                    <div class="corner top-right"></div>
                                    <div class="corner bottom-left"></div>
                                    <div class="corner bottom-right"></div>
                                </div>
                                <div id="qr-reader" class="qr-reader-container"></div>
                            </div>

                            <!-- Scanner Info -->
                            <div class="scanner-info mt-3">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="ri-focus-2-line text-primary me-2"></i>
                                    <span class="text-muted">Hướng mã QR vào khung bên trên</span>
                                </div>
                            </div>
                        </div>

                        <!-- Results -->
                        <div id="qr-reader-results" class="text-center scanner-results"></div>
                    </div>
                </div>
            `;

            // Reset variables
            currentQrHash = null;
            userLocation = null;

            // Restart scanner
            initializeScanner();
        }

        // Initialize the QR scanner
        function initializeScanner() {
            var resultContainer = document.getElementById('qr-reader-results');
            var lastResult, countResults = 0;

            function onScanSuccess(decodedText, decodedResult) {
                if (decodedText !== lastResult) {
                    ++countResults;
                    lastResult = decodedText;

                    console.log(`Scan result: ${decodedText}`, decodedResult);

                    // Extract QR hash from different formats
                    let qrHash = null;

                    try {
                        // Case 1: Full URL with domain
                        const url = new URL(decodedText);
                        const checkinMatch = url.pathname.match(/\/checkin\/([a-zA-Z0-9]+)$/);
                        if (checkinMatch) {
                            qrHash = checkinMatch[1];
                        }
                    } catch (e) {
                        // Case 2: Token/ID only (not a full URL)
                        const tokenPattern = /^[a-zA-Z0-9]{20,}$/;
                        if (tokenPattern.test(decodedText)) {
                            qrHash = decodedText;
                        } else if (decodedText.includes('/checkin/')) {
                            // Case 3: Relative URL path
                            const pathMatch = decodedText.match(/\/checkin\/([a-zA-Z0-9]+)$/);
                            if (pathMatch) {
                                qrHash = pathMatch[1];
                            }
                        }
                    }

                    if (qrHash) {
                        resultContainer.innerHTML = `
                            <div class="alert alert-success">
                                <i class="ri-check-double-line me-2"></i>
                                Mã QR hợp lệ! Đang tải thông tin sự kiện...
                            </div>
                        `;

                        // Stop scanner
                        html5QrcodeScanner.clear().then(() => {
                            loadEventInfo(qrHash);
                        });
                    } else {
                        resultContainer.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="ri-close-circle-line me-2"></i>
                                Mã QR không hợp lệ. Trang sẽ tự động tải lại trong <span id="error-countdown">3</span> giây...
                            </div>
                        `;

                        // Countdown and reload page after 3 seconds
                        let countdown = 3;
                        const countdownElement = document.getElementById('error-countdown');
                        const countdownInterval = setInterval(() => {
                            countdown--;
                            if (countdownElement) {
                                countdownElement.textContent = countdown;
                            }
                            if (countdown <= 0) {
                                clearInterval(countdownInterval);
                                // Stop scanner before reload
                                if (html5QrcodeScanner) {
                                    html5QrcodeScanner.clear().finally(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    window.location.reload();
                                }
                            }
                        }, 1000);
                    }
                }
            }

            html5QrcodeScanner = new Html5QrcodeScanner(
                "qr-reader", {
                    fps: 10,
                    qrbox: 250,
                    aspectRatio: 1.0,
                    disableFlip: false
                }
            );

            html5QrcodeScanner.render(onScanSuccess);
        }

        // Load event information and display checkin form
        function loadEventInfo(qrHash) {
            currentQrHash = qrHash;

            fetch(`/events/checkin/event-info/${qrHash}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayEventInfo(data.data);
                    } else {
                        showError(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Có lỗi xảy ra khi tải thông tin sự kiện.');
                });
        }

        // Display event information and auto checkin
        function displayEventInfo(data) {
            const event = data.event;
            const existingCheckin = data.existingCheckin;

            // Update header title
            document.querySelector('.scanner-header .card-title').textContent = 'Check-in Sự Kiện';
            document.querySelector('.scanner-header .card-subtitle').textContent = event.name;

            // Replace scanner body content with event info
            const scannerBody = document.querySelector('.scanner-body');

            if (existingCheckin) {
                // Already checked in - show error
                scannerBody.innerHTML = `
                    <div class="row justify-content-center">
                        <div class="col-xl-8 col-lg-10">
                            <!-- Event Information Card -->
                            <div class="text-center mb-4">
                                ${event.image ? `
                                                                                            <img src="${event.image}" alt="${event.name}" class="img-fluid rounded mb-3" style="max-height: 150px; object-fit: cover;">
                                                                                        ` : `
                                                                                            <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="height: 120px;">
                                                                                                <i class="bi bi-calendar-event display-4 text-muted"></i>
                                                                                            </div>
                                                                                        `}
                                <h4 class="text-primary mb-2">${event.name}</h4>
                                <div class="d-flex justify-content-center align-items-center text-muted mb-3">
                                    <i class="bi bi-geo-alt me-2"></i>
                                    <span class="me-3">${event.classRoom.name}</span>
                                    <i class="bi bi-calendar3 me-2"></i>
                                    <span>${event.formatted_event_date}</span>
                                </div>
                            </div>

                            <!-- Already Checked In Error -->
                            <div class="text-center">
                                <div class="alert alert-warning border-0 py-4" style="background: linear-gradient(135deg, #fff3cd, #ffeaa7); color: #856404;">
                                    <div class="mb-3">
                                        <i class="bi bi-exclamation-triangle-fill display-4 text-warning"></i>
                                    </div>
                                    <h4 class="fw-bold mb-3">Đã tham gia sự kiện!</h4>
                                    <p class="mb-2 fw-medium">Bạn đã check-in cho sự kiện này rồi.</p>
                                    <p class="mb-2 fw-medium">
                                        <i class="bi bi-clock me-2"></i>Thời gian: ${existingCheckin.formatted_checkin_time}
                                    </p>
                                    ${existingCheckin.distance ? `
                                                                                                <p class="mb-0 text-muted">
                                                                                                    <i class="bi bi-geo-alt me-2"></i>Khoảng cách: ${existingCheckin.distance}
                                                                                                </p>
                                                                                            ` : ''}
                                </div>
                            </div>

                            <!-- Back Button -->
                            <div class="text-center mt-4">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetScanner()">
                                    <i class="ri-qr-scan-2-line me-2"></i> Quét QR Khác
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                // Not checked in yet - show processing and auto check-in
                scannerBody.innerHTML = `
                    <div class="row justify-content-center">
                        <div class="col-xl-8 col-lg-10">
                            <!-- Event Information Card -->
                            <div class="text-center mb-4">
                                ${event.image ? `
                                                                                                <img src="${event.image}" alt="${event.name}" class="img-fluid rounded mb-3" style="max-height: 150px; object-fit: cover;">
                                                                                            ` : `
                                                                                                <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="height: 120px;">
                                                                                                    <i class="bi bi-calendar-event display-4 text-muted"></i>
                                                                                                </div>
                                                                                            `}
                                <h4 class="text-primary mb-2">${event.name}</h4>
                                <div class="d-flex justify-content-center align-items-center text-muted mb-3">
                                    <i class="bi bi-geo-alt me-2"></i>
                                    <span class="me-3">${event.classRoom.name}</span>
                                    <i class="bi bi-calendar3 me-2"></i>
                                    <span>${event.formatted_event_date}</span>
                                </div>
                            </div>

                            <!-- Auto Check-in Process -->
                            <div class="text-center">
                                <div id="auto-checkin-status" class="alert alert-info border-0 py-4" style="background: linear-gradient(135deg, #e3f2fd, #f3e5f5); color: #1565c0;">
                                    <div class="mb-3">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                    <h5 class="fw-medium mb-2">Đang xử lý check-in tự động...</h5>
                                    <p class="mb-0">Đang xác định vị trí và thực hiện check-in</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Start auto check-in process
                autoCheckin();
            }
        }

        // Auto check-in process
        function autoCheckin() {
            const statusElement = document.getElementById('auto-checkin-status');

            // Get user location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        userLocation = {
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude
                        };

                        // Update status to processing check-in
                        statusElement.innerHTML = `
                            <div class="mb-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <h5 class="fw-medium mb-2">Đang thực hiện check-in...</h5>
                            <p class="mb-0">Vị trí đã xác định, đang check-in tự động</p>
                        `;

                        // Perform auto check-in
                        performAutoCheckin(userLocation);
                    },
                    function(error) {
                        let errorMessage = 'Không thể xác định vị trí. ';
                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage += 'Bạn đã từ chối quyền truy cập vị trí.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage += 'Thông tin vị trí không khả dụng.';
                                break;
                            case error.TIMEOUT:
                                errorMessage += 'Yêu cầu xác định vị trí đã hết thời gian.';
                                break;
                        }

                        showAutoCheckinError(errorMessage + ' Vui lòng thử lại.');
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 300000 // 5 minutes
                    }
                );
            } else {
                showAutoCheckinError('Trình duyệt không hỗ trợ định vị địa lý.');
            }
        }

        // Perform the actual check-in request
        function performAutoCheckin(location) {
            fetch(`/events/checkin/${currentQrHash}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        latitude: location.latitude,
                        longitude: location.longitude
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAutoCheckinSuccess(data.message, data.data);
                    } else {
                        showAutoCheckinError(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAutoCheckinError('Có lỗi xảy ra khi check-in. Vui lòng thử lại.');
                });
        }

        // Show successful auto check-in
        function showAutoCheckinSuccess(message, checkinData) {
            const statusElement = document.getElementById('auto-checkin-status');
            statusElement.className = 'alert alert-success border-0 py-4';
            statusElement.style.background = 'linear-gradient(135deg, #d4edda, #c3e6cb)';
            statusElement.style.color = '#155724';
            statusElement.innerHTML = `
                <div class="mb-3">
                    <i class="bi bi-check-circle-fill display-4 text-success"></i>
                </div>
                <h4 class="fw-bold mb-3">Check-in thành công!</h4>
                <p class="mb-2 fw-medium">${message}</p>
                ${checkinData ? `
                                                                            <p class="mb-0 text-muted">
                                                                                <i class="bi bi-clock me-2"></i>${checkinData.checkin_time || 'Vừa xong'}
                                                                                ${checkinData.distance ? ` • <i class="bi bi-geo-alt ms-2 me-1"></i>${checkinData.distance}` : ''}
                                                                            </p>
                                                                        ` : ''}
                <div class="mt-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetScanner()">
                        <i class="ri-qr-scan-2-line me-2"></i> Quét QR Khác
                    </button>
                </div>
            `;
        }

        // Show auto check-in error and return to scanner
        function showAutoCheckinError(errorMessage) {
            const statusElement = document.getElementById('auto-checkin-status');
            statusElement.className = 'alert alert-danger border-0 py-4';
            statusElement.style.background = 'linear-gradient(135deg, #f8d7da, #f5c6cb)';
            statusElement.style.color = '#721c24';
            statusElement.innerHTML = `
                <div class="mb-3">
                    <i class="bi bi-x-circle-fill display-4 text-danger"></i>
                </div>
                <h5 class="fw-bold mb-3">Check-in thất bại</h5>
                <p class="mb-3">${errorMessage}</p>
                <div class="mt-4">
                    <button type="button" class="btn btn-outline-secondary" onclick="resetScanner()">
                        <i class="ri-qr-scan-2-line me-2"></i> Quét Lại QR Code
                    </button>
                </div>
                <p class="mt-3 mb-0 text-muted small">Tự động quay lại scanner trong <span id="countdown">5</span> giây...</p>
            `;

            // Auto return to scanner after 5 seconds
            let countdown = 5;
            const countdownElement = statusElement.querySelector('#countdown');
            const countdownInterval = setInterval(() => {
                countdown--;
                if (countdownElement) {
                    countdownElement.textContent = countdown;
                }
                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                    resetScanner();
                }
            }, 1000);
        }

        // Show error message in QR results area
        function showError(message) {
            const resultContainer = document.getElementById('qr-reader-results');
            resultContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="ri-error-warning-line me-2"></i>
                    ${message}
                </div>
            `;

            // Clear error message after 3 seconds
            setTimeout(() => {
                resultContainer.innerHTML = '';
            }, 3000);
        }

        // Initialize when document ready
        docReady(function() {
            initializeScanner();
        });
    </script>
@endsection
