<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications for the current club
     */
    /**
     * Updated: Unread notifications are shown first, then read notifications, both sorted by created_at desc.
     */
    public function index()
    {
        $clubId = session('club_id');

        $notifications = Notification::where(function ($query) use ($clubId) {
                $query->where('target_type', 'all_clubs')
                    ->orWhereJsonContains('target_club_ids', $clubId);
            })
            ->where('status', 'active')
            ->with(['sender', 'reads' => function ($query) use ($clubId) {
                $query->where('club_id', $clubId);
            }])
            ->withCount(['reads as is_read' => function ($query) use ($clubId) {
                $query->where('club_id', $clubId)->whereNotNull('read_at');
            }])
            ->orderBy('is_read', 'asc') // Unread first (is_read = 0), then read (is_read = 1)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('clubs.notifications.index', compact('notifications'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification)
    {
        $clubId = session('club_id');

        // Check if this notification is for the current club
        if (
            $notification->target_type !== 'all_clubs' &&
            !in_array($clubId, $notification->target_club_ids ?? [])
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thao tác với thông báo này'
            ], 403);
        }

        $notification->markAsReadByClub($clubId);

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu thông báo là đã đọc'
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $clubId = session('club_id');

        $notifications = Notification::where(function ($query) use ($clubId) {
            $query->where('target_type', 'all_clubs')
                ->orWhereJsonContains('target_club_ids', $clubId);
        })
            ->where('status', 'active')
            ->whereDoesntHave('reads', function ($query) use ($clubId) {
                $query->where('club_id', $clubId)->whereNotNull('read_at');
            })
            ->get();

        foreach ($notifications as $notification) {
            $notification->markAsReadByClub($clubId);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu tất cả thông báo là đã đọc',
            'count' => $notifications->count()
        ]);
    }

    /**
     * Get unread notification count
     */
    public function unreadCount()
    {
        $clubId = session('club_id');

        $count = Notification::where(function ($query) use ($clubId) {
            $query->where('target_type', 'all_clubs')
                ->orWhereJsonContains('target_club_ids', $clubId);
        })
            ->where('status', 'active')
            ->whereDoesntHave('reads', function ($query) use ($clubId) {
                $query->where('club_id', $clubId)->whereNotNull('read_at');
            })
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Get recent unread notifications for sidebar dropdown
     * Updated: Modified to only return unread notifications instead of all recent notifications
     */
    public function recent()
    {
        $clubId = session('club_id');

        $notifications = Notification::where(function ($query) use ($clubId) {
            $query->where('target_type', 'all_clubs')
                ->orWhereJsonContains('target_club_ids', $clubId);
        })
            ->where('status', 'active')
            ->whereDoesntHave('reads', function ($query) use ($clubId) {
                $query->where('club_id', $clubId)->whereNotNull('read_at');
            })
            ->with(['sender', 'reads' => function ($query) use ($clubId) {
                $query->where('club_id', $clubId);
            }])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Add is_read_by_club property to each notification (should all be false for unread)
        $notifications->each(function ($notification) use ($clubId) {
            $notification->is_read_by_club = false;
        });

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }
}
