@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2 mb-md-0">Xem Ảnh Sự Kiện</h1>


            <div class="">
                <form method="GET" action="{{ route('backend.club.events.images.gallery-view') }}"
                    class="row g-2 align-items-end">
                    <div class="col">
                        <label for="semester_id" class="form-label">Chọn học kỳ</label>
                        <select name="semester_id" id="semester_id" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Tất cả học kỳ --</option>
                            @foreach ($semesters as $semester)
                                <option value="{{ $semester->id }}" {{ $semesterId == $semester->id ? 'selected' : '' }}>
                                    {{ $semester->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <label for="event_id" class="form-label">Chọn sự kiện</label>
                        <select name="event_id" id="event_id" class="form-select" onchange="this.form.submit()"
                            {{ !$semesterId ? 'disabled' : '' }}>
                            <option value="">-- Tất cả sự kiện --</option>
                            @foreach ($events as $event)
                                <option value="{{ $event->id }}" {{ $eventId == $event->id ? 'selected' : '' }}>
                                    {{ $event->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>


        </div>

        <div class="row" id="galleryContainer">
            @forelse($images as $image)
                <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                    <div class="card position-relative gallery-image-card">
                        <a href="{{ asset('storage/' . $image->image_path) }}" class="glightbox" data-gallery="gallery1">
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="Event Image" class="card-img-top"
                                style="height: 200px; object-fit: cover;">
                        </a>
                        <div class="card-body p-2">
                            <a href="{{ route('backend.club.events.images.download', [$image->event_id, $image->id]) }}"
                                class="btn btn-outline-primary w-100">
                                <i class="fe fe-download"></i> Tải ảnh
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fe fe-image fs-24 mb-2"></i>
                        <p class="mb-0">Hãy lựa chọn một sự kiện để xem ảnh.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
