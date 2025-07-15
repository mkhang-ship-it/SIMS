<?php
// Updated: Refactored for new DB structure (UUIDs, session('club_id') scoping, eager loading, and permission checks).
// - Uses session('club_id') for club scoping (like MemberController)
// - Eager loads event relationship for images
// - All CRUD methods updated for new DB (UUIDs, validation, file handling)
// - All responses and permission checks updated for new approach
// - Updated to match MemberController style and new DB structure

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventImage;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class EventImageController extends Controller
{
    public function index(Event $event)
    {
        $clubId = session('club_id');
        if ($event->club_id !== $clubId) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Bạn không có quyền truy cập sự kiện này!');
        }
        $images = $event->images()->orderByDesc('created_at')->get();
        return view('clubs.events.gallary', compact('event', 'images'));
    }

    public function store(Request $request, Event $event)
    {
        $clubId = session('club_id');
        if ($event->club_id !== $clubId) {
            return redirect()->route('backend.club.events.index')
                ->with('error', 'Bạn không có quyền thêm ảnh cho sự kiện này!');
        }
        $request->validate([
            'images' => 'required|array|min:1|max:30',
            'images.*' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:5048'
            ],
        ]);
        try {
            $uploadedImages = [];
            $eventName = Str::slug($event->name, '_');
            foreach ($request->file('images') as $imageFile) {
                $timestamp = time() . '_' . random_int(100, 999);
                $randomNumbers = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $extension = $imageFile->getClientOriginalExtension();
                $filename = $timestamp . '_' . $eventName . '_' . $randomNumbers . '.' . $extension;
                $path = $imageFile->storeAs('event_images', $filename, 'public');
                $uploadedImages[] = $event->images()->create([
                    'image_path' => $path,
                ]);
            }
            $count = count($uploadedImages);
            return redirect()->route('backend.club.events.images.index', $event)
                ->with('success', "Đã thêm thành công {$count} hình ảnh.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tải lên hình ảnh.')
                ->withInput();
        }
    }

    public function download(Event $event, EventImage $image)
    {
        $clubId = session('club_id');
        if ($event->club_id !== $clubId || $image->event_id !== $event->id) {
            return redirect()->route('backend.club.events.images.index', $event)
                ->with('error', 'Bạn không có quyền tải ảnh này!');
        }
        if (!Storage::disk('public')->exists($image->image_path)) {
            return redirect()->back()->with('error', 'File không tồn tại.');
        }
        $filename = basename($image->image_path);
        $filePath = Storage::disk('public')->path($image->image_path);
        return Response::download($filePath, $filename);
    }

    public function destroy(Event $event, EventImage $image)
    {
        $clubId = session('club_id');
        if ($event->club_id !== $clubId || $image->event_id !== $event->id) {
            return response()->json(['success' => false], 403);
        }
        try {
            if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    public function bulkDelete(Request $request, Event $event)
    {
        $clubId = session('club_id');
        if ($event->club_id !== $clubId) {
            return response()->json(['success' => false], 403);
        }
        try {
            $request->validate([
                'image_ids' => 'required|array',
                'image_ids.*' => 'string|exists:event_images,id'
            ]);
            $imageIds = $request->image_ids;
            $images = EventImage::whereIn('id', $imageIds)->where('event_id', $event->id)->get();
            if ($images->count() !== count($imageIds)) {
                return response()->json(['success' => false], 403);
            }
            $deletedCount = 0;
            foreach ($images as $image) {
                if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
                $image->delete();
                $deletedCount++;
            }
            return response()->json(['success' => true, 'deleted_count' => $deletedCount]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // Chỉ hiển thị các sự kiện của club hiện tại (lọc theo club_id của user đăng nhập)
    public function galleryView(Request $request)
    {
        $user = Auth::user();
        $clubId = session('club_id');
        $semesters = Semester::orderBy('start_date', 'desc')->where('status', 'active')->get();
        $semesterId = $request->input('semester_id');
        $eventId = $request->input('event_id');
        $events = collect();
        $images = collect();
        $selectedEvent = null;
        if ($semesterId) {
            $events = Event::where('semester_id', $semesterId)
                ->where('club_id', $clubId)
                ->orderBy('event_date', 'desc')
                ->get();
        }
        if ($eventId) {
            $selectedEvent = Event::where('id', $eventId)
                ->where('club_id', $clubId)
                ->first();
            if ($selectedEvent) {
                $images = $selectedEvent->images()->orderByDesc('created_at')->get();
            }
        }
        return view('clubs.events.gallery-view', compact('semesters', 'semesterId', 'events', 'eventId', 'images', 'selectedEvent'));
    }
}
