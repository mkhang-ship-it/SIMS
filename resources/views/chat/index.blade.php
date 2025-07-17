@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="main-chart-wrapper p-2 gap-2 d-lg-flex">
            <div class="chat-info border">
                <a aria-label="anchor" href="javascript:void(0)"
                    class="btn btn-secondary btn-icon rounded-circle chat-add-icon" data-bs-toggle="modal"
                    data-bs-target="#chatAddModal">
                    <i class="ri-add-line"></i>
                </a>
                <div class="d-flex align-items-center justify-content-between w-100 p-3 border-bottom">
                    <div>
                        <h5 class="fw-semibold mb-0">Messages</h5>
                    </div>
                </div>
                <div class="chat-search p-3 border-bottom">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0" placeholder="Search Chat"
                            aria-describedby="button-addon2">
                        <button aria-label="button" class="btn btn-light" type="button" id="button-addon2"><i
                                class="ri-search-line text-muted"></i></button>
                    </div>
                </div>
                <ul class="nav nav-tabs tab-style-2 nav-justified mb-0 border-bottom d-flex" id="myTab1" role="tablist">
                    <li class="nav-item border-end me-0" role="presentation">
                        <button class="nav-link active h-100" id="users-tab" data-bs-toggle="tab"
                            data-bs-target="#users-tab-pane" type="button" role="tab" aria-controls="users-tab-pane"
                            aria-selected="true"><i
                                class="ri-history-line me-1 align-middle d-inline-block"></i>Recent</button>
                    </li>
                    <li class="nav-item border-end me-0" role="presentation">
                        <button class="nav-link h-100" id="groups-tab" data-bs-toggle="tab"
                            data-bs-target="#groups-tab-pane" type="button" role="tab" aria-controls="groups-tab-pane"
                            aria-selected="false"><i
                                class="ri-group-2-line me-1 align-middle d-inline-block"></i>Groups</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active border-0 chat-users-tab" id="users-tab-pane" role="tabpanel"
                        aria-labelledby="users-tab" tabindex="0">
                        <ul class="list-unstyled mb-0 mt-2 chat-users-tab" id="chat-msg-scroll">
                            @forelse($chats->where('is_group', false) as $chat)
                                @php
                                    $isGroup = $chat->is_group;
                                    $lastMsg = $chat->messages->last();
                                    $otherUsers = $chat->users->where('id', '!=', auth()->id());
                                    $chatName = $isGroup ? $chat->name : $otherUsers->first()->name ?? 'No Name';
                                    $avatar = $isGroup
                                        ? asset('assets/images/faces/18.jpg')
                                        : asset(
                                            'assets/images/faces/' . ($otherUsers->first()?->avatar ?? '2') . '.jpg',
                                        );
                                    $lastMsgText = $lastMsg
                                        ? ($lastMsg->type === 'text'
                                            ? $lastMsg->content
                                            : '[' . $lastMsg->type . ']')
                                        : '';
                                    $lastMsgTime = $lastMsg ? $lastMsg->created_at->format('H:i d/m/Y') : '';
                                    $status = $isGroup
                                        ? 'online'
                                        : (optional($otherUsers->first())->is_online
                                            ? 'online'
                                            : 'offline');
                                @endphp
                                <li class="checkforactive">
                                    <a href="javascript:void(0);" data-chat-id="{{ $chat->id }}">
                                        <div class="d-flex align-items-top">
                                            <div class="me-1 lh-1">
                                                <span class="avatar avatar-md {{ $status }} me-2 avatar-rounded">
                                                    <img src="{{ $avatar }}" alt="img">
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="mb-0 fw-semibold">
                                                    {{ $chatName }} <span
                                                        class="float-end text-muted fw-normal fs-11">{{ $lastMsgTime }}</span>
                                                </p>
                                                <p class="fs-12 mb-0">
                                                    <span class="chat-msg text-truncate">{{ $lastMsgText }}</span>
                                                    @if ($lastMsg && $lastMsg->sender_id == auth()->id())
                                                        <span class="chat-read-icon float-end align-middle"><i
                                                                class="ri-check-double-fill"></i></span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li>
                                    <div class="text-center text-muted">Không có cuộc trò chuyện cá nhân nào</div>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                    <div class="tab-pane fade border-0 chat-groups-tab" id="groups-tab-pane" role="tabpanel"
                        aria-labelledby="groups-tab" tabindex="0">
                        <ul class="list-unstyled mb-0 mt-2 chat-groups-tab">
                            @forelse($chats->where('is_group', true) as $chat)
                                @php
                                    $lastMsg = $chat->messages->last();
                                    $chatName = $chat->name;
                                    $avatar = asset('assets/images/faces/18.jpg');
                                    $lastMsgText = $lastMsg
                                        ? ($lastMsg->type === 'text'
                                            ? $lastMsg->content
                                            : '[' . $lastMsg->type . ']')
                                        : '';
                                    $lastMsgTime = $lastMsg ? $lastMsg->created_at->format('H:i d/m/Y') : '';
                                    $status = 'online';
                                @endphp
                                <li class="checkforactive">
                                    <a href="javascript:void(0);" data-chat-id="{{ $chat->id }}">
                                        <div class="d-flex align-items-top">
                                            <div class="me-1 lh-1">
                                                <span class="avatar avatar-md {{ $status }} me-2 avatar-rounded">
                                                    <img src="{{ $avatar }}" alt="img">
                                                </span>
                                            </div>
                                            <div class="flex-fill">
                                                <p class="mb-0 fw-semibold">
                                                    {{ $chatName }} <span
                                                        class="float-end text-muted fw-normal fs-11">{{ $lastMsgTime }}</span>
                                                </p>
                                                <p class="fs-12 mb-0">
                                                    <span class="chat-msg text-truncate">{{ $lastMsgText }}</span>
                                                    @if ($lastMsg && $lastMsg->sender_id == auth()->id())
                                                        <span class="chat-read-icon float-end align-middle"><i
                                                                class="ri-check-double-fill"></i></span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li>
                                    <div class="text-center text-muted">Không có nhóm nào</div>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
            <div class="main-chat-area border">
                <div class="d-flex align-items-center p-2 border-bottom">
                    <div class="me-2 lh-1">
                        <span class="avatar avatar-lg online me-2 avatar-rounded chatstatusperson">
                            <img class="chatimageperson" src="../assets/images/faces/2.jpg" alt="img">
                        </span>
                    </div>
                    <div class="flex-fill">
                        <p class="mb-0 fw-semibold fs-14">
                            <a href="javascript:void(0);" class="chatnameperson responsive-userinfo-open">Emiley
                                Jackson</a>
                        </p>
                        <p class="text-muted mb-0 chatpersonstatus">online</p>
                    </div>
                    <div class="d-flex">
                        <button aria-label="button" type="button"
                            class="btn btn-icon btn-outline-light ms-2 responsive-userinfo-open">
                            <i class="ti ti-user-circle" id="responsive-chat-close"></i>
                        </button>
                        <div class="dropdown ms-2">
                            <button aria-label="button" class="btn btn-icon btn-outline-light btn-wave waves-light"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti ti-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Clear Chat</a></li>
                            </ul>
                        </div>
                        <button aria-label="button" type="button"
                            class="btn btn-icon btn-outline-light ms-2 responsive-chat-close">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                </div>
                <div class="chat-content" id="main-chat-content">
                    <ul class="list-unstyled">
                        <li class="chat-day-label">
                            <span>Today</span>
                        </li>
                        <li class="chat-item-start">
                            <div class="chat-list-inner">
                                <div class="chat-user-profile">
                                    <span class="avatar avatar-md online avatar-rounded chatstatusperson">
                                        <img class="chatimageperson" src="../assets/images/faces/2.jpg" alt="img">
                                    </span>
                                </div>
                                <div class="ms-3">
                                    <span class="chatting-user-info">
                                        <span class="chatnameperson">Emiley Jackson</span> <span
                                            class="msg-sent-time">11:48PM</span>
                                    </span>
                                    <div class="main-chat-msg">
                                        <div>
                                            <p class="mb-0">Nice to meet you 😀</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="chat-item-end">
                            <div class="chat-list-inner">
                                <div class="me-3">
                                    <span class="chatting-user-info">
                                        <span class="msg-sent-time"><span
                                                class="chat-read-mark align-middle d-inline-flex"><i
                                                    class="ri-check-double-line"></i></span>11:50PM</span> You
                                    </span>
                                    <div class="main-chat-msg">
                                        <div>
                                            <p class="mb-0">It is a long established fact that a reader will be
                                                distracted by the readable content of a page when looking at its
                                                layout</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="chat-user-profile">
                                    <span class="avatar avatar-md online avatar-rounded">
                                        <img src="../assets/images/faces/15.jpg" alt="img">
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li class="chat-item-end">
                            <div class="chat-list-inner">
                                <div class="me-3">
                                    <span class="chatting-user-info">
                                        <span class="msg-sent-time"><span
                                                class="chat-read-mark align-middle d-inline-flex"><i
                                                    class="ri-check-double-line"></i></span>11:52PM</span> You
                                    </span>
                                    <div class="main-chat-msg">
                                        <div>
                                            <p class="mb-0">Some of the recent images taken are nice 👌</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="chat-user-profile">
                                    <span class="avatar avatar-md online avatar-rounded">
                                        <img src="../assets/images/faces/15.jpg" alt="img">
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li class="chat-item-start">
                            <div class="chat-list-inner">
                                <div class="chat-user-profile">
                                    <span class="avatar avatar-md online avatar-rounded chatstatusperson">
                                        <img class="chatimageperson" src="../assets/images/faces/2.jpg" alt="img">
                                    </span>
                                </div>
                                <div class="ms-3">
                                    <span class="chatting-user-info">
                                        <span class="chatnameperson">Emiley Jackson</span> <span
                                            class="msg-sent-time">11:55PM</span>
                                    </span>
                                    <div class="main-chat-msg">
                                        <div>
                                            <p class="mb-0">Here are some of them have a look</p>
                                        </div>
                                        <div>
                                            <p class="mb-0 d-sm-flex d-block">
                                                <a aria-label="anchor" href="gallery.html"
                                                    class="avatar avatar-xl m-1"><img
                                                        src="../assets/images/media/media-64.jpg" alt=""></a>
                                                <a aria-label="anchor" href="gallery.html"
                                                    class="avatar avatar-xl m-1"><img
                                                        src="../assets/images/media/media-63.jpg" alt=""></a>
                                                <a aria-label="anchor" href="gallery.html"
                                                    class="avatar avatar-xl m-1"><img
                                                        src="../assets/images/media/media-62.jpg" alt=""></a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                    </ul>
                </div>
                <div class="chat-footer">
                    <input class="form-control" placeholder="Type your message here..." type="text">
                    <a aria-label="anchor" class="btn btn-icon mx-2 btn-success-light" href="javascript:void(0)">
                        <i class="ri-emotion-line"></i>
                    </a>
                    <a aria-label="anchor" class="btn btn-primary btn-icon btn-send" href="javascript:void(0)">
                        <i class="ri-send-plane-2-line"></i>
                    </a>
                </div>
            </div>
            <div class="chat-user-details border" id="chat-user-details">
                <div class="text-center mb-5">
                    <span class="avatar avatar-rounded online avatar-xxl me-2 mb-3 chatstatusperson">
                        <img class="chatimageperson" src="../assets/images/faces/2.jpg" alt="img">
                    </span>
                    <p class="mb-1 fs-15 fw-semibold text-dark lh-1 chatnameperson">Emiley Jackson</p>
                    <p class="fs-12 text-muted"><span class="chatnameperson">emaileyjackson2134</span>@gmail.com</p>
                </div>
                <div class="mb-5">
                    <div class="fw-semibold mb-4">Shared Files
                        <span class="badge bg-primary-transparent rounded-circle ms-1">4</span>
                        <span class="float-end fs-11"><a href="javascript:void(0);"
                                class="link-primary text-underline"><u>View All</u></a></span>
                    </div>
                    <ul class="shared-files list-unstyled">
                        <li>
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <span class="shared-file-icon">
                                        <i class="ti ti-file-text"></i>
                                    </span>
                                </div>
                                <div class="flex-fill">
                                    <p class="fs-12 fw-semibold mb-0">Project Details.pdf</p>
                                    <p class="mb-0 text-muted fs-11">24,Oct 2022 - 14:24PM</p>
                                </div>
                                <div class="fs-18">
                                    <a aria-label="anchor" href="javascript:void(0)"><i
                                            class="ri-download-2-line text-muted"></i></a>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <span class="shared-file-icon">
                                        <i class="ri-image-line"></i>
                                    </span>
                                </div>
                                <div class="flex-fill">
                                    <p class="fs-12 fw-semibold mb-0">Img_02.Jpg</p>
                                    <p class="mb-0 text-muted fs-11">22,Oct 2022 - 10:19AM</p>
                                </div>
                                <div class="fs-18">
                                    <a aria-label="anchor" href="javascript:void(0)"><i
                                            class="ri-download-2-line text-muted"></i></a>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <span class="shared-file-icon">
                                        <i class="ri-video-line"></i>
                                    </span>
                                </div>
                                <div class="flex-fill">
                                    <p class="fs-12 fw-semibold mb-0">Video_15_02_2022.MP4</p>
                                    <p class="mb-0 text-muted fs-11">22,Oct 2022 - 10:18AM</p>
                                </div>
                                <div class="fs-18">
                                    <a href="javascript:void(0)"><i class="ri-download-2-line text-muted"></i></a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="mb-0">
                    <div class="fw-semibold mb-4">Photos & Media
                        <span class="badge bg-primary-transparent rounded-circle ms-1">22</span>
                        <span class="float-end fs-11"><a href="javascript:void(0);"
                                class="link-primary text-underline"><u>View All</u></a></span>
                    </div>
                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4">
                            <a aria-label="anchor" href="gallery.html" class="chat-media">
                                <img src="../assets/images/media/media-56.jpg" alt="">
                            </a>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4">
                            <a aria-label="anchor" href="gallery.html" class="chat-media">
                                <img src="../assets/images/media/media-52.jpg" alt="">
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal chọn user hoặc tạo nhóm --}}
    <div class="modal fade" id="chatAddModal" tabindex="-1" aria-labelledby="chatAddModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="chatAddModalLabel">Bắt đầu cuộc trò chuyện</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="chatAddTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="private-tab" data-bs-toggle="tab"
                                data-bs-target="#private" type="button" role="tab">Nhắn tin riêng</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="group-tab" data-bs-toggle="tab" data-bs-target="#group"
                                type="button" role="tab">Tạo nhóm</button>
                        </li>
                    </ul>
                    <div class="tab-content mt-3">
                        <div class="tab-pane fade show active" id="private" role="tabpanel">
                            <div class="mb-2">Chọn user để nhắn tin:</div>
                            <select class="form-select" id="privateUserSelect">
                                <option value="">-- Chọn user --</option>
                            </select>
                            <button class="btn btn-primary mt-2 w-100" id="startPrivateChatBtn">Bắt đầu nhắn tin</button>
                        </div>
                        <div class="tab-pane fade" id="group" role="tabpanel">
                            <div class="mb-2">Chọn thành viên nhóm:</div>
                            <div id="groupUserList" style="max-height:200px;overflow:auto;"></div>
                            <input type="text" class="form-control mt-2" id="groupNameInput" placeholder="Tên nhóm">
                            <button class="btn btn-success mt-2 w-100" id="createGroupBtn">Tạo nhóm</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.15.2/echo.iife.js"></script>
    <script>
        (function() {
            "use strict";

            var myElement1 = document.getElementById('chat-msg-scroll');
            new SimpleBar(myElement1, {
                autoHide: true
            });

            var myElement2 = document.getElementById('groups-tab-pane');
            new SimpleBar(myElement2, {
                autoHide: true
            });

            var myElement4 = document.getElementById('main-chat-content');
            new SimpleBar(myElement4, {
                autoHide: true
            });

            var myElement5 = document.getElementById('chat-user-details');
            new SimpleBar(myElement5, {
                autoHide: true
            });

            document.querySelector(".responsive-chat-close").addEventListener("click", () => {
                document.querySelector(".main-chart-wrapper").classList.remove("responsive-chat-open")
            })

            document.querySelectorAll(".responsive-userinfo-open").forEach((ele) => {
                ele.addEventListener("click", () => {
                    document.querySelector("#chat-user-details").classList.add("open")
                })
            })

            document.querySelector(".chat-info").addEventListener("click", () => {
                document.querySelector("#chat-user-details").classList.remove("open")
            })
            document.querySelector(".chat-content").addEventListener("click", () => {
                document.querySelector("#chat-user-details").classList.remove("open")
            })

        })();

        let changeTheInfo = (element, name, img, status) => {
            document.querySelectorAll(".checkforactive").forEach((ele) => {
                ele.classList.remove("active")
            })
            element.closest("li").classList.add("active")
            document.querySelectorAll(".chatnameperson").forEach((ele) => {
                ele.innerText = name
            })
            let image = `../assets/images/faces/${img}.jpg`
            document.querySelectorAll(".chatimageperson").forEach((ele) => {
                ele.src = image
            })

            document.querySelectorAll(".chatstatusperson").forEach((ele) => {
                ele.classList.remove("online")
                ele.classList.remove("offline")
                ele.classList.add(status)
            })

            document.querySelector(".chatpersonstatus").innerText = status === 'online' ? 'Online' : 'Offline';

            document.querySelector(".main-chart-wrapper").classList.add("responsive-chat-open")
        }
    </script>
    <script>
        // Enhanced auto-scroll functionality for chat
        let currentChatId = null;
        let currentUserId = '{{ auth()->id() }}';
        let chats = @json($chats);
        let isUserScrolling = false;
        let scrollTimeout;

        // Laravel Echo + Pusher configuration
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ config('broadcasting.connections.pusher.key') }}',
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
            forceTLS: true,
            encrypted: true,
            auth: {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            },
            authEndpoint: '/broadcasting/auth',
        });

        // Improved scroll to bottom function
        function scrollToBottom(smooth = true) {
            const chatContent = document.querySelector('#main-chat-content');
            if (!chatContent) return;

            // Hàm scroll thực tế
            const doScroll = () => {
                if (smooth) {
                    chatContent.scrollTo({
                        top: chatContent.scrollHeight,
                        behavior: 'smooth'
                    });
                } else {
                    chatContent.scrollTop = chatContent.scrollHeight;
                }
            };

            // Scroll ngay lập tức
            doScroll();

            // Đảm bảo scroll sau khi DOM render xong
            requestAnimationFrame(() => {
                doScroll();

                // Scroll một lần nữa sau khi tất cả animation hoàn thành
                setTimeout(() => {
                    doScroll();
                }, 100);
            });
        }

        // Check if user is at bottom of chat
        function isAtBottom() {
            const chatContent = document.querySelector('#main-chat-content');
            if (!chatContent) return true;

            const threshold = 50; // pixels from bottom
            return chatContent.scrollHeight - chatContent.scrollTop - chatContent.clientHeight < threshold;
        }

        // Track user scrolling
        function setupScrollTracking() {
            const chatContent = document.querySelector('#main-chat-content');
            if (!chatContent) return;

            let scrollTimer;

            chatContent.addEventListener('scroll', () => {
                isUserScrolling = true;

                // Clear existing timer
                if (scrollTimer) {
                    clearTimeout(scrollTimer);
                }

                // Set user scrolling to false after 1 second of no scrolling
                scrollTimer = setTimeout(() => {
                    isUserScrolling = false;
                }, 1000);
            });
        }

        // Enhanced message append function with auto-scroll
        function appendMessage(message, isOwn = false) {
            const ul = document.querySelector('#main-chat-content ul');
            if (!ul) return;

            // Kiểm tra xem tin nhắn đã tồn tại chưa (để tránh duplicate từ real-time event)
            const existingMessages = ul.querySelectorAll('.chat-item-end, .chat-item-start');
            const messageContent = message.content;
            const messageTime = message.created_at;

            // Kiểm tra tin nhắn duplicate trong 5 giây gần đây
            const recentMessages = Array.from(existingMessages).slice(-5);
            const isDuplicate = recentMessages.some(msg => {
                const msgText = msg.querySelector('.main-chat-msg p')?.textContent;
                const msgSender = msg.classList.contains('chat-item-end') ? currentUserId : 'other';
                const currentSender = (isOwn || message.sender_id == currentUserId) ? currentUserId : 'other';

                return msgText === messageContent && msgSender === currentSender;
            });

            if (isDuplicate) {
                console.log('Duplicate message detected, skipping...');
                return;
            }

            const wasAtBottom = isAtBottom();
            const li = document.createElement('li');
            li.className = isOwn || message.sender_id == currentUserId ? 'chat-item-end' : 'chat-item-start';

            // Format time H:i d/m/Y
            let createdAt = message.created_at;
            if (createdAt && createdAt.length >= 19) {
                const d = new Date(createdAt);
                const pad = n => n < 10 ? '0' + n : n;
                createdAt = pad(d.getHours()) + ':' + pad(d.getMinutes()) + ' ' +
                    pad(d.getDate()) + '/' + pad(d.getMonth() + 1) + '/' + d.getFullYear();
            }

            let html = '';
            if (li.className === 'chat-item-end') {
                html = `<div class="chat-list-inner">
            <div class="me-3">
                <span class="chatting-user-info">
                    <span class="msg-sent-time">
                        <span class="chat-read-mark align-middle d-inline-flex">
                            <i class="ri-check-double-line"></i>
                        </span>
                        ${createdAt}
                    </span> You
                </span>
                <div class="main-chat-msg">
                    <div>
                        <p class="mb-0">${message.content}</p>
                    </div>
                </div>
            </div>
            <div class="chat-user-profile">
                <span class="avatar avatar-md online avatar-rounded">
                    <img src="../assets/images/faces/15.jpg" alt="img">
                </span>
            </div>
        </div>`;
            } else {
                html = `<div class="chat-list-inner">
            <div class="chat-user-profile">
                <span class="avatar avatar-md online avatar-rounded chatstatusperson">
                    <img class="chatimageperson" src="../assets/images/faces/2.jpg" alt="img">
                </span>
            </div>
            <div class="ms-3">
                <span class="chatting-user-info">
                    <span class="chatnameperson">${message.sender_name || ''}</span>
                    <span class="msg-sent-time">${createdAt}</span>
                </span>
                <div class="main-chat-msg">
                    <div>
                        <p class="mb-0">${message.content}</p>
                    </div>
                </div>
            </div>
        </div>`;
            }

            li.innerHTML = html;
            ul.appendChild(li);

            // Auto-scroll với cải thiện
            if (isOwn || message.sender_id == currentUserId) {
                // Luôn scroll cho tin nhắn của chính mình
                setTimeout(() => {
                    scrollToBottom(true);
                }, 10);
            } else if (wasAtBottom && !isUserScrolling) {
                // Chỉ auto-scroll cho tin nhắn của người khác nếu đang ở cuối
                setTimeout(() => {
                    scrollToBottom(true);
                }, 10);
            } else if (!wasAtBottom) {
                // Hiện thị thông báo tin nhắn mới nếu không ở cuối
                showNewMessageIndicator();
            }
        }

        // Show new message indicator
        function showNewMessageIndicator() {
            // Remove existing indicator
            const existingIndicator = document.querySelector('.new-message-indicator');
            if (existingIndicator) {
                existingIndicator.remove();
            }

            // Create new message indicator
            const indicator = document.createElement('div');
            indicator.className = 'new-message-indicator';
            indicator.innerHTML = `
        <div class="alert alert-info d-flex align-items-center justify-content-between mb-0"
             style="position: sticky; top: 0; z-index: 10; cursor: pointer;">
            <span>New messages ↓</span>
            <button class="btn btn-sm btn-info" onclick="scrollToBottomAndHideIndicator()">
                <i class="ri-arrow-down-line"></i>
            </button>
        </div>
    `;

            const chatContent = document.querySelector('#main-chat-content');
            chatContent.insertBefore(indicator, chatContent.firstChild);
        }

        // Scroll to bottom and hide indicator
        function scrollToBottomAndHideIndicator() {
            const indicator = document.querySelector('.new-message-indicator');
            if (indicator) {
                indicator.remove();
            }
            scrollToBottom(true);
        }

        // Send message function with auto-scroll
        document.querySelector('.btn-send').addEventListener('click', function() {
            const input = document.querySelector('.chat-footer input');
            const content = input.value.trim();
            if (!content || !currentChatId) {
                return;
            }

            // Tạo tin nhắn tạm thời để hiển thị ngay lập tức
            const tempMessage = {
                content: content,
                sender_id: currentUserId,
                created_at: new Date().toISOString(),
                type: 'text',
                sender_name: 'You'
            };

            // Hiển thị tin nhắn ngay lập tức
            appendMessage(tempMessage, true);

            // Clear input ngay lập tức
            input.value = '';

            // Gửi tin nhắn đến server
            fetch("{{ route('chat.messages.send') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        chat_id: currentChatId,
                        content: content,
                        type: 'text'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Tin nhắn đã được gửi thành công
                        // Có thể cập nhật trạng thái đã gửi ở đây nếu cần
                        console.log('Message sent successfully');

                        // Cập nhật message ID thực nếu cần
                        if (data.message && data.message.id) {
                            // Có thể cập nhật tin nhắn tạm thời với ID thực
                            updateMessageStatus(data.message.id, 'sent');
                        }
                    }
                })
                .catch(err => {
                    console.error('Error sending message:', err);
                    // Có thể hiển thị thông báo lỗi hoặc đánh dấu tin nhắn failed
                    showMessageError(tempMessage);
                });
        });

        // Hàm cập nhật trạng thái tin nhắn
        function updateMessageStatus(messageId, status) {
            // Tìm tin nhắn và cập nhật trạng thái
            const messages = document.querySelectorAll('.chat-item-end');
            const lastMessage = messages[messages.length - 1];
            if (lastMessage) {
                const readMark = lastMessage.querySelector('.chat-read-mark i');
                if (readMark) {
                    readMark.className = status === 'sent' ? 'ri-check-double-line' : 'ri-check-line';
                }
            }
        }

        // Hàm hiển thị lỗi tin nhắn
        function showMessageError(message) {
            const messages = document.querySelectorAll('.chat-item-end');
            const lastMessage = messages[messages.length - 1];
            if (lastMessage) {
                const readMark = lastMessage.querySelector('.chat-read-mark i');
                if (readMark) {
                    readMark.className = 'ri-error-warning-line';
                    readMark.style.color = 'red';
                }
            }
        }

        // Handle Enter key press
        document.querySelector('.chat-footer input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.querySelector('.btn-send').click();
            }
        });

        // Store current channel reference
        let currentChannel = null;

        // Enhanced chat selection with auto-scroll
        document.querySelectorAll('.chat-users-tab a, .chat-groups-tab a').forEach(function(a) {
            a.addEventListener('click', function() {
                const chatId = this.getAttribute('data-chat-id');
                if (!chatId) return;

                currentChatId = chatId;

                // Update UI to show active chat
                document.querySelectorAll('.checkforactive').forEach(li => li.classList.remove('active'));
                this.closest('.checkforactive').classList.add('active');

                // Clear any existing new message indicator
                const indicator = document.querySelector('.new-message-indicator');
                if (indicator) {
                    indicator.remove();
                }

                // Load messages
                fetch(`/chat/${chatId}/messages`)
                    .then(res => res.json())
                    .then(messages => {
                        const ul = document.querySelector('#main-chat-content ul');
                        ul.innerHTML = '<li class="chat-day-label"><span>Today</span></li>';

                        messages.forEach(m => {
                            const li = document.createElement('li');
                            li.className = m.sender_id == currentUserId ? 'chat-item-end' :
                                'chat-item-start';

                            // Format time
                            let createdAt = m.created_at;
                            if (createdAt && createdAt.length >= 19) {
                                const d = new Date(createdAt);
                                const pad = n => n < 10 ? '0' + n : n;
                                createdAt = pad(d.getHours()) + ':' + pad(d.getMinutes()) +
                                    ' ' +
                                    pad(d.getDate()) + '/' + pad(d.getMonth() + 1) + '/' + d
                                    .getFullYear();
                            }

                            let html = '';
                            if (li.className === 'chat-item-end') {
                                html = `<div class="chat-list-inner">
                            <div class="me-3">
                                <span class="chatting-user-info">
                                    <span class="msg-sent-time">
                                        <span class="chat-read-mark align-middle d-inline-flex">
                                            <i class="ri-check-double-line"></i>
                                        </span>
                                        ${createdAt}
                                    </span> You
                                </span>
                                <div class="main-chat-msg">
                                    <div><p class="mb-0">${m.content}</p></div>
                                </div>
                            </div>
                            <div class="chat-user-profile">
                                <span class="avatar avatar-md online avatar-rounded">
                                    <img src="../assets/images/faces/15.jpg" alt="img">
                                </span>
                            </div>
                        </div>`;
                            } else {
                                html = `<div class="chat-list-inner">
                            <div class="chat-user-profile">
                                <span class="avatar avatar-md online avatar-rounded chatstatusperson">
                                    <img class="chatimageperson" src="../assets/images/faces/2.jpg" alt="img">
                                </span>
                            </div>
                            <div class="ms-3">
                                <span class="chatting-user-info">
                                    <span class="chatnameperson">${m.sender_name || ''}</span>
                                    <span class="msg-sent-time">${createdAt}</span>
                                </span>
                                <div class="main-chat-msg">
                                    <div><p class="mb-0">${m.content}</p></div>
                                </div>
                            </div>
                        </div>`;
                            }

                            li.innerHTML = html;
                            ul.appendChild(li);
                        });

                        // Đảm bảo scroll xuống cuối sau khi load xong tất cả tin nhắn
                        // Sử dụng MutationObserver để theo dõi khi DOM thay đổi
                        const observer = new MutationObserver((mutations) => {
                            observer.disconnect(); // Chỉ observe một lần

                            // Scroll xuống cuối ngay lập tức
                            scrollToBottom(false);

                            // Đảm bảo scroll một lần nữa sau khi tất cả render xong
                            setTimeout(() => {
                                scrollToBottom(false);
                            }, 50);

                            // Scroll lần cuối sau khi SimpleBar khởi tạo xong
                            setTimeout(() => {
                                scrollToBottom(false);
                            }, 200);
                        });

                        // Bắt đầu observe
                        observer.observe(ul, {
                            childList: true,
                            subtree: true
                        });

                        // Fallback scroll nếu observer không hoạt động
                        setTimeout(() => {
                            scrollToBottom(false);
                        }, 100);
                    })
                    .catch(err => {
                        console.error('Error loading messages:', err);
                    });

                // Update chat header info
                const chat = chats.find(c => c.id == chatId);
                if (chat) {
                    const isGroup = chat.is_group;
                    const name = isGroup ? chat.name : (chat.users.find(u => u.id != currentUserId)?.name ||
                        'No Name');
                    const avatar = isGroup ? '18' : (chat.users.find(u => u.id != currentUserId)?.avatar ||
                        '2');
                    const status = isGroup ? 'online' : (chat.users.find(u => u.id != currentUserId)
                        ?.is_online ? 'online' : 'offline');

                    document.querySelectorAll('.chatnameperson').forEach(ele => {
                        ele.innerText = name;
                    });
                    document.querySelectorAll('.chatimageperson').forEach(ele => {
                        ele.src = `../assets/images/faces/${avatar}.jpg`;
                    });
                    document.querySelectorAll('.chatstatusperson').forEach(ele => {
                        ele.classList.remove('online', 'offline');
                        ele.classList.add(status);
                    });
                    document.querySelector('.chatpersonstatus').innerText = status === 'online' ? 'Online' :
                        'Offline';
                }

                // Subscribe to this chat's channel
                subscribeToChat(chatId);
            });
        });

        function ensureScrollToBottom() {
            const chatContent = document.querySelector('#main-chat-content');
            if (!chatContent) return;

            // Kiểm tra nếu SimpleBar đã khởi tạo
            if (chatContent.SimpleBar) {
                chatContent.SimpleBar.getScrollElement().scrollTop = chatContent.SimpleBar.getScrollElement().scrollHeight;
            } else {
                chatContent.scrollTop = chatContent.scrollHeight;
            }
        }

        // Enhanced subscription function
        function subscribeToChat(chatId) {
            // Leave previous channel if exists
            if (currentChannel) {
                window.Echo.leave(currentChannel);
            }

            // Join new channel
            currentChannel = `chat.${chatId}`;

            window.Echo.private(currentChannel)
                .listen('MessageSent', (e) => {
                    // Only append if in this chat and not from current user
                    if (currentChatId == chatId && e.message.sender_id != currentUserId) {
                        appendMessage(e.message, false);
                    }
                })
                .subscribed(() => {
                    console.log('Subscribed to chat:', chatId);
                })
                .error((error) => {
                    console.error('Echo error:', error);
                });
        }

        // Initialize scroll tracking when page loads
        document.addEventListener('DOMContentLoaded', function() {
            setupScrollTracking();

            // Add CSS for new message indicator
            const style = document.createElement('style');
            style.textContent = `
        .new-message-indicator {
            position: sticky;
            top: 0;
            z-index: 1000;
            margin-bottom: 10px;
        }
        .new-message-indicator .alert {
            margin-bottom: 0;
            border-radius: 0;
            animation: slideDown 0.3s ease-out;
        }
        @keyframes slideDown {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    `;
            document.head.appendChild(style);
        });

        // User management code (keeping existing functionality)
        let allUsers = [];
        fetch('{{ route('chat.users.get') }}')
            .then(res => res.json())
            .then(data => {
                const users = data.users;
                allUsers = users.filter(u => u.id != currentUserId);

                // Populate private chat select
                const select = document.getElementById('privateUserSelect');
                allUsers.forEach(u => {
                    const opt = document.createElement('option');
                    opt.value = u.id;
                    opt.textContent = u.name + ' (' + u.email + ')';
                    select.appendChild(opt);
                });

                // Populate group creation list
                const groupList = document.getElementById('groupUserList');
                allUsers.forEach(u => {
                    const div = document.createElement('div');
                    div.innerHTML =
                        `<label><input type='checkbox' value='${u.id}'> ${u.name} (${u.email})</label>`;
                    groupList.appendChild(div);
                });
            })
            .catch(err => {
                console.error('Error fetching users:', err);
            });

        // Private chat function
        document.getElementById('startPrivateChatBtn').onclick = function() {
            const userId = document.getElementById('privateUserSelect').value;
            if (!userId) return alert('Chọn user!');

            fetch("{{ route('chat.private.start') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        user_id: userId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.chat) {
                        currentChatId = data.chat.id;
                        const modal = bootstrap.Modal.getInstance(document.getElementById('chatAddModal'));
                        modal.hide();
                        location.reload();
                    }
                })
                .catch(err => {
                    console.error('Error starting private chat:', err);
                });
        };

        // Group creation function
        document.getElementById('createGroupBtn').onclick = function() {
            const name = document.getElementById('groupNameInput').value.trim();
            const userIds = Array.from(document.querySelectorAll('#groupUserList input[type=checkbox]:checked')).map(
                cb => cb.value);

            if (!name) return alert('Nhập tên nhóm!');
            if (userIds.length < 2) return alert('Chọn ít nhất 2 thành viên!');

            fetch("{{ route('chat.group.create') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        name: name,
                        user_ids: userIds
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.chat) {
                        currentChatId = data.chat.id;
                        const modal = bootstrap.Modal.getInstance(document.getElementById('chatAddModal'));
                        modal.hide();
                        location.reload();
                    }
                })
                .catch(err => {
                    console.error('Error creating group:', err);
                });
        };

        let sendMessageTimeout;

        function debouncedSendMessage() {
            clearTimeout(sendMessageTimeout);
            sendMessageTimeout = setTimeout(() => {
                document.querySelector('.btn-send').click();
            }, 100);
        }

        // Handle Enter key press với debounce
        document.querySelector('.chat-footer input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.querySelector('.btn-send').click();
            }
        });

        // Thêm loading state cho button send
        function setLoadingState(isLoading) {
            const sendBtn = document.querySelector('.btn-send');
            const sendIcon = sendBtn.querySelector('i');

            if (isLoading) {
                sendBtn.disabled = true;
                sendIcon.className = 'ri-loader-4-line';
                sendIcon.style.animation = 'spin 1s linear infinite';
            } else {
                sendBtn.disabled = false;
                sendIcon.className = 'ri-send-plane-2-line';
                sendIcon.style.animation = 'none';
            }
        }

        // CSS cho loading animation
        const style = document.createElement('style');
        style.textContent = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .message-error {
        color: #dc3545 !important;
    }

    .message-sending {
        opacity: 0.7;
    }

    .message-sent {
        opacity: 1;
    }
`;
        document.head.appendChild(style);
    </script>
@endsection
