@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="main-chart-wrapper p-2 gap-2 d-lg-flex">
            <div class="chat-info border">
                <a aria-label="anchor" href="javascript:void(0)"
                    class="btn btn-secondary btn-icon rounded-circle chat-add-icon">
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
                            <li class="checkforactive">
                                <a href="javascript:void(0);" onclick="changeTheInfo(this,'Sujika','5','online')">
                                    <div class="d-flex align-items-top">
                                        <div class="me-1 lh-1">
                                            <span class="avatar avatar-md online me-2 avatar-rounded">
                                                <img src="../assets/images/faces/5.jpg" alt="img">
                                            </span>
                                        </div>
                                        <div class="flex-fill">
                                            <p class="mb-0 fw-semibold">
                                                Sujika <span class="float-end text-muted fw-normal fs-11">1:32PM</span>
                                            </p>
                                            <p class="fs-12 mb-0">
                                                <span class="chat-msg text-truncate">Need to go for lunch?</span>
                                                <span class="chat-read-icon float-end align-middle"><i
                                                        class="ri-check-double-fill"></i></span>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="checkforactive">
                                <a href="javascript:void(0);" onclick="changeTheInfo(this,'Emiley Jackson','2','online')">
                                    <div class="d-flex align-items-top">
                                        <div class="me-1 lh-1">
                                            <span class="avatar avatar-md online me-2 avatar-rounded">
                                                <img src="../assets/images/faces/2.jpg" alt="img">
                                            </span>
                                        </div>
                                        <div class="flex-fill">
                                            <p class="mb-0 fw-semibold">
                                                Emiley Jackson <span
                                                    class="float-end text-muted fw-normal fs-11">12:24PM</span>
                                            </p>
                                            <p class="fs-12 mb-0 chat-msg-typing ">
                                                <span class="chat-msg text-truncate">Typing...</span>
                                                <span class="chat-read-icon float-end align-middle"><i
                                                        class="ri-check-double-fill"></i></span>
                                                <span class="badge bg-success-transparent rounded-circle float-end">2</span>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="chat-msg-unread checkforactive">
                                <a href="javascript:void(0);" onclick="changeTheInfo(this,'McGreggor','10','online')">
                                    <div class="d-flex align-items-top">
                                        <div class="me-1 lh-1">
                                            <span class="avatar avatar-md online me-2 avatar-rounded">
                                                <img src="../assets/images/faces/10.jpg" alt="img">
                                            </span>
                                        </div>
                                        <div class="flex-fill">
                                            <p class="mb-0 fw-semibold">
                                                McGreggor <span class="float-end text-muted fw-normal fs-11">1:16PM</span>
                                            </p>
                                            <p class="fs-12 mb-0">
                                                <span class="chat-msg text-truncate">Nice to meet you 😊</span>
                                                <span class="chat-read-icon float-end align-middle"><i
                                                        class="ri-check-double-fill"></i></span>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="checkforactive">
                                <a href="javascript:void(0);" onclick="changeTheInfo(this,'Alissa','8','online')">
                                    <div class="d-flex align-items-top">
                                        <div class="me-1 lh-1">
                                            <span class="avatar avatar-md online me-2 avatar-rounded">
                                                <img src="../assets/images/faces/8.jpg" alt="img">
                                            </span>
                                        </div>
                                        <div class="flex-fill">
                                            <p class="mb-0 fw-semibold">
                                                Alissa <span class="float-end text-muted fw-normal fs-11">12:45PM</span>
                                            </p>
                                            <p class="fs-12 mb-0">
                                                <span class="chat-msg text-truncate">Chat with you
                                                    later,bye</span>
                                                <span class="chat-read-icon float-end align-middle"><i
                                                        class="ri-check-double-fill"></i></span>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-pane fade border-0 chat-groups-tab" id="groups-tab-pane" role="tabpanel"
                        aria-labelledby="groups-tab" tabindex="0">
                        <ul class="list-unstyled mb-0 mt-2 ">
                            <li class="checkforactive">
                                <a href="javascript:void(0);"
                                    onclick="changeTheInfo(this,'Family Together 😍','18','online')">
                                    <div class="d-flex align-items-top">
                                        <div class="me-1 lh-1">
                                            <span class="avatar avatar-md online me-2 avatar-rounded">
                                                <img src="../assets/images/faces/17.jpg" alt="img">
                                            </span>
                                        </div>
                                        <div class="flex-fill">
                                            <p class="mb-0 fw-semibold">
                                                Family Together 😍 <span
                                                    class="float-end text-muted fw-normal fs-11">12:24PM</span>
                                            </p>
                                            <p class="fs-12 mb-0 chat-msg-typing ">
                                                <span class="chat-msg text-truncate">Hira Typing...</span>
                                                <span class="chat-read-icon float-end align-middle"><i
                                                        class="ri-check-double-fill"></i></span>
                                                <span
                                                    class="badge bg-success-transparent rounded-circle float-end">2</span>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="chat-msg-unread checkforactive">
                                <a href="javascript:void(0);" onclick="changeTheInfo(this,'Work Buddies','19','online')">
                                    <div class="d-flex align-items-top">
                                        <div class="me-1 lh-1">
                                            <span class="avatar avatar-md online me-2 avatar-rounded">
                                                <img src="../assets/images/faces/18.jpg" alt="img">
                                            </span>
                                        </div>
                                        <div class="flex-fill">
                                            <p class="mb-0 fw-semibold">
                                                Work Buddies <span
                                                    class="float-end text-muted fw-normal fs-11">1:16PM</span>
                                            </p>
                                            <p class="fs-12 mb-0">
                                                <span class="chat-msg text-truncate"><span
                                                        class="group-indivudial">Rams:</span>Happy to be part of
                                                    this group</span>
                                                <span class="chat-read-icon float-end align-middle"><i
                                                        class="ri-check-double-fill"></i></span>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="chat-inactive checkforactive">
                                <a href="javascript:void(0);"
                                    onclick="changeTheInfo(this,'Friends Forever 😎','20','offline')">
                                    <div class="d-flex align-items-top">
                                        <div class="me-1 lh-1">
                                            <span class="avatar avatar-md offline me-2 avatar-rounded">
                                                <img src="../assets/images/faces/19.jpg" alt="img">
                                            </span>
                                        </div>
                                        <div class="flex-fill">
                                            <p class="mb-0 fw-semibold">
                                                Friends Forever 😎 <span class="float-end text-muted fw-normal fs-11">3
                                                    days
                                                    ago</span>
                                            </p>
                                            <p class="fs-12 mb-0">
                                                <span
                                                    class="chat-msg text-truncate">Simon,Melissa,Amanda,Patrick,Siddique</span>
                                                <span class="chat-read-icon float-end align-middle"><i
                                                        class="ri-check-double-fill"></i></span>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </li>

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
                                            class="msg-sent-time">11:51PM</span>
                                    </span>
                                    <div class="main-chat-msg">
                                        <div>
                                            <p class="mb-0">Who are you ?</p>
                                        </div>
                                        <div>
                                            <p class="mb-0">I don't know anyone named jeremiah.</p>
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
                        <li class="chat-item-start">
                            <div class="chat-list-inner">
                                <div class="chat-user-profile">
                                    <span class="avatar avatar-md online avatar-rounded">
                                        <img class="chatimageperson" src="../assets/images/faces/2.jpg" alt="img">
                                    </span>
                                </div>
                                <div class="ms-3">
                                    <span class="chatting-user-info chatnameperson">
                                        Emiley Jackson <span class="msg-sent-time">11:45PM</span>
                                    </span>
                                    <div class="main-chat-msg">
                                        <div>
                                            <p class="mb-0">Happy to talk with you,chat you later 👋</p>
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
                                        <i class="ri-image-line"></i>
                                    </span>
                                </div>
                                <div class="flex-fill">
                                    <p class="fs-12 fw-semibold mb-0">Img_15.Jpg</p>
                                    <p class="mb-0 text-muted fs-11">22,Oct 2022 - 10:18AM</p>
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
@endsection

@section('scripts')
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

            document.querySelector(".chatpersonstatus").innerText = status

            document.querySelector(".main-chart-wrapper").classList.add("responsive-chat-open")
        }
    </script>
@endsection
