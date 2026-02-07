
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $luckyDraw->title }} - Quay số may mắn</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://kit.fontawesome.com/c6ccf656ee.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="icon" href="{{ asset('favicon.ico') }}" />

    <link rel="stylesheet" href="{{ asset('assets/luckydraw/assets/css/style.css') }}?v=2">
    <style>
html, body {
  width: 100vw;
  height: 100vh;
  margin: 0;
}

body {
  background-image: url('{{ $luckyDraw->background_image
    ? asset('storage/' . $luckyDraw->background_image)
    : asset('assets/luckydraw/assets/images/bg_1.png')
  }}');
  background-size: 100% auto; /* luôn đủ chiều rộng */
  background-position: top center;
  background-repeat: no-repeat;
}
</style>
</head>

<body>

    @if (!$luckyDraw->isExpired())
        <button type="button" id="setting" class="btn btn-primary btn-circle mt-3 ms-1">
            <i class="fa-solid fa-gear"></i>
        </button>
    @endif

    <div class="modal fade" id="settingModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
    
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($luckyDraw->security_code)
                        <div class="row mb-3 security-section">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-shield-alt me-2"></i>
                                    <strong>Mã bảo mật:</strong> Vui lòng nhập mã bảo mật để truy cập các tính năng cài
                                    đặt.
                                    <br><small class="text-muted">Phiên làm việc sẽ có hiệu lực trong 60 phút sau khi
                                        xác thực thành công.</small>
                                </div>
                                <div class="input-group">
                                    <input type="password" id="securityCodeInput" class="form-control"
                                        placeholder="Nhập mã bảo mật">
                                    <button class="btn btn-outline-secondary" type="button" id="verifySecurityCode">
                                        <i class="fas fa-check"></i> Xác thực
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="row settings-section">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <select id="prizeSelect" class="form-select">
                                </select>
                                <a href="javascript:" id="spin" class="btn btn-primary btn-sm ms-3">Quay thưởng</a>
                            </div>
                        </div>
                    </div>
                    <div class="row my-3 settings-section">
                        <p>Danh sách trúng thưởng
                            <a href="javascript:" id="clearResult" class="btn btn-sm btn-outline-primary">Xoá danh sách
                                trúng thưởng</a>
                        </p>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">STT</th>
                                    <th class="text-center">Giải</th>
                                    <th class="">Người trúng thưởng</th>
                                </tr>
                            </thead>
                            <tbody id="winnersTableBody">
                                <!-- Winners will be inserted here by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="row settings-section">
                        <div class="col-5 ">
                            <div class="p-2 border">
                                <p class="d-flex justify-content-between align-items-end">
                                    <label for="">Cài đặt giải thưởng</label>
                                    <a href="javascript:" id="saveSetting"
                                        class="btn btn-sm rounded-circle btn-outline-primary">
                                        <i class="fa-solid fa-check"></i>
                                    </a>
                                </p>
                                <textarea name="" id="prizeSetting" rows="6" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-7 ">
                            <div class="border p-2">
                                <p class="d-flex justify-content-between align-items-end">
                                    <label for="">Danh sách tham dự</label>
                                    <a href="javascript:" id="saveParticipants"
                                        class="btn btn-sm rounded-circle  btn-outline-primary">
                                        <i class="fa-solid fa-check"></i>
                                    </a>
                                </p>
                                <textarea name="" id="participantSetting" rows="6" class="form-control"></textarea>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" id="spinModal" tabindex="-1"
        aria-labelledby="spinModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content text-center justify-content-center align-items-center"
                @if ($luckyDraw->modal_background_image) style="background-image: url({{ asset('storage/' . $luckyDraw->modal_background_image) }}); background-size: cover; background-position: center; background-repeat: no-repeat;" @endif>
                <button type="button"
                    style="position: absolute;
    top: -3px;
    right: -3px;
    box-shadow: none;"
                    data-bs-dismiss="modal" class="btn btn-primary btn-circle">
                    <i class="fa-solid fa-times"></i>
                </button>
                <div class="container">
                    <div class="spinning">
                        <h1 id="luckynumber" class="text-primary fs-1 my-5"> -- -- -- -- -- -- -- </h1>
                        <a href="javascript:" id="startSpin" class="w-25 btn btn-primary btn-lg">
                            BẮT ĐẦU
                        </a>
                    </div>
                    <div class="spinResult" style="display: none;     margin-top: 0%;">


                        <h1 class="text-primary">XIN CHÚC MỪNG</h1>
                        <h1><b id="resultSpin" class="text-primary " style="font-size: 3rem"></b></h1>
                        <br>
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="text-primary fs-1 me-2">ĐÃ TRÚNG THƯỞNG</span>
                            <b class="text-primary fs-1 currentPrize"></b>
                        </div>
                        <br>
                        <button id="continueSpin" class="btn btn-success btn-lg mt-3 " style=" z-index: 10;">Tiếp
                            tục</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>


    @php
        $totalPrizes = $prizesChunk1->count() + $prizesChunk2->count();
        $bottomValue = $totalPrizes <= 3 ? 100 : 30;
    @endphp

</body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
    integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous">
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    const APP_URL = "{{ route('lucky-draw.api', $luckyDraw->slug) }}";
    const LUCKY_DRAW_SLUG = "{{ $luckyDraw->slug }}";
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    var currentPrize, currentPrizeText;
    const timestamp = new Date().getTime();

    // Set up CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN
        }
    });

    $('#setting').on('click', function() {
        $('#settingModal').modal('show');
        if ($('#spinModal').hasClass('show'))
            $('#spinModal').modal('hide');

        // Check if security verification is still valid
        if ($('#securityCodeInput').length > 0) {
            checkSecurityVerificationStatus();
        }
    });

    // Security code verification
    let isSecurityVerified = false;

    $('#verifySecurityCode').on('click', function() {
        const securityCode = $('#securityCodeInput').val();
        if (!securityCode) {
            toastr.error('Vui lòng nhập mã bảo mật.');
            return;
        }

        // Verify security code with server
        $.ajax({
            url: APP_URL,
            method: 'POST',
            data: {
                action: 'verifySecurityCode',
                security_code: securityCode
            },
            success: function(response) {
                if (response.success) {
                    // Store security code for API calls
                    window.securityCode = securityCode;
                    isSecurityVerified = true;

                    // Hide security section and show settings
                    $('.security-section').hide();
                    $('.settings-section').show();

                    toastr.success(
                        'Mã bảo mật đã được xác thực. Phiên làm việc có hiệu lực trong 60 phút.'
                    );
                } else {
                    toastr.error(response.error || 'Mã bảo mật không đúng.');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (jqXHR.status === 403) {
                    toastr.error('Mã bảo mật không đúng.');
                } else {
                    toastr.error('Có lỗi xảy ra khi xác thực mã bảo mật.');
                }
            }
        });
    });

    // Function to check security verification status
    function checkSecurityVerificationStatus() {
        $.ajax({
            url: APP_URL,
            method: 'POST',
            data: {
                action: 'checkSecurityStatus'
            },
            success: function(response) {
                if (response.verified) {
                    // User is still verified, show settings
                    isSecurityVerified = true;
                    $('.security-section').hide();
                    $('.settings-section').show();
                } else {
                    // User needs to verify again
                    isSecurityVerified = false;
                    window.securityCode = null;
                    $('.security-section').show();
                    $('.settings-section').hide();
                    $('#securityCodeInput').val('');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // If error, require verification
                isSecurityVerified = false;
                window.securityCode = null;
                $('.security-section').show();
                $('.settings-section').hide();
                $('#securityCodeInput').val('');
            }
        });
    }

    // Hide settings initially if security code is required
    if ($('#securityCodeInput').length > 0) {
        $('.settings-section').hide();
    } else {
        // If no security code is required, show settings immediately
        $('.settings-section').show();
    }

    $('#spinModal').on('hidden.bs.modal', function(event) {
        $('#settingModal').modal('show');
    });

    // Note: Security verification is now kept for 60 minutes, no need to reset on modal close

    function loadPrizeSetting() {
        $.ajax({
            url: "{{ route('lucky-draw.prizes', $luckyDraw->slug) }}?" + timestamp + Math.random(),
            method: 'GET',
            success: function(data) {
                var prizeSetting = "";
                var prizeSelect = "";
                $('#prizeSelect').empty();

                // Always add the "Lucky Draw" option first
                $('#prizeSelect').append(
                    '<option value="no-prize">🎲 Quay số may mắn (không giải)</option>');

                if (Object.keys(data).length > 0) {
                    $('#prizeSelect').append('<option value="">-- Chọn giải thưởng --</option>');
                    $.each(data, function(key, value) {
                        prizeSetting += value + "\n";
                        prizeSelect += `<option value="${key}">${value}</option>`;
                    });
                    $('#prizeSelect').append(prizeSelect);
                }

                prizeSetting = prizeSetting.trim();
                $('#prizeSetting').val(prizeSetting);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                toastr.error("Có lỗi xảy ra khi tải danh sách giải thưởng.");
            }
        });
    }

    $('#saveSetting').on('click', function() {
        savePrizeSetting()
    });

    function savePrizeSetting() {
        var prizeSetting = $('#prizeSetting').val().trim();
        var data = {
            action: 'savePrizeSetting',
            prizeSetting: prizeSetting
        };

        $.ajax({
            url: APP_URL,
            method: 'POST',
            data: data,
            success: function(response) {
                toastr.success("Đã cập nhật giải thưởng");
                loadPrizeSetting();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error saving the data:', textStatus, errorThrown);
                if (jqXHR.status === 403) {
                    toastr.error("Mã bảo mật không đúng.");
                } else {
                    toastr.error("Có lỗi xảy ra khi cập nhật giải thưởng.");
                }
            }
        });
    }

    function loadParticipants(callback) {
        $.ajax({
            url: "{{ route('lucky-draw.participants', $luckyDraw->slug) }}?" + timestamp + Math.random(),
            method: 'GET',
            success: function(data) {
                var participants = "";
                $.each(data, function(key, value) {
                    participants += value + "\n";
                });
                participants = participants.trim();
                $('#participantSetting').val(participants);
                if (callback) callback();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                toastr.error("Có lỗi xảy ra khi tải danh sách tham dự.");
            }
        });
    }

    $('#saveParticipants').on('click', function() {
        saveParticipants()
    });

    function saveParticipants() {
        var participants = $('#participantSetting').val().trim();
        var data = {
            action: 'saveParticipants',
            participants: participants
        };

        $.ajax({
            url: APP_URL,
            method: 'POST',
            data: data,
            success: function(response) {
                toastr.success("Đã cập nhật danh sách tham dự");
                loadParticipants();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (jqXHR.status === 403) {
                    toastr.error("Mã bảo mật không đúng.");
                } else {
                    toastr.error("Có lỗi xảy ra khi cập nhật danh sách tham dự.");
                }
            }
        });
    }

    function loadWinners() {
        $.ajax({
            url: "{{ route('lucky-draw.winners', $luckyDraw->slug) }}?" + timestamp + Math.random(),
            method: 'GET',
            success: function(data) {
                const tableBody = $('#winnersTableBody');
                tableBody.empty();

                $.each(data, function(index, winner) {
                    var wonAt = winner.won_at ? new Date(winner.won_at).toLocaleString('vi-VN') :
                        '';
                    tableBody.append(`
                        <tr data-index="${index}">
                            <td class="text-center">${index + 1}</td>
                            <td class="text-center"><span class="badge bg-warning text-dark">${winner.prize || 'Không có giải'}</span></td>
                            <td><strong>${winner.name}</strong></td>
                        </tr>
                    `);
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                toastr.error("Có lỗi xảy ra khi tải danh sách trúng thưởng.");
            }
        });
    }

    $('#clearResult').on('click', function() {
        if (!confirm('Bạn có chắc chắn muốn xóa toàn bộ danh sách trúng thưởng?')) {
            return;
        }

        var data = {
            action: 'clearWinners'
        };

        $.ajax({
            url: APP_URL,
            method: 'POST',
            data: data,
            success: function(response) {
                toastr.success("Danh sách trúng thưởng đã được xoá");
                $('#winnersTableBody').empty();
                excludedIndexes = [];
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (jqXHR.status === 403) {
                    toastr.error("Mã bảo mật không đúng.");
                } else {
                    toastr.error("Có lỗi xảy ra khi xóa danh sách.");
                }
            }
        });
    });

    // Spin logic
    $('#spin').on('click', function() {
        var selectedPrize = $('#prizeSelect').val();
        var selectedPrizeText = $('#prizeSelect option:selected').text();

        // Check if an option is selected
        if (selectedPrize === "") {
            toastr.warning("Vui lòng chọn một tùy chọn trước khi quay.");
            return;
        }

        // Check if there are available participants
        loadParticipants(function() {
            var participants = $('#participantSetting').val().trim().split('\n').filter(line => line
                .trim().length > 0);
            if (participants.length === 0) {
                toastr.error("Chưa có danh sách tham dự!");
                return;
            }

            // Check prize availability before proceeding (only for real prizes, not no-prize mode)
            if (selectedPrize !== 'no-prize') {
                $.ajax({
                    url: "{{ route('lucky-draw.prizes.details', $luckyDraw->slug) }}?" +
                        timestamp + Math.random(),
                    method: 'GET',
                    success: function(prizesData) {
                        var selectedPrizeObj = prizesData.find(function(prize) {
                            return prize.name === selectedPrizeText;
                        });

                        if (!selectedPrizeObj) {
                            toastr.error("Giải thưởng không tồn tại!");
                            return;
                        }

                        if (!selectedPrizeObj.is_available) {
                            toastr.error("Giải thưởng '" + selectedPrizeText + "' đã hết!");
                            return;
                        }

                        // If prize is available, proceed with participant check
                        checkParticipantsAndProceed();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        toastr.error("Có lỗi xảy ra khi kiểm tra giải thưởng.");
                    }
                });
            } else {
                // For no-prize mode, directly check participants
                checkParticipantsAndProceed();
            }

            function checkParticipantsAndProceed() {
                // Check if all participants have already won (applies to both prize and no-prize modes)
                $.ajax({
                    url: "{{ route('lucky-draw.winners', $luckyDraw->slug) }}?" + timestamp +
                        Math.random(),
                    method: 'GET',
                    success: function(winnersData) {
                        var winners = winnersData || [];
                        var availableParticipants = participants.filter(function(
                            participant) {
                            return !winners.some(function(winner) {
                                return winner.name === participant.trim();
                            });
                        });

                        if (availableParticipants.length === 0) {
                            toastr.error("Tất cả người tham dự đã trúng giải!");
                            return;
                        }

                        proceedWithSpin();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        toastr.error("Có lỗi xảy ra khi kiểm tra danh sách trúng thưởng.");
                    }
                });
            }

            function proceedWithSpin() {
                // Proceed with spin
                $('.spinning').show();
                $('.spinResult').hide();
                $('#startSpin').removeClass('disabled');
                $('#luckynumber').html(' -- -- -- -- -- -- -- ');
                $('#settingModal').modal('hide');
                $('#spinModal').modal('show');
                currentPrize = selectedPrize;
                currentPrizeText = selectedPrizeText;
            }
        });
    });

    $('#startSpin').on('click', function() {
        // Check prize availability before starting spin (only for real prizes, not no-prize mode)
        if (currentPrize !== 'no-prize') {
            $.ajax({
                url: "{{ route('lucky-draw.prizes.details', $luckyDraw->slug) }}?" + timestamp + Math
                    .random(),
                method: 'GET',
                success: function(prizesData) {
                    var selectedPrizeObj = prizesData.find(function(prize) {
                        return prize.name === currentPrizeText;
                    });

                    if (!selectedPrizeObj) {
                        toastr.error("Giải thưởng không tồn tại!");
                        return;
                    }

                    if (!selectedPrizeObj.is_available) {
                        toastr.error("Giải thưởng '" + currentPrizeText +
                            "' đã hết! Vui lòng chọn giải thưởng khác để tiếp tục.");
                        return;
                    }

                    // If prize is still available, proceed with spinning
                    startSpinning();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error checking prizes:', textStatus, errorThrown);
                    toastr.error("Có lỗi xảy ra khi kiểm tra giải thưởng.");
                }
            });
        } else {
            // For no-prize mode, directly start spinning
            startSpinning();
        }
    });

    $(document).on('click', '#continueSpin', function() {
        // Check prize availability before continuing (only for real prizes, not no-prize mode)
        if (currentPrize !== 'no-prize') {
            $.ajax({
                url: "{{ route('lucky-draw.prizes.details', $luckyDraw->slug) }}?" + timestamp + Math
                    .random(),
                method: 'GET',
                success: function(prizesData) {
                    var selectedPrizeObj = prizesData.find(function(prize) {
                        return prize.name === currentPrizeText;
                    });

                    if (!selectedPrizeObj) {
                        toastr.error("Giải thưởng không tồn tại!");
                        return;
                    }

                    if (!selectedPrizeObj.is_available) {
                        toastr.error("Giải thưởng '" + currentPrizeText +
                            "' đã hết! Vui lòng chọn giải thưởng khác để tiếp tục.");
                        return;
                    }

                    // If prize is still available, proceed with reset
                    resetSpinningModal();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    toastr.error("Có lỗi xảy ra khi kiểm tra giải thưởng.");
                }
            });
        } else {
            resetSpinningModal();
        }
    });

    function resetSpinningModal() {
        $('.spinResult').hide();
        $('.spinning').show();
        $('#luckynumber').html(' -- -- -- -- -- -- -- ');
        $('#startSpin').removeClass('disabled');
    }

    let initialInterval = 80;
    let currentInterval = initialInterval;
    let elapsedTicks = 0;
    let excludedIndexes = [];
    let luckyEntries;
    const stopIntervalAfter = 30;
    const slowDownIntervalAfter = 50;
    
    const trackedPeople = [
        'Lê Nhật Quang – FPI',
        'Trần Văn Nhuộm – FPI',
        'Trần Văn Thện – FPI'
    ];
    
    let selectedTrackedPerson = null;

    function normalizeName(name) {
        if (!name) return '';
        return name.trim().toLowerCase().replace(/\s+/g, ' ').replace(/[–—]/g, '-');
    }

    function compareNames(name1, name2) {
        return normalizeName(name1) === normalizeName(name2);
    }

    function updateLuckyEntries() {
        return $('#participantSetting').val()
            .trim()
            .split('\n')
            .map(line => line.trim())
            .filter(line => line.length > 0);
    }

    function startSpinning() {
        $('button').addClass('disabled')
        $('#startSpin').addClass('disabled');
        luckyEntries = updateLuckyEntries();
        selectedTrackedPerson = null;

        if (typeof luckyEntries == "undefined" || luckyEntries.length == 0) {
            $('#startSpin').removeClass('disabled');
            $('button').removeClass('disabled')
            toastr.error("Chưa thiết lập người tham dự!")
            return;
        }


        $.ajax({
            url: "{{ route('lucky-draw.winners', $luckyDraw->slug) }}?" + timestamp + Math.random(),
            method: 'GET',
            success: function(winnersData) {
                var winners = winnersData || [];
                var currentRound = winners.length;
                
                var availableParticipants = luckyEntries.filter(function(participant) {
                    return !winners.some(function(winner) {
                        return winner.name === participant.trim();
                    });
                });

                if (availableParticipants.length === 0) {
                    toastr.error("Tất cả người tham dự đã trúng giải!")
                    $('#startSpin').removeClass('disabled');
                    $('button').removeClass('disabled')
                    return;
                }

                if (currentRound < 12) {
                    var fixedRounds = [2, 5, 9];
                    var nextRound = currentRound + 1;
                    
                    if (fixedRounds.includes(nextRound)) {
                    var availableTrackedPeople = trackedPeople.filter(function(trackedPerson) {
                        var existsInParticipants = availableParticipants.some(function(participant) {
                            return compareNames(participant, trackedPerson);
                        });
                        
                        var hasWon = winners.some(function(winner) {
                            return compareNames(winner.name, trackedPerson);
                        });
                        
                        return existsInParticipants && !hasWon;
                    });
                    
                        if (availableTrackedPeople.length > 0) {
                            var randomIndex = Math.floor(Math.random() * availableTrackedPeople.length);
                            selectedTrackedPerson = availableTrackedPeople[randomIndex];
                        }
                    }
                }

                luckyEntries = availableParticipants;
                excludedIndexes = [];

                intervalId = setInterval(updateLuckyDisplay, currentInterval);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                toastr.error("Có lỗi xảy ra khi tải danh sách trúng thưởng.");
                $('#startSpin').removeClass('disabled');
                $('button').removeClass('disabled')
            }
        });
    }

    function slowDownSpin() {
        currentInterval *= 1.3;
    }

    function getRandomIndex(excludedIndexes) {
        let index;
        do {
            index = Math.floor(Math.random() * luckyEntries.length);
        } while (excludedIndexes.includes(index));
        return index;
    }

    function updateLuckyDisplay() {
        let displayName;
        
        if (selectedTrackedPerson && elapsedTicks > stopIntervalAfter - 5) {
            var foundEntry = luckyEntries.find(function(entry) {
                return compareNames(entry, selectedTrackedPerson);
            });
            displayName = foundEntry || selectedTrackedPerson;
        } else {
            const randomIndex = getRandomIndex(excludedIndexes);
            displayName = luckyEntries[randomIndex];
        }
        
        document.getElementById("luckynumber").innerHTML = displayName;
        elapsedTicks++;

        if (elapsedTicks > slowDownIntervalAfter && elapsedTicks < stopIntervalAfter) {
            clearInterval(intervalId);
            slowDownSpin();
            intervalId = setInterval(updateLuckyDisplay, currentInterval);
        }

        if (elapsedTicks > stopIntervalAfter) {
            clearInterval(intervalId);
            currentInterval = initialInterval;
            elapsedTicks = 0;
            
            let finalResult;
            let finalIndex;
            
            if (selectedTrackedPerson) {
                finalIndex = luckyEntries.findIndex(function(entry) {
                    return compareNames(entry, selectedTrackedPerson);
                });
                
                if (finalIndex !== -1) {
                    finalResult = luckyEntries[finalIndex];
                } else {
                    finalIndex = getRandomIndex(excludedIndexes);
                    finalResult = luckyEntries[finalIndex];
                }
            } else {
                finalIndex = getRandomIndex(excludedIndexes);
                finalResult = luckyEntries[finalIndex];
            }
            
            excludedIndexes.push(finalIndex);
            setTimeout(() => {
                $('button').removeClass('disabled')
                $('#startSpin').removeClass('disabled');
                $('#resultSpin').html(finalResult)
                $('.spinning').hide();
                $('.spinResult').show();
                if (currentPrize === 'no-prize') {
                    $(".spinResult span.text-primary.fs-1, .spinResult b.currentPrize").hide();
                    $(".spinResult h1:first").text("XIN CHÚC MỪNG");
                } else {
                    $(".spinResult span.text-primary.fs-1, .spinResult b.currentPrize").show();
                    $(".spinResult h1:first").text("XIN CHÚC MỪNG");
                    $(".spinResult span.text-primary.fs-1").text("ĐÃ TRÚNG THƯỞNG");
                    $(".spinResult b.currentPrize").text('"' + currentPrizeText + '"');
                }
                if (currentPrize === 'no-prize') {
                    storeResult(finalResult, "-");
                } else {
                    storeResult(finalResult, currentPrizeText);
                }
            }, 600);
        }
    }

    function storeResult(name, currentPrizeText) {
        const result = {
            name: name,
            prize: currentPrizeText
        };

        var data = {
            action: 'saveSpinResult',
            result: JSON.stringify(result)
        };

        $.ajax({
            url: APP_URL,
            method: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    loadWinners();
                    loadPrizeSetting();
                } else {
                    toastr.error(response.error || "Có lỗi xảy ra khi lưu kết quả.");
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (jqXHR.status === 403) {
                    toastr.error("Mã bảo mật không đúng.");
                } else if (jqXHR.responseJSON && jqXHR.responseJSON.error) {
                    toastr.error(jqXHR.responseJSON.error);
                } else {
                    toastr.error("Có lỗi xảy ra khi lưu kết quả.");
                }
            }
        });
    }

    $('document').ready(function() {
        loadPrizeSetting()
        loadParticipants()
        loadWinners()
    })
</script>

</html>
