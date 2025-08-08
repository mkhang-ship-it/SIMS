<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Semester;
use App\Models\Club;
use App\Models\EventImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class EventImageController extends Controller
{
    public function index(Request $request)
    {
        // Lấy danh sách học kỳ
        $semesters = Semester::orderBy('start_date', 'desc')->where('status', 'active')->get();
        $semesterId = $request->input('semester_id');
        $clubId = $request->input('club_id');
        $eventId = $request->input('event_id');

        $clubs = collect();
        $events = collect();
        $images = collect();
        $selectedEvent = null;

        // Bước 1: Nếu có chọn học kỳ, lấy danh sách câu lạc bộ có sự kiện trong học kỳ đó
        if ($semesterId) {
            $clubs = Club::whereHas('events', function ($query) use ($semesterId) {
                $query->where('semester_id', $semesterId);
            })->where('status', 'active')->orderBy('name')->get();
        }

        // Bước 2: Nếu có chọn câu lạc bộ, lấy danh sách sự kiện của câu lạc bộ trong học kỳ đó
        if ($semesterId && $clubId) {
            $events = Event::where('semester_id', $semesterId)
                ->where('club_id', $clubId)
                ->orderBy('event_date', 'desc')
                ->get();
        }

        // Bước 3: Nếu có chọn sự kiện, lấy ảnh của sự kiện đó
        if ($eventId) {
            $selectedEvent = Event::find($eventId);
            if ($selectedEvent) {
                $images = $selectedEvent->images;
            }
        }

        return view('events.gallery-view', compact(
            'semesters',
            'semesterId',
            'clubs',
            'clubId',
            'events',
            'eventId',
            'images',
            'selectedEvent'
        ));
    }

    public function download(Event $event, EventImage $image)
    {
        if ($image->event_id !== $event->id) {
            return redirect()->route('backend.events.images.gallery-view')
                ->with('error', 'Hình ảnh không thuộc về sự kiện này.');
        }

        if (!Storage::disk('public')->exists($image->image_path)) {
            return redirect()->back()->with('error', 'File không tồn tại.');
        }

        $filename = basename($image->image_path);
        $filePath = Storage::disk('public')->path($image->image_path);
        return Response::download($filePath, $filename);
    }
}
