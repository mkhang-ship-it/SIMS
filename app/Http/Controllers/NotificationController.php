<?php
// Updated: Changed validation in store() and update() methods to use simple $request->validate() instead of Validator::make()

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications
     */
    public function index()
    {
        $notifications = Notification::with(['sender'])
            ->orderBy('created_at', 'desc')
            ->get();

        $clubs = Club::where('status', 'active')->orderBy('name')->get();

        return view('notifications.index', compact('notifications', 'clubs'));
    }


    /**
     * Store a newly created notification
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_type' => 'required|in:all_clubs,specific_clubs',
            'target_club_ids' => 'required_if:target_type,specific_clubs|array',
            'target_club_ids.*' => 'exists:clubs,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // 2MB max
            'files.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar|max:10240' // 10MB max per file
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('notifications/images', $imageName, 'public');
        }

        // Handle files upload
        $filesPath = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('notifications/files', $fileName, 'public');
                $filesPath[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $filePath,
                    'size' => $file->getSize()
                ];
            }
        }

        $notification = Notification::create([
            'title' => $request->title,
            'content' => $request->content,
            'image_path' => $imagePath,
            'files_path' => !empty($filesPath) ? $filesPath : null,
            'target_type' => $request->target_type,
            'target_club_ids' => $request->target_type === 'specific_clubs' ? $request->target_club_ids : null,
            'sender_id' => Auth::id(),
            'status' => 'active'
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thông báo đã được gửi thành công',
                'data' => $notification
            ]);
        }

        return redirect()->route('backend.notifications.index')
            ->with('success', 'Thông báo đã được gửi thành công');
    }

    /**
     * Update the specified notification
     */
    public function update(Request $request, Notification $notification)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_type' => 'required|in:all_clubs,specific_clubs',
            'target_club_ids' => 'required_if:target_type,specific_clubs|array',
            'target_club_ids.*' => 'exists:clubs,id',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // 2MB max
            'files.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar|max:10240' // 10MB max per file
        ]);

        // Handle image upload
        $imagePath = $notification->image_path;
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($notification->image_path && Storage::disk('public')->exists($notification->image_path)) {
                Storage::disk('public')->delete($notification->image_path);
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('notifications/images', $imageName, 'public');
        }

        // Handle files upload
        $filesPath = $notification->files_path ?? [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('notifications/files', $fileName, 'public');
                $filesPath[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $filePath,
                    'size' => $file->getSize()
                ];
            }
        }

        $notification->update([
            'title' => $request->title,
            'content' => $request->content,
            'image_path' => $imagePath,
            'files_path' => !empty($filesPath) ? $filesPath : null,
            'target_type' => $request->target_type,
            'target_club_ids' => $request->target_type === 'specific_clubs' ? $request->target_club_ids : null,
            'status' => $request->status
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thông báo đã được cập nhật thành công',
                'data' => $notification
            ]);
        }

        return redirect()->route('backend.notifications.index')
            ->with('success', 'Thông báo đã được cập nhật thành công');
    }

    /**
     * Remove the specified notification
     */
    public function destroy(Notification $notification)
    {
        // Delete associated image if exists
        if ($notification->image_path && Storage::disk('public')->exists($notification->image_path)) {
            Storage::disk('public')->delete($notification->image_path);
        }

        // Delete associated files if exist
        if ($notification->files_path) {
            foreach ($notification->files_path as $file) {
                if (isset($file['path']) && Storage::disk('public')->exists($file['path'])) {
                    Storage::disk('public')->delete($file['path']);
                }
            }
        }

        $notification->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thông báo đã được xóa thành công'
            ]);
        }

        return redirect()->route('backend.notifications.index')
            ->with('success', 'Thông báo đã được xóa thành công');
    }

    /**
     * Get notification statistics
     */
    public function statistics()
    {
        $totalNotifications = Notification::count();
        $activeNotifications = Notification::where('status', 'active')->count();
        $totalReads = \App\Models\NotificationRead::count();
        $totalClubs = Club::where('status', 'active')->count();

        $recentNotifications = Notification::with(['sender'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_notifications' => $totalNotifications,
                'active_notifications' => $activeNotifications,
                'total_reads' => $totalReads,
                'total_clubs' => $totalClubs,
                'recent_notifications' => $recentNotifications
            ]
        ]);
    }
}
