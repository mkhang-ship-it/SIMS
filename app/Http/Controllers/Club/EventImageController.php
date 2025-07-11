<?php
// Updated: Added download functionality for individual images, bulk delete functionality with clean JSON responses (no messages), multiple image upload support with array validation (max 30 images), and changed delete method to return JSON instead of redirect for AJAX compatibility. Modified image filename format to timestamp_event_name_6_random_numbers pattern. Fixed route conflicts by using separate bulk-delete endpoint. Fixed middleware permission check to return JSON for AJAX requests. Fixed bulk delete method to use string IDs instead of integers. Added galleryView method for public event image viewing with semester and event filters, only allowing viewing and downloading images (no upload/delete).

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
        $images = $event->images;
        return view('clubs.events.gallary', compact('event', 'images'));
    }

    public function store(Request $request, Event $event)
    {
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
            $eventName = Str::slug($event->name, '_'); // Chuyển tên sự kiện thành slug an toàn

            foreach ($request->file('images') as $imageFile) {
                // Tạo tên file theo format: timestamp_tên_sự_kiện_6_số_ngẫu_nhiên
                $timestamp = time() . '_' . random_int(100, 999); // Thêm random để tránh trùng lặp
                $randomNumbers = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT); // 6 số ngẫu nhiên
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
        if ($image->event_id !== $event->id) {
            return redirect()->route('backend.club.events.images.index', $event)
                ->with('error', 'Hình ảnh không thuộc về sự kiện này.');
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
        if ($image->event_id !== $event->id) {
            return response()->json(['success' => false], 403);
        }

        try {
            // Xóa file hình ảnh nếu có
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

    // Updated: Chỉ hiển thị các sự kiện của club hiện tại (lọc theo club_id của user đăng nhập)
    public function galleryView(Request $request)
    {
        $user = Auth::user();
        // Lấy danh sách học kỳ
        $semesters = Semester::orderBy('start_date', 'desc')->where('status', 'active')->get();
        $semesterId = $request->input('semester_id');
        $eventId = $request->input('event_id');
        $events = collect();
        $images = collect();
        $selectedEvent = null;

        if ($semesterId) {
            $events = Event::where('semester_id', $semesterId)
                ->where('club_id', $user->club_id)
                ->orderBy('event_date', 'desc')
                ->get();
        }
        if ($eventId) {
            $selectedEvent = Event::where('id', $eventId)
                ->where('club_id', $user->club_id)
                ->first();
            if ($selectedEvent) {
                $images = $selectedEvent->images;
            }
        }

        return view('clubs.events.gallery-view', compact('semesters', 'semesterId', 'events', 'eventId', 'images', 'selectedEvent'));
    }
}
