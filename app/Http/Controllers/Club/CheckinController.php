<?php
// Updated CheckinController - Refactored for new DB structure using UserClubRole and session('club_id')
// Now checks club membership via UserClubRole, not $user->club_id
// All queries and logic updated to match new multi-club membership model
// View logic updated if necessary to match new structure

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventCheckin;
use App\Models\User;
use App\Models\UserClubRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Semester;

class CheckinController extends Controller
{
    /**
     * Show QR scanner page for check-in
     */
    public function scanner()
    {
        return view('clubs.events.checkin-scanner');
    }

    /**
     * Get event information for QR hash (AJAX endpoint)
     */
    public function getEventInfo($qrHash)
    {
        $event = Event::with(['club', 'semester', 'slot', 'classRoom'])
            ->where('qr_hash', $qrHash)
            ->first();

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Mã QR không hợp lệ.'
            ], 404);
        }

        $user = Auth::user();
        $clubId = session('club_id');

        // Check if user is member of the club organizing this event via UserClubRole
        $userClubRole = UserClubRole::where('user_id', $user->id)
            ->where('club_id', $event->club_id)
            ->where('status', 'active')
            ->first();
        if (!$userClubRole || $clubId != $event->club_id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không phải thành viên của CLB tổ chức sự kiện này.'
            ], 403);
        }

        // Get existing check-in if any
        $existingCheckin = EventCheckin::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'event' => [
                    'id' => $event->id,
                    'name' => $event->name,
                    'description' => $event->description,
                    'image' => $event->image ? asset('storage/' . $event->image) : null,
                    'formatted_event_date' => $event->formatted_event_date,
                    'time_range' => $event->time_range,
                    'status_label' => $event->status_label,
                    'status_color' => $event->status_color,
                    'club' => [
                        'name' => $event->club->name ?? 'N/A'
                    ],
                    'classRoom' => [
                        'name' => $event->classRoom->name ?? 'N/A'
                    ],
                    'semester' => [
                        'name' => $event->semester->name ?? 'N/A'
                    ]
                ],
                'existingCheckin' => $existingCheckin ? [
                    'formatted_checkin_time' => $existingCheckin->formatted_checkin_time,
                    'distance' => $existingCheckin->distance ? round($existingCheckin->distance, 2) . ' km' : null,
                    'browser_name' => $existingCheckin->browser_name
                ] : null
            ]
        ]);
    }

    /**
     * Process check-in request
     */
    public function checkin(Request $request, $qrHash)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $event = Event::with(['classRoom'])
            ->where('qr_hash', $qrHash)
            ->first();

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Mã QR không hợp lệ.'
            ], 404);
        }

        $user = Auth::user();
        $clubId = session('club_id');

        // Check if user is member of the club organizing this event via UserClubRole
        $userClubRole = UserClubRole::where('user_id', $user->id)
            ->where('club_id', $event->club_id)
            ->where('status', 'active')
            ->first();
        if (!$userClubRole || $clubId != $event->club_id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không phải thành viên của CLB tổ chức sự kiện này.'
            ], 403);
        }

        // Validate check-in constraints
        $validationResult = EventCheckin::canCheckIn(
            $event,
            $user,
            $request->latitude,
            $request->longitude
        );

        if (!$validationResult['success']) {
            return response()->json([
                'success' => false,
                'message' => $validationResult['message']
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Create check-in record
            $checkin = EventCheckin::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'checkin_time' => Carbon::now(),
                'user_agent' => $request->userAgent(),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'distance' => $validationResult['distance']
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Check-in thành công!',
                'data' => [
                    'checkin_time' => $checkin->formatted_checkin_time,
                    'distance' => $checkin->distance ? round($checkin->distance, 2) . ' km' : 'N/A'
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi check-in. Vui lòng thử lại.'
            ], 500);
        }
    }

    /**
     * Show list of events that current user has participated in
     */
    public function myParticipatedEvents(Request $request)
    {
        $user = Auth::user();
        $clubId = session('club_id');

        // Get all active semesters for filter dropdown
        $semesters = Semester::where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        // Get all UserClubRole ids for this user in this club
        $userClubRoleIds = UserClubRole::where('user_id', $user->id)
            ->where('club_id', $clubId)
            ->where('status', 'active')
            ->pluck('id');

        // Base query for participated events
        $query = EventCheckin::with([
            'event' => function ($query) {
                $query->with(['club', 'semester', 'slot', 'classRoom']);
            }
        ])
            ->where('user_id', $user->id);

        // Apply semester filter if provided
        $semesterFilter = null;
        if ($request->filled('semester_id') && $request->semester_id != 'all') {
            $semesterFilter = $request->semester_id;
            $query->whereHas('event', function ($eventQuery) use ($request) {
                $eventQuery->where('semester_id', $request->semester_id);
            });
        }

        // Only show checkins for events in this club
        $query->whereHas('event', function ($eventQuery) use ($clubId) {
            $eventQuery->where('club_id', $clubId);
        });

        $participatedEvents = $query->orderBy('checkin_time', 'desc')
            ->paginate(15)
            ->appends($request->query()); // Preserve query parameters in pagination

        // Calculate statistics separately for accuracy
        $baseStatsQuery = EventCheckin::where('user_id', $user->id)
            ->whereHas('event', function ($eventQuery) use ($clubId) {
                $eventQuery->where('club_id', $clubId);
            });

        if ($semesterFilter) {
            $baseStatsQuery->whereHas('event', function ($eventQuery) use ($semesterFilter) {
                $eventQuery->where('semester_id', $semesterFilter);
            });
        }

        $statistics = [
            'total' => $baseStatsQuery->count(),
            'completed' => $baseStatsQuery->whereHas('event', function ($eventQuery) {
                $eventQuery->where('status', 'completed');
            })->count(),
            'active' => $baseStatsQuery->whereHas('event', function ($eventQuery) {
                $eventQuery->where('status', 'active');
            })->count(),
            'pending' => $baseStatsQuery->whereHas('event', function ($eventQuery) {
                $eventQuery->where('status', 'pending');
            })->count()
        ];

        return view('clubs.events.my-participated', compact('participatedEvents', 'semesters', 'statistics'));
    }
}
