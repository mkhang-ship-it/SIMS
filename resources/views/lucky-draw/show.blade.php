{{-- Updated: Added prize availability checks and participant eligibility validation for lucky draw system - Added checks to prevent spinning when prizes are exhausted or participants have already won --}}
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
</head>

<body
    style="background-image: url({{ $luckyDraw->background_image ? asset('storage/' . $luckyDraw->background_image) : asset('assets/luckydraw/assets/images/bg_1.png') }}) !important;">
    @if (!$luckyDraw->isExpired())
        <button type="button" id="setting" class="btn btn-primary btn-circle mt-3 ms-1">
            <i class="fa-solid fa-gear"></i>
        </button>
    @endif

    <div class="modal fade" id="settingModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 w-100 text-center" id="exampleModalLabel">
                        <img src="{{ asset('assets/luckydraw/assets/images/LOGO_BTEC.png') }}" style="height: 40px"
                            alt="">
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <select id="prizeSelect" class="form-select">
                                </select>
                                <a href="javascript:" id="spin" class="btn btn-primary btn-sm ms-3">Quay thưởng</a>
                            </div>
                        </div>
                    </div>
                    <div class="row my-3">
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
                    <div class="row">
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
            <div class="modal-content text-center justify-content-center align-items-center">
                <button type="button" style="position: absolute;
    top: -3px;
    right: -3px;
    box-shadow: none;"
                    data-bs-dismiss="modal" class="btn btn-primary btn-circle">
                    <i class="fa-solid fa-times"></i>
                </button>
                <div class="container">
                    <div class="spinning">
                        <h1 id="" class="text-primary fs-1 currentPrize"></h1>
                        <h1 id="luckynumber" class="text-primary fs-1 my-5"> -- -- -- -- -- -- -- </h1>
                        <a href="javascript:" id="startSpin" class="w-25 btn btn-primary btn-lg">
                            BẮT ĐẦU
                        </a>
                    </div>
                    <div class="spinResult" style="display: none;     margin-top: 10%;">
                        <dotlottie-player
                            src="https://lottie.host/f1ff7b5d-aa51-4bdc-ba73-fdcb8388dee9/7QM24NkP8I.json?v=2"
                            background="transparent" speed="1"
                            style="width: 600px;
    height: 600px;
    position: absolute;
    top: -2%;
    left: 28%;"
                            loop autoplay></dotlottie-player>

                        <h1 class="text-primary fs-1">XIN CHÚC MỪNG</h1>
                        <h1><b id="resultSpin" class="text-primary " style="font-size: 3rem"></b></h1>
                        <br>
                        <span class="text-primary fs-1">ĐÃ TRÚNG GIẢI </span> <br><b
                            class="text-primary fs-1 currentPrize"></b>
                        <br>
                        <button id="continueSpin" class="btn btn-success btn-lg mt-3 " style=" z-index: 10;">Tiếp
                            tục</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>

    {{--
        Updated: Adjust bottom position to 100px if there are 3 or fewer prizes in total, else keep at 30px.
    --}}
    @php
        $totalPrizes = $prizesChunk1->count() + $prizesChunk2->count();
        $bottomValue = $totalPrizes <= 3 ? 100 : 30;
    @endphp
    <div style="position: absolute; bottom: {{ $bottomValue }}px" class="text-center w-100 d-lg-block d-none">
        @if ($prizesChunk1->isNotEmpty())
            <div class="container gift1 w-100 d-flex justify-content-evenly align-items-center">
                @foreach ($prizesChunk1 as $index => $prize)
                    @if ($prize->special_gift)
                        <img src="{{ asset($prize->image_url) }}" id="specialGift"
                            class="animate__animated animate__bounceInDown animate__slow animate__delay-1s home-img"
                            alt="{{ $prize->name }}" style="height: 220px;">
                    @else
                        <img src="{{ asset($prize->image_url) }}"
                            class="animate__animated animate__slow home-img
                        @if ($index == 0) animate__backInLeft
                        @elseif ($index == 2) animate__backInRight
                        @else animate__bounceInDown animate__delay-1s @endif"
                            alt="{{ $prize->name }}"
                            style="@if ($index == 0) transform: scaleX(-1); @endif height: 220px;">
                    @endif
                @endforeach
            </div>
        @endif

        @if ($prizesChunk2->isNotEmpty())
            <div class="container gift1 w-100 d-flex justify-content-evenly align-items-center">
                @foreach ($prizesChunk2 as $index => $prize)
                    <img src="{{ asset($prize->image_url) }}"
                        class="animate__animated animate__bounceInUp animate__delay-1s home-img"
                        alt="{{ $prize->name }}">
                @endforeach
            </div>
        @endif
    </div>
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
    });

    $('#spinModal').on('hidden.bs.modal', function(event) {
        $('#settingModal').modal('show');
    });

    function loadPrizeSetting() {
        $.ajax({
            url: "{{ route('lucky-draw.prizes', $luckyDraw->slug) }}?" + timestamp + Math.random(),
            method: 'GET',
            success: function(data) {
                console.log('Prizes loaded:', data);
                var prizeSetting = "";
                var prizeSelect = "";
                $('#prizeSelect').empty();
                $('#prizeSelect').append('<option value="">-- Chọn giải thưởng --</option>');
                $.each(data, function(key, value) {
                    prizeSetting += value + "\n";
                    prizeSelect += `<option value="${key}">${value}</option>`;
                });
                prizeSetting = prizeSetting.trim();
                $('#prizeSetting').val(prizeSetting);
                $('#prizeSelect').append(prizeSelect);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error loading prizes:', textStatus, errorThrown);
                toastr.error("Có lỗi xảy ra khi tải danh sách giải thưởng");
            }
        });
    }

    $('#saveSetting').on('click', function() {
        savePrizeSetting()
    });

    function savePrizeSetting() {
        var prizeSetting = $('#prizeSetting').val().trim();
        $.ajax({
            url: APP_URL,
            method: 'POST',
            data: {
                action: 'savePrizeSetting',
                prizeSetting: prizeSetting
            },
            success: function(response) {
                toastr.success("Đã cập nhật giải thưởng");
                loadPrizeSetting();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error saving the data:', textStatus, errorThrown);
                toastr.error("Có lỗi xảy ra khi cập nhật giải thưởng");
            }
        });
    }

    function loadParticipants(callback) {
        $.ajax({
            url: "{{ route('lucky-draw.participants', $luckyDraw->slug) }}?" + timestamp + Math.random(),
            method: 'GET',
            success: function(data) {
                console.log('Participants loaded:', data);
                var participants = "";
                $.each(data, function(key, value) {
                    participants += value + "\n";
                });
                participants = participants.trim();
                $('#participantSetting').val(participants);
                if (callback) callback();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error loading participants:', textStatus, errorThrown);
                toastr.error("Có lỗi xảy ra khi tải danh sách tham dự");
            }
        });
    }

    $('#saveParticipants').on('click', function() {
        saveParticipants()
    });

    function saveParticipants() {
        var participants = $('#participantSetting').val().trim();
        $.ajax({
            url: APP_URL,
            method: 'POST',
            data: {
                action: 'saveParticipants',
                participants: participants
            },
            success: function(response) {
                toastr.success("Đã cập nhật danh sách tham dự");
                loadParticipants();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error saving the data:', textStatus, errorThrown);
                toastr.error("Có lỗi xảy ra khi cập nhật danh sách tham dự");
            }
        });
    }

    function loadWinners() {
        $.ajax({
            url: "{{ route('lucky-draw.winners', $luckyDraw->slug) }}?" + timestamp + Math.random(),
            method: 'GET',
            success: function(data) {
                console.log('Winners loaded:', data);
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
                console.error('Error loading winners:', textStatus, errorThrown);
                toastr.error("Có lỗi xảy ra khi tải danh sách trúng thưởng");
            }
        });
    }

    $('#clearResult').on('click', function() {
        if (!confirm('Bạn có chắc chắn muốn xóa toàn bộ danh sách trúng thưởng?')) {
            return;
        }

        $.ajax({
            url: APP_URL,
            method: 'POST',
            data: {
                action: 'clearWinners'
            },
            success: function(response) {
                toastr.success("Danh sách trúng thưởng đã được xoá");
                $('#winnersTableBody').empty();
                excludedIndexes = [];
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error clearing the winners list:', textStatus, errorThrown);
                toastr.error("Có lỗi xảy ra khi xóa danh sách");
            }
        });
    });

    // Spin logic
    $('#spin').on('click', function() {
        var selectedPrize = $('#prizeSelect').val();
        var selectedPrizeText = $('#prizeSelect option:selected').text();

        // Check if prize is selected and available
        if (selectedPrize === "") {
            toastr.warning("Vui lòng chọn giải thưởng trước khi quay");
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

            // Check if all participants have already won
            $.ajax({
                url: "{{ route('lucky-draw.winners', $luckyDraw->slug) }}?" + timestamp + Math
                    .random(),
                method: 'GET',
                success: function(winnersData) {
                    var winners = winnersData || [];
                    var availableParticipants = participants.filter(function(participant) {
                        return !winners.some(function(winner) {
                            return winner.name === participant.trim();
                        });
                    });

                    if (availableParticipants.length === 0) {
                        toastr.error("Tất cả người tham dự đã trúng giải!");
                        return;
                    }

                    // Proceed with spin
                    $('.spinning').show();
                    $('.spinResult').hide();
                    $('#startSpin').removeClass('disabled');
                    $('#luckynumber').html(' -- -- -- -- -- -- -- ');
                    $('#settingModal').modal('hide');
                    $('#spinModal').modal('show');
                    currentPrize = selectedPrize;
                    currentPrizeText = selectedPrizeText;
                    $('.currentPrize').html(currentPrizeText);
                    $('.currentPrize').show();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error checking winners:', textStatus, errorThrown);
                    toastr.error("Có lỗi xảy ra khi kiểm tra danh sách trúng thưởng");
                }
            });
        });
    });

    $('#startSpin').on('click', function() {
        startSpinning();
    });

    $(document).on('click', '#continueSpin', function() {
        $('.spinResult').hide();
        $('.spinning').show();
        $('#luckynumber').html(' -- -- -- -- -- -- -- ');
        $('#startSpin').removeClass('disabled');
    });

    let initialInterval = 80;
    let currentInterval = initialInterval;
    let elapsedTicks = 0;
    let excludedIndexes = [];
    let luckyEntries;
    const stopIntervalAfter = 90;
    const slowDownIntervalAfter = 80;

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

        if (typeof luckyEntries == "undefined" || luckyEntries.length == 0) {
            $('#startSpin').removeClass('disabled');
            $('button').removeClass('disabled')
            toastr.error("Chưa thiết lập người tham dự!")
            return;
        }

        // Get winners to exclude them from spinning
        $.ajax({
            url: "{{ route('lucky-draw.winners', $luckyDraw->slug) }}?" + timestamp + Math.random(),
            method: 'GET',
            success: function(winnersData) {
                var winners = winnersData || [];
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

                // Update luckyEntries to only include available participants
                luckyEntries = availableParticipants;
                excludedIndexes = []; // Reset excluded indexes for new spin

                intervalId = setInterval(updateLuckyDisplay, currentInterval);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error loading winners for spin:', textStatus, errorThrown);
                toastr.error("Có lỗi xảy ra khi tải danh sách trúng thưởng");
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
        const newIndex = getRandomIndex(excludedIndexes);
        document.getElementById("luckynumber").innerHTML = luckyEntries[newIndex];
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
            excludedIndexes.push(newIndex);
            setTimeout(() => {
                $('button').removeClass('disabled')
                $('#startSpin').removeClass('disabled');
                $('#resultSpin').html(luckyEntries[newIndex])
                $('.spinning').hide();
                $('.spinResult').show();
                if (currentPrizeText == "") {
                    $(".spinResult span.text-primary.fs-1, .spinResult b.currentPrize").hide();
                } else {
                    $(".spinResult span.text-primary.fs-1, .spinResult b.currentPrize").show();
                }
                storeResult(luckyEntries[newIndex], currentPrizeText)
            }, 600);
        }
    }

    function storeResult(name, currentPrizeText) {
        const result = {
            name: name,
            prize: currentPrizeText
        };

        $.ajax({
            url: APP_URL,
            method: 'POST',
            data: {
                action: 'saveSpinResult',
                result: JSON.stringify(result)
            },
            success: function(response) {
                if (response.success) {
                    loadWinners();
                    loadPrizeSetting(); // Refresh prize list to update availability
                    toastr.success("Kết quả đã được lưu thành công!");
                } else {
                    toastr.error(response.error || "Có lỗi xảy ra khi lưu kết quả");
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error saving the result:', textStatus, errorThrown);
                if (jqXHR.responseJSON && jqXHR.responseJSON.error) {
                    toastr.error(jqXHR.responseJSON.error);
                } else {
                    toastr.error("Có lỗi xảy ra khi lưu kết quả");
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
